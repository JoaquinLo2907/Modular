<?php
include '../php/conecta.php';
$con = conecta();

// Verificar si el parámetro 'id' está presente en la URL
if (isset($_GET['id'])) {
    $docente_id = $_GET['id'];

    // Preparar la consulta para obtener solo los datos del docente con ese ID
    $sql = "SELECT docente_id, nombre, apellido, telefono, correo, activo, puesto, genero, fecha_nacimiento, salario, direccion, creado_en, actualizado_en FROM docentes WHERE docente_id = ?";

    // Preparar la declaración
    if ($stmt = $con->prepare($sql)) {
        // Vincular el parámetro
        $stmt->bind_param("i", $docente_id);
        $stmt->execute();

        // Obtener los resultados
        $result = $stmt->get_result();

        // Verificar si se encontró el docente
        if ($result->num_rows > 0) {
            // Obtener los datos del docente
            $docente = $result->fetch_assoc();
            header('Content-Type: application/json');
            echo json_encode($docente); // Devolver los datos en formato JSON
        } else {
            echo json_encode(["error" => "No se encontró el docente con el ID proporcionado."]);
        }

        // Cerrar la declaración
        $stmt->close();
    } else {
        echo json_encode(["error" => "Error al preparar la consulta."]);
    }
} else {
    echo json_encode(["error" => "El parámetro 'id' es obligatorio."]);
}

// Cerrar la conexión
$con->close();
?>
