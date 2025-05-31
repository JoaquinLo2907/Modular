<?php
require 'conecta.php';
$conexion = conecta();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $fecha_nacimiento = $_POST['fecha_nacimiento'];
    $grado = $_POST['grado'];
    $grupo = $_POST['grupo'];
    $tutor_id = $_POST['tutor_id'];

    // 1. Buscar el usuario_id del tutor seleccionado
    $stmt_tutor = $conexion->prepare("SELECT usuario_id FROM tutores WHERE tutor_id = ?");
    $stmt_tutor->bind_param("i", $tutor_id);
    $stmt_tutor->execute();
    $stmt_tutor->bind_result($usuario_id);
    $stmt_tutor->fetch();
    $stmt_tutor->close();

    if (!$usuario_id) {
        echo json_encode(['success' => false, 'message' => 'Tutor sin usuario_id']);
        exit;
    }

    // 2. Insertar al estudiante con ese usuario_id
    $stmt = $conexion->prepare("INSERT INTO estudiantes (usuario_id, nombre, apellido, fecha_nacimiento, grado, grupo, tutor_id, activo, creado_en, actualizado_en) 
                                VALUES (?, ?, ?, ?, ?, ?, ?, 1, NOW(), NOW())");

    if (!$stmt) {
        echo json_encode(['success' => false, 'message' => 'Error al preparar consulta']);
        exit;
    }

    $stmt->bind_param("isssisi", $usuario_id, $nombre, $apellido, $fecha_nacimiento, $grado, $grupo, $tutor_id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al insertar estudiante']);
    }

    $stmt->close();
    $conexion->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
}

?>
