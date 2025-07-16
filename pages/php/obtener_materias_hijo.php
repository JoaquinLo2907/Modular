<?php
require 'conecta.php';
session_start();

$tutor_id = $_SESSION['tutor_id'] ?? 0;

if ($tutor_id <= 0) {
    echo json_encode(["status" => "error", "message" => "Tutor no autenticado"]);
    exit;
}

$con = conecta();

$sql = "
    SELECT 
        e.estudiante_id,
        e.nombre AS nombre_estudiante,
        e.apellido AS apellido_estudiante,
        c.grado,
        c.grupo,
        m.materia_id,
        m.nombre AS nombre_materia,
        m.nivel_grado,
        m.ciclo,
        m.descripcion,
        m.foto_url
    FROM tutor_estudiante te
    INNER JOIN estudiantes e ON te.estudiante_id = e.estudiante_id
    INNER JOIN inscripciones i ON i.estudiante_id = e.estudiante_id
    INNER JOIN clases c ON i.clase_id = c.clase_id
    INNER JOIN clase_asignacion ca ON ca.clase_id = c.clase_id
    INNER JOIN materias m ON ca.materia_id = m.materia_id
    WHERE te.tutor_id = ?
    ORDER BY e.estudiante_id, m.nombre
";

$stmt = $con->prepare($sql);
$stmt->bind_param("i", $tutor_id);
$stmt->execute();
$result = $stmt->get_result();

$materias = [];
while ($row = $result->fetch_assoc()) {
    $materias[] = $row;
}

echo json_encode([
    "status" => "success",
    "data" => $materias
]);
?>
