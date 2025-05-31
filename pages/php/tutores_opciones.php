<?php
header('Content-Type: application/json');
include 'conecta.php';
$con = conecta();

$sql = "SELECT tutor_id, nombre, apellido FROM tutores WHERE activo = 1";
$result = $con->query($sql);

$tutores = [];
while ($row = $result->fetch_assoc()) {
    $tutores[] = $row;
}

echo json_encode($tutores);
$con->close();
?>
