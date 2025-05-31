<?php
include 'conecta.php';
$con = conecta();

// Obtener el ID enviado desde el frontend
$data = json_decode(file_get_contents('php://input'), true);
$docente_id = $data['id'] ?? null;

if (!$docente_id) {
    echo json_encode(['success' => false, 'message' => 'ID no proporcionado']);
    exit;
}

// Verificar si el docente existe y está activo
$sql_check = "SELECT docente_id, activo FROM docentes WHERE docente_id = ?";
$stmt_check = $con->prepare($sql_check);
$stmt_check->bind_param("i", $docente_id);
$stmt_check->execute();
$result_check = $stmt_check->get_result();

if ($result_check->num_rows == 0) {
    echo json_encode(['success' => false, 'message' => 'El ID no existe en la base de datos']);
    exit;
}

$docente = $result_check->fetch_assoc();
if ($docente['activo'] == 0) {
    echo json_encode(['success' => false, 'message' => 'El docente ya está eliminado']);
    exit;
}

// Actualizar "activo" a 0 (borrado lógico)
$sql = "UPDATE docentes SET activo = 0 WHERE docente_id = ? AND activo = 1";
$stmt = $con->prepare($sql);
$stmt->bind_param("i", $docente_id);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'No se pudo eliminar el docente.']);
}

$stmt->close();
$con->close();

?>
