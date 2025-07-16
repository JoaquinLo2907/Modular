<?php include '../php/auth.php'; ?>
<script>
  const userRol = <?php echo $_SESSION['rol'] ?? 'null'; ?>;
</script>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Calificaciones Docente</title>
  <link href="../../assets/css/bootstrap.min.css" rel="stylesheet">
  <link href="../../assets/css/datatables.min.css" rel="stylesheet">
  <link href="../../assets/css/style.css" rel="stylesheet">
</head>
<body class="ms-body">

<main class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4>Calificaciones por Periodo</h4>
    <select id="sel_periodo" class="form-select w-auto"></select>
  </div>

  <div class="table-responsive">
    <table id="tabla-calificaciones-docente" class="table table-bordered table-striped w-100">
      <thead>
        <tr>
          <th>Alumno</th>
          <th>Materia</th>
          <th>Calificación</th>
          <th>Acciones</th>
          <th>Cal. 1</th>
          <th>Cal. 2</th>
          <th>Cal. 3</th>

        </tr>
      </thead>
      <tbody></tbody>
    </table>
  </div>
</main>

<!-- Modal para editar calificación -->
<div class="modal fade" id="modalEditar" tabindex="-1">
  <div class="modal-dialog">
    <form id="formEditar" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Editar Calificaciones del Periodo</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="edit_id" name="calificacion_id">
        <input type="hidden" id="edit_estudiante" name="estudiante_id">
        <input type="hidden" id="edit_materia_id" name="materia_id">
        <input type="hidden" id="edit_periodo" name="periodo_id">

        <div class="mb-3">
          <label>Materia</label>
          <input type="text" id="edit_materia" class="form-control" readonly>
        </div>

        <div class="mb-3">
          <label>Calificación 1</label>
          <input type="number" id="edit_calificacion1" name="calificacion1" class="form-control" min="0" max="10" step="0.01">
        </div>

        <div class="mb-3">
          <label>Calificación 2</label>
          <input type="number" id="edit_calificacion2" name="calificacion2" class="form-control" min="0" max="10" step="0.01">
        </div>

        <div class="mb-3">
          <label>Calificación 3</label>
          <input type="number" id="edit_calificacion3" name="calificacion3" class="form-control" min="0" max="10" step="0.01">
        </div>
      </div>

      <div class="modal-footer">
        <button type="submit" class="btn btn-success">Guardar</button>
      </div>
    </form>
  </div>
</div>



<script src="../../assets/js/jquery-3.3.1.min.js"></script>
<script src="../../assets/js/bootstrap.min.js"></script>
<script src="../../assets/js/datatables.min.js"></script>
<script src="../scripts/cargarCalificacionesDoc.js"></script>
</body>
</html>



