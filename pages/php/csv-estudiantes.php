<?php
require 'conecta.php';
$conexion = conecta();
if (!$conexion) {
    die("Error de conexión: " . mysqli_connect_error());
}

// Nombre del archivo con fecha
$filename = "estudiantes_export_" . date('Ymd_His') . ".csv";

// Envío de cabeceras para forzar descarga
header('Content-Type: text/csv; charset=utf-8');
// Para Excel en Windows, incluimos BOM UTF-8
echo "\xEF\xBB\xBF";
header("Content-Disposition: attachment; filename={$filename}");

// Abrimos output stream
$out = fopen('php://output', 'w');

// 1) Cabecera del CSV
fputcsv($out, [
    'estudiante_id',
    'nombre',
    'apellido',
    'fecha_nacimiento',
    'grado',
    'grupo',
    'tutor_id',
    'tutor_nombre',
    'activo',
    'creado_en',
    'actualizado_en'
]);

// 2) Consulta de datos
$sql = "
  SELECT 
    e.estudiante_id,
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
  LEFT JOIN tutores t ON e.tutor_id = t.tutor_id
  ORDER BY e.estudiante_id
";
if ($res = $conexion->query($sql)) {
    while ($row = $res->fetch_assoc()) {
        // Emitimos fila
        fputcsv($out, [
          $row['estudiante_id'],
          $row['nombre'],
          $row['apellido'],
          $row['fecha_nacimiento'],
          $row['grado'],
          $row['grupo'],
          $row['tutor_id'],
          $row['tutor_nombre'],
          $row['activo'],
          $row['creado_en'],
          $row['actualizado_en']
        ]);
    }
    $res->free();
}
fclose($out);
exit;
