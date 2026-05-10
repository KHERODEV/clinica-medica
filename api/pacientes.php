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

        // LISTAR / OBTENER UN PACIENTE
        case 'GET':
            // Obtener un paciente por ID
            if (isset($_GET['id'])) {
                $stmt = $db->prepare("SELECT * FROM pacientes WHERE id = :id LIMIT 1");
                $stmt->execute([':id' => $_GET['id']]);
                $paciente = $stmt->fetch();
                if ($paciente) {
                    echo json_encode(['success' => true, 'data' => $paciente]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Paciente no encontrado']);
                }
                break;
            }

            // Listar pacientes con búsqueda y paginación
            $pagina = (int)($_GET['pagina'] ?? 1);
            $por_pagina = (int)($_GET['por_pagina'] ?? 10);
            $busqueda = trim($_GET['busqueda'] ?? '');
            $offset = ($pagina - 1) * $por_pagina;

            $where = "WHERE activo = true";
            $params = [];

            if ($busqueda) {
                $where .= " AND (nombre ILIKE :busqueda OR apellido ILIKE :busqueda OR rut ILIKE :busqueda)";
                $params[':busqueda'] = "%{$busqueda}%";
            }

            // Total
            $stmtTotal = $db->prepare("SELECT COUNT(*) as total FROM pacientes {$where}");
            $stmtTotal->execute($params);
            $total = $stmtTotal->fetch()['total'];

            // Datos
            $params[':limit'] = $por_pagina;
            $params[':offset'] = $offset;
            $stmt = $db->prepare("SELECT * FROM pacientes {$where} ORDER BY created_at DESC LIMIT :limit OFFSET :offset");
            $stmt->bindValue(':limit', $por_pagina, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            if ($busqueda) {
                $stmt->bindValue(':busqueda', "%{$busqueda}%");
            }
            $stmt->execute();
            $pacientes = $stmt->fetchAll();

            echo json_encode([
                'success' => true,
                'data' => $pacientes,
                'total' => $total,
                'pagina' => $pagina,
                'por_pagina' => $por_pagina
            ]);
            break;

        // CREAR PACIENTE
        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);

            $nombre = trim($data['nombre'] ?? '');
            $apellido = trim($data['apellido'] ?? '');
            $rut = trim($data['rut'] ?? '');
            $fecha_nacimiento = trim($data['fecha_nacimiento'] ?? '');

            if (!$nombre || !$apellido || !$rut || !$fecha_nacimiento) {
                echo json_encode(['success' => false, 'message' => 'Campos obligatorios incompletos']);
                exit;
            }

            // Verificar RUT duplicado
            $stmtCheck = $db->prepare("SELECT id FROM pacientes WHERE rut = :rut");
            $stmtCheck->execute([':rut' => $rut]);
            if ($stmtCheck->fetch()) {
                echo json_encode(['success' => false, 'message' => 'El RUT ya está registrado']);
                exit;
            }

            $stmt = $db->prepare("
                INSERT INTO pacientes (nombre, apellido, rut, fecha_nacimiento, genero, telefono, email, direccion, prevision, contacto_emergencia, telefono_emergencia)
                VALUES (:nombre, :apellido, :rut, :fecha_nacimiento, :genero, :telefono, :email, :direccion, :prevision, :contacto_emergencia, :telefono_emergencia)
            ");
            $stmt->execute([
                ':nombre' => $nombre,
                ':apellido' => $apellido,
                ':rut' => $rut,
                ':fecha_nacimiento' => $fecha_nacimiento,
                ':genero' => $data['genero'] ?? null,
                ':telefono' => $data['telefono'] ?? null,
                ':email' => $data['email'] ?? null,
                ':direccion' => $data['direccion'] ?? null,
                ':prevision' => $data['prevision'] ?? null,
                ':contacto_emergencia' => $data['contacto_emergencia'] ?? null,
                ':telefono_emergencia' => $data['telefono_emergencia'] ?? null,
            ]);

            echo json_encode(['success' => true, 'message' => 'Paciente registrado correctamente']);
            break;

        // EDITAR PACIENTE
        case 'PUT':
            $data = json_decode(file_get_contents('php://input'), true);
            $id = trim($data['id'] ?? '');

            if (!$id) {
                echo json_encode(['success' => false, 'message' => 'ID requerido']);
                exit;
            }

            $stmt = $db->prepare("
                UPDATE pacientes SET
                    nombre = :nombre,
                    apellido = :apellido,
                    rut = :rut,
                    fecha_nacimiento = :fecha_nacimiento,
                    genero = :genero,
                    telefono = :telefono,
                    email = :email,
                    direccion = :direccion,
                    prevision = :prevision,
                    contacto_emergencia = :contacto_emergencia,
                    telefono_emergencia = :telefono_emergencia
                WHERE id = :id
            ");
            $stmt->execute([
                ':id' => $id,
                ':nombre' => trim($data['nombre'] ?? ''),
                ':apellido' => trim($data['apellido'] ?? ''),
                ':rut' => trim($data['rut'] ?? ''),
                ':fecha_nacimiento' => trim($data['fecha_nacimiento'] ?? ''),
                ':genero' => $data['genero'] ?? null,
                ':telefono' => $data['telefono'] ?? null,
                ':email' => $data['email'] ?? null,
                ':direccion' => $data['direccion'] ?? null,
                ':prevision' => $data['prevision'] ?? null,
                ':contacto_emergencia' => $data['contacto_emergencia'] ?? null,
                ':telefono_emergencia' => $data['telefono_emergencia'] ?? null,
            ]);

            echo json_encode(['success' => true, 'message' => 'Paciente actualizado correctamente']);
            break;

        // ELIMINAR PACIENTE
        case 'DELETE':
            $data = json_decode(file_get_contents('php://input'), true);
            $id = trim($data['id'] ?? '');

            if (!$id) {
                echo json_encode(['success' => false, 'message' => 'ID requerido']);
                exit;
            }

            // Eliminación lógica
            $stmt = $db->prepare("UPDATE pacientes SET activo = false WHERE id = :id");
            $stmt->execute([':id' => $id]);

            echo json_encode(['success' => true, 'message' => 'Paciente eliminado correctamente']);
            break;

        default:
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            break;
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
