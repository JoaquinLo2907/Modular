<?php
session_start();
header('Content-Type: application/json');
require 'conecta.php';

if (!isset($_SESSION['docente_id'])) {
    echo json_encode(["error" => "No autorizado"]);
    exit;
}

$docente_id = $_SESSION['docente_id'];
$con = conecta();

$query = "
    SELECT DISTINCT 
        m.materia_id, m.ciclo, e.grado, e.grupo
    FROM materias m
    INNER JOIN asignacion_materias am ON m.materia_id = am.materia_id
    INNER JOIN estudiantes e ON am.estudiante_id = e.estudiante_id
    WHERE m.docente_id = ?
";

$stmt = $con->prepare($query);
$stmt->bind_param("i", $docente_id);
$stmt->execute();
$res = $stmt->get_result();

$grupos = [];
while ($row = $res->fetch_assoc()) {
    $grupos[] = $row;
}

echo json_encode($grupos);
$stmt->close();
$con->close();
