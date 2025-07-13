<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'conecta.php';
$con = conecta();
if (!$con) {
    die("Error de conexión: " . mysqli_connect_error());
}

header('Content-Type: application/json');

$sql = "
  SELECT materia_id,
         nombre
    FROM materias
   ORDER BY nombre
";
$result = $con->query($sql);
if (!$result) {
    die("Error en listar_materias: " . $con->error);
}

$lista = [];
while ($row = $result->fetch_assoc()) {
    $lista[] = $row;
}

echo json_encode($lista, JSON_UNESCAPED_UNICODE);
$con->close();
