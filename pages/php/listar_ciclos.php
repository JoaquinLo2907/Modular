<?php
require '../php/conecta.php';
$con = conecta();

// Traer sólo los ciclos activos
$res = $con->query("SELECT ciclo_id, nombre FROM ciclos_escolares WHERE estado='activo'");  // :contentReference[oaicite:0]{index=0}

$ciclos = [];
while ($r = $res->fetch_assoc()) {
  $ciclos[] = $r;
}

header('Content-Type: application/json');
echo json_encode($ciclos);