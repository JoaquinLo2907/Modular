<?php
require 'conecta.php';
session_start();
header('Content-Type: application/json');

$con = conecta();
if (!$con) {
  http_response_code(500);
  echo json_encode(['success' => false, 'message' => 'No se pudo conectar a la base de datos.']);
  exit;
}

$id = intval($_POST['calificacion_id'] ?? 0);
$valor1 = floatval($_POST['calificacion1'] ?? null);
$valor2 = floatval($_POST['calificacion2'] ?? null);
$valor3 = floatval($_POST['calificacion3'] ?? null);

if ($id > 0) {
  // 🔄 Actualizar calificaciones existentes
  $stmt = $con->prepare("UPDATE calificaciones 
                         SET calificacion_1 = ?, calificacion_2 = ?, calificacion_3 = ?, actualizado_en = NOW() 
                         WHERE calificacion_id = ?");
  $stmt->bind_param("dddi", $valor1, $valor2, $valor3, $id);
  $stmt->execute();
  echo json_encode(['success' => true, 'modo' => 'update']);
  exit;
}

// ➕ Insertar nuevas calificaciones
$estudiante_id = intval($_POST['estudiante_id'] ?? 0);
$materia_id = intval($_POST['materia_id'] ?? 0);
$periodo_id = intval($_POST['periodo_id'] ?? 0);

if (!$estudiante_id || !$materia_id || !$periodo_id) {
  http_response_code(400);
  echo json_encode(['success' => false, 'message' => 'Faltan datos para insertar.']);
  exit;
}

$stmt = $con->prepare("INSERT INTO calificaciones 
  (estudiante_id, materia_id, periodo_id, calificacion_1, calificacion_2, calificacion_3, creado_en, actualizado_en)
  VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())");
$stmt->bind_param("iiiddd", $estudiante_id, $materia_id, $periodo_id, $valor1, $valor2, $valor3);
$stmt->execute();

echo json_encode(['success' => true, 'modo' => 'insert']);
