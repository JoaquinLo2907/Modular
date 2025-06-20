<?php
require 'conecta.php';
header('Content-Type: application/json');

$conexion = conecta();

if ($_SERVER["REQUEST_METHOD"] === "GET") {
  $sql = "SELECT 
            p.pago_id, 
            e.nombre, 
            e.apellido, 
            e.grado, 
            e.grupo,
            p.monto, 
            p.fecha_pago, 
            p.fecha_vencimiento, 
            p.estado,
            p.creado_en, 
            p.actualizado_en
          FROM pagos p
          INNER JOIN estudiantes e ON p.estudiante_id = e.estudiante_id
          ORDER BY p.fecha_pago DESC";

  $result = $conexion->query($sql);

  $pagos = [];
  while ($row = $result->fetch_assoc()) {
    $pagos[] = $row;
  }

  echo json_encode($pagos);
  $conexion->close();

} elseif ($_SERVER["REQUEST_METHOD"] === "POST") {
  $input = json_decode(file_get_contents("php://input"), true);
  $pago_id = intval($input['pago_id'] ?? 0);
  $estado = $input['estado'] ?? '';

  if ($pago_id > 0 && in_array($estado, ['pagado', 'pendiente'])) {
    $stmt = $conexion->prepare("UPDATE pagos SET estado = ?, actualizado_en = NOW() WHERE pago_id = ?");
    $stmt->bind_param("si", $estado, $pago_id);

    if ($stmt->execute()) {
      echo json_encode(["success" => true]);
    } else {
      echo json_encode(["success" => false, "message" => "Error al actualizar estado"]);
    }

    $stmt->close();
  } else {
    echo json_encode(["success" => false, "message" => "Datos inválidos"]);
  }

  $conexion->close();

} else {
  echo json_encode(["success" => false, "message" => "Método no permitido"]);
}
?>
