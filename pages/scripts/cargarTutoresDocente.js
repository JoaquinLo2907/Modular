document.addEventListener('DOMContentLoaded', () => {
  const selectGrupo = document.getElementById('filtroGrupo');
  const tablaTutores = document.getElementById('tablaTutores');

function cargarGrupos() {
  fetch('../php/obtener_grupos_docente.php')
    .then(res => res.json())
    .then(grupos => {
      if (!selectGrupo) return;
      selectGrupo.innerHTML = '<option value="">Todos los grupos</option>';
      grupos.forEach(g => {
        const opt = document.createElement('option');
        opt.value = `${g.grado}-${g.grupo}-${g.materia_id}`; // valor que puede usarse en filtros
        opt.textContent = `${g.grado} - ${g.grupo} (${g.ciclo}) - ID: ${g.materia_id}`;
        selectGrupo.appendChild(opt);
      });
    })
    .catch(err => {
      console.error('❌ Error al cargar grupos:', err);
    });
}


  function cargarTutores(filtro = '') {
    const url = filtro
      ? `../php/obtener_tutores_docente.php${filtro}`
      : '../php/obtener_tutores_docente.php';

    fetch(url)
      .then(res => res.json())
      .then(data => {
        console.log("✅ Datos recibidos del servidor:", data);

        if (!Array.isArray(data)) {
          console.warn("⚠️ Respuesta inesperada del servidor:", data);
          alert(data.error || 'Error desconocido al cargar tutores');
          return;
        }

        // Destruir y recargar DataTable con nuevos datos
        if ($.fn.DataTable.isDataTable('#tablaTutores')) {
          const table = $('#tablaTutores').DataTable();
          table.clear().rows.add(data).draw();
        } else {
          $('#tablaTutores').DataTable({
            data: data,
            columns: [
              { data: 'nombre' },
              { data: 'apellido' },
              { data: 'telefono' },
              { data: 'correo' },
              { data: 'direccion' }
            ],
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
              info: "Mostrando _START_ a _END_ de _TOTAL_ tutores",
              emptyTable: "No se encontraron tutores."
            }
          });
        }
      })
      .catch(err => {
        console.error("❌ Error al cargar tutores:", err);
      });
  }

  // Evento para cambio de grupo
  if (selectGrupo) {
    selectGrupo.addEventListener('change', () => {
      const valor = selectGrupo.value;
      if (valor) {
        const [grado, grupo] = valor.split('-');
        cargarTutores(`?grado=${grado}&grupo=${grupo}`);
      } else {
        cargarTutores();
      }
    });
  }

  // Inicialización
  cargarGrupos();
  cargarTutores();
});
