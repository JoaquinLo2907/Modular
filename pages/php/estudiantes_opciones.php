<?php
require 'conecta.php';
$con = conecta();

$sql = "SELECT estudiante_id, nombre, apellido, grado, grupo FROM estudiantes WHERE activo = 1";
$resultado = $con->query($sql);

$estudiantes = [];

while ($fila = $resultado->fetch_assoc()) {
    $estudiantes[] = $fila;
}

header('Content-Type: application/json');
echo json_encode($estudiantes);
?>
