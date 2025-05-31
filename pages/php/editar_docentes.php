<?php
require '../php/conecta.php';

// Inicia el buffer para evitar salida inesperada
ob_start();
header('Content-Type: application/json');

$conexion = conecta();
if (!$conexion) {
    http_response_code(500);
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Error de conexión a la base de datos.']);
    exit;
}

// Recibir y decodificar JSON
$data = json_decode(file_get_contents('php://input'), true);
if (!$data) {
    http_response_code(400);
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Datos no recibidos correctamente.']);
    exit;
}

// Obtener los datos del docente desde la solicitud
$docente_id = $data['id'] ?? null;
$nombre = $data['nombre'] ?? '';
$apellido = $data['apellido'] ?? '';
$telefono = $data['telefono'] ?? '';
$correo = $data['correo'] ?? '';
$activo = $data['activo'] ?? 1;
$puesto = $data['puesto'] ?? '';
$genero = $data['genero'] ?? '';
$fecha_nacimiento = $data['fecha_nacimiento'] ?? null;
$salario = $data['salario'] ?? '';
$direccion = $data['direccion'] ?? '';
$foto_url = $data['foto_url'] ?? '';

if (!$docente_id) {
    http_response_code(400);
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'ID no proporcionado.']);
    exit;
}

// Validación adicional opcional (por ejemplo, evitar 0000-00-00)
if ($fecha_nacimiento === '0000-00-00') {
    $fecha_nacimiento = null;
}

// Consulta SQL
$sql = "UPDATE docentes 
        SET nombre = ?, apellido = ?, telefono = ?, correo = ?, activo = ?, puesto = ?, 
            genero = ?, fecha_nacimiento = ?, salario = ?, direccion = ?, foto_url = ?, 
            actualizado_en = NOW() 
        WHERE docente_id = ?";

$stmt = $conexion->prepare($sql);
if (!$stmt) {
    http_response_code(500);
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Error al preparar la consulta.']);
    exit;
}

// Importante: corregir el número de parámetros (12) y su tipo
// s = string, i = int, d = double (float)
$stmt->bind_param(
    'ssssissssssi',
    $nombre, $apellido, $telefono, $correo, 
    $activo, $puesto, $genero, $fecha_nacimiento, 
    $salario, $direccion, $foto_url, $docente_id
);

if ($stmt->execute()) {
    ob_end_clean();
    echo json_encode(['success' => true]);
} else {
    http_response_code(500);
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Error al ejecutar la consulta.']);
}

$stmt->close();
$conexion->close();
?>
