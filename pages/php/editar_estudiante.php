<?php
require 'conecta.php';
$con = conecta();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['estudiante_id'];
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $fecha_nacimiento = $_POST['fecha_nacimiento'];
    $grado = $_POST['grado'];
    $grupo = $_POST['grupo'];
    $usuario_id = $_POST['usuario_id'];

    $stmt = $con->prepare("UPDATE estudiantes SET usuario_id = ?, nombre = ?, apellido = ?, fecha_nacimiento = ?, grado = ?, grupo = ?, actualizado_en = NOW() WHERE estudiante_id = ?");
    $stmt->bind_param("isssisi", $usuario_id, $nombre, $apellido, $fecha_nacimiento, $grado, $grupo, $id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al actualizar']);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
}

$con->close();
?>
