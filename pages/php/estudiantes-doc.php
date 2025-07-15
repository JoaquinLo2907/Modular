<?php
session_start();
require 'conecta.php';
$con = conecta();

header('Content-Type: application/json');

// Validar sesión
if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['rol'])) {
  http_response_code(401);
  echo json_encode(["error" => "No autorizado"]);
  exit;
}

$usuario_id = $_SESSION['usuario_id'];
$rol = $_SESSION['rol']; // 1 = docente, 2 = admin
$materia_id = isset($_GET['materia_id']) ? intval($_GET['materia_id']) : 0;

$estudiantes = [];

if ($rol == 1) {
  // 🧑‍🏫 DOCENTE: solo estudiantes asignados a sus materias
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
      CONCAT(t.nombre, ' ', t.apellido) AS tutor_nombre,
      m.materia_id,
      m.ciclo
    FROM estudiantes e
    JOIN tutores t ON e.tutor_id = t.tutor_id
    JOIN asignacion_materias am ON am.estudiante_id = e.estudiante_id
    JOIN materias m ON am.materia_id = m.materia_id
    JOIN docentes d ON m.docente_id = d.docente_id
    WHERE d.usuario_id = ?
  ";

  // Agregar filtro por materia si se solicita
  if ($materia_id > 0) {
    $query .= " AND m.materia_id = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("ii", $usuario_id, $materia_id);
  } else {
    $stmt = $con->prepare($query);
    $stmt->bind_param("i", $usuario_id);
  }

} else {
  // 👑 ADMIN: ver todos los estudiantes activos (sin importar materia)
  $query = "
    SELECT 
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
    LEFT JOIN tutores t ON e.tutor_id = t.tutor_id
    WHERE e.activo = 1
  ";

  $stmt = $con->prepare($query);
}

if (!$stmt) {
  echo json_encode(["error" => "Error en la preparación de la consulta"]);
  exit;
}

$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
  $estudiantes[] = $row;
}

echo json_encode($estudiantes, JSON_UNESCAPED_UNICODE);

$stmt->close();
$con->close();
