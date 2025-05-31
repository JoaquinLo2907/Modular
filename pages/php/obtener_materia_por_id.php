<?php
include 'conecta.php';
$con = conecta();

if (isset($_GET['materia_id'])) {
    $materia_id = $_GET['materia_id'];

    // Consulta para obtener la materia por ID
    $sql = "SELECT materia_id, nombre, nivel_grado FROM materias WHERE materia_id = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("i", $materia_id);
    $stmt->execute();

    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $materia = $result->fetch_assoc();
        echo json_encode($materia);
    } else {
        echo json_encode([]);
    }

    $stmt->close();
    $con->close();
}
?>
