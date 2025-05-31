<?php
include 'pages/php/auth.php';

// Asegura que el usuario sea tutor
if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 2) {
  header("Location: ../index.php");
  exit();
}

require 'pages/php/conecta.php';
$con = conecta();
$usuario_id = $_SESSION['usuario_id'];

$stmt = $con->prepare("SELECT nombre, apellido, grado, grupo FROM estudiantes WHERE usuario_id = ? AND activo = 1");
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();
$estudiantes = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Tutor</title>
  <link href="assets/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/css/style.css" rel="stylesheet">
  <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css" rel="stylesheet">
  <link href="vendors/iconic-fonts/font-awesome/css/all.min.css" rel="stylesheet">
  <link rel="icon" type="image/png" sizes="32x32" href="assets/img/weicon/weicon.ico">
</head>
<body class="ms-body ms-aside-left-open ms-primary-theme">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container">
    <a class="navbar-brand" href="#"><i class="fa fa-user"></i> Tutor</a>
    <div class="ml-auto">
      <span class="text-white mr-3">Bienvenido, <?php echo $_SESSION['correo']; ?></span>
      <a href="index.php?logout=true" class="btn btn-outline-light btn-sm">Cerrar sesión</a>
    </div>
  </div>
</nav>

<div class="ms-content-wrapper mt-5">
  <div class="container">
    <div class="card">
      <div class="card-header bg-primary text-white">
        <h5 class="mb-0">Estudiantes Asignados</h5>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-bordered" id="tabla-estudiantes">
            <thead class="thead-dark">
              <tr>
                <th>Nombre</th>
                <th>Apellido</th>
                <th>Grado</th>
                <th>Grupo</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($estudiantes as $alumno): ?>
              <tr>
                <td><?php echo htmlspecialchars($alumno['nombre']); ?></td>
                <td><?php echo htmlspecialchars($alumno['apellido']); ?></td>
                <td><?php echo htmlspecialchars($alumno['grado']); ?></td>
                <td><?php echo htmlspecialchars($alumno['grupo']); ?></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="assets/js/jquery-3.3.1.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
<script>
  $(document).ready(function () {
    $('#tabla-estudiantes').DataTable({
      language: {
        url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
      }
    });
  });
</script>

</body>
</html>
