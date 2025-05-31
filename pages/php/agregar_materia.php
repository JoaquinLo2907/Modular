<?php
include '../php/conecta.php';
$con = conecta();

$nombre = $_POST['nombre'];
$nivel = $_POST['nivel_grado'];
$descripcion = $_POST['descripcion'];
$fotoUrl = '';

if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
    $directorio = "../../assets/img/uploads/";
    $nombreFoto = basename($_FILES["foto"]["name"]);
    $rutaFoto = $directorio . $nombreFoto;
    $fotoUrl = "assets/img/uploads/" . $nombreFoto;

    move_uploaded_file($_FILES["foto"]["tmp_name"], $rutaFoto);
}

$sql = "INSERT INTO materias (nombre, nivel_grado, descripcion, foto_url, creado_en, actualizado_en) 
        VALUES ('$nombre', '$nivel', '$descripcion', '$fotoUrl', NOW(), NOW())";

if ($con->query($sql)) {
    echo "Materia agregada correctamente";
} else {
    echo "Error: " . $con->error;
}

$con->close();
?>
