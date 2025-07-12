<?php
session_start();
require 'conecta.php';
$con = conecta();

header('Content-Type: application/json');

$usuario_id = $_SESSION['usuario_id'] ?? null;
if (!$usuario_id) {
  echo json_encode([]);
  exit;
}

$query = "
  SELECT m.materia_id, m.nombre, m.nivel_grado, m.ciclo
  FROM materias m
  INNER JOIN docentes d ON m.docente_id = d.docente_id
  WHERE d.usuario_id = ?
";


$stmt = $con->prepare($query);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();

$materias = [];

while ($row = $result->fetch_assoc()) {
  $materias[] = $row;
}

echo json_encode($materias);
