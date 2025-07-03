<?php

// Incluir la conexión a la base de datos
require '../php/conecta.php';
$conexion = conecta();

// 1) Verificar conexión
if (!$conexion) {
    header('Content-Type: text/plain; charset=utf-8');
    echo "Error: No se pudo conectar a la base de datos.";
    exit;
}

// 2) Sólo procesar POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Content-Type: text/plain; charset=utf-8');
    echo "Error: Acceso no autorizado.";
    exit;
}

// 3) Obtener datos del formulario
$nombre       = $_POST['nombre'] ?? '';
$apellido     = $_POST['apellido'] ?? '';
$correo       = $_POST['email'] ?? '';
$password1    = $_POST['contraseña'] ?? '';
$password2    = $_POST['confirmar_contraseña'] ?? '';
$rol          = intval($_POST['rol'] ?? 0);

// 4) Validar contraseñas
if ($password1 !== $password2) {
    header('Content-Type: text/plain; charset=utf-8');
    echo "Error: Las contraseñas no coinciden.";
    exit;
}
$password_hash = password_hash($password1, PASSWORD_BCRYPT);

// 5) Manejo de la imagen
$directorio   = "../../assets/img/uploads/";
$nombreImagen = basename($_FILES['imagen']['name'] ?? '');
$rutaImagen   = $directorio . $nombreImagen;
$rutaBD       = "assets/img/docentes/" . $nombreImagen;

if (
    !isset($_FILES['imagen']) ||
    $_FILES['imagen']['error'] !== UPLOAD_ERR_OK
) {
    header('Content-Type: text/plain; charset=utf-8');
    echo "Error: al subir la imagen (código {$_FILES['imagen']['error']}).";
    exit;
}

if (!move_uploaded_file($_FILES['imagen']['tmp_name'], $rutaImagen)) {
    header('Content-Type: text/plain; charset=utf-8');
    echo "Error: no se pudo guardar la imagen en el servidor.";
    exit;
}

// 6) Verificar email duplicado
$sql = "SELECT 1 FROM usuarios WHERE correo = ?";
$stmt = $conexion->prepare($sql);
if (!$stmt) {
    header('Content-Type: text/plain; charset=utf-8');
    echo "Error: fallo al preparar consulta de verificación.";
    exit;
}
$stmt->bind_param('s', $correo);
$stmt->execute();
$res = $stmt->get_result();
if ($res->num_rows > 0) {
    header('Content-Type: text/plain; charset=utf-8');
    echo "Error: El correo ya está registrado.";
    $stmt->close();
    exit;
}
$stmt->close();

// 7) Insertar en usuarios
$sql = "INSERT INTO usuarios (nombre_usuario, contraseña, correo, rol)
        VALUES (?, ?, ?, ?)";
$stmt = $conexion->prepare($sql);
if (!$stmt) {
    header('Content-Type: text/plain; charset=utf-8');
    echo "Error: fallo al preparar consulta de usuario.";
    exit;
}
$stmt->bind_param('sssi', $nombre, $password_hash, $correo, $rol);
if (!$stmt->execute()) {
    header('Content-Type: text/plain; charset=utf-8');
    echo "Error: No se pudo registrar el usuario.";
    $stmt->close();
    exit;
}
$usuario_id = $stmt->insert_id;
$stmt->close();

// 8) Si es docente (rol = 1), insertar en la tabla docentes
if ($rol === 1) {
    $direccion  = $_POST['direccion']  ?? '';
    $telefono   = $_POST['telefono']   ?? '';
    $puesto     = $_POST['puesto']     ?? '';
    $genero     = $_POST['genero']     ?? '';
    $nacimiento = $_POST['nacimiento'] ?? '';
    $salario    = $_POST['salario']    ?? '';

    $sql = "INSERT INTO docentes
            (usuario_id, nombre, apellido, telefono, correo, rol, puesto, genero,
             fecha_nacimiento, salario, direccion, foto_url)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conexion->prepare($sql);
    if (!$stmt) {
        header('Content-Type: text/plain; charset=utf-8');
        echo "Error: fallo al preparar consulta de docente.";
        exit;
    }
    $stmt->bind_param(
        'issssisssdss',
        $usuario_id, $nombre, $apellido, $telefono, $correo, $rol,
        $puesto, $genero, $nacimiento, $salario, $direccion, $rutaBD
    );
    if (!$stmt->execute()) {
        header('Content-Type: text/plain; charset=utf-8');
        echo "Error: No se pudo registrar el docente.";
        $stmt->close();
        exit;
    }
    $stmt->close();

    // 9) Éxito final
    header('Content-Type: text/plain; charset=utf-8');
    echo "Docente registrado con éxito.";
    $conexion->close();
    exit;
}

// 10) Si llega aquí, se registró un usuario sin rol docente
header('Content-Type: text/plain; charset=utf-8');
echo "Usuario registrado con éxito.";
$conexion->close();
exit;
?>
