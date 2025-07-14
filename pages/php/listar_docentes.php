<?php
require '../php/conecta.php';
$con = conecta();

// Traer sólo los docentes con activo = 1
$res = $con->query("SELECT docente_id, nombre, apellido FROM docentes WHERE activo = 1");  // :contentReference[oaicite:1]{index=1}

$docentes = [];
while ($r = $res->fetch_assoc()) {
  $docentes[] = $r;
}

header('Content-Type: application/json');
echo json_encode($docentes);