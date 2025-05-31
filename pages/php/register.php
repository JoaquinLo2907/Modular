<?php
// Incluir la conexión a la base de datos
require '../php/conecta.php';

// Establecer la conexión llamando a la función conecta()
$conexion = conecta();

// Verificar si la conexión se estableció correctamente
if (!$conexion) {
    die("Error: No se pudo establecer la conexión a la base de datos.");
}

// Verificar si el formulario fue enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener los datos del formulario
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $correo = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Encriptar la contraseña
    $rol = $_POST['rol']; // El rol será 2 (Tutor) por defecto

    // Verificar si el correo ya existe
    $sql_verificar = "SELECT * FROM usuarios WHERE correo = ?";
    $stmt_verificar = $conexion->prepare($sql_verificar);
    if (!$stmt_verificar) {
        die("Error al preparar la consulta: " . $conexion->error);
    }
    $stmt_verificar->bind_param('s', $correo);
    $stmt_verificar->execute();
    $resultado = $stmt_verificar->get_result();

    if ($resultado->num_rows > 0) {
        // Si el correo ya existe, mostrar alerta y redirigir
        echo "<script>
                alert('El correo ya está registrado. Por favor, use otro correo.');
                window.location.href = 'default-register.html';
              </script>";
        exit;
    }

    // Insertar en la tabla `usuarios`
    $sql_usuario = "INSERT INTO usuarios (nombre_usuario, contraseña, correo, rol) VALUES (?, ?, ?, ?)";
    $stmt_usuario = $conexion->prepare($sql_usuario);
    if (!$stmt_usuario) {
        die("Error al preparar la consulta: " . $conexion->error);
    }
    $stmt_usuario->bind_param('sssi', $nombre, $password, $correo, $rol);

    if ($stmt_usuario->execute()) {
        $usuario_id = $stmt_usuario->insert_id; // Obtener el ID del usuario insertado

        // Solo si el rol es 2 (Tutor), insertar en la tabla `tutores`
        if ($rol == 2) {
            $telefono = $_POST['telefono'];
            $direccion = $_POST['direccion'];

            $sql_tutor = "INSERT INTO tutores (usuario_id, nombre, apellido, telefono, correo, direccion, rol) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt_tutor = $conexion->prepare($sql_tutor);
            if (!$stmt_tutor) {
                die("Error al preparar la consulta: " . $conexion->error);
            }
            $stmt_tutor->bind_param('isssssi', $usuario_id, $nombre, $apellido, $telefono, $correo, $direccion, $rol);
            $stmt_tutor->execute();
            $stmt_tutor->close();
        } else {
            // Si el rol no es 2, mostrar un error
            echo "<script>
                    alert('Rol no válido.');
                    window.location.href = 'default-register.html';
                  </script>";
            exit;
        }

        // Redireccionar al login principal
        header('Location: ../prebuilt-pages/default-login.html');
        exit;
    } else {
        echo "<script>
                alert('Error al registrar el usuario.');
                window.location.href = 'default-register.html';
              </script>";
    }

    // Cerrar las conexiones
    $stmt_usuario->close();
    $conexion->close();
} else {
    echo "<script>
            alert('Acceso no autorizado.');
            window.location.href = 'default-login.html';
          </script>";
}
?>