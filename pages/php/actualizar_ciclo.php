<?php
header('Content-Type: application/json');
require 'conecta.php';
$con = conecta();

// Leer JSON
$data = json_decode(file_get_contents('php://input'), true);

// 1) Validar que venga el ID
if (empty($data['ciclo_id'])) {
    echo json_encode(['success' => false, 'message' => 'ID de ciclo inválido']);
    exit;
}

// 2) Validar fechas del ciclo
$iniC = strtotime($data['fecha_inicio']);
$finC = strtotime($data['fecha_fin']);
if ($iniC === false || $finC === false || $iniC >= $finC) {
    echo json_encode(['success' => false, 'message' => 'El rango de fechas del ciclo es inválido']);
    exit;
}

// 3) Validar periodos (nombre, rango, dentro del ciclo y sin solapamientos)
$periodosRaw = $data['periodos'] ?? [];
// Convertir a lista de [ini, fin, nombre]
$periodos = [];
foreach ($periodosRaw as $idx => $p) {
    $nom = trim($p['nombre'] ?? '');
    $ini = strtotime($p['fecha_inicio'] ?? '');
    $fin = strtotime($p['fecha_fin'] ?? '');

    if ($nom === '' || $ini === false || $fin === false) {
        echo json_encode(['success' => false, 'message' => "Periodo #".($idx+1)." con datos incompletos."]);
        exit;
    }
    if ($ini >= $fin) {
        echo json_encode(['success' => false, 'message' => "Periodo “{$nom}” con fechas inválidas."]);
        exit;
    }
    if ($ini < $iniC || $fin > $finC) {
        echo json_encode(['success' => false, 'message' => "Periodo “{$nom}” sale del rango del ciclo."]);
        exit;
    }
    $periodos[] = ['ini'=>$ini, 'fin'=>$fin, 'nombre'=>$nom, 'raw'=>$p];
}
// Ordenar por fecha de inicio y comprobar solapamientos
usort($periodos, function($a,$b){ return $a['ini'] <=> $b['ini']; });
for ($i = 1; $i < count($periodos); $i++) {
    if ($periodos[$i]['ini'] < $periodos[$i-1]['fin']) {
        echo json_encode(['success' => false, 'message' => 'Hay periodos que se solapan entre sí.']);
        exit;
    }
}

// 4) Si todo OK, proceder a la transacción
try {
    $con->begin_transaction();

    // 4.1) Actualizar el ciclo
    $upd = $con->prepare("
      UPDATE ciclos_escolares
      SET nombre       = ?,
          fecha_inicio = ?,
          fecha_fin    = ?,
          estado       = ?,
          observaciones= ?
      WHERE ciclo_id   = ?
    ");
    $upd->bind_param(
      "sssssi",
      $data['nombre'],
      $data['fecha_inicio'],
      $data['fecha_fin'],
      $data['estado'],
      $data['observaciones'],
      $data['ciclo_id']
    );
    $upd->execute();
    $upd->close();

    // 4.2) Insertar / actualizar periodos
    $upP = $con->prepare("
      UPDATE periodos
      SET nombre = ?, fecha_inicio = ?, fecha_fin = ?
      WHERE periodo_id = ?
    ");
    $inP = $con->prepare("
      INSERT INTO periodos (nombre, fecha_inicio, fecha_fin, ciclo_id)
      VALUES (?, ?, ?, ?)
    ");
    foreach ($periodosRaw as $p) {
        if (!empty($p['periodo_id'])) {
            // Actualizar existente
            $upP->bind_param(
              "sssi",
              $p['nombre'],
              $p['fecha_inicio'],
              $p['fecha_fin'],
              $p['periodo_id']
            );
            $upP->execute();
        } else {
            // Insertar nuevo
            $inP->bind_param(
              "sssi",
              $p['nombre'],
              $p['fecha_inicio'],
              $p['fecha_fin'],
              $data['ciclo_id']
            );
            $inP->execute();
        }
    }
    $upP->close();
    $inP->close();

    $con->commit();
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    $con->rollback();
    echo json_encode(['success' => false, 'message' => 'Error interno: '.$e->getMessage()]);
}
