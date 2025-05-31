<?php
header('Content-Type: text/html; charset=UTF-8');

require_once __DIR__ . '/conecta.php';
$con = conecta();

$sql = "SELECT estudiante_id,
               nombre,
               apellido,
               grado,
               grupo
        FROM   estudiantes
        ORDER  BY nombre, apellido";

$res = $con->query($sql);

if (!$res) {
    echo '<option disabled>Error de consulta</option>';
    exit;
}
if ($res->num_rows === 0) {
    echo '<option disabled>No hay estudiantes</option>';
    exit;
}

while ($e = $res->fetch_assoc()) {
    echo '<option value="' . $e['estudiante_id'] . '" ' .
               'data-grado="' . $e['grado']  . '" ' .
               'data-grupo="' . $e['grupo'] . '">';

    echo 'ID: '   . $e['estudiante_id'] .
         ' - '    . $e['nombre'] . ' ' . $e['apellido'] .
         ' - Grado: ' . $e['grado'] .
         ' | Grupo: '   . $e['grupo'];

    echo '</option>';
}
?>
