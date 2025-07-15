<?php
header('Content-Type: application/json');
require 'conecta.php';
$con = conecta();

$data = json_decode(file_get_contents('php://input'), true);

if (
    empty($data['clase_id']) ||
    empty($data['ciclo_id']) ||
    empty($data['grado']) ||
    empty($data['grupo'])
) {
    echo json_encode(['success' => false, 'message' => 'Datos incompletos para actualizar']);
    exit;
}

try {
    $con->begin_transaction();

    // 1) Actualizar los datos de la clase
    $stmt = $con->prepare("
        UPDATE clases
        SET ciclo_id = ?, grado = ?, grupo = ?
        WHERE clase_id = ?
    ");
    $stmt->bind_param(
        "issi",
        $data['ciclo_id'],
        $data['grado'],
        $data['grupo'],
        $data['clase_id']
    );
    $stmt->execute();
    $stmt->close();

    // 2) Borrar asignaciones actuales
    $del = $con->prepare("
        DELETE FROM clase_asignacion
        WHERE clase_id = ?
    ");
    $del->bind_param("i", $data['clase_id']);
    $del->execute();
    $del->close();

    // 3) Insertar las nuevas asignaciones
    $assigns = $data['asignaciones'] ?? [];
    $stmtA = $con->prepare("
        INSERT INTO clase_asignacion (clase_id, materia_id, docente_id)
        VALUES (?, ?, ?)
    ");
    foreach ($assigns as $a) {
        $stmtA->bind_param(
            "iii",
            $data['clase_id'],
            $a['materia_id'],
            $a['docente_id']
        );
        $stmtA->execute();
    }
    $stmtA->close();

    $con->commit();
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    $con->rollback();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
