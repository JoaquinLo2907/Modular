<?php
include '../php/conecta.php';
$con = conecta();

// Desactivar la visualización de errores en producción
ini_set('display_errors', 0);  // Desactivar errores para evitar salidas no deseadas
error_reporting(E_ALL);        // Asegurarse de reportar todos los errores

// Limpiar cualquier salida anterior antes de devolver la respuesta
ob_clean();

header('Content-Type: application/json');

// Depuración: guardar los datos recibidos en un archivo (comentado temporalmente)
#file_put_contents('debug_log.txt', print_r($_POST, true)); // Guardar en un archivo

echo json_encode(["debug" => $_POST]);  // Ver los datos que se reciben

// Verificar si se enviaron los datos del formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener los datos del formulario
    $docente_id = $_POST['docente_id'];
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $email = $_POST['email'];
    $telefono = $_POST['telefono'];
    $puesto = $_POST['puesto'];
    $genero = $_POST['genero'];
    $nacimiento = $_POST['nacimiento'];
    $salario = $_POST['salario'];
    $direccion = $_POST['direccion'];
    

    // Validar que los datos no estén vacíos
    if (empty($nombre) || empty($apellido) || empty($email) || empty($telefono) || empty($puesto) || empty($genero) || empty($nacimiento) || empty($salario) || empty($direccion)) {
        echo json_encode(["error" => "Todos los campos son obligatorios."]);
        exit;
    }

    // Preparar la consulta para actualizar los datos del docente
    $sql = "UPDATE docentes SET 
                nombre = ?, 
                apellido = ?, 
                correo = ?, 
                telefono = ?, 
                puesto = ?, 
                genero = ?, 
                fecha_nacimiento = ?, 
                salario = ?, 
                direccion = ?, 
                actualizado_en = NOW() 
            WHERE docente_id = ?";

    // Depuración de la consulta SQL
    #file_put_contents('debug_log.txt', "Consulta SQL: " . $sql . "\n", FILE_APPEND);

    // Preparar la declaración
    if ($stmt = $con->prepare($sql)) {
        // Vincular los parámetros
        $stmt->bind_param("sssssssssi", $nombre, $apellido, $email, $telefono, $puesto, $genero, $nacimiento, $salario, $direccion, $docente_id);

        // Ejecutar la consulta
        if ($stmt->execute()) {
            echo json_encode(["success" => true, "message" => "Datos actualizados correctamente."]);
        } else {
            // Error al ejecutar la consulta
            echo json_encode(["error" => "Error al ejecutar la consulta: " . $stmt->error]);
        }

        // Cerrar la declaración
        $stmt->close();
    } else {
        // Error al preparar la consulta
        echo json_encode(["error" => "Error al preparar la consulta: " . $con->error]);
    }
} else {
    echo json_encode(["error" => "Método de solicitud no válido."]);
}

// Cerrar la conexión
$con->close();
?>
