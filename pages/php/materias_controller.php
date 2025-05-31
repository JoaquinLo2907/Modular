<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

// Solo incluir una vez el archivo de conexión
include_once 'conecta.php';
$con = conecta();

if (!$con) {
    echo json_encode(['success' => false, 'error' => 'Error de conexión a la base de datos.']);
    exit;
}

$accion = $_POST['accion'] ?? '';

// Eliminar materia
if ($accion == 'eliminar' && isset($_POST['materia_id'])) {
    $materia_id = $_POST['materia_id'];

    $sql = "DELETE FROM materias WHERE materia_id = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("i", $materia_id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => $stmt->error]);
    }

    $stmt->close();

// Editar materia
} elseif ($accion == 'editar' && isset($_POST['materia_id'], $_POST['nombre'], $_POST['nivel_grado'])) {
    $materia_id = $_POST['materia_id'];
    $nombre = $_POST['nombre'];
    $nivel_grado = $_POST['nivel_grado'];

    $sql = "UPDATE materias SET nombre = ?, nivel_grado = ? WHERE materia_id = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("ssi", $nombre, $nivel_grado, $materia_id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => $stmt->error]);
    }

    $stmt->close();

// Acción no válida
} else {
    echo json_encode(['success' => false, 'error' => 'Acción no válida o datos incompletos.']);
}

$con->close();
