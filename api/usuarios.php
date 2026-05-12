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
            // Obtener un usuario por ID
            if (isset($_GET['id'])) {
                $stmt = $db->prepare("SELECT id, nombre, apellido, email, rol, activo FROM usuarios WHERE id = :id LIMIT 1");
                $stmt->execute([':id' => $_GET['id']]);
                $usuario = $stmt->fetch();
                if ($usuario) {
                    echo json_encode(['success' => true, 'data' => $usuario]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Usuario no encontrado']);
                }
                break;
            }

            // Listar usuarios
            $pagina = (int)($_GET['pagina'] ?? 1);
            $por_pagina = (int)($_GET['por_pagina'] ?? 10);
            $busqueda = trim($_GET['busqueda'] ?? '');
            $rol = trim($_GET['rol'] ?? '');
            $offset = ($pagina - 1) * $por_pagina;

            $where = "WHERE activo = true";
            $params = [];

            if ($busqueda) {
                $where .= " AND (nombre ILIKE :busqueda OR apellido ILIKE :busqueda OR email ILIKE :busqueda)";
                $params[':busqueda'] = "%{$busqueda}%";
            }
            if ($rol) {
                $where .= " AND rol = :rol";
                $params[':rol'] = $rol;
            }

            // Total
            $stmtTotal = $db->prepare("SELECT COUNT(*) as total FROM usuarios {$where}");
            $stmtTotal->execute($params);
            $total = $stmtTotal->fetch()['total'];

            // Datos
            $stmt = $db->prepare("SELECT id, nombre, apellido, email, rol, activo FROM usuarios {$where} ORDER BY created_at DESC LIMIT :limit OFFSET :offset");
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            $stmt->bindValue(':limit', $por_pagina, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            $usuarios = $stmt->fetchAll();

            echo json_encode([
                'success' => true,
                'data' => $usuarios,
                'total' => $total,
                'pagina' => $pagina
            ]);
            break;

        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);

            $nombre   = trim($data['nombre'] ?? '');
            $apellido = trim($data['apellido'] ?? '');
            $email    = trim($data['email'] ?? '');
            $rol      = trim($data['rol'] ?? '');
            $password = trim($data['password'] ?? '');

            if (!$nombre || !$apellido || !$email || !$rol || !$password) {
                echo json_encode(['success' => false, 'message' => 'Todos los campos son obligatorios']);
                exit;
            }

            // Verificar email duplicado
            $stmtCheck = $db->prepare("SELECT id FROM usuarios WHERE email = :email");
            $stmtCheck->execute([':email' => $email]);
            if ($stmtCheck->fetch()) {
                echo json_encode(['success' => false, 'message' => 'El email ya está registrado']);
                exit;
            }

            $passwordHash = password_hash($password, PASSWORD_BCRYPT);

            $stmt = $db->prepare("
                INSERT INTO usuarios (nombre, apellido, email, password, rol)
                VALUES (:nombre, :apellido, :email, :password, :rol)
            ");
            $stmt->execute([
                ':nombre'   => $nombre,
                ':apellido' => $apellido,
                ':email'    => $email,
                ':password' => $passwordHash,
                ':rol'      => $rol,
            ]);

            echo json_encode(['success' => true, 'message' => 'Usuario creado correctamente']);
            break;

        case 'PUT':
            $data = json_decode(file_get_contents('php://input'), true);
            $id = trim($data['id'] ?? '');

            if (!$id) {
                echo json_encode(['success' => false, 'message' => 'ID requerido']);
                exit;
            }

            $password = trim($data['password'] ?? '');

            if ($password) {
                if (strlen($password) < 8) {
                    echo json_encode(['success' => false, 'message' => 'La contraseña debe tener mínimo 8 caracteres']);
                    exit;
                }
                $passwordHash = password_hash($password, PASSWORD_BCRYPT);
                $stmt = $db->prepare("
                    UPDATE usuarios SET
                        nombre = :nombre,
                        apellido = :apellido,
                        email = :email,
                        rol = :rol,
                        password = :password
                    WHERE id = :id
                ");
                $stmt->execute([
                    ':id'       => $id,
                    ':nombre'   => trim($data['nombre'] ?? ''),
                    ':apellido' => trim($data['apellido'] ?? ''),
                    ':email'    => trim($data['email'] ?? ''),
                    ':rol'      => trim($data['rol'] ?? ''),
                    ':password' => $passwordHash,
                ]);
            } else {
                $stmt = $db->prepare("
                    UPDATE usuarios SET
                        nombre = :nombre,
                        apellido = :apellido,
                        email = :email,
                        rol = :rol
                    WHERE id = :id
                ");
                $stmt->execute([
                    ':id'       => $id,
                    ':nombre'   => trim($data['nombre'] ?? ''),
                    ':apellido' => trim($data['apellido'] ?? ''),
                    ':email'    => trim($data['email'] ?? ''),
                    ':rol'      => trim($data['rol'] ?? ''),
                ]);
            }

            echo json_encode(['success' => true, 'message' => 'Usuario actualizado correctamente']);
            break;

        case 'DELETE':
            $data = json_decode(file_get_contents('php://input'), true);
            $id = trim($data['id'] ?? '');

            if (!$id) {
                echo json_encode(['success' => false, 'message' => 'ID requerido']);
                exit;
            }

            $stmt = $db->prepare("UPDATE usuarios SET activo = false WHERE id = :id");
            $stmt->execute([':id' => $id]);

            echo json_encode(['success' => true, 'message' => 'Usuario eliminado correctamente']);
            break;

        default:
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            break;
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
