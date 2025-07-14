<?php
require 'conecta.php';
header('Content-Type: application/json');

// Conexión
$con = conecta();
if (!$con) {
    echo json_encode([]);
    exit;
}

// Consulta: sólo estudiantes activos
$sql = "
  SELECT 
    estudiante_id,
    nombre,
    apellido,
    grado,
    grupo,
    activo
  FROM estudiantes
  WHERE activo = 1
  ORDER BY apellido, nombre
";
$result = $con->query($sql);

// Recolectar en array
$estudiantes = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $estudiantes[] = $row;
    }
}

// Devolver JSON
echo json_encode($estudiantes);