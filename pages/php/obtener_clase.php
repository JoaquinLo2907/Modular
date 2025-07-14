<?php
// File: obtener_clases.php

require 'conecta.php';
header('Content-Type: application/json');

$con = conecta();
if (!$con) {
    echo json_encode([]);
    exit;
}

// Ajusta el JOIN y el filtro de estados según tu esquema
$sql = "
  SELECT 
    cl.clase_id,
    ce.nombre   AS ciclo,
    cl.grado,
    cl.grupo
  FROM clases cl
  JOIN ciclos_escolares ce ON ce.ciclo_id = cl.ciclo_id
  WHERE ce.estado = 'activo'
  ORDER BY ce.fecha_inicio DESC, cl.grado, cl.grupo
";

$res = $con->query($sql);
$clases = [];
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $clases[] = $row;
    }
}

echo json_encode($clases);
