<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Carga el helper de conexión desde esta misma carpeta
require_once __DIR__ . '/conecta.php';
$con = conecta();
if (!$con) {
    http_response_code(500);
    exit('Error de conexión: ' . mysqli_connect_error());
}

header('Content-Type: application/json; charset=utf-8');

// Lista todos los períodos registrados en la tabla `periodos`
$sql = "
  SELECT 
    periodo_id,
    nombre
  FROM periodos
  ORDER BY nombre DESC
";

$result = $con->query($sql);
if (!$result) {
    http_response_code(500);
    exit('Error en listar_periodos.php: ' . $con->error);
}

$periodos = [];
while ($row = $result->fetch_assoc()) {
    // Cada elemento: { periodo_id: 1, nombre: "2025-A" }
    $periodos[] = $row;
}

echo json_encode($periodos, JSON_UNESCAPED_UNICODE);
$con->close();
