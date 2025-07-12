// cargarDocentes.js
document.addEventListener('DOMContentLoaded', () => {
  let dataTable;

  // 1) Función para cargar y renderizar la tabla
  function cargarDocentes() {
    fetch('../php/obtener_profesores.php')
      .then(res => res.json())
      .then(data => {
        if (!Array.isArray(data)) return console.error('Datos inválidos');
        const tbody = document.getElementById('docentes-lista');
        tbody.innerHTML = '';

        data.forEach(d => {
          const tr = document.createElement('tr');
          tr.innerHTML = `
            <td>${d.nombre} ${d.apellido}</td>
            <td>${d.puesto}</td>
            <td>${d.correo}</td>
            <td>${d.telefono}</td>
            <td>$${d.salario}</td>
            <td>${d.direccion}</td>
            <td>${formatDate(d.fecha_nacimiento)}</td>
            <td>
              <button class="btn btn-sm btn-outline-warning btn-editar" data-id="${d.docente_id}">
                <i class="fa fa-edit"></i>
              </button>
              <button class="btn btn-sm btn-outline-danger btn-eliminar" data-id="${d.docente_id}">
                <i class="fa fa-trash"></i>
              </button>
            </td>`;
          tbody.appendChild(tr);
        });

        // Inicializar o refrescar DataTable
        if (!dataTable) {
          dataTable = $('#tablaProfesores').DataTable({
            language: {
              search: "Buscar:",
              paginate: { next: "Siguiente", previous: "Anterior" },
              lengthMenu: "Mostrar _MENU_ registros",
              info: "Mostrando _START_ a _END_ de _TOTAL_ profesores"
            }
          });
        } else {
          dataTable.clear()
                   .rows.add($('#tablaProfesores tbody tr'))
                   .draw();
        }

        // Enganchar eventos
        document.querySelectorAll('.btn-editar').forEach(btn =>
          btn.addEventListener('click', () => editarDocente(btn.dataset.id))
        );
        document.querySelectorAll('.btn-eliminar').forEach(btn =>
          btn.addEventListener('click', () => eliminarDocente(btn.dataset.id))
        );
      })
      .catch(err => console.error('Error al cargar los docentes:', err));
  }

  // 2) Formatea la fecha a YYYY-MM-DD
  function formatDate(dateString) {
    if (!dateString) return '';
    const d = new Date(dateString);
    return [
      d.getFullYear(),
      String(d.getMonth() + 1).padStart(2, '0'),
      String(d.getDate()).padStart(2, '0')
    ].join('-');
  }

  // 3) Abre el modal y carga los datos del docente
  function editarDocente(id) {
    fetch(`../php/editar_docentes.php?id=${id}`)
      .then(res => res.json())
      .then(data => {
        // Rellena el formulario del modal
        $('#edit-docente-id').val(data.docente_id);
        $('#edit-nombre').val(data.nombre);
        $('#edit-apellido').val(data.apellido);
        $('#edit-telefono').val(data.telefono);
        $('#edit-correo').val(data.correo);
        $('#edit-activo').val(data.activo);
        $('#edit-puesto').val(data.puesto);
        $('#edit-genero').val(data.genero);
        $('#edit-fecha-nacimiento').val(formatDate(data.fecha_nacimiento));
        $('#edit-salario').val(data.salario);
        $('#edit-direccion').val(data.direccion);
        $('#preview-foto').attr('src', `../../${data.foto_url}`);
        $('#edit-creado-en').val(data.creado_en);
        $('#edit-actualizado-en').val(data.actualizado_en);
        // Mostrar modal
        $('#modalEditarProfesor').modal('show');
      })
      .catch(err => Swal.fire('Error', 'No se pudieron cargar los datos', 'error'));
  }

  // 4) Confirma y elimina al docente
  function eliminarDocente(id) {
    Swal.fire({
      title: '¿Estás seguro?',
      text: 'No podrás revertir esta acción',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Sí, eliminar',
      cancelButtonText: 'Cancelar'
    }).then(result => {
      if (!result.isConfirmed) return;
      fetch('../php/eliminar_docentes.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id })
      })
        .then(res => res.json())
        .then(resp => {
          if (resp.success) {
            Swal.fire('Eliminado', 'El docente ha sido eliminado', 'success');
            cargarDocentes();
          } else {
            Swal.fire('Error', resp.message, 'error');
          }
        })
        .catch(() => Swal.fire('Error', 'Falló la petición', 'error'));
    });
  }

  // 5) Primera carga
  cargarDocentes();
});
