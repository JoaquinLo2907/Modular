<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'conecta.php';
$con = conecta();
if (!$con) {
    die("Error de conexión: " . mysqli_connect_error());
}

// Parámetros esperados
$calif_id     = (int) ($_POST['calificacion_id'] ?? 0);
$calificacion = is_numeric($_POST['calificacion']) 
               ? (float) $_POST['calificacion'] 
               : null;

header('Content-Type: application/json');

$stmt = $con->prepare("
  UPDATE calificaciones
     SET calificacion = ?
   WHERE calificacion_id = ?
");
if (!$stmt) {
    die(json_encode(['success'=>false, 'error'=>$con->error]));
}
$stmt->bind_param("di", $calificacion, $calif_id);
$stmt->execute();

echo json_encode([
  'success' => $stmt->affected_rows >= 0
]);

$stmt->close();
$con->close();
