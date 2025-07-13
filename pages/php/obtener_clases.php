<?php
// File: obtener_clases.php
header('Content-Type: application/json');
require 'conecta.php';
$con = conecta();

$sql = "
  SELECT
    c.clase_id,
    ce.nombre    AS ciclo,
    m.nombre     AS materia,
    CONCAT(d.nombre,' ',d.apellido) AS docente,
    c.grado,
    c.grupo
  FROM clases c
  JOIN ciclos_escolares ce
    ON c.ciclo_id = ce.ciclo_id
  JOIN clase_asignacion ca
    ON c.clase_id = ca.clase_id
  JOIN materias m
    ON ca.materia_id = m.materia_id
  JOIN docentes d
    ON ca.docente_id = d.docente_id
";

$res  = $con->query($sql);
$data = [];
while ($row = $res->fetch_assoc()) {
    $data[] = $row;
}
echo json_encode($data);

