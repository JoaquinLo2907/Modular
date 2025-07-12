<?php
include 'conecta.php';
$con = conecta();
$stmt = $con->prepare(
  "INSERT INTO periodos (nombre, fecha_inicio, fecha_fin)
   VALUES (?, ?, ?)"
);
$stmt->bind_param(
  "sss",
  $_POST['nombre'],
  $_POST['fecha_inicio'],
  $_POST['fecha_fin']
);
$stmt->execute();
echo json_encode(['success'=>$stmt->affected_rows>0]);
