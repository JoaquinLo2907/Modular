<?php
header('Content-Type: application/json');

// 1. Ajusta la ruta a tu conexión (si tu HTML está en pages/tutor/
// y tu PHP en pages/php, quedaría así):
require '../php/conecta.php';

$conexion = conecta();
if (!$conexion) {
    http_response_code(500);
    echo json_encode(['error' => 'No se pudo conectar a la base de datos']);
    exit;
}

// 2. Consulta los campos que necesitas según tu tabla `tutores`
// Esquema de `tutores`: tutor_id, nombre, apellido, telefono, correo, direccion, activo, rol 
$sql = "SELECT 
          tutor_id,
          nombre,
          apellido,
          telefono,
          correo,
          direccion,
          activo
        FROM tutores";

if ($res = $conexion->query($sql)) {
    $tutores = [];
    while ($row = $res->fetch_assoc()) {
        // opcional: convertir activo a booleano o texto
        $row['activo'] = $row['activo'] == 1;
        $tutores[] = $row;
    }
    echo json_encode($tutores);
} else {
    // en caso de error en la consulta
    http_response_code(500);
    echo json_encode(['error' => 'Error en la consulta']);
}
