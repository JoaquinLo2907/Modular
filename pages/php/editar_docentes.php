<?php
require '../php/conecta.php';

ob_start();
header('Content-Type: application/json');

$conexion = conecta();
if (!$conexion) {
    http_response_code(500);
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Error de conexión a la base de datos.']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
if (!$data) {
    http_response_code(400);
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Datos no recibidos correctamente.']);
    exit;
}

$docente_id       = $data['id'] ?? null;
$nombre           = $data['nombre'] ?? '';
$apellido         = $data['apellido'] ?? '';
$telefono         = $data['telefono'] ?? '';
$correo           = $data['correo'] ?? '';
$activo           = $data['activo'] ?? 1;
$puesto           = $data['puesto'] ?? '';
$genero           = $data['genero'] ?? '';
$fecha_nacimiento = $data['fecha_nacimiento'] ?? null;
$salario          = $data['salario'] ?? '';
$direccion        = $data['direccion'] ?? '';
$foto_url         = $data['foto_url'] ?? '';

$nueva_contraseña     = $data['nueva_contraseña'] ?? '';
$confirmar_contraseña = $data['confirmar_contraseña'] ?? '';

if (!$docente_id) {
    http_response_code(400);
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'ID no proporcionado.']);
    exit;
}

// ✅ Actualizar contraseña si fue proporcionada y es válida
if (!empty($nueva_contraseña) || !empty($confirmar_contraseña)) {
    if ($nueva_contraseña !== $confirmar_contraseña) {
        http_response_code(400);
        ob_end_clean();
        echo json_encode(['success' => false, 'message' => 'Las contraseñas no coinciden.']);
        exit;
    }

    $query_id = $conexion->prepare("SELECT usuario_id FROM docentes WHERE docente_id = ?");
    $query_id->bind_param("i", $docente_id);
    $query_id->execute();
    $query_id->bind_result($usuario_id);
    $query_id->fetch();
    $query_id->close();

    if ($usuario_id) {
        $password_hash = password_hash($nueva_contraseña, PASSWORD_BCRYPT);
        // ⚠️ Aquí usamos 'contraseña' porque así se llama en tu tabla
        $update_pass = $conexion->prepare("UPDATE usuarios SET contraseña = ? WHERE usuario_id = ?");
        $update_pass->bind_param("si", $password_hash, $usuario_id);
        if (!$update_pass->execute()) {
            http_response_code(500);
            ob_end_clean();
            echo json_encode(['success' => false, 'message' => 'Error al actualizar la contraseña.']);
            $update_pass->close();
            $conexion->close();
            exit;
        }
        $update_pass->close();
    } else {
        http_response_code(400);
        ob_end_clean();
        echo json_encode(['success' => false, 'message' => 'Usuario no encontrado para este docente.']);
        $conexion->close();
        exit;
    }
}

if ($fecha_nacimiento === '0000-00-00') {
    $fecha_nacimiento = null;
}

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
