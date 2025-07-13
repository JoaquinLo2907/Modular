<?php
header('Content-Type: application/json');
require 'conecta.php';
$con = conecta();

$data = json_decode(file_get_contents('php://input'), true);

if (empty($data['ciclo_id']) || empty($data['grado']) || empty($data['grupo'])) {
    echo json_encode(['success' => false, 'message' => 'Datos de clase incompletos']);
    exit;
}

try {
    $con->begin_transaction();

    // 1) Insertar la clase
    $stmt = $con->prepare("
        INSERT INTO clases (ciclo_id, grado, grupo)
        VALUES (?, ?, ?)
    ");
    $stmt->bind_param(
        "iss",
        $data['ciclo_id'],
        $data['grado'],
        $data['grupo']
    );
    $stmt->execute();
    $clase_id = $stmt->insert_id;
    $stmt->close();

    // 2) Insertar asignaciones
    $assigns = $data['asignaciones'] ?? [];
    // compatibilidad: un par materia/docente directo
    if (empty($assigns) && isset($data['materia_id'], $data['docente_id'])) {
        $assigns = [[
            'materia_id' => $data['materia_id'],
            'docente_id' => $data['docente_id']
        ]];
    }
    $stmtA = $con->prepare("
        INSERT INTO clase_asignacion (clase_id, materia_id, docente_id)
        VALUES (?, ?, ?)
    ");
    foreach ($assigns as $a) {
        $stmtA->bind_param(
            "iii",
            $clase_id,
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
