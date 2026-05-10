<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
header('Content-Type: application/json');
require_once '../includes/db.php';

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];
$db = getDB();

try {
    switch ($method) {

        case 'GET':

            // Obtener pacientes y médicos para los selects del modal
            if (isset($_GET['action']) && $_GET['action'] === 'selects') {
                $pacientes = $db->query("SELECT id, nombre, apellido, rut FROM pacientes WHERE activo = true ORDER BY nombre ASC")->fetchAll();
                $medicos = $db->query("SELECT id, nombre, apellido FROM usuarios WHERE rol = 'medico' AND activo = true ORDER BY nombre ASC")->fetchAll();
                echo json_encode(['success' => true, 'pacientes' => $pacientes, 'medicos' => $medicos]);
                break;
            }

            // Obtener una cita por ID
            if (isset($_GET['id'])) {
                $stmt = $db->prepare("SELECT * FROM citas WHERE id = :id LIMIT 1");
                $stmt->execute([':id' => $_GET['id']]);
                $cita = $stmt->fetch();
                if ($cita) {
                    $cita['fecha'] = substr($cita['fecha'], 0, 10);
                    $cita['hora'] = substr($cita['hora'], 0, 5);
                    echo json_encode(['success' => true, 'data' => $cita]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Cita no encontrada']);
                }
                break;
            }

            // Listar citas con filtros y paginación
            $pagina = (int)($_GET['pagina'] ?? 1);
            $por_pagina = (int)($_GET['por_pagina'] ?? 10);
            $busqueda = trim($_GET['busqueda'] ?? '');
            $estado = trim($_GET['estado'] ?? '');
            $fecha = trim($_GET['fecha'] ?? '');
            $offset = ($pagina - 1) * $por_pagina;

            $where = "WHERE 1=1";
            $params = [];

            if ($busqueda) {
                $where .= " AND (p.nombre ILIKE :busqueda OR p.apellido ILIKE :busqueda OR u.nombre ILIKE :busqueda)";
                $params[':busqueda'] = "%{$busqueda}%";
            }
            if ($estado) {
                $where .= " AND c.estado = :estado";
                $params[':estado'] = $estado;
            }
            if ($fecha) {
                $where .= " AND c.fecha = :fecha";
                $params[':fecha'] = $fecha;
            }

            $sqlBase = "FROM citas c
                JOIN pacientes p ON c.paciente_id = p.id
                JOIN usuarios u ON c.medico_id = u.id
                {$where}";

            // Total
            $stmtTotal = $db->prepare("SELECT COUNT(*) as total {$sqlBase}");
            $stmtTotal->execute($params);
            $total = $stmtTotal->fetch()['total'];

            // Datos
            $stmt = $db->prepare("
                SELECT
                    c.id, c.fecha, c.hora, c.motivo, c.estado, c.observaciones,
                    c.paciente_id, c.medico_id,
                    p.nombre || ' ' || p.apellido AS paciente,
                    u.nombre AS medico,
                    TO_CHAR(c.fecha, 'DD/MM/YYYY') AS fecha_formato,
                    SUBSTR(c.hora::text, 1, 5) AS hora_formato
                SELECT c.id, c.fecha, c.hora, c.motivo, c.estado,
                    p.nombre || ' ' || p.apellido AS paciente,
                    u.nombre AS medico,
                    TO_CHAR(c.fecha, 'DD/MM/YYYY') AS fecha,
                    SUBSTR(c.hora::text, 1, 5) AS hora
                {$sqlBase}
                ORDER BY c.fecha DESC, c.hora ASC
                LIMIT :limit OFFSET :offset
            ");
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            $stmt->bindValue(':limit', $por_pagina, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            $citas = $stmt->fetchAll();

            echo json_encode([
                'success' => true,
                'data' => $citas,
                'total' => $total,
                'pagina' => $pagina
            ]);
            break;

        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);

            if (!($data['paciente_id'] ?? '') || !($data['medico_id'] ?? '') || !($data['fecha'] ?? '') || !($data['hora'] ?? '')) {
                echo json_encode(['success' => false, 'message' => 'Campos obligatorios incompletos']);
                exit;
            }

            $stmt = $db->prepare("
                INSERT INTO citas (paciente_id, medico_id, fecha, hora, motivo, estado, observaciones)
                VALUES (:paciente_id, :medico_id, :fecha, :hora, :motivo, :estado, :observaciones)
            ");
            $stmt->execute([
                ':paciente_id'   => $data['paciente_id'],
                ':medico_id'     => $data['medico_id'],
                ':fecha'         => $data['fecha'],
                ':hora'          => $data['hora'],
                ':motivo'        => $data['motivo'] ?? null,
                ':estado'        => $data['estado'] ?? 'pendiente',
                ':observaciones' => $data['observaciones'] ?? null,
            ]);

            echo json_encode(['success' => true, 'message' => 'Cita agendada correctamente']);
            break;

        case 'PUT':
            $data = json_decode(file_get_contents('php://input'), true);
            $id = trim($data['id'] ?? '');

            if (!$id) {
                echo json_encode(['success' => false, 'message' => 'ID requerido']);
                exit;
            }

            $stmt = $db->prepare("
                UPDATE citas SET
                    paciente_id = :paciente_id,
                    medico_id = :medico_id,
                    fecha = :fecha,
                    hora = :hora,
                    motivo = :motivo,
                    estado = :estado,
                    observaciones = :observaciones
                WHERE id = :id
            ");
            $stmt->execute([
                ':id'            => $id,
                ':paciente_id'   => $data['paciente_id'],
                ':medico_id'     => $data['medico_id'],
                ':fecha'         => $data['fecha'],
                ':hora'          => $data['hora'],
                ':motivo'        => $data['motivo'] ?? null,
                ':estado'        => $data['estado'] ?? 'pendiente',
                ':observaciones' => $data['observaciones'] ?? null,
            ]);

            echo json_encode(['success' => true, 'message' => 'Cita actualizada correctamente']);
            break;

        case 'DELETE':
            $data = json_decode(file_get_contents('php://input'), true);
            $id = trim($data['id'] ?? '');

            if (!$id) {
                echo json_encode(['success' => false, 'message' => 'ID requerido']);
                exit;
            }

            $stmt = $db->prepare("DELETE FROM citas WHERE id = :id");
            $stmt->execute([':id' => $id]);

            echo json_encode(['success' => true, 'message' => 'Cita eliminada correctamente']);
            break;

        default:
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            break;
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
