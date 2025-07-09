<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/conecta.php';
$con = conecta();
if (!$con) {
    http_response_code(500);
    echo json_encode(['error' => 'No se pudo conectar a la base de datos']);
    exit;
}

$sql = "
  SELECT 
    e.estudiante_id,
    e.tutor_id,
    CONCAT(t.nombre, ' ', t.apellido) AS tutor_nombre,
    e.nombre,
    e.apellido,
    e.fecha_nacimiento,
    e.grado,
    e.grupo,
    e.activo,
    e.creado_en,
    e.actualizado_en
  FROM estudiantes e
  LEFT JOIN tutores t ON e.tutor_id = t.tutor_id
  WHERE e.activo = 1
  ORDER BY e.estudiante_id
";

if (!($result = $con->query($sql))) {
    http_response_code(500);
    echo json_encode(['error' => 'Error en consulta SQL: ' . $con->error]);
    exit;
}

echo json_encode($result->fetch_all(MYSQLI_ASSOC));
$con->close();

