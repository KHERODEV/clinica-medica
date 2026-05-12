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

            // Selects para el modal
            if (isset($_GET['action']) && $_GET['action'] === 'selects') {
                $pacientes = $db->query("SELECT id, nombre, apellido, rut FROM pacientes WHERE activo = true ORDER BY nombre ASC")->fetchAll();
                $medicos = $db->query("SELECT id, nombre, apellido FROM usuarios WHERE rol = 'medico' AND activo = true ORDER BY nombre ASC")->fetchAll();
                echo json_encode(['success' => true, 'pacientes' => $pacientes, 'medicos' => $medicos]);
                break;
            }

            // Obtener un registro por ID
            if (isset($_GET['id'])) {
                $stmt = $db->prepare("SELECT * FROM historial WHERE id = :id LIMIT 1");
                $stmt->execute([':id' => $_GET['id']]);
                $registro = $stmt->fetch();
                if ($registro) {
                    $registro['fecha'] = substr($registro['fecha'], 0, 10);
                    echo json_encode(['success' => true, 'data' => $registro]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Registro no encontrado']);
                }
                break;
            }

            // Listar historial con filtros
            $pagina = (int)($_GET['pagina'] ?? 1);
            $por_pagina = (int)($_GET['por_pagina'] ?? 10);
            $busqueda = trim($_GET['busqueda'] ?? '');
            $fecha = trim($_GET['fecha'] ?? '');
            $offset = ($pagina - 1) * $por_pagina;

            $where = "WHERE 1=1";
            $params = [];

            if ($busqueda) {
                $where .= " AND (p.nombre ILIKE :busqueda OR p.apellido ILIKE :busqueda OR h.diagnostico ILIKE :busqueda)";
                $params[':busqueda'] = "%{$busqueda}%";
            }
            if ($fecha) {
                $where .= " AND h.fecha = :fecha";
                $params[':fecha'] = $fecha;
            }

            $sqlBase = "FROM historial h
                JOIN pacientes p ON h.paciente_id = p.id
                JOIN usuarios u ON h.medico_id = u.id
                {$where}";

            // Total
            $stmtTotal = $db->prepare("SELECT COUNT(*) as total {$sqlBase}");
            $stmtTotal->execute($params);
            $total = $stmtTotal->fetch()['total'];

            // Datos
            $stmt = $db->prepare("
                SELECT
                    h.id, h.diagnostico, h.tratamiento, h.sintomas, h.observaciones,
                    h.paciente_id, h.medico_id,
                    p.nombre || ' ' || p.apellido AS paciente,
                    u.nombre AS medico,
                    TO_CHAR(h.fecha, 'DD/MM/YYYY') AS fecha
                {$sqlBase}
                ORDER BY h.fecha DESC
                LIMIT :limit OFFSET :offset
            ");
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            $stmt->bindValue(':limit', $por_pagina, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            $registros = $stmt->fetchAll();

            echo json_encode([
                'success' => true,
                'data' => $registros,
                'total' => $total,
                'pagina' => $pagina
            ]);
            break;

        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);

            if (!($data['paciente_id'] ?? '') || !($data['medico_id'] ?? '') || !($data['diagnostico'] ?? '') || !($data['fecha'] ?? '')) {
                echo json_encode(['success' => false, 'message' => 'Campos obligatorios incompletos']);
                exit;
            }

            $stmt = $db->prepare("
                INSERT INTO historial (paciente_id, medico_id, fecha, sintomas, diagnostico, tratamiento, observaciones)
                VALUES (:paciente_id, :medico_id, :fecha, :sintomas, :diagnostico, :tratamiento, :observaciones)
            ");
            $stmt->execute([
                ':paciente_id'   => $data['paciente_id'],
                ':medico_id'     => $data['medico_id'],
                ':fecha'         => $data['fecha'],
                ':sintomas'      => $data['sintomas'] ?? null,
                ':diagnostico'   => $data['diagnostico'],
                ':tratamiento'   => $data['tratamiento'] ?? null,
                ':observaciones' => $data['observaciones'] ?? null,
            ]);

            echo json_encode(['success' => true, 'message' => 'Registro creado correctamente']);
            break;

        case 'PUT':
            $data = json_decode(file_get_contents('php://input'), true);
            $id = trim($data['id'] ?? '');

            if (!$id) {
                echo json_encode(['success' => false, 'message' => 'ID requerido']);
                exit;
            }

            $stmt = $db->prepare("
                UPDATE historial SET
                    paciente_id = :paciente_id,
                    medico_id = :medico_id,
                    fecha = :fecha,
                    sintomas = :sintomas,
                    diagnostico = :diagnostico,
                    tratamiento = :tratamiento,
                    observaciones = :observaciones
                WHERE id = :id
            ");
            $stmt->execute([
                ':id'            => $id,
                ':paciente_id'   => $data['paciente_id'],
                ':medico_id'     => $data['medico_id'],
                ':fecha'         => $data['fecha'],
                ':sintomas'      => $data['sintomas'] ?? null,
                ':diagnostico'   => $data['diagnostico'],
                ':tratamiento'   => $data['tratamiento'] ?? null,
                ':observaciones' => $data['observaciones'] ?? null,
            ]);

            echo json_encode(['success' => true, 'message' => 'Registro actualizado correctamente']);
            break;

        case 'DELETE':
            $data = json_decode(file_get_contents('php://input'), true);
            $id = trim($data['id'] ?? '');

            if (!$id) {
                echo json_encode(['success' => false, 'message' => 'ID requerido']);
                exit;
            }

            $stmt = $db->prepare("DELETE FROM historial WHERE id = :id");
            $stmt->execute([':id' => $id]);

            echo json_encode(['success' => true, 'message' => 'Registro eliminado correctamente']);
            break;

        default:
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            break;
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
