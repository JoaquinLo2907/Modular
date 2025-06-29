<?php
require 'conecta.php';

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $conexion = conecta();

  $grado = $_POST['grado'] ?? null;
  $grupo = $_POST['grupo'] ?? null;
  $monto = $_POST['monto'] ?? null;
  $fecha_pago = $_POST['fecha_pago'] ?? null;
  $fecha_vencimiento = $_POST['fecha_vencimiento'] ?? null;
  $estado = $_POST['estado'] ?? null;

  if (!$grado || !$grupo || !$monto || !$fecha_pago || !$fecha_vencimiento || !$estado) {
    echo json_encode(["success" => false, "message" => "Datos incompletos."]);
    exit;
  }

  $stmt = $conexion->prepare("SELECT estudiante_id FROM estudiantes WHERE grado = ? AND grupo = ? AND activo = 1");
  $stmt->bind_param("ss", $grado, $grupo);
  $stmt->execute();
  $result = $stmt->get_result();
  $estudiantes = $result->fetch_all(MYSQLI_ASSOC);
  $stmt->close();

  if (count($estudiantes) === 0) {
    echo json_encode(["success" => false, "message" => "No se encontraron estudiantes para el grado y grupo especificados."]);
    exit;
  }

  $stmt_insert = $conexion->prepare("INSERT INTO pagos (estudiante_id, monto, fecha_pago, fecha_vencimiento, estado, creado_en, actualizado_en)
                                      VALUES (?, ?, ?, ?, ?, NOW(), NOW())");

  $stmt_insert->bind_param("idsss", $estudiante_id, $monto, $fecha_pago, $fecha_vencimiento, $estado);
  $errores = [];

  foreach ($estudiantes as $est) {
    $estudiante_id = $est['estudiante_id'];
    if (!$stmt_insert->execute()) {
      $errores[] = "Error con estudiante ID $estudiante_id: " . $stmt_insert->error;
    }
  }

  $stmt_insert->close();
  $conexion->close();

  if (empty($errores)) {
    echo json_encode(["success" => true]);
  } else {
    echo json_encode(["success" => false, "message" => $errores]);
  }

} else {
  echo json_encode(["success" => false, "message" => "Método no permitido."]);
}
