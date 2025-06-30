<?php
header('Content-Type: application/json; charset=utf-8');
// Esto garantiza que PHP busque conecta.php en el mismo directorio que este script
require_once __DIR__ . '/conecta.php';
$con = conecta();

if (!$con) {
    http_response_code(500);
    echo json_encode(['error' => 'No se pudo conectar a la base de datos']);
    exit;
}

$sql = "SELECT tutor_id, nombre, apellido FROM tutores WHERE activo = 1";
if ($result = $con->query($sql)) {
    // fetch_all está disponible en mysqli para sacar todo en un array
    $tutores = $result->fetch_all(MYSQLI_ASSOC);
    echo json_encode($tutores);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Error en la consulta: '.$con->error]);
}

$con->close();
