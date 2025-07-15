function cargarEstudiantes() {
  const url = (typeof userRol !== 'undefined' && userRol == 1)
    ? '../php/estudiantes-doc.php'
    : '../php/estudiantes.php';

  $.ajax({
    url: url,
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
              <td>${est.tutor_id}</td>
              <td>${est.tutor_nombre || 'Sin tutor'}</td>
              <td>${est.nombre}</td>
              <td>${est.apellido}</td>
              <td>${est.fecha_nacimiento}</td>
              <td>${est.grado}</td>
              <td>${est.grupo}</td>
              <td>${est.materia_id}</td>
              <td>${est.ciclo}</td>
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
      if (!res.ok) throw new Error('HTTP ' + res.status);
      return res.json();
    })
    .then(tutores => {
      let options = '<option value="" disabled>Seleccione un tutor</option>';
      tutores.forEach(tutor => {
        const sel = tutor.tutor_id == selectedId ? ' selected' : '';
        options += `<option value="${tutor.tutor_id}"${sel}>${tutor.nombre} ${tutor.apellido}</option>`;
      });
      $('#edit-tutor-id').html(options);
    })
    .catch(err => {
      console.error('Error cargando tutores:', err);
      $('#edit-tutor-id').html('<option value="">Error al cargar tutores</option>');
    });
}

function cargarMateriasDocente() {
  fetch('../php/materias-docente.php')
    .then(r => r.json())
    .then(materias => {
      let opciones = '<option value="">Seleccione una materia</option>';
      materias.forEach(m => {
        opciones += `<option value="${m.materia_id}">${m.nombre} (ID: ${m.materia_id} | Ciclo: ${m.ciclo})</option>`;
      });
      $('#materia-select').html(opciones);
    })
    .catch(err => console.error('Error al cargar materias del docente:', err));
}


$(document).ready(function () {
  cargarMateriasDocente(); // Solo cargar el select

  $('#materia-select').on('change', function () {
    const materiaId = $(this).val();
    if (materiaId) {
      cargarEstudiantes(materiaId);
    } else {
      $('#student-body').html('');
      $('#data-table-4').DataTable().clear().draw();
    }
  });

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
        const materiaId = $('#materia-select').val();
        cargarEstudiantes(materiaId);
      } else {
        alert('Error al eliminar estudiante.');
      }
    });
  });

  $(document).on('click', '.btn-editar', function () {
    const id = $(this).data('id');
    fetch(`../php/obtener_estudiante.php?id=${id}`)
      .then(r => r.json())
      .then(data => {
        $('#edit-id').val(data.estudiante_id);
        $('#edit-nombre').val(data.nombre);
        $('#edit-apellido').val(data.apellido);
        $('#edit-nacimiento').val(data.fecha_nacimiento);
        $('#edit-grado').val(data.grado);
        $('#edit-grupo').val(data.grupo);
        cargarTutores(data.tutor_id).then(() => {
          $('#modalEditarEstudiante').modal('show');
        });
      })
      .catch(err => {
        console.error('Error al obtener datos del estudiante:', err);
        alert('No se pudo cargar la información del estudiante.');
      });
  });

  $('#formEditarEstudiante').on('submit', function (e) {
    e.preventDefault();
    const formData = new FormData(this);
    fetch('../php/editar_estudiante.php', {
      method: 'POST',
      body: formData
    })
    .then(r => r.json())
    .then(data => {
      if (data.success) {
        $('#modalEditarEstudiante').modal('hide');
        Swal.fire({ icon: 'success', title: '¡Estudiante actualizado!', timer: 1500, showConfirmButton: false });
        const materiaId = $('#materia-select').val();
        cargarEstudiantes(materiaId);
      } else {
        alert(`No se pudo actualizar: ${data.message || 'Error desconocido'}`);
      }
    })
    .catch(err => {
      console.error('Fetch error al actualizar estudiante:', err);
      alert('Error de red al actualizar estudiante.');
    });
  });
});
