<?php
require 'conecta.php';
$con = conecta();

header('Content-Type: application/json');

$sql = "SELECT usuario_id, nombre_usuario FROM usuarios"; // Quitado el WHERE activo = 1
$result = $con->query($sql);

$usuarios = [];
while ($row = $result->fetch_assoc()) {
  $usuarios[] = $row;
}

echo json_encode($usuarios);
$con->close();
?>
