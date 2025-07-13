<?php
header('Content-Type: application/json');
require 'conecta.php';
$con = conecta();

$id = intval($_GET['id'] ?? 0);
if (!$id) {
    echo json_encode(['success' => false, 'message' => 'ID inválido']);
    exit;
}

// Obtener datos del ciclo
$stmt = $con->prepare("
  SELECT ciclo_id, nombre, fecha_inicio, fecha_fin, estado, observaciones
  FROM ciclos_escolares
  WHERE ciclo_id = ?
");
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();
$ciclo = $res->fetch_assoc();
$stmt->close();

if (!$ciclo) {
    echo json_encode(['success' => false, 'message' => 'Ciclo no encontrado']);
    exit;
}

// Obtener periodos asociados
$stmt2 = $con->prepare("
  SELECT periodo_id, nombre, fecha_inicio, fecha_fin
  FROM periodos
  WHERE ciclo_id = ?
  ORDER BY periodo_id ASC
");
$stmt2->bind_param("i", $id);
$stmt2->execute();
$res2 = $stmt2->get_result();
$periodos = $res2->fetch_all(MYSQLI_ASSOC);
$stmt2->close();

echo json_encode([
  'success'  => true,
  'ciclo'    => $ciclo,
  'periodos' => $periodos
]);
