<?php
// File: guardar_inscripciones.php

require 'conecta.php';
header('Content-Type: application/json');

$con = conecta();
if (!$con) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error al conectar a la base de datos.']);
    exit;
}

// Leer el body como JSON
$input = json_decode(file_get_contents('php://input'), true);
$clase_id    = isset($input['clase_id']) ? intval($input['clase_id']) : 0;
$estudiantes = isset($input['estudiantes']) && is_array($input['estudiantes'])
              ? array_map('intval', $input['estudiantes'])
              : [];

// Validaciones básicas
if ($clase_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'clase_id inválido.']);
    exit;
}

$con->begin_transaction();

try {
    // 1) Eliminar todas las inscripciones de esta clase
    $del = $con->prepare("DELETE FROM inscripciones WHERE clase_id = ?");
    $del->bind_param('i', $clase_id);
    $del->execute();
    $del->close();

    // 2) Insertar las nuevas inscripciones
    if (count($estudiantes) > 0) {
        $ins = $con->prepare("
            INSERT INTO inscripciones (estudiante_id, clase_id)
            VALUES (?, ?)
        ");
        foreach ($estudiantes as $est_id) {
            $ins->bind_param('ii', $est_id, $clase_id);
            $ins->execute();
        }
        $ins->close();
    }

    $con->commit();
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    $con->rollback();
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error al guardar inscripciones: ' . $e->getMessage()
    ]);
}
