<?php
header('Content-Type: application/json');
require 'conecta.php';
$con = conecta();

// 1) Leer y validar JSON
$data = json_decode(file_get_contents('php://input'), true);
if (empty($data)) {
    echo json_encode(['success' => false, 'message' => 'Datos inválidos']);
    exit;
}

// 2) Validar nombre del ciclo
if (empty(trim($data['nombre'] ?? ''))) {
    echo json_encode(['success' => false, 'message' => 'El nombre del ciclo es obligatorio']);
    exit;
}

// 3) Validar rango de fechas del ciclo
$iniC = strtotime($data['fecha_inicio'] ?? '');
$finC = strtotime($data['fecha_fin'] ?? '');
if ($iniC === false || $finC === false || $iniC >= $finC) {
    echo json_encode(['success' => false, 'message' => 'El rango de fechas del ciclo es inválido']);
    exit;
}

// 4) Validar periodos
$periodosRaw = $data['periodos'] ?? [];
$periodos = [];
foreach ($periodosRaw as $idx => $p) {
    $nom = trim($p['nombre'] ?? '');
    $ini = strtotime($p['fecha_inicio'] ?? '');
    $fin = strtotime($p['fecha_fin'] ?? '');

    if ($nom === '' || $ini === false || $fin === false) {
        echo json_encode(['success' => false, 'message' => "Periodo #".($idx+1)." con datos incompletos"]);
        exit;
    }
    if ($ini >= $fin) {
        echo json_encode(['success' => false, 'message' => "Periodo “{$nom}” con rango inválido"]);
        exit;
    }
    if ($ini < $iniC || $fin > $finC) {
        echo json_encode(['success' => false, 'message' => "Periodo “{$nom}” sale del rango del ciclo"]);
        exit;
    }
    $periodos[] = ['ini' => $ini, 'fin' => $fin];
}

// 5) Verificar solapamientos
usort($periodos, fn($a, $b) => $a['ini'] <=> $b['ini']);
for ($i = 1; $i < count($periodos); $i++) {
    if ($periodos[$i]['ini'] < $periodos[$i-1]['fin']) {
        echo json_encode(['success' => false, 'message' => 'Hay periodos que se solapan']);
        exit;
    }
}

// 6) Insertar todo en transacción
try {
    $con->begin_transaction();

    // 6.1) Insertar ciclo
    $stmt = $con->prepare("
        INSERT INTO ciclos_escolares 
            (nombre, fecha_inicio, fecha_fin, estado, observaciones)
        VALUES (?, ?, ?, ?, ?)
    ");
    $stmt->bind_param(
        "sssss",
        $data['nombre'],
        $data['fecha_inicio'],
        $data['fecha_fin'],
        $data['estado'],
        $data['observaciones']
    );
    $stmt->execute();
    $ciclo_id = $con->insert_id;
    $stmt->close();

    // 6.2) Insertar periodos dinámicos
    if (!empty($periodosRaw)) {
        $stmtP = $con->prepare("
            INSERT INTO periodos (nombre, fecha_inicio, fecha_fin, ciclo_id)
            VALUES (?, ?, ?, ?)
        ");
        foreach ($periodosRaw as $p) {
            $stmtP->bind_param(
                "sssi",
                $p['nombre'],
                $p['fecha_inicio'],
                $p['fecha_fin'],
                $ciclo_id
            );
            $stmtP->execute();
        }
        $stmtP->close();
    }

    $con->commit();
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    $con->rollback();
    echo json_encode(['success' => false, 'message' => 'Error interno: '.$e->getMessage()]);
}