<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/conecta.php';
$con = conecta();
if (!$con) {
  http_response_code(500);
  echo json_encode(['error'=>'No se pudo conectar']);
  exit;
}

$sql = "
  SELECT 
    e.estudiante_id,
    e.usuario_id,
    u.nombre_usuario,
    e.nombre,
    e.apellido,
    e.fecha_nacimiento,
    e.grado,
    e.grupo,
    e.tutor_id,
    CONCAT(t.nombre,' ',t.apellido) AS tutor_nombre,
    e.activo,
    e.creado_en,
    e.actualizado_en
  FROM estudiantes e
  LEFT JOIN usuarios u   ON e.usuario_id = u.usuario_id
  LEFT JOIN tutores t    ON e.tutor_id   = t.tutor_id
  WHERE e.activo = 1
  ORDER BY e.estudiante_id
";

if (!($result = $con->query($sql))) {
  http_response_code(500);
  echo json_encode(['error'=>'SQL: '.$con->error]);
  exit;
}

echo json_encode($result->fetch_all(MYSQLI_ASSOC));
$con->close();
