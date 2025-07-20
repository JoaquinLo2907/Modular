<?php
require 'conecta.php';
session_start();
header('Content-Type: application/json');

$con = conecta();
if (!$con) {
  http_response_code(500);
  echo json_encode(['error' => 'Error de conexión.']);
  exit;
}

$usuario_id = $_SESSION['usuario_id'] ?? null;
$rol = $_SESSION['rol'] ?? null;
$periodo_id = $_GET['periodo_id'] ?? null;

if (!$usuario_id || $rol != 1) {
  http_response_code(403);
  echo json_encode(['error' => 'Acceso denegado.']);
  exit;
}

// Obtener docente_id
$stmt_doc = $con->prepare("SELECT docente_id FROM docentes WHERE usuario_id = ?");
$stmt_doc->bind_param('i', $usuario_id);
$stmt_doc->execute();
$res_doc = $stmt_doc->get_result();
if ($res_doc->num_rows === 0) {
  echo json_encode([]);
  exit;
}
$docente_id = $res_doc->fetch_assoc()['docente_id'];

$sql = "
SELECT 
  COALESCE(c.calificacion_id, 0) AS calificacion_id,
  CONCAT(e.nombre, ' ', e.apellido) AS alumno,
  m.nombre AS materia,
  c.calificacion_1 AS calificacion1,
  c.calificacion_2 AS calificacion2,
  c.calificacion_3 AS calificacion3,
  c.promedio,
  e.estudiante_id,
  m.materia_id
FROM clase_asignacion ca
JOIN materias m      ON ca.materia_id = m.materia_id
JOIN clases cl       ON ca.clase_id = cl.clase_id
JOIN estudiantes e   ON e.grado = cl.grado AND e.grupo = cl.grupo
LEFT JOIN calificaciones c ON 
     c.estudiante_id = e.estudiante_id 
 AND c.materia_id = m.materia_id 
 AND c.periodo_id = ?
WHERE ca.docente_id = ?
";


$stmt = $con->prepare($sql);
$stmt->bind_param("ii", $periodo_id, $docente_id);
$stmt->execute();
$result = $stmt->get_result();

$datos = [];
while ($row = $result->fetch_assoc()) {
  // Formatear datos vacíos como '—'
  foreach (['calificacion1', 'calificacion2', 'calificacion3', 'promedio'] as $campo)
 {
    $row[$campo] = is_null($row[$campo]) ? '—' : $row[$campo];
  }
  $datos[] = $row;
}

echo json_encode($datos);
$con->close();
