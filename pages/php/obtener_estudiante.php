<?php
require 'conecta.php';
$con = conecta();

header('Content-Type: application/json');

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $stmt = $con->prepare("SELECT estudiante_id, nombre, apellido, fecha_nacimiento, grado, grupo FROM estudiantes WHERE estudiante_id = ? AND activo = 1");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        echo json_encode($resultado->fetch_assoc());
    } else {
        echo json_encode(['error' => 'Estudiante no encontrado']);
    }

    $stmt->close();
} else {
    echo json_encode(['error' => 'ID no proporcionado']);
}

$con->close();
?>
