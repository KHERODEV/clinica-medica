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

try {
    $db = getDB();

    // Total pacientes
    $stmt = $db->query("SELECT COUNT(*) as total FROM pacientes WHERE activo = true");
    $total_pacientes = $stmt->fetch()['total'];

    // Citas de hoy
    $stmt = $db->query("SELECT COUNT(*) as total FROM citas WHERE fecha = CURRENT_DATE");
    $citas_hoy = $stmt->fetch()['total'];

    // Citas pendientes
    $stmt = $db->query("SELECT COUNT(*) as total FROM citas WHERE estado = 'pendiente'");
    $citas_pendientes = $stmt->fetch()['total'];

    // Total médicos
    $stmt = $db->query("SELECT COUNT(*) as total FROM usuarios WHERE rol = 'medico' AND activo = true");
    $total_medicos = $stmt->fetch()['total'];

    // Citas del día con detalle
    $stmt = $db->query("
        SELECT
            c.hora,
            c.estado,
            p.nombre || ' ' || p.apellido AS paciente,
            u.nombre AS medico
        FROM citas c
        JOIN pacientes p ON c.paciente_id = p.id
        JOIN usuarios u ON c.medico_id = u.id
        WHERE c.fecha = CURRENT_DATE
        ORDER BY c.hora ASC
        LIMIT 5
    ");
    $citas_del_dia = $stmt->fetchAll();

    // Formatear hora
    foreach ($citas_del_dia as &$cita) {
        $cita['hora'] = substr($cita['hora'], 0, 5);
    }

    // Últimos pacientes registrados
    $stmt = $db->query("
        SELECT
            nombre,
            apellido,
            rut,
            TO_CHAR(created_at, 'DD/MM/YYYY') AS fecha
        FROM pacientes
        ORDER BY created_at DESC
        LIMIT 5
    ");
    $ultimos_pacientes = $stmt->fetchAll();

    echo json_encode([
        'success' => true,
        'data' => [
            'total_pacientes'  => $total_pacientes,
            'citas_hoy'        => $citas_hoy,
            'citas_pendientes' => $citas_pendientes,
            'total_medicos'    => $total_medicos,
            'citas_del_dia'    => $citas_del_dia,
            'ultimos_pacientes' => $ultimos_pacientes
        ]
    ]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
