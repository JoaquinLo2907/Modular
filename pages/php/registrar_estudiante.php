<?php
require 'conecta.php';
header('Content-Type: application/json');

// 0) Conexión a la base de datos
$conexion = conecta();
if (!$conexion) {
    echo json_encode([
        'success' => false,
        'message' => 'No se pudo conectar a la base de datos.'
    ]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => 'Método no permitido'
    ]);
    exit;
}

// 1) Recogemos y saneamos los datos del formulario
$nombre           = trim($_POST['nombre'] ?? '');
$apellido         = trim($_POST['apellido'] ?? '');
$fecha_nacimiento = trim($_POST['fecha_nacimiento'] ?? '');
$grado            = intval($_POST['grado'] ?? 0);
$grupo            = trim($_POST['grupo'] ?? '');
$tutor_id         = intval($_POST['tutor_id'] ?? 0);

// 2) Validaciones

// Nombre y Apellido: sólo letras (incluyendo tildes y ñ) y espacios, mínimo 2 caracteres
$regex_nombre = '/^[A-Za-zÁÉÍÓÚáéíóúÑñ ]{2,}$/u';
if (!preg_match($regex_nombre, $nombre)) {
    echo json_encode(['success' => false, 'message' => 'Nombre inválido. Sólo letras y espacios, mínimo 2 caracteres.']);
    exit;
}
if (!preg_match($regex_nombre, $apellido)) {
    echo json_encode(['success' => false, 'message' => 'Apellido inválido. Sólo letras y espacios, mínimo 2 caracteres.']);
    exit;
}

// Fecha de nacimiento: formato YYYY-MM-DD y fecha real
$d = DateTime::createFromFormat('Y-m-d', $fecha_nacimiento);
if (!($d && $d->format('Y-m-d') === $fecha_nacimiento)) {
    echo json_encode(['success' => false, 'message' => 'Fecha de nacimiento inválida.']);
    exit;
}

// Grado: número entre 1 y 12
if ($grado < 1 || $grado > 12) {
    echo json_encode(['success' => false, 'message' => 'Grado debe ser un número entre 1 y 12.']);
    exit;
}

// Grupo: valores permitidos A, B o C
$grupos_validos = ['A','B','C'];
if (!in_array($grupo, $grupos_validos, true)) {
    echo json_encode(['success' => false, 'message' => 'Debe seleccionar un grupo válido.']);
    exit;
}

// Tutor: debe ser ID positivo
if ($tutor_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Debe seleccionar un tutor.']);
    exit;
}

// 3) Preparamos el INSERT
$sql = "
  INSERT INTO estudiantes
    (nombre, apellido, fecha_nacimiento, grado, grupo, tutor_id, activo, creado_en, actualizado_en)
  VALUES
    (?, ?, ?, ?, ?, ?, 1, NOW(), NOW())
";
$stmt = $conexion->prepare($sql);
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Error al preparar la consulta: '.$conexion->error]);
    exit;
}

// 4) Enlazamos parámetros y ejecutamos
$stmt->bind_param(
    'sssisi',
    $nombre,
    $apellido,
    $fecha_nacimiento,
    $grado,
    $grupo,
    $tutor_id
);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Error al insertar estudiante: '.$stmt->error
    ]);
}

$stmt->close();
$conexion->close();
