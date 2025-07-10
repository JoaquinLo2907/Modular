<?php
require 'conecta.php';
$con = conecta();

header('Content-Type: application/json');

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $stmt = $con->prepare("
        SELECT estudiante_id, nombre, apellido, fecha_nacimiento, grado, grupo, tutor_id 
        FROM estudiantes 
        WHERE estudiante_id = ? AND activo = 1
    ");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $resultado = $stmt->get_result();

    echo json_encode($resultado->fetch_assoc() ?: ['error' => 'Estudiante no encontrado']);
    $stmt->close();
} else {
    $query = "SELECT estudiante_id, nombre, apellido, fecha_nacimiento, grado, grupo FROM estudiantes WHERE activo = 1";
    $resultado = $con->query($query);

    $estudiantes = [];
    while ($row = $resultado->fetch_assoc()) {
        $estudiantes[] = $row;
    }
    echo json_encode($estudiantes);
}

$con->close();
?>
