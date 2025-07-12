<?php
header('Content-Type: text/plain; charset=utf-8');
require '../php/conecta.php';
$conexion = conecta();

// 1) Conexión
if (!$conexion) {
    http_response_code(500);
    echo "Error: No se pudo conectar a la base de datos.";
    exit;
}

// 2) Sólo POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo "Error: acceso no autorizado.";
    exit;
}

// 3) Captura
$nombre     = trim($_POST['nombre']              ?? '');
$apellido   = trim($_POST['apellido']            ?? '');
$correo     = trim($_POST['email']               ?? '');
$pass1      = $_POST['contraseña']               ?? '';
$pass2      = $_POST['confirmar_contraseña']     ?? '';
$rol        = intval($_POST['rol']               ?? 0);
$puesto     = trim($_POST['puesto']              ?? '');
$genero     = trim($_POST['genero']              ?? '');
$telefono   = trim($_POST['telefono']            ?? '');
$nacimiento = $_POST['nacimiento']               ?? '';
$salario    = trim($_POST['salario']             ?? '');
$direccion  = trim($_POST['direccion']           ?? '');

// 4) Validaciones

// 4.1) Contraseñas iguales y fuertes
if ($pass1 !== $pass2) {
    echo "Error: Las contraseñas no coinciden."; exit;
}
if (!preg_match('/^(?=.*[A-Z])(?=.*\d).{8,}$/', $pass1)) {
    echo "Error: La contraseña debe tener mínimo 8 caracteres, una mayúscula y un número."; exit;
}
$pass_hash = password_hash($pass1, PASSWORD_BCRYPT);

// 4.2) Nombre/apellido
if (!preg_match('/^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]{2,}$/u', $nombre)) {
    echo "Error: Nombre inválido."; exit;
}
if (!preg_match('/^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]{2,}$/u', $apellido)) {
    echo "Error: Apellido inválido."; exit;
}

// 4.3) Correo
if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
    echo "Error: Correo inválido."; exit;
}

// 4.4) Puesto y género
if (!in_array($puesto, ['profesor','coordinador'], true)) {
    echo "Error: Puesto no válido."; exit;
}
if (!in_array($genero, ['masculino','femenino','otro'], true)) {
    echo "Error: Género no válido."; exit;
}

// 4.5) Teléfono
if (!preg_match('/^\d{7,15}$/', $telefono)) {
    echo "Error: Teléfono inválido."; exit;
}

// 4.6) Fecha
$d = DateTime::createFromFormat('Y-m-d', $nacimiento);
if (!$d || $d->format('Y-m-d') !== $nacimiento) {
    echo "Error: Fecha de nacimiento inválida."; exit;
}

// 4.7) Salario
if (!is_numeric($salario) || floatval($salario) <= 0) {
    echo "Error: Salario inválido."; exit;
}

// 4.8) Dirección
if (strlen($direccion) < 5) {
    echo "Error: Dirección demasiado corta."; exit;
}

// 4.9) Imagen
if (!isset($_FILES['imagen']) || $_FILES['imagen']['error'] !== UPLOAD_ERR_OK) {
    echo "Error: Problema al subir la imagen."; exit;
}
$info = getimagesize($_FILES['imagen']['tmp_name']);
if (!$info || !in_array($info['mime'], ['image/jpeg','image/png'], true)) {
    echo "Error: Formato de imagen no válido."; exit;
}
if ($_FILES['imagen']['size'] > 2*1024*1024) {
    echo "Error: Imagen supera 2 MB."; exit;
}
$destDir = "../../assets/img/uploads/";
if (!move_uploaded_file($_FILES['imagen']['tmp_name'], $destDir . basename($_FILES['imagen']['name']))) {
    echo "Error: No se pudo guardar la imagen."; exit;
}
$fotoUrl = "assets/img/uploads/" . basename($_FILES['imagen']['name']);

// 5) Verificar correo único
$stmt = $conexion->prepare("SELECT 1 FROM usuarios WHERE correo = ?");
$stmt->bind_param('s', $correo);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows) {
    echo "Error: El correo ya está registrado."; exit;
}
$stmt->close();

// 6) Insertar en usuarios
$stmt = $conexion->prepare(
    "INSERT INTO usuarios (nombre_usuario, contraseña, correo, rol)
     VALUES (?, ?, ?, ?)"
);
$stmt->bind_param('sssi', $nombre, $pass_hash, $correo, $rol);
if (!$stmt->execute()) {
    echo "Error: No se pudo registrar el usuario."; exit;
}
$userId = $stmt->insert_id;
$stmt->close();

// 7) Insertar en docentes
$stmt = $conexion->prepare(
    "INSERT INTO docentes
     (usuario_id, nombre, apellido, telefono, correo, rol, puesto, genero,
      fecha_nacimiento, salario, direccion, foto_url)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
);
$stmt->bind_param(
    'issssissdiss',
    $userId, $nombre, $apellido, $telefono, $correo,
    $rol, $puesto, $genero, $nacimiento, $salario,
    $direccion, $fotoUrl
);
if (!$stmt->execute()) {
    echo "Error: No se pudo registrar el docente."; exit;
}
$stmt->close();

echo "Docente registrado con éxito.";
$conexion->close();
exit;
?>
