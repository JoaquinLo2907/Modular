<?php
header('Content-Type: application/json');
require 'conecta.php';
$con = conecta();

$data = json_decode(file_get_contents('php://input'), true);

if (empty($data['clase_id'])) {
    echo json_encode(['success' => false, 'message' => 'clase_id es requerido']);
    exit;
}

try {
    $stmt = $con->prepare("
        DELETE FROM clases
        WHERE clase_id = ?
    ");
    $stmt->bind_param("i", $data['clase_id']);
    $stmt->execute();
    $stmt->close();

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
