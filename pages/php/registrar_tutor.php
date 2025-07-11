<?php
require '../php/conecta.php';
header('Content-Type: application/json');
$conexion = conecta();

// 1) Método
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  echo json_encode(['success' => false, 'message' => 'Método no permitido']);
  exit;
}

// 2) Recoger
$nombre       = trim($_POST['nombre']       ?? '');
$apellido     = trim($_POST['apellido']     ?? '');
$telefono     = trim($_POST['telefono']     ?? '');
$correo       = trim($_POST['correo']       ?? '');
$direccion    = trim($_POST['direccion']    ?? '');
$estudianteId = intval($_POST['estudiante_id'] ?? 0);
$pass1        = $_POST['password']  ?? '';
$pass2        = $_POST['password2'] ?? '';
$rol          = 2;

// 3) Validar
$errors = [];
if (!$nombre || !$apellido || !$telefono || !$correo || !$direccion) {
  $errors[] = 'Todos los campos son obligatorios';
}
if ($pass1 !== $pass2) {
  $errors[] = 'Las contraseñas no coinciden';
}
if ($errors) {
  echo json_encode(['success' => false, 'message' => implode('. ', $errors)]);
  exit;
}

// 4) Correo duplicado
$stmt = $conexion->prepare("SELECT 1 FROM usuarios WHERE correo = ?");
$stmt->bind_param('s', $correo);
$stmt->execute();
$rs = $stmt->get_result();
if ($rs->num_rows) {
  echo json_encode(['success' => false, 'message' => 'Correo ya registrado']);
  exit;
}

// 5) Insert usuario
$hash = password_hash($pass1, PASSWORD_BCRYPT);
$stmt = $conexion->prepare("INSERT INTO usuarios (nombre_usuario, contraseña, correo, rol) VALUES (?, ?, ?, ?)");
$stmt->bind_param('sssi', $correo, $hash, $correo, $rol);
if (!$stmt->execute()) {
  echo json_encode(['success' => false, 'message' => 'Error al crear usuario']);
  exit;
}
$usuarioId = $stmt->insert_id;

// 6) Insert tutor
$stmt = $conexion->prepare("
  INSERT INTO tutores (usuario_id, nombre, apellido, telefono, correo, direccion, activo, rol)
  VALUES (?, ?, ?, ?, ?, ?, 1, ?)
");
$stmt->bind_param('isssssi', $usuarioId, $nombre, $apellido, $telefono, $correo, $direccion, $rol);
if (!$stmt->execute()) {
  echo json_encode(['success' => false, 'message' => 'Error al crear tutor']);
  exit;
}
$tutorId = $stmt->insert_id;

// 7) Vinculación opcional
if ($estudianteId > 0) {
  $upd = $conexion->prepare("UPDATE estudiantes SET tutor_id = ? WHERE estudiante_id = ?");
  $upd->bind_param('ii', $tutorId, $estudianteId);
  $upd->execute();
}

// 8) Éxito
echo json_encode(['success' => true, 'message' => 'Tutor registrado con éxito']);
exit;
