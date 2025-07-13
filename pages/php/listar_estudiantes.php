<?php
require_once __DIR__ . '/conecta.php';
$con = conecta();
if (!$con) {
    http_response_code(500);
    exit('Error al conectar a la BD');
}

header('Content-Type: application/json');
$sql = "SELECT estudiante_id, CONCAT(nombre,' ',apellido) AS alumno FROM estudiantes WHERE activo=1";
$result = $con->query($sql);
if (!$result) {
    http_response_code(500);
    exit('Error en listar_estudiantes: ' . $con->error);
}

$lista = [];
while ($row = $result->fetch_assoc()) {
    $lista[] = $row;
}
echo json_encode($lista, JSON_UNESCAPED_UNICODE);
$con->close();
