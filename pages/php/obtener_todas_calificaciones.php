<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Carga la conexión
require_once __DIR__ . '/conecta.php';
$con = conecta();
if (!$con) {
    http_response_code(500);
    exit('Error de conexión: ' . mysqli_connect_error());
}

header('Content-Type: application/json; charset=utf-8');

// Capturamos el periodo_id (int) desde la URL
$periodo_id = isset($_GET['periodo_id']) ? (int) $_GET['periodo_id'] : 0;

if ($periodo_id === 0) {
    // 1) Sin filtrar: trae todas las calificaciones de todos los periodos
    $sql = "
      SELECT
        e.estudiante_id,
        CONCAT(e.nombre, ' ', e.apellido) AS alumno,
        COALESCE(m.nombre, '—')       AS materia,
        COALESCE(c.calificacion, '—') AS calificacion,
        COALESCE(p.nombre, '—')       AS periodo,
        c.calificacion_id             AS calificacion_id
      FROM estudiantes e
      LEFT JOIN calificaciones c
        ON e.estudiante_id = c.estudiante_id
      LEFT JOIN materias m
        ON c.materia_id = m.materia_id
      LEFT JOIN periodos p
        ON c.periodo_id = p.periodo_id
      ORDER BY e.nombre, m.nombre
    ";
    $stmt = $con->prepare($sql);
} else {
    // 2) Filtrado por el periodo seleccionado
    $sql = "
      SELECT
        e.estudiante_id,
        CONCAT(e.nombre, ' ', e.apellido) AS alumno,
        COALESCE(m.nombre, '—')       AS materia,
        COALESCE(c.calificacion, '—') AS calificacion,
        COALESCE(p.nombre, '—')       AS periodo,
        c.calificacion_id             AS calificacion_id
      FROM estudiantes e
      LEFT JOIN calificaciones c
        ON e.estudiante_id = c.estudiante_id
       AND c.periodo_id = ?
      LEFT JOIN materias m
        ON c.materia_id = m.materia_id
      LEFT JOIN periodos p
        ON c.periodo_id = p.periodo_id
      ORDER BY e.nombre, m.nombre
    ";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("i", $periodo_id);
}

if (!$stmt) {
    http_response_code(500);
    exit('Error en la preparación de la consulta: ' . $con->error);
}

$stmt->execute();
$result = $stmt->get_result();

$calificaciones = [];
while ($row = $result->fetch_assoc()) {
    $calificaciones[] = $row;
}

echo json_encode($calificaciones, JSON_UNESCAPED_UNICODE);

$stmt->close();
$con->close();
