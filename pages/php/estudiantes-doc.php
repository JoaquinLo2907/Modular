<?php
session_start();
require 'conecta.php';
$con = conecta();

// Asegurarse que el usuario esté logueado como docente
if (!isset($_SESSION['usuario_id'])) {
  http_response_code(401);
  echo json_encode(["error" => "No autorizado"]);
  exit;
}

$docente_id = $_SESSION['usuario_id'];

header('Content-Type: application/json');

$query = "
  SELECT DISTINCT
    e.estudiante_id,
    e.nombre,
    e.apellido,
    e.fecha_nacimiento,
    e.grado,
    e.grupo,
    e.activo,
    e.creado_en,
    e.actualizado_en,
    t.tutor_id,
    CONCAT(t.nombre, ' ', t.apellido) AS tutor_nombre
  FROM estudiantes e
  JOIN tutores t ON e.tutor_id = t.tutor_id
  JOIN asignacion_materias am ON am.estudiante_id = e.estudiante_id
  JOIN materias m ON am.materia_id = m.materia_id
  JOIN docentes d ON m.docente_id = d.docente_id
  WHERE d.usuario_id = ?
";


$stmt = $con->prepare($query);

if (!$stmt) {
  echo json_encode(["error" => "Error en la preparación de la consulta."]);
  exit;
}

$stmt->bind_param("i", $docente_id);
$stmt->execute();

$result = $stmt->get_result();
$estudiantes = [];

while ($row = $result->fetch_assoc()) {
  $estudiantes[] = $row;
}

echo json_encode($estudiantes, JSON_UNESCAPED_UNICODE);

$stmt->close();
$con->close();
