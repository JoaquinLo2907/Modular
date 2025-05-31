<?php
// php/csv-profesores.php
// ------------------------------------------------------------
// Descarga directa de un CSV con todos los registros de `docentes`

require_once __DIR__ . '/conecta.php';   // ← tu helper de conexión
$conexion = conecta();
if (!$conexion) {
    http_response_code(500);
    exit('Error al conectar a la base de datos');
}

/* 1. Consulta ─────────────────────────────────────────────── */
$sql = "SELECT
            docente_id,
            usuario_id,
            nombre,
            apellido,
            telefono,
            correo,
            activo,
            creado_en,
            actualizado_en,
            rol,
            puesto,
            genero,
            fecha_nacimiento,
            salario,
            direccion,
            foto_url
        FROM docentes";
$result = $conexion->query($sql);

if (!$result) {
    http_response_code(500);
    exit('Error en la consulta: ' . $conexion->error);
}

if ($result->num_rows === 0) {
    http_response_code(204); // Sin contenido
    exit('No hay registros en la tabla docentes.');
}

/* 2. Cabeceras para forzar la descarga ────────────────────── */
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="csv-profesores.csv"');
header('Pragma: no-cache');
header('Expires: 0');

/* 3. Escritura del CSV  (se envía directo al navegador) ───── */
$salida = fopen('php://output', 'w');

// Encabezados (misma lista que la consulta)
fputcsv($salida, [
    'docente_id',
    'usuario_id',
    'nombre',
    'apellido',
    'telefono',
    'correo',
    'activo',
    'creado_en',
    'actualizado_en',
    'rol',
    'puesto',
    'genero',
    'fecha_nacimiento',
    'salario',
    'direccion',
    'foto_url'
]);

// Filas
while ($fila = $result->fetch_assoc()) {
    fputcsv($salida, $fila);
}

fclose($salida);
$conexion->close();
exit;
