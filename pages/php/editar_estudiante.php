<?php
require 'conecta.php';
$con = conecta();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['estudiante_id']);
    $nombre = trim($_POST['nombre']);
    $apellido = trim($_POST['apellido']);
    $fecha_nacimiento = $_POST['fecha_nacimiento'];
    $grado = intval($_POST['grado']);
    $grupo = $_POST['grupo'];
    $tutor_id = intval($_POST['tutor_id']);

    // 🔄 1. Actualizar tabla estudiantes
    $stmt1 = $con->prepare("
        UPDATE estudiantes 
        SET nombre = ?, apellido = ?, fecha_nacimiento = ?, grado = ?, grupo = ?, tutor_id = ?, actualizado_en = NOW()
        WHERE estudiante_id = ?
    ");

    if (!$stmt1) {
        echo json_encode(['success' => false, 'message' => 'Error al preparar la consulta de estudiantes']);
        exit;
    }

    $stmt1->bind_param("sssissi", $nombre, $apellido, $fecha_nacimiento, $grado, $grupo, $tutor_id, $id);
    $ok1 = $stmt1->execute();
    $stmt1->close();

    // 🔁 2. Actualizar tabla de relación tutor_estudiante
    // Eliminar relación previa (si existe)
    $stmt2 = $con->prepare("DELETE FROM tutor_estudiante WHERE estudiante_id = ?");
    $stmt2->bind_param("i", $id);
    $stmt2->execute();
    $stmt2->close();

    // Insertar nueva relación
    $stmt3 = $con->prepare("INSERT INTO tutor_estudiante (tutor_id, estudiante_id, asignado_en) VALUES (?, ?, NOW())");
    $stmt3->bind_param("ii", $tutor_id, $id);
    $ok3 = $stmt3->execute();
    $stmt3->close();

    if ($ok1 && $ok3) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Error al actualizar los datos',
        ]);
    }

} else {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
}

$con->close();
