<?php
header('Content-Type: text/plain; charset=utf-8');
require '../php/conecta.php';
$conexion = conecta();

// 0) Conexión
if (!$conexion) {
    http_response_code(500);
    echo "Error: No se pudo conectar a la base de datos.";
    exit;
}

// 1) Sólo POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo "Error: acceso no autorizado.";
    exit;
}

// 2) Captura
$passwordRaw     = $_POST['password']        ?? '';
$confirmPassword = $_POST['confirmPassword'] ?? '';
$nombre          = trim($_POST['nombre']      ?? '');
$apellido        = trim($_POST['apellido']    ?? '');
$correo          = trim($_POST['email']       ?? '');
$rol             = intval($_POST['rol']       ?? 0);
$telefono        = trim($_POST['telefono']    ?? '');
$direccion       = trim($_POST['direccion']   ?? '');

// 3) Validaciones servidor

// 3.1) Contraseñas iguales
if ($passwordRaw !== $confirmPassword) {
    echo "Error: las contraseñas no coinciden.";
    exit;
}

// 3.2) Nombre y apellido
if (empty($nombre) || !preg_match('/^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]{2,}$/u', $nombre)) {
    echo "Error: nombre inválido. Sólo letras y espacios, mínimo 2 caracteres.";
    exit;
}
if (empty($apellido) || !preg_match('/^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]{2,}$/u', $apellido)) {
    echo "Error: apellido inválido. Sólo letras y espacios, mínimo 2 caracteres.";
    exit;
}

// 3.3) Correo válido
if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
    echo "Error: correo electrónico inválido.";
    exit;
}

// 3.4) Contraseña fuerte
if (strlen($passwordRaw) < 8
    || !preg_match('/[A-Z]/', $passwordRaw)
    || !preg_match('/[a-z]/', $passwordRaw)
    || !preg_match('/\d/', $passwordRaw)
) {
    echo "Error: la contraseña debe tener mínimo 8 caracteres, al menos una mayúscula y un número.";
    exit;
}

// 3.5) Teléfono y dirección (si es tutor)
if ($rol === 2) {
    if (!preg_match('/^[0-9]{7,15}$/', $telefono)) {
        echo "Error: teléfono inválido. Sólo dígitos, entre 7 y 15 caracteres.";
        exit;
    }
    if (strlen($direccion) < 5) {
        echo "Error: dirección demasiado corta.";
        exit;
    }
}

// 4) Verificar email único
$sql_ver = "SELECT 1 FROM usuarios WHERE correo = ?";
$stmt_ver = $conexion->prepare($sql_ver);
$stmt_ver->bind_param('s', $correo);
$stmt_ver->execute();
$stmt_ver->store_result();
if ($stmt_ver->num_rows > 0) {
    echo "Error: el correo ya está registrado.";
    exit;
}
$stmt_ver->close();

// 5) Insertar en usuarios
$passwordHash = password_hash($passwordRaw, PASSWORD_BCRYPT);
$sql_usr = "INSERT INTO usuarios (nombre_usuario, contraseña, correo, rol) VALUES (?, ?, ?, ?)";
$stmt_usr = $conexion->prepare($sql_usr);
$stmt_usr->bind_param('sssi', $nombre, $passwordHash, $correo, $rol);
if (!$stmt_usr->execute()) {
    http_response_code(500);
    echo "Error al registrar el usuario.";
    exit;
}
$usuario_id = $stmt_usr->insert_id;
$stmt_usr->close();

// 6) Si es tutor, insertar detalles
if ($rol === 2) {
    $sql_tut = "INSERT INTO tutores 
                (usuario_id, nombre, apellido, telefono, correo, direccion, rol)
                VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt_tut = $conexion->prepare($sql_tut);
    $stmt_tut->bind_param('isssssi',
        $usuario_id, $nombre, $apellido, $telefono, $correo, $direccion, $rol
    );
    if (!$stmt_tut->execute()) {
        http_response_code(500);
        echo "Error al registrar los datos del tutor.";
        exit;
    }
    $stmt_tut->close();
} else {
    echo "Error: rol no válido.";
    exit;
}

// 7) Éxito
$conexion->close();
echo "Registro exitoso";
exit;
?>
