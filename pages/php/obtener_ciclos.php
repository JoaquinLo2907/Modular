<?php
header('Content-Type: application/json');
require 'conecta.php';
$con = conecta();

$query = "
  SELECT ciclo_id, nombre, fecha_inicio, fecha_fin, estado, observaciones
  FROM ciclos_escolares
  ORDER BY fecha_inicio DESC
";
$result = $con->query($query);
$ciclos = $result->fetch_all(MYSQLI_ASSOC);

echo json_encode($ciclos);
