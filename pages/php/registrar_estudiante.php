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

// 1) Recogemos y saneamos
$nombre           = trim($_POST['nombre']           ?? '');
$apellido         = trim($_POST['apellido']         ?? '');
$fecha_nacimiento = trim($_POST['fecha_nacimiento'] ?? '');
$grado            = intval($_POST['grado']         ?? 0);
$grupo            = trim($_POST['grupo']           ?? '');
$tutor_id         = intval($_POST['tutor_id']      ?? 0);

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

// 3) Empezamos la transacción
$conexion->begin_transaction();

try {
    // 4) Insertar en estudiantes (sin tutor_id)
    $sql1 = "
      INSERT INTO estudiantes
        (nombre, apellido, fecha_nacimiento, grado, grupo, activo, creado_en, actualizado_en)
      VALUES
        (?, ?, ?, ?, ?, 1, NOW(), NOW())
    ";
    $stmt1 = $conexion->prepare($sql1);
    $stmt1->bind_param('sssis', $nombre, $apellido, $fecha_nacimiento, $grado, $grupo);

    if (!$stmt1->execute()) {
        throw new Exception('Error al insertar estudiante: ' . $stmt1->error);
    }

    // 5) Recuperar el ID generado del estudiante
    $estudiante_id = $conexion->insert_id;
    $stmt1->close();

    // 6) Insertar en la tabla de relación
    $sql2 = "
      INSERT INTO tutor_estudiante
        (tutor_id, estudiante_id)
      VALUES
        (?, ?)
    ";
    $stmt2 = $conexion->prepare($sql2);
    $stmt2->bind_param('ii', $tutor_id, $estudiante_id);

    if (!$stmt2->execute()) {
        throw new Exception('Error al asignar tutor: ' . $stmt2->error);
    }

    // Si quieres obtener el tutorEstudiante_id recién creado:
    $tutorEstudiante_id = $conexion->insert_id;
    $stmt2->close();

    // 7) Confirmamos la transacción
    $conexion->commit();

    // 8) Respondemos con éxito y el id de la relación
    echo json_encode([
        'success'              => true,
        'estudiante_id'        => $estudiante_id,
        'tutorEstudiante_id'   => $tutorEstudiante_id
    ]);
} catch (Exception $e) {
    $conexion->rollback();
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
$conexion->close();
