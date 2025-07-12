<?php
header('Content-Type: application/json');
require 'conecta.php';
session_start();

$rol = $_SESSION['rol'] ?? null;

$usuario_id = ($rol == 1 && isset($_SESSION['docente_id']))
    ? $_SESSION['docente_id']
    : ($_SESSION['usuario_id'] ?? null);



if (!$usuario_id) {
    echo json_encode(['error' => 'No hay sesión activa']);
    exit;
}

$con = conecta();
if (!$con) {
    http_response_code(500);
    echo json_encode(['error' => 'Error de conexión']);
    exit;
}

$grado = isset($_GET['grado']) ? trim($_GET['grado']) : null;
$grupo = isset($_GET['grupo']) ? trim($_GET['grupo']) : null;

$tutores = [];

if ($rol == 1) {
    // DOCENTE: solo ve tutores de sus materias
    $query = "
        SELECT DISTINCT t.tutor_id, t.nombre, t.apellido, t.telefono, t.correo, t.direccion, t.activo
        FROM tutores t
        INNER JOIN estudiantes e ON t.tutor_id = e.tutor_id
        INNER JOIN asignacion_materias am ON e.estudiante_id = am.estudiante_id
        INNER JOIN materias m ON am.materia_id = m.materia_id
        WHERE m.docente_id = ?
    ";

    if (!empty($grado) && !empty($grupo)) {
        $query .= " AND e.grado = ? AND e.grupo = ?";
        $stmt = $con->prepare($query);
        $stmt->bind_param("iss", $usuario_id, $grado, $grupo);
    } else {
        $stmt = $con->prepare($query);
        $stmt->bind_param("i", $usuario_id);
    }
} else {
    // ADMIN: ve todos los tutores
    $query = "SELECT tutor_id, nombre, apellido, telefono, correo, direccion, activo FROM tutores";
    $stmt = $con->prepare($query);
}

if ($stmt) {
    $stmt->execute();
    $res = $stmt->get_result();

    while ($row = $res->fetch_assoc()) {
        $row['activo'] = $row['activo'] ? true : false;
        $tutores[] = $row;
    }

    echo json_encode($tutores);
    $stmt->close();
} else {
    http_response_code(500);
    echo json_encode([
        'error' => 'Error al preparar consulta',
        'usuario_id' => $usuario_id,
        'rol' => $rol
    ]);
}

$con->close();
