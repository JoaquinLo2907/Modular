// cargarAlumnos.js

// 1. Carga la tabla de estudiantes (ahora con tutor_nombre)
function cargarEstudiantes() {
  $.ajax({
    url: "../php/estudiantes.php",
    method: "GET",
    dataType: "json",
    success: data => {
      console.log("ESTUDIANTES RECIBIDOS:", data);
      let tbody = '';
      data.forEach(est => {
        if (est.activo == 1) {
          tbody += `
            <tr>
              <td>${est.estudiante_id}</td>
              <td>${est.tutor_nombre ?? 'Sin tutor'}</td>    <!-- aquí va el nombre -->
              <td>${est.nombre}</td>
              <td>${est.apellido}</td>
              <td>${est.fecha_nacimiento}</td>
              <td>${est.grado}</td>
              <td>${est.grupo}</td>
              <td>${est.tutor_id}</td>                      <!-- aquí va el ID -->
              <td>${est.activo == 1 ? 'Sí' : 'No'}</td>
              <td>${est.creado_en}</td>
              <td>${est.actualizado_en}</td>
              <td>
                <button class="btn btn-sm btn-outline-warning btn-editar" data-id="${est.estudiante_id}">Editar</button>
                <button class="btn btn-sm btn-outline-danger btn-eliminar" data-id="${est.estudiante_id}">Eliminar</button>
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
          paginate: { next: "Siguiente", previous: "Anterior" }
        }
      });
    },
    error: (xhr, status, err) => console.error("Error al cargar estudiantes:", err)
  });
}

// 2. Carga los tutores en el <select> y marca el seleccionado
function cargarTutores(selectedId = null) {
  return fetch('../php/tutores_opciones.php')
    .then(res => {
      console.log("tutores_opciones.php status:", res.status);
      if (!res.ok) throw new Error('HTTP ' + res.status);
      return res.json();
    })
    .then(tutores => {
      console.log("TUTORES RECIBIDOS:", tutores);
      let options = '<option value="" disabled>Seleccione un tutor</option>';
      tutores.forEach(tutor => {
        const sel = tutor.tutor_id == selectedId ? ' selected' : '';
        options += `
          <option value="${tutor.tutor_id}"${sel}>
            ${tutor.nombre} ${tutor.apellido}
          </option>`;
      });
      $('#edit-tutor-id').html(options);
    })
    .catch(err => {
      console.error('Error cargando tutores:', err);
      $('#edit-tutor-id').html('<option value="">Error al cargar tutores</option>');
    });
}

$(document).ready(function () {
  cargarEstudiantes();

  // Borrado lógico
  $(document).on('click', '.btn-eliminar', function () {
    const id = $(this).data('id');
    if (!confirm('¿Deseas eliminar este estudiante?')) return;
    fetch('../php/eliminar_estudiante.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ id })
    })
    .then(r => r.json())
    .then(resp => {
      if (resp.success) {
        Swal.fire({ icon: 'success', title: 'Eliminado', timer: 1500, showConfirmButton: false });
        cargarEstudiantes();
      } else {
        alert('Error al eliminar estudiante.');
      }
    });
  });

  // Abrir modal de edición
  $(document).on('click', '.btn-editar', function () {
    const id = $(this).data('id');
    fetch(`../php/obtener_estudiante.php?id=${id}`)
      .then(r => r.json())
      .then(data => {
        console.log("ESTUDIANTE PARA EDITAR:", data);
        $('#edit-id').val(data.estudiante_id);
        $('#edit-nombre').val(data.nombre);
        $('#edit-apellido').val(data.apellido);
        $('#edit-nacimiento').val(data.fecha_nacimiento);
        $('#edit-grado').val(data.grado);
        $('#edit-grupo').val(data.grupo);
        // Cargamos tutores y luego el modal
        cargarTutores(data.tutor_id).then(() => {
          $('#modalEditarEstudiante').modal('show');
        });
      })
      .catch(err => {
        console.error('Error al obtener datos del estudiante:', err);
        alert('No se pudo cargar la información del estudiante.');
      });
  });

  // Enviar formulario de edición
  $('#formEditarEstudiante').on('submit', function (e) {
    e.preventDefault();
    const formData = new FormData(this);
    // muestro lo que voy a mandar
    for (let [key, val] of formData.entries()) console.log(key, val);

    fetch('../php/editar_estudiante.php', {
      method: 'POST',
      body: formData
    })
    .then(r => {
      console.log('editar_estudiante.php status:', r.status);
      return r.json();
    })
    .then(data => {
      console.log('Respuesta al editar:', data);
      if (data.success) {
        $('#modalEditarEstudiante').modal('hide');
        Swal.fire({ icon: 'success', title: '¡Estudiante actualizado!', timer: 1500, showConfirmButton: false });
        cargarEstudiantes();
      } else {
        alert(`No se pudo actualizar: ${data.message || data.error || 'Error desconocido'}`);
      }
    })
    .catch(err => {
      console.error('Fetch error al actualizar estudiante:', err);
      alert('Error de red al actualizar estudiante.');
    });
  });
});
