<?php
require 'conecta.php';
$con = conecta();

header('Content-Type: application/json');

$id = $_GET['id'] ?? null;

if (!$id) {
  echo json_encode([]);
  exit;
}

$query = "
  SELECT m.nombre AS materia, c.calificacion
  FROM calificaciones c
  INNER JOIN materias m ON c.materia_id = m.materia_id
  WHERE c.estudiante_id = ?
";

$stmt = $con->prepare($query);

if (!$stmt) {
  echo json_encode(["error" => "Error en la preparación de la consulta."]);
  exit;
}

$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

$calificaciones = [];

while ($row = $result->fetch_assoc()) {
  $calificaciones[] = $row;
}

echo json_encode($calificaciones);

$stmt->close();
$con->close();
