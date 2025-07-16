<?php
session_start();
require 'conecta.php';
$con = conecta();

$usuario_id = $_SESSION['usuario_id'] ?? null;
if (!$usuario_id) {
  http_response_code(401);
  exit('No autenticado');
}

$stmt = $con->prepare("
  SELECT m.materia_id, m.nombre
  FROM materias m
  JOIN docentes d ON m.docente_id = d.docente_id
  WHERE d.usuario_id = ?
");
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$res = $stmt->get_result();

$materias = [];
while ($row = $res->fetch_assoc()) {
  $materias[] = $row;
}

echo json_encode($materias);
