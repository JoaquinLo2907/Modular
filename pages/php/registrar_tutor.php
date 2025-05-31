<?php
// pages/php/registrar_tutor.php

// 1. Conexión
require '../php/conecta.php';
$conexion = conecta();
if (!$conexion) {
    die("Error: No se pudo establecer la conexión a la base de datos.");
}

// 2. Solo POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo "<script>
            alert('Acceso no autorizado.');
            window.location.href = '../tutores/addtutor.html';
          </script>";
    exit;
}

// 3. Obtener datos del formulario
$nombre       = trim($_POST['nombre']       ?? '');
$apellido     = trim($_POST['apellido']     ?? '');
$telefono     = trim($_POST['telefono']     ?? '');
$correo       = trim($_POST['correo']       ?? '');
$direccion    = trim($_POST['direccion']    ?? '');
$estudianteId = intval($_POST['estudiante_id'] ?? 0);
$password1    = $_POST['password']  ?? '';
$password2    = $_POST['password2'] ?? '';
$rol          = 2;                // 2 = Tutor

// 4. Validaciones básicas
if ($nombre === '' || $apellido === '' || $correo === '' ||
    $telefono === '' || $direccion === '' || $password1 === '' || $password2 === '') {
    echo "<script>
            alert('Todos los campos son obligatorios.');
            window.location.href = '../tutor/addtutor.html';
          </script>";
    exit;
}

if ($password1 !== $password2) {
    echo "<script>
            alert('Las contraseñas no coinciden.');
            window.location.href = '../tutor/addtutor.html';
          </script>";
    exit;
}

// 5. Verificar duplicado de correo
$sqlVer = "SELECT 1 FROM usuarios WHERE correo = ?";
$stmtVer = $conexion->prepare($sqlVer);
$stmtVer->bind_param('s', $correo);
$stmtVer->execute();
$resVer = $stmtVer->get_result();

if ($resVer->num_rows > 0) {
    echo "<script>
            alert('El correo ya está registrado. Por favor usa otro.');
            window.location.href = '../tutor/addtutor.html';
          </script>";
    exit;
}

// 6. Insertar en USUARIOS
$passwordHash = password_hash($password1, PASSWORD_BCRYPT);
$sqlUsr = "INSERT INTO usuarios (nombre_usuario, contraseña, correo, rol)
           VALUES (?, ?, ?, ?)";
$stmtUsr = $conexion->prepare($sqlUsr);
$stmtUsr->bind_param('sssi', $correo, $passwordHash, $correo, $rol);

if (!$stmtUsr->execute()) {
    echo "<script>
            alert('Error al registrar usuario.');
            window.location.href = '../tutor/addtutor.html';
          </script>";
    exit;
}
$usuarioId = $stmtUsr->insert_id;   // FK para tutores

// 7. Insertar en TUTORES
$sqlTut = "INSERT INTO tutores
              (usuario_id, nombre, apellido, telefono, correo, direccion, activo, rol)
           VALUES (?, ?, ?, ?, ?, ?, 1, ?)";
$stmtTut = $conexion->prepare($sqlTut);
$stmtTut->bind_param('isssssi',
    $usuarioId,
    $nombre,
    $apellido,
    $telefono,
    $correo,
    $direccion,
    $rol
);

if (!$stmtTut->execute()) {
    echo "<script>
            alert('Error al registrar tutor.');
            window.location.href = '../tutor/addtutor.html';
          </script>";
    exit;
}
$tutorId = $stmtTut->insert_id;

/* 8. (Opcional) Vincular tutor ↔ estudiante
   ────────────────────────────────────────── */
if ($estudianteId > 0) {
    /* 8.a  Escribir el tutor en la tabla estudiantes */
    $sqlUpd = "UPDATE estudiantes
                  SET tutor_id = ?
                WHERE estudiante_id = ?";
    $stmtUpd = $conexion->prepare($sqlUpd);
    if ($stmtUpd) {
        $stmtUpd->bind_param('ii', $tutorId, $estudianteId);
        $stmtUpd->execute();
        $stmtUpd->close();
    }
}


// 9. Éxito
echo "<script>
        alert('Tutor registrado con éxito.');
        window.location.href = '../tutor/addtutor.html';
      </script>";
exit;

/* 10. Cierre */
$stmtUsr->close();
$stmtTut->close();
$stmtVer->close();
$conexion->close();
?>
