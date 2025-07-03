<?php
// Incluir la conexión a la base de datos
require '../php/conecta.php';
$conexion = conecta();
if (!$conexion) {
    die("Error: No se pudo conectar a la base de datos.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1) Capturamos el raw de ambas contraseñas
    $passwordRaw       = $_POST['password'];
    $confirmPassword   = $_POST['confirmPassword'];

    // 2) Validamos que coincidan
    if ($passwordRaw !== $confirmPassword) {
        echo "<script>
                alert('Las contraseñas no coinciden. Por favor inténtalo de nuevo.');
                window.location.href = '../prebuilt-pages/default-register.html';
              </script>";
        exit;
    }

    // 3) Si coinciden, continuamos con el resto
    $nombre    = $_POST['nombre'];
    $apellido  = $_POST['apellido'];
    $correo    = $_POST['email'];
    // Encriptamos después de validar
    $password  = password_hash($passwordRaw, PASSWORD_BCRYPT);
    $rol       = $_POST['rol']; // 2 = Tutor

    // Verificar si el correo ya existe
    $sql_verificar = "SELECT 1 FROM usuarios WHERE correo = ?";
    $stmt_verificar = $conexion->prepare($sql_verificar);
    $stmt_verificar->bind_param('s', $correo);
    $stmt_verificar->execute();
    $stmt_verificar->store_result();
    if ($stmt_verificar->num_rows > 0) {
        echo "<script>
                alert('El correo ya está registrado. Por favor, use otro correo.');
                window.location.href = 'default-register.html';
              </script>";
        exit;
    }

    // Insertar en `usuarios`
    $sql_usuario = "INSERT INTO usuarios (nombre_usuario, contraseña, correo, rol) VALUES (?, ?, ?, ?)";
    $stmt_usuario = $conexion->prepare($sql_usuario);
    $stmt_usuario->bind_param('sssi', $nombre, $password, $correo, $rol);

    if ($stmt_usuario->execute()) {
        $usuario_id = $stmt_usuario->insert_id;

        if ($rol == 2) {
            $telefono  = $_POST['telefono'];
            $direccion = $_POST['direccion'];
            $sql_tutor = "
              INSERT INTO tutores 
                (usuario_id, nombre, apellido, telefono, correo, direccion, rol)
              VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt_tutor = $conexion->prepare($sql_tutor);
            $stmt_tutor->bind_param('isssssi',
                $usuario_id, $nombre, $apellido, $telefono, $correo, $direccion, $rol
            );
            $stmt_tutor->execute();
            $stmt_tutor->close();
        } else {
            echo "<script>
                    alert('Rol no válido.');
                    window.location.href = 'default-register.html';
                  </script>";
            exit;
        }

        // Redirigir al login
        header('Location: ../prebuilt-pages/default-login.html');
        exit;
    } else {
        echo "<script>
                alert('Error al registrar el usuario.');
                window.location.href = 'default-register.html';
              </script>";
    }

    $stmt_usuario->close();
    $conexion->close();
} else {
    echo "<script>
            alert('Acceso no autorizado.');
            window.location.href = 'default-login.html';
          </script>";
}
?>
