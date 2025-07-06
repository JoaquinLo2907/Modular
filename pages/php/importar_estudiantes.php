<?php
require 'conecta.php';
$conexion = conecta();
if (!$conexion) {
    die("Error de conexión: " . mysqli_connect_error());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['archivo_csv'])) {
    $archivo = $_FILES['archivo_csv']['tmp_name'];

    if (($handle = fopen($archivo, 'r')) !== FALSE) {
        // Leer y descartar encabezado
        $encabezado = fgetcsv($handle, 1000, ',');

        // Procesar cada fila
        while (($datos = fgetcsv($handle, 1000, ',')) !== FALSE) {
            // Si no tenemos al menos 7 columnas, saltamos esta fila
            if (count($datos) < 7) {
                continue;
            }

            // Asignar columnas con trim() para quitar espacios
            $nombre   = trim($datos[0]);
            $apellido = trim($datos[1]);
            $fnac     = trim($datos[2]);
            $grado    = trim($datos[3]);
            $grupo    = trim($datos[4]);
            $tutor_id = intval($datos[5]);
            $activo   = intval($datos[6]);

            // Preparar e insertar
            $stmt = $conexion->prepare("
                INSERT INTO estudiantes
                  (nombre, apellido, fecha_nacimiento, grado, grupo, tutor_id, activo)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->bind_param(
                'sssissi',
                $nombre,
                $apellido,
                $fnac,
                $grado,
                $grupo,
                $tutor_id,
                $activo
            );
            $stmt->execute();
            $stmt->close();
        }

        fclose($handle);

        // Redirigir SIN haber impreso nada antes
        header("Location: ../student/studenttable.html?import=ok");
        exit;
    } else {
        die("No se pudo abrir el archivo.");
    }
} else {
    die("Acceso inválido.");
}
?>
