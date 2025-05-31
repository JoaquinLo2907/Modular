function cargarEstudiantes() {
  $.ajax({
    url: "../php/estudiantes.php",
    method: "GET",
    dataType: "json",
    success: function (data) {
      let tbody = '';
      data.forEach(function (estudiante) {
        if (estudiante.activo == 1) {
          tbody += `
            <tr>
              <td>${estudiante.estudiante_id}</td>
              <td>${estudiante.nombre_usuario ?? 'Sin usuario'}</td>
              <td>${estudiante.nombre}</td>
              <td>${estudiante.apellido}</td>
              <td>${estudiante.fecha_nacimiento}</td>
              <td>${estudiante.grado}</td>
              <td>${estudiante.grupo}</td>
              <td>${estudiante.tutor_id}</td>
              <td>Sí</td>
              <td>${estudiante.creado_en}</td>
              <td>${estudiante.actualizado_en}</td>
              <td>
                <button class="btn btn-sm btn-outline-warning btn-editar" data-id="${estudiante.estudiante_id}">Editar</button>
                <button class="btn btn-sm btn-outline-danger btn-eliminar" data-id="${estudiante.estudiante_id}">Eliminar</button>
              </td>
            </tr>
          `;
        }
      });

      $('#data-table-4').DataTable().destroy();
      $('#student-body').html(tbody);

      $('#data-table-4').DataTable({
        responsive: true,
        pageLength: 10,
        language: {
          search: "Buscar:",
          lengthMenu: "Mostrar _MENU_ registros por página",
          zeroRecords: "No se encontraron resultados",
          info: "Mostrando página _PAGE_ de _PAGES_",
          infoEmpty: "No hay registros disponibles",
          infoFiltered: "(filtrado de _MAX_ registros totales)",
          paginate: {
            next: "Siguiente",
            previous: "Anterior"
          }
        }
      });
    },
    error: function (xhr, status, error) {
      console.error("Error al obtener los datos de estudiantes:", error);
    }
  });
}

$(document).ready(function () {
  cargarEstudiantes();

  // Eliminar estudiante (borrado lógico)
  $(document).on('click', '.btn-eliminar', function () {
    const id = $(this).data('id');
    if (confirm('¿Deseas eliminar este estudiante?')) {
      fetch('../php/eliminar_estudiante.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id: id })
      })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          Swal.fire({
            icon: 'success',
            title: 'Estudiante eliminado',
            text: 'Se ha realizado el borrado lógico correctamente.',
            timer: 2500,
            showConfirmButton: false
          });
          cargarEstudiantes();
        } else {
          alert('Error al eliminar estudiante.');
        }
      });
    }
  });

  // Abrir modal con datos del estudiante
  $(document).on('click', '.btn-editar', function () {
    const id = $(this).data('id');
    fetch(`../php/obtener_estudiante.php?id=${id}`)
      .then(res => res.json())
      .then(data => {
        $('#edit-id').val(data.estudiante_id);
        $('#edit-nombre').val(data.nombre);
        $('#edit-apellido').val(data.apellido);
        $('#edit-nacimiento').val(data.fecha_nacimiento);
        $('#edit-grado').val(data.grado);
        $('#edit-grupo').val(data.grupo);

        fetch('../php/usuarios_opciones.php')
          .then(res => res.json())
          .then(usuarios => {
            let options = '<option value="">Seleccione un usuario</option>';
            usuarios.forEach(usuario => {
              const selected = usuario.usuario_id == data.usuario_id ? 'selected' : '';
              options += `<option value="${usuario.usuario_id}" ${selected}>${usuario.nombre_usuario}</option>`;
            });
            $('#edit-usuario-id').html(options);
          });

        $('#modalEditarEstudiante').modal('show');
      });
  });

  // Enviar cambios del modal
  $('#formEditarEstudiante').on('submit', function (e) {
    e.preventDefault();
    const formData = new FormData(this);

    fetch('../php/editar_estudiante.php', {
      method: 'POST',
      body: formData
    })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        $('#modalEditarEstudiante').modal('hide');
        Swal.fire({
          icon: 'success',
          title: '¡Estudiante actualizado!',
          text: 'Los cambios se guardaron correctamente.',
          timer: 2500,
          showConfirmButton: false
        });
        cargarEstudiantes();
      } else {
        alert('Error al actualizar estudiante.');
      }
    });
  });
});