<?php
require 'conecta.php';
$conexion = conecta();
if (!$conexion) {
    die("Error de conexión: " . mysqli_connect_error());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['archivo_csv'])) {
    $archivo  = $_FILES['archivo_csv']['tmp_name'];
    $inserted = 0;  // contador de inserciones realizadas

    if (($handle = fopen($archivo, 'r')) !== FALSE) {
        // Leer y descartar encabezado
        fgetcsv($handle, 1000, ',');

        while (($datos = fgetcsv($handle, 1000, ',')) !== FALSE) {
            if (count($datos) < 7) {
                continue;
            }

            $nombre   = trim($datos[0]);
            $apellido = trim($datos[1]);

            // — Conversión de fecha —
            $rawDate = trim($datos[2]);
            $dt = DateTime::createFromFormat('d/m/Y', $rawDate);
            if (!$dt) {
                $dt = DateTime::createFromFormat('Y-m-d', $rawDate);
            }
            if (!$dt) {
                error_log("SKIP: Fecha inválida raw='{$rawDate}'");
                continue;
            }
            $fnac = $dt->format('Y-m-d');
            // ————————————————

            $grado    = trim($datos[3]);
            $grupo    = trim($datos[4]);
            $tutor_id = intval(preg_replace('/[^\d]/', '', $datos[5]));
            $activo   = intval(trim($datos[6]));

            // Comprueba existencia del tutor
            $chk = $conexion->prepare("SELECT 1 FROM tutores WHERE tutor_id = ?");
            $chk->bind_param("i", $tutor_id);
            $chk->execute();
            $chk->store_result();
            if ($chk->num_rows === 0) {
                error_log("SKIP: tutor_id {$tutor_id} no existe");
                $chk->close();
                continue;
            }
            $chk->close();

            // Inserta la fila con fecha correcta
            $stmt = $conexion->prepare("
                INSERT INTO estudiantes
                  (nombre, apellido, fecha_nacimiento, grado, grupo, tutor_id, activo, creado_en, actualizado_en)
                VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
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
            if ($stmt->execute()) {
                $inserted++;
            } else {
                error_log("ERROR INSERT: " . $stmt->error);
            }
            $stmt->close();
        }

        fclose($handle);

        // Redirigir incluyendo el conteo
        if ($inserted > 0) {
            header("Location: ../student/studenttable.html?import=ok&count={$inserted}");
        } else {
            header("Location: ../student/studenttable.html?import=none");
        }
        exit;
    } else {
        header("Location: ../student/studenttable.html?import=error");
        exit;
    }
} else {
    header("Location: ../student/studenttable.html?import=error");
    exit;
}
