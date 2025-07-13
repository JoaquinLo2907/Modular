<?php
header('Content-Type: application/json');
require 'conecta.php';
$data = json_decode(file_get_contents('php://input'), true);
if (!$data['ciclo_id']) {
  http_response_code(400);
  echo json_encode(['success'=>false,'message'=>'Falta ciclo_id']);
  exit;
}
// Lógica de toggle… luego:
echo json_encode(['success'=>true]);
