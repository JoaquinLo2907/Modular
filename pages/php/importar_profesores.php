<?php
require '../php/conecta.php';
$conexion = conecta();
if (!$conexion) {
    die("Error de conexión: " . mysqli_connect_error());
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_FILES['archivo_csv'])) {
    header("Location: ../professors/allprofessor.html?import=proferror");
    exit;
}

$archivo  = $_FILES['archivo_csv']['tmp_name'];
$inserted = 0;

if (($handle = fopen($archivo, 'r')) === FALSE) {
    header("Location: ../professors/allprofessor.html?import=proferror");
    exit;
}

// Omitir encabezado
fgetcsv($handle, 1000, ',');

while (($datos = fgetcsv($handle, 1000, ',')) !== FALSE) {
    if (count($datos) < 10) {
        error_log("SKIP: columnas insuficientes (" . count($datos) . ")");
        continue;
    }
    $datos = array_slice($datos, 0, 10);

    // Mapear y limpiar
    list(
        $nombre,
        $apellido,
        $correo,
        $telefono,
        $puestoRaw,
        $genero,
        $rawDate,
        $salarioRaw,
        $direccion,
        $foto_url
    ) = array_map('trim', $datos);

    // Normalize puesto into a variable
    $puesto = (strtolower($puestoRaw) === 'coordinador') ? 'Coordinador' : 'Profesor';

    // Formatear fecha
    $dt = DateTime::createFromFormat('d/m/Y', $rawDate)
       ?: DateTime::createFromFormat('Y-m-d', $rawDate);
    if (!$dt) {
        error_log("SKIP: fecha inválida '{$rawDate}'");
        continue;
    }
    $fecha_nacimiento = $dt->format('Y-m-d');

    // Salario
    $salario = floatval(str_replace(',', '.', $salarioRaw));

    // Prepara usuario
    $contraseñaHash = password_hash($nombre . '123', PASSWORD_BCRYPT);
    $rol = 1;

    // 1) Verificar existencia de usuario
    $usuario_id = null;
    $stmt = $conexion->prepare("SELECT usuario_id FROM usuarios WHERE correo = ?");
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $stmt->bind_result($usuario_id);
    $stmt->fetch();
    $stmt->close();

    if ($usuario_id) {
        error_log("SKIP: usuario ya existe => {$correo}");
        continue;
    }

    // 2) Insertar en usuarios
    $insU = $conexion->prepare("
        INSERT INTO usuarios
          (nombre_usuario, contraseña, correo, rol, creado_en, actualizado_en)
        VALUES (?, ?, ?, ?, NOW(), NOW())
    ");
    $insU->bind_param("sssi", $nombre, $contraseñaHash, $correo, $rol);
    if (!$insU->execute()) {
        error_log("ERROR INSERT USUARIO: " . $insU->error);
        $insU->close();
        continue;
    }
    $usuario_id = $insU->insert_id;
    $insU->close();

    // 3) Insertar en docentes
    $insD = $conexion->prepare("
        INSERT INTO docentes
          (usuario_id, nombre, apellido, telefono, correo, rol,
           puesto, genero, fecha_nacimiento, salario, direccion, foto_url, creado_en, actualizado_en)
        VALUES (?,?,?,?,?,?,?,?,?,?,?,? ,NOW(),NOW())
    ");
    // Todas deben ser variables
    $uId   = $usuario_id;
    $nom   = $nombre;
    $ape   = $apellido;
    $tel   = $telefono;
    $mail  = $correo;
    $r     = $rol;
    $p     = $puesto;
    $g     = $genero;
    $fn    = $fecha_nacimiento;
    $sal   = $salario;
    $dir   = $direccion;
    $foto  = $foto_url;

    $insD->bind_param(
        "issssisssdss",
        $uId,
        $nom,
        $ape,
        $tel,
        $mail,
        $r,
        $p,
        $g,
        $fn,
        $sal,
        $dir,
        $foto
    );
    if ($insD->execute()) {
        $inserted++;
    } else {
        error_log("ERROR INSERT DOCENTES: " . $insD->error);
    }
    $insD->close();
}

fclose($handle);

// Redirigir con resultado
if ($inserted > 0) {
    header("Location: ../professors/allprofessor.html?import=profok&count={$inserted}");
} else {
    header("Location: ../professors/allprofessor.html?import=profnone");
}
exit;
