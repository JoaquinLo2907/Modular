document.addEventListener('DOMContentLoaded', () => {
  let dataTable;

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

        // (Re)inicializa DataTable solo una vez
        if (!dataTable) {
          dataTable = $('#tablaProfesores').DataTable({
            language: {
              search: "Buscar:",
              paginate: {
                next: "Siguiente",
                previous: "Anterior"
              },
              lengthMenu: "Mostrar _MENU_ registros",
              info: "Mostrando _START_ a _END_ de _TOTAL_ profesores"
            }
          });
        } else {
          dataTable.clear().rows.add($('#tablaProfesores tbody tr')).draw();
        }

        // Vuelve a enganchas los eventos de editar/eliminar
        document.querySelectorAll('.btn-editar').forEach(btn =>
          btn.addEventListener('click', () => editarDocente(btn.dataset.id))
        );
        document.querySelectorAll('.btn-eliminar').forEach(btn =>
          btn.addEventListener('click', () => eliminarDocente(btn.dataset.id))
        );
      })
      .catch(err => console.error('Error al cargar los docentes:', err));
  }

  function formatDate(dateString) {
    if (!dateString) return '';
    const d = new Date(dateString);
    return `${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,'0')}-${String(d.getDate()).padStart(2,'0')}`;
  }

  // … mantén aquí tus funciones editarDocente(), eliminarDocente(), formEditar, etc. …

  // Carga inicial
  cargarDocentes();
});
