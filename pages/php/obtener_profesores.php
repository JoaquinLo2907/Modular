<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../php/conecta.php';
$con = conecta();

if (!$con) {
    die("Error de conexión: " . mysqli_connect_error());
}

// Modificar la consulta para filtrar solo docentes activos
$sql = "SELECT docente_id, nombre, apellido, telefono, correo, activo, puesto, genero, fecha_nacimiento, salario, direccion, creado_en, actualizado_en, foto_url FROM docentes WHERE activo = 1";

$result = $con->query($sql);

if (!$result) {
    die("Error en la consulta: " . $con->error);
}

$docentes = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $row['foto_url'] = $row['foto_url'] ? $row['foto_url'] : ''; // Si no hay foto, vacía el campo
        $docentes[] = $row;
    }
}

header('Content-Type: application/json');
echo json_encode($docentes);

$con->close();
?>