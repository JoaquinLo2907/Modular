<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../php/conecta.php'; // Asegúrate de que la ruta de 'conecta.php' sea correcta
$con = conecta();

if (!$con) {
    die("Error de conexión: " . mysqli_connect_error());
}

$sql = "SELECT materia_id, nombre, nivel_grado, descripcion, foto_url FROM materias";


$result = $con->query($sql);

if (!$result) {
    die("Error en la consulta: " . $con->error);
}

$materias = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Si no hay foto, vacía el campo (similar a como lo haces con los docentes)
        $row['foto_url'] = $row['foto_url'] ? $row['foto_url'] : ''; 
        $materias[] = $row;
    }
}

header('Content-Type: application/json');
echo json_encode($materias);

$con->close();
?>