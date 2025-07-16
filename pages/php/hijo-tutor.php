<?php
require 'conecta.php';
session_start();

header('Content-Type: application/json');

$tutor_id = $_SESSION['tutor_id'] ?? 0;

if ($tutor_id > 0) {
    $con = conecta();

    $stmt = $con->prepare("
        SELECT e.estudiante_id, e.nombre, e.apellido
        FROM tutor_estudiante te
        JOIN estudiantes e ON te.estudiante_id = e.estudiante_id
        WHERE te.tutor_id = ?
    ");
    $stmt->bind_param("i", $tutor_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $estudiantes = [];
    while ($row = $result->fetch_assoc()) {
        $estudiantes[] = $row;
    }

    echo json_encode([
        "status" => "success",
        "data" => $estudiantes
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Tutor no autenticado"
    ]);
}
