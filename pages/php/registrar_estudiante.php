<?php
require 'conecta.php';
$conexion = conecta();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1) Recogemos los datos del formulario
    $nombre            = trim($_POST['nombre']);
    $apellido          = trim($_POST['apellido']);
    $fecha_nacimiento  = trim($_POST['fecha_nacimiento']);
    $grado             = intval($_POST['grado']);
    $grupo             = trim($_POST['grupo']);
    $tutor_id          = intval($_POST['tutor_id']);

    // 2) Preparamos el INSERT sin usuario_id
    $sql = "
      INSERT INTO estudiantes
        (nombre, apellido, fecha_nacimiento, grado, grupo, tutor_id, activo, creado_en, actualizado_en)
      VALUES
        (?, ?, ?, ?, ?, ?, 1, NOW(), NOW())
    ";
    $stmt = $conexion->prepare($sql);
    if (!$stmt) {
        echo json_encode(['success' => false, 'message' => 'Error al preparar la consulta']);
        exit;
    }

    // 3) Enlazamos sólo los 6 parámetros que quedan
    $stmt->bind_param(
      'sssisi',
      $nombre,
      $apellido,
      $fecha_nacimiento,
      $grado,
      $grupo,
      $tutor_id
    );

    // 4) Ejecutamos y devolvemos JSON
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode([
          'success' => false,
          'message' => 'Error al insertar estudiante: '.$stmt->error
        ]);
    }

    $stmt->close();
    $conexion->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
}
