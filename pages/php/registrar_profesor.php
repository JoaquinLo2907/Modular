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
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $correo = $_POST['email'];
    $password = password_hash($_POST['contraseña'], PASSWORD_BCRYPT); // Encriptar la contraseña
    $rol = $_POST['rol'];

    // Manejo de imagen
    $directorio = "../../assets/img/uploads/"; // Carpeta donde se guardarán las imágenes
    $nombreImagen = basename($_FILES["imagen"]["name"]);
    $rutaImagen = $directorio . $nombreImagen;
    $rutaBD = "assets/img/uploads/" . $nombreImagen; // Ruta relativa para la BD

    // Validar que se haya subido correctamente el archivo
    if ($_FILES["imagen"]["error"] !== UPLOAD_ERR_OK) {
        echo "<script>
                alert('Error al subir la imagen. Código de error: {$_FILES['imagen']['error']}');
                window.location.href = 'default-register.html';
              </script>";
        exit;
    }

    // Mover la imagen a la carpeta
    if (!move_uploaded_file($_FILES["imagen"]["tmp_name"], $rutaImagen)) {
        echo "<script>
                alert('Error al guardar la imagen en el servidor.');
                window.location.href = 'default-register.html';
              </script>";
        exit;
    }

    if (file_exists($rutaImagen)) {
        // Construir la URL completa de la imagen en el servidor
        $urlImagen = "http://localhost/proyecto/assets/img/uploads/" . $nombreImagen;
    
        // Usar un script para mostrar el mensaje con el enlace
        echo "<script>
                alert('Imagen subida correctamente. Verifica aquí: <a href=\"$urlImagen\" target=\"_blank\">$nombreImagen</a>');
              </script>";
    } else {
        echo "<script>
                alert('Error al subir la imagen.');
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

        // Insertar en la tabla `docentes` si el rol es 1 (Docente)
        if ($rol == 1) {
            $direccion = $_POST['direccion'];
            $telefono = $_POST['telefono'];
            $puesto = $_POST['puesto'];
            $genero = $_POST['genero'];
            $nacimiento = $_POST['nacimiento'];
            $salario = $_POST['salario'];

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

    // Cerrar las conexiones
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
