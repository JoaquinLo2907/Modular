<?php
require 'conecta.php';
$con = conecta();

header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);
$id = $data['id'] ?? null;

if (!$id) {
    echo json_encode(['success' => false, 'message' => 'ID no proporcionado']);
    exit;
}

$stmt = $con->prepare("UPDATE estudiantes SET activo = 0, actualizado_en = NOW() WHERE estudiante_id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute() && $stmt->affected_rows > 0) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'No se pudo eliminar']);
}

$stmt->close();
$con->close();
?>
    