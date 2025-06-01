<?php
// Incluir la conexión a la base de datos
require '../php/conecta.php';

// Establecer la conexión llamando a la función conecta()
$conexion = conecta();

// Verificar si la conexión se estableció correctamente
if (!$conexion) {
    die("Error: No se pudo establecer la conexión a la base de datos.");
}

// Verificar si el formulario fue enviado correctamente
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener los datos del formulario
    $nombre    = $_POST['nombre'];
    $apellido  = $_POST['apellido'];
    $correo    = $_POST['email'];
    $password1 = $_POST['contraseña'] ?? '';
    $password2 = $_POST['confirmar_contraseña'] ?? '';
    $rol       = $_POST['rol'];

    // Validar coincidencia de contraseñas
    if ($password1 !== $password2) {
        echo "<script>
                alert('Las contraseñas no coinciden. Intente nuevamente.');
                window.history.back();
              </script>";
        exit;
    }

    // Encriptar la contraseña
    $password = password_hash($password1, PASSWORD_BCRYPT);

    // Manejo de imagen
    $directorio = "../../assets/img/uploads/";
    $nombreImagen = basename($_FILES["imagen"]["name"]);
    $rutaImagen = $directorio . $nombreImagen;
    $rutaBD = "assets/img/uploads/" . $nombreImagen;

    if ($_FILES["imagen"]["error"] !== UPLOAD_ERR_OK) {
        echo "<script>
                alert('Error al subir la imagen. Código: {$_FILES['imagen']['error']}');
                window.location.href = 'default-register.html';
              </script>";
        exit;
    }

    if (!move_uploaded_file($_FILES["imagen"]["tmp_name"], $rutaImagen)) {
        echo "<script>
                alert('Error al guardar la imagen en el servidor.');
                window.location.href = 'default-register.html';
              </script>";
        exit;
    }

    // Confirmación visual de imagen (opcional)
    if (file_exists($rutaImagen)) {
        $urlImagen = "http://localhost/proyecto/assets/img/uploads/" . $nombreImagen;
        echo "<script>
                alert('Imagen subida correctamente: $urlImagen');
              </script>";
    }

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
        echo "<script>
                alert('El correo ya está registrado. Use otro correo.');
                window.location.href = 'default-register.html';
              </script>";
        exit;
    }

    // Insertar en la tabla usuarios
    $sql_usuario = "INSERT INTO usuarios (nombre_usuario, contraseña, correo, rol) VALUES (?, ?, ?, ?)";
    $stmt_usuario = $conexion->prepare($sql_usuario);
    if (!$stmt_usuario) {
        die("Error al preparar la consulta: " . $conexion->error);
    }
    $stmt_usuario->bind_param('sssi', $nombre, $password, $correo, $rol);

    if ($stmt_usuario->execute()) {
        $usuario_id = $stmt_usuario->insert_id;

        // Si es docente, insertar en docentes
        if ($rol == 1) {
            $direccion  = $_POST['direccion'];
            $telefono   = $_POST['telefono'];
            $puesto     = $_POST['puesto'];
            $genero     = $_POST['genero'];
            $nacimiento = $_POST['nacimiento'];
            $salario    = $_POST['salario'];

            $sql_docente = "INSERT INTO docentes (usuario_id, nombre, apellido, telefono, correo, rol, puesto, genero, fecha_nacimiento, salario, direccion, foto_url) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt_docente = $conexion->prepare($sql_docente);
            if (!$stmt_docente) {
                die("Error al preparar la consulta: " . $conexion->error);
            }
            $stmt_docente->bind_param('issssisssdss', $usuario_id, $nombre, $apellido, $telefono, $correo, $rol, $puesto, $genero, $nacimiento, $salario, $direccion, $rutaBD);
            
            if ($stmt_docente->execute()) {
                echo "<script>
                        alert('Docente registrado con éxito.');
                        window.location.href = '../professors/addprofessor.html';
                      </script>";
                exit;
            } else {
                echo "<script>
                        alert('Error al registrar el docente.');
                        window.location.href = 'default-register.html';
                      </script>";
            }
        }
    } else {
        echo "<script>
                alert('Error al registrar el usuario.');
                window.location.href = 'default-register.html';
              </script>";
    }

    // Cerrar
    $stmt_usuario->close();
    $stmt_verificar->close();
    $conexion->close();
} else {
    echo "<script>
            alert('Acceso no autorizado.');
            window.location.href = 'default-login.html';
          </script>";
}
?>
