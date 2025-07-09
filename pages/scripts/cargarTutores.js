document.addEventListener('DOMContentLoaded', () => {
  let tabla; 

  // Función para formatear fecha (si la tuvieras en tutorprofile.html)
  const formatDate = d => {
    if (!d) return '';
    const dt = new Date(d);
    return `${dt.getFullYear()}-${String(dt.getMonth()+1).padStart(2,'0')}-${String(dt.getDate()).padStart(2,'0')}`;
  };

  function cargarTutores() {
    fetch('../php/obtener_tutores.php')
      .then(res => res.json())
      .then(data => {
        const tbody = document.getElementById('tutores-lista');
        tbody.innerHTML = '';
        data.forEach(t => {
          const tr = document.createElement('tr');
          tr.innerHTML = `
            <td>${t.nombre}</td>
            <td>${t.apellido}</td>
            <td>${t.telefono}</td>
            <td>${t.correo}</td>
            <td>${t.direccion}</td>
            <td>${t.activo==1? 'Sí':'No'}</td>
            <td>
              <button class="btn btn-sm btn-outline-warning btn-editar" data-id="${t.tutor_id}">
                <i class="fa fa-edit"></i>
              </button>
              <button class="btn btn-sm btn-outline-danger btn-eliminar" data-id="${t.tutor_id}">
                <i class="fa fa-trash"></i>
              </button>
            </td>`;
          tbody.appendChild(tr);
        });

        // Inicializa DataTable una sola vez
        if (!tabla) {
          tabla = $('#tablaTutores').DataTable({
            dom: 'Bfrtip',
            buttons: [
              {
                extend: 'excelHtml5',
                text: '<i class="fa fa-file-excel"></i> Excel',
                titleAttr: 'Exportar a Excel'
              },
              {
                extend: 'csvHtml5',
                text: '<i class="fa fa-file-csv"></i> CSV',
                titleAttr: 'Exportar a CSV'
              }
            ],
            language: {
              search: "Buscar:",
              paginate: { next: "Siguiente", previous: "Anterior" },
              lengthMenu: "Mostrar _MENU_ registros",
              info: "Mostrando _START_ a _END_ de _TOTAL_ tutores"
            }
          });
        } else {
          tabla.clear().rows.add($('#tablaTutores tbody tr')).draw();
        }

        // Aquí podrías volver a enganchar eventos editar/eliminar
        document.querySelectorAll('.btn-editar').forEach(btn =>
          btn.addEventListener('click', () => editarTutor(btn.dataset.id))
        );
        document.querySelectorAll('.btn-eliminar').forEach(btn =>
          btn.addEventListener('click', () => eliminarTutor(btn.dataset.id))
        );
      })
      .catch(console.error);
  }

  // Botón Exportar genérico (en caso de necesitar lógica extra)
  document.getElementById('btnExportarTutores').addEventListener('click', () => {
    tabla.button('.buttons-excel').trigger();
  });

  // Carga inicial
  cargarTutores();
});
