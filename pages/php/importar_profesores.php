<?php
require '../php/conecta.php';

$conexion = conecta();

if (!$conexion) {
    die("Error de conexión: " . mysqli_connect_error());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['archivo_csv'])) {
    $archivo = $_FILES['archivo_csv']['tmp_name'];

    if (($handle = fopen($archivo, "r")) !== FALSE) {
        $encabezado = fgetcsv($handle, 1000, ","); // Ignora encabezados

        while (($datos = fgetcsv($handle, 1000, ",")) !== FALSE) {
            // Orden del CSV:
            // nombre, apellido, correo, telefono, puesto, genero, fecha_nacimiento, salario, direccion, foto_url

            $nombre           = $datos[0];
            $apellido         = $datos[1];
            $correo           = $datos[2];
            $telefono         = $datos[3];

            // Validar puesto (solo Profesor o Coordinador)
            $puesto = strtolower(trim($datos[4])) === 'coordinador' ? 'Coordinador' : 'Profesor';

            $genero           = $datos[5];
            $fecha_nacimiento = $datos[6];
            $salario          = floatval($datos[7]);
            $direccion        = $datos[8];
            $foto_url         = $datos[9];

            // Contraseña: nombre + "123"
            $contraseñaPlano = $nombre . "123";
            $contraseñaHash = password_hash($contraseñaPlano, PASSWORD_BCRYPT);

            $rol = 1; // Siempre rol de profesor

            // Verificar si ya existe el correo
            $sql_verifica = "SELECT * FROM usuarios WHERE correo = ?";
            $stmt_verifica = $conexion->prepare($sql_verifica);
            $stmt_verifica->bind_param("s", $correo);
            $stmt_verifica->execute();
            $resultado = $stmt_verifica->get_result();

            if ($resultado->num_rows > 0) {
                $stmt_verifica->close();
                continue;
            }
            $stmt_verifica->close();

            // Insertar en usuarios
            $sql_usuario = "INSERT INTO usuarios (nombre_usuario, contraseña, correo, rol) VALUES (?, ?, ?, ?)";
            $stmt_usuario = $conexion->prepare($sql_usuario);
            $stmt_usuario->bind_param("sssi", $nombre, $contraseñaHash, $correo, $rol);

            if ($stmt_usuario->execute()) {
                $usuario_id = $stmt_usuario->insert_id;

                // Insertar en docentes
                $sql_docente = "INSERT INTO docentes (usuario_id, nombre, apellido, telefono, correo, rol, puesto, genero, fecha_nacimiento, salario, direccion, foto_url)
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt_docente = $conexion->prepare($sql_docente);
                $stmt_docente->bind_param(
                    "issssisssdss",
                    $usuario_id,
                    $nombre,
                    $apellido,
                    $telefono,
                    $correo,
                    $rol,
                    $puesto,
                    $genero,
                    $fecha_nacimiento,
                    $salario,
                    $direccion,
                    $foto_url
                );
                $stmt_docente->execute();
                $stmt_docente->close();
            }

            $stmt_usuario->close();
        }

        fclose($handle);

        echo "<script>
                alert('Importación completada correctamente.');
                window.location.href = '../professors/allprofessor.html';
              </script>";
    } else {
        echo "<script>
                alert('No se pudo abrir el archivo.');
                window.history.back();
              </script>";
    }
} else {
    echo "<script>
            alert('Acceso inválido.');
            window.history.back();
          </script>";
}
?>
