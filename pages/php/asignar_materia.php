<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'conecta.php';
$con = conecta();
if (!$con) {
    die("Error de conexión: " . mysqli_connect_error());
}

// Parámetros esperados
$estudiante_id = (int) ($_POST['estudiante_id'] ?? 0);
$materia_id    = (int) ($_POST['materia_id'] ?? 0);
$periodo_id    = (int) ($_POST['periodo_id'] ?? 0);

header('Content-Type: application/json');

$stmt = $con->prepare("
  INSERT INTO calificaciones (estudiante_id, materia_id, periodo_id)
  VALUES (?, ?, ?)
");
if (!$stmt) {
    die(json_encode(['success'=>false, 'error'=>$con->error]));
}
$stmt->bind_param("iii", $estudiante_id, $materia_id, $periodo_id);
$stmt->execute();

echo json_encode([
  'success' => $stmt->affected_rows > 0
]);

$stmt->close();
$con->close();
