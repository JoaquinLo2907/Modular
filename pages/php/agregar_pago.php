<?php
require 'conecta.php';

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $conexion = conecta();

  $estudiante_id = $_POST['estudiante_id'] ?? null;
  $monto = $_POST['monto'] ?? null;
  $fecha_pago = $_POST['fecha_pago'] ?? null;
  $fecha_vencimiento = $_POST['fecha_vencimiento'] ?? null;
  $estado = $_POST['estado'] ?? null;

  if (!$estudiante_id || !$monto || !$fecha_pago || !$fecha_vencimiento || !$estado) {
    echo json_encode(["success" => false, "message" => "Datos incompletos."]);
    exit;
  }

  $stmt = $conexion->prepare("INSERT INTO pagos (estudiante_id, monto, fecha_pago, fecha_vencimiento, estado, creado_en, actualizado_en)
                              VALUES (?, ?, ?, ?, ?, NOW(), NOW())");

  $stmt->bind_param("idsss", $estudiante_id, $monto, $fecha_pago, $fecha_vencimiento, $estado);

  if ($stmt->execute()) {
    echo json_encode(["success" => true]);
  } else {
    echo json_encode(["success" => false, "message" => "Error al guardar: " . $stmt->error]);
  }

  $stmt->close();
  $conexion->close();

} else {
  echo json_encode(["success" => false, "message" => "Método no permitido."]);
}
