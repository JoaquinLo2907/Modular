<?php
require 'conecta.php';
$con = conecta();

$sql = "SELECT 
            p.pago_id, 
            CONCAT(e.nombre, ' ', e.apellido) AS estudiante_nombre,
            p.monto, 
            p.fecha_pago, 
            p.fecha_vencimiento, 
            p.estado, 
            p.creado_en, 
            p.actualizado_en
        FROM pagos p
        JOIN estudiantes e ON p.estudiante_id = e.estudiante_id
        WHERE p.estado = 1"; // solo pagos activos

$result = $con->query($sql);
$pagos = [];

while ($row = $result->fetch_assoc()) {
    $pagos[] = $row;
}

echo json_encode($pagos);
?>
