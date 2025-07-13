<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../php/conecta.php';
$con = conecta();

if (!$con) {
    echo json_encode(["error" => "Error de conexión"]);
    exit;
}

$usuario_id = $_SESSION['usuario_id'] ?? null;
$rol = $_SESSION['rol'] ?? null;

if (!$usuario_id || !isset($rol)) {
    echo json_encode(["error" => "Sesión inválida"]);
    exit;
}

$materias = [];

if ($rol == 1) {
    // DOCENTE - solo sus materias
    $stmtDocente = $con->prepare("SELECT docente_id FROM docentes WHERE usuario_id = ?");
    $stmtDocente->bind_param("i", $usuario_id);
    $stmtDocente->execute();
    $resDocente = $stmtDocente->get_result();

    if ($resDocente->num_rows > 0) {
        $docente_id = $resDocente->fetch_assoc()['docente_id'];

        $stmt = $con->prepare("SELECT materia_id, nombre, nivel_grado, descripcion, foto_url 
                               FROM materias WHERE docente_id = ?");
        $stmt->bind_param("i", $docente_id);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $row['foto_url'] = $row['foto_url'] ?: '';
            $materias[] = $row;
        }
    }

} else {
    // ADMIN o TUTOR - ver todo
    $sql = "SELECT materia_id, nombre, nivel_grado, descripcion, foto_url FROM materias";
    $result = $con->query($sql);

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $row['foto_url'] = $row['foto_url'] ?: '';
            $materias[] = $row;
        }
    }
}

header('Content-Type: application/json');
echo json_encode([
    "rol" => $rol,
    "materias" => $materias
]);

$con->close();
