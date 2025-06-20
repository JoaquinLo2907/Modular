<?php
require 'conecta.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $input = json_decode(file_get_contents('php://input'), true);

  if (!isset($input['pago_id'], $input['estado'])) {
    echo json_encode(['success' => false, 'message' => 'Datos incompletos.']);
    exit;
  }

  $conexion = conecta();
  $stmt = $conexion->prepare("UPDATE pagos SET estado = ?, actualizado_en = NOW() WHERE pago_id = ?");
  $stmt->bind_param("si", $input['estado'], $input['pago_id']);

  if ($stmt->execute()) {
    echo json_encode(['success' => true]);
  } else {
    echo json_encode(['success' => false, 'message' => 'Error al actualizar.']);
  }

  $stmt->close();
  $conexion->close();
} else {
  echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
}
