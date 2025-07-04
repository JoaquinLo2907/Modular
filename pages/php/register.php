<?php
header('Content-Type: text/plain; charset=utf-8');
require '../php/conecta.php';
$conexion = conecta();

// 0) Verificamos conexión
if (!$conexion) {
    http_response_code(500);
    echo "Error: No se pudo conectar a la base de datos.";
    exit;
}

// 1) Sólo aceptamos POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo "Error: acceso no autorizado.";
    exit;
}

// 2) Capturamos y validamos contraseñas
$passwordRaw     = $_POST['password'] ?? '';
$confirmPassword = $_POST['confirmPassword'] ?? '';
if ($passwordRaw !== $confirmPassword) {
    echo "Error: las contraseñas no coinciden.";
    exit;
}

// 3) Preparamos datos
$nombre   = trim($_POST['nombre'] ?? '');
$apellido = trim($_POST['apellido'] ?? '');
$correo   = trim($_POST['email'] ?? '');
$rol      = intval($_POST['rol'] ?? 0);
$password = password_hash($passwordRaw, PASSWORD_BCRYPT);

// 4) Verificar si el correo ya existe
$sql_verificar = "SELECT 1 FROM usuarios WHERE correo = ?";
$stmt_verificar = $conexion->prepare($sql_verificar);
$stmt_verificar->bind_param('s', $correo);
$stmt_verificar->execute();
$stmt_verificar->store_result();
if ($stmt_verificar->num_rows > 0) {
    echo "Error: el correo ya está registrado.";
    $stmt_verificar->close();
    exit;
}
$stmt_verificar->close();

// 5) Insertar en usuarios
$sql_usuario = "INSERT INTO usuarios (nombre_usuario, contraseña, correo, rol) VALUES (?, ?, ?, ?)";
$stmt_usuario = $conexion->prepare($sql_usuario);
$stmt_usuario->bind_param('sssi', $nombre, $password, $correo, $rol);
if (!$stmt_usuario->execute()) {
    http_response_code(500);
    echo "Error al registrar el usuario.";
    $stmt_usuario->close();
    $conexion->close();
    exit;
}
$usuario_id = $stmt_usuario->insert_id;
$stmt_usuario->close();

// 6) Si es tutor (rol 2), insertar en tutores
if ($rol === 2) {
    $telefono  = trim($_POST['telefono']  ?? '');
    $direccion = trim($_POST['direccion'] ?? '');
    $sql_tutor = "INSERT INTO tutores (usuario_id, nombre, apellido, telefono, correo, direccion, rol)
                  VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt_tutor = $conexion->prepare($sql_tutor);
    $stmt_tutor->bind_param('isssssi',
        $usuario_id, $nombre, $apellido, $telefono, $correo, $direccion, $rol
    );
    if (!$stmt_tutor->execute()) {
        http_response_code(500);
        echo "Error al registrar los datos del tutor.";
        $stmt_tutor->close();
        $conexion->close();
        exit;
    }
    $stmt_tutor->close();
} else {
    echo "Error: rol no válido.";
    $conexion->close();
    exit;
}

// 7) Todo OK
$conexion->close();
echo "Registro exitoso";
exit;
?>
