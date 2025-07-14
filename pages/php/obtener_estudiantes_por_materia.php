<?php
session_start();
require 'conecta.php';
$con = conecta();

header('Content-Type: application/json');

$usuario_id = $_SESSION['usuario_id'] ?? null;
$docente_id = $_SESSION['docente_id'] ?? null;

if (!$usuario_id || !$docente_id) {
  echo json_encode([]);
  exit;
}

$materia_id = $_GET['materia_id'] ?? null;

$query = "
SELECT 
  e.estudiante_id,
  e.tutor_id,
  t.nombre AS tutor_nombre,
  t.apellido AS tutor_apellido,
  e.nombre,
  e.apellido,
  e.fecha_nacimiento,
  e.grado,
  e.grupo,
  e.activo,
  e.creado_en,
  e.actualizado_en,
  m.materia_id,
  m.ciclo
FROM asignacion_materias am
INNER JOIN materias m ON am.materia_id = m.materia_id
INNER JOIN estudiantes e ON am.estudiante_id = e.estudiante_id
LEFT JOIN tutores t ON e.tutor_id = t.tutor_id
WHERE m.docente_id = ?
";

$types = "i";
$params = [$docente_id];

// Si se recibe un materia_id específico
if (!empty($materia_id)) {
  $query .= " AND m.materia_id = ?";
  $types .= "i";
  $params[] = $materia_id;
}

$stmt = $con->prepare($query);
if (!$stmt) {
  echo json_encode(["error" => "Error en prepare: " . $con->error]);
  exit;
}

$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

$estudiantes = [];
while ($row = $result->fetch_assoc()) {
  $nombre = $row['tutor_nombre'] ?? '';
  $apellido = $row['tutor_apellido'] ?? '';
  $row['tutor_nombre'] = trim($nombre . ' ' . $apellido);
  $estudiantes[] = $row;
}

echo json_encode($estudiantes);

$stmt->close();
$con->close();
