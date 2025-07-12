<?php
require 'conecta.php';
$con = conecta();
header('Content-Type: application/json');

$id = $_POST['calificacion_id'] ?? null;
if (!$id) {
  echo json_encode(['success'=>false, 'message'=>'ID de calificación faltante']);
  exit;
}

$stmt = $con->prepare("DELETE FROM calificaciones WHERE calificacion_id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
  echo json_encode(['success'=>true]);
} else {
  echo json_encode(['success'=>false, 'message'=>$con->error]);
}

$stmt->close();
$con->close();