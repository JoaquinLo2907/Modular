<?php
session_start();
require 'conecta.php';
header('Content-Type: application/json');

$con = conecta();

if (!isset($_GET['materia_id'])) {
  echo json_encode([]);
  exit;
}

$materia_id = intval($_GET['materia_id']);

$query = "
  SELECT e.estudiante_id, e.nombre, e.apellido
  FROM estudiantes e
  INNER JOIN asignacion_materias am ON e.estudiante_id = am.estudiante_id
  WHERE am.materia_id = ?
";

$stmt = $con->prepare($query);
if (!$stmt) {
  echo json_encode(["error" => "Error en la preparación de la consulta."]);
  exit;
}

$stmt->bind_param("i", $materia_id);
$stmt->execute();
$result = $stmt->get_result();

$estudiantes = [];
while ($row = $result->fetch_assoc()) {
  $estudiantes[] = $row;
}

echo json_encode($estudiantes, JSON_UNESCAPED_UNICODE);
$stmt->close();
$con->close();
