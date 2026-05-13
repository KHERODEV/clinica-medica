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

            // Obtener una receta por ID
            if (isset($_GET['id'])) {
                $stmt = $db->prepare("SELECT * FROM recetas WHERE id = :id LIMIT 1");
                $stmt->execute([':id' => $_GET['id']]);
                $receta = $stmt->fetch();
                if ($receta) {
                    echo json_encode(['success' => true, 'data' => $receta]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Receta no encontrada']);
                }
                break;
            }

            // Listar recetas con filtros
            $pagina = (int)($_GET['pagina'] ?? 1);
            $por_pagina = (int)($_GET['por_pagina'] ?? 10);
            $busqueda = trim($_GET['busqueda'] ?? '');
            $fecha = trim($_GET['fecha'] ?? '');
            $offset = ($pagina - 1) * $por_pagina;

            $where = "WHERE 1=1";
            $params = [];

            if ($busqueda) {
                $where .= " AND (p.nombre ILIKE :busqueda OR p.apellido ILIKE :busqueda OR r.medicamento ILIKE :busqueda)";
                $params[':busqueda'] = "%{$busqueda}%";
            }
            if ($fecha) {
                $where .= " AND DATE(r.created_at) = :fecha";
                $params[':fecha'] = $fecha;
            }

            $sqlBase = "FROM recetas r
                JOIN pacientes p ON r.paciente_id = p.id
                JOIN usuarios u ON r.medico_id = u.id
                {$where}";

            // Total
            $stmtTotal = $db->prepare("SELECT COUNT(*) as total {$sqlBase}");
            $stmtTotal->execute($params);
            $total = $stmtTotal->fetch()['total'];

            // Datos
            $stmt = $db->prepare("
                SELECT
                    r.id, r.medicamento, r.dosis, r.frecuencia, r.duracion, r.indicaciones,
                    r.paciente_id, r.medico_id,
                    p.nombre || ' ' || p.apellido AS paciente,
                    u.nombre AS medico,
                    TO_CHAR(r.created_at, 'DD/MM/YYYY') AS fecha
                {$sqlBase}
                ORDER BY r.created_at DESC
                LIMIT :limit OFFSET :offset
            ");
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            $stmt->bindValue(':limit', $por_pagina, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            $recetas = $stmt->fetchAll();

            echo json_encode([
                'success' => true,
                'data' => $recetas,
                'total' => $total,
                'pagina' => $pagina
            ]);
            break;

        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);

            if (!($data['paciente_id'] ?? '') || !($data['medico_id'] ?? '') || !($data['medicamento'] ?? '') || !($data['dosis'] ?? '') || !($data['frecuencia'] ?? '') || !($data['duracion'] ?? '')) {
                echo json_encode(['success' => false, 'message' => 'Campos obligatorios incompletos']);
                exit;
            }

            $stmt = $db->prepare("
                INSERT INTO recetas (paciente_id, medico_id, medicamento, dosis, frecuencia, duracion, indicaciones)
                VALUES (:paciente_id, :medico_id, :medicamento, :dosis, :frecuencia, :duracion, :indicaciones)
            ");
            $stmt->execute([
                ':paciente_id'  => $data['paciente_id'],
                ':medico_id'    => $data['medico_id'],
                ':medicamento'  => $data['medicamento'],
                ':dosis'        => $data['dosis'],
                ':frecuencia'   => $data['frecuencia'],
                ':duracion'     => $data['duracion'],
                ':indicaciones' => $data['indicaciones'] ?? null,
            ]);

            echo json_encode(['success' => true, 'message' => 'Receta creada correctamente']);
            break;

        case 'PUT':
            $data = json_decode(file_get_contents('php://input'), true);
            $id = trim($data['id'] ?? '');

            if (!$id) {
                echo json_encode(['success' => false, 'message' => 'ID requerido']);
                exit;
            }

            $stmt = $db->prepare("
                UPDATE recetas SET
                    paciente_id = :paciente_id,
                    medico_id = :medico_id,
                    medicamento = :medicamento,
                    dosis = :dosis,
                    frecuencia = :frecuencia,
                    duracion = :duracion,
                    indicaciones = :indicaciones
                WHERE id = :id
            ");
            $stmt->execute([
                ':id'           => $id,
                ':paciente_id'  => $data['paciente_id'],
                ':medico_id'    => $data['medico_id'],
                ':medicamento'  => $data['medicamento'],
                ':dosis'        => $data['dosis'],
                ':frecuencia'   => $data['frecuencia'],
                ':duracion'     => $data['duracion'],
                ':indicaciones' => $data['indicaciones'] ?? null,
            ]);

            echo json_encode(['success' => true, 'message' => 'Receta actualizada correctamente']);
            break;

        case 'DELETE':
            $data = json_decode(file_get_contents('php://input'), true);
            $id = trim($data['id'] ?? '');

            if (!$id) {
                echo json_encode(['success' => false, 'message' => 'ID requerido']);
                exit;
            }

            $stmt = $db->prepare("DELETE FROM recetas WHERE id = :id");
            $stmt->execute([':id' => $id]);

            echo json_encode(['success' => true, 'message' => 'Receta eliminada correctamente']);
            break;

        default:
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            break;
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
