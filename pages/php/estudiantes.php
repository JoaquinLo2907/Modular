<?php
header('Content-Type: application/json');
include 'conecta.php';
$con = conecta();

$sql = "SELECT 
            e.estudiante_id,
            e.usuario_id,
            u.nombre_usuario,
            e.nombre,
            e.apellido,
            e.fecha_nacimiento,
            e.grado,
            e.grupo,
            e.tutor_id,
            e.activo,
            e.creado_en,
            e.actualizado_en
        FROM estudiantes e
        LEFT JOIN usuarios u ON e.usuario_id = u.usuario_id";

$result = $con->query($sql);

$estudiantes = [];

while ($row = $result->fetch_assoc()) {
    $estudiantes[] = $row;
}

echo json_encode($estudiantes);
$con->close();
?>
