<?php
require 'conecta.php';
$con = conecta();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validar campos esperados
    $id = $_POST['estudiante_id'];
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $fecha_nacimiento = $_POST['fecha_nacimiento'];
    $grado = $_POST['grado'];
    $grupo = $_POST['grupo'];
    $tutor_id = $_POST['tutor_id'];

    // Preparar y ejecutar la consulta
    $stmt = $con->prepare("
        UPDATE estudiantes 
        SET tutor_id = ?, nombre = ?, apellido = ?, fecha_nacimiento = ?, grado = ?, grupo = ?, actualizado_en = NOW() 
        WHERE estudiante_id = ?
    ");

    if (!$stmt) {
        echo json_encode(['success' => false, 'message' => 'Error al preparar la consulta']);
        exit;
    }

    $stmt->bind_param("isssisi", $tutor_id, $nombre, $apellido, $fecha_nacimiento, $grado, $grupo, $id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Error al ejecutar la consulta',
            'error' => $stmt->error
        ]);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
}

$con->close();
?>
