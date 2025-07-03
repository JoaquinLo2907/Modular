// assets/js/cargarDocentes.js

document.addEventListener('DOMContentLoaded', function () {

  // 1) Carga la lista de docentes
  function cargarDocentes() {
    fetch('../php/obtener_profesores.php')
      .then(res => res.json())
      .then(data => {
        const lista = document.getElementById('docentes-lista');
        lista.innerHTML = '';
        if (!Array.isArray(data)) return console.error('Datos inválidos');

        data.forEach(d => {
          const li = document.createElement('li');
          li.className = 'list-group-item d-flex justify-content-between align-items-center';
          li.innerHTML = `
            <div class="d-flex justify-content-between align-items-center flex-wrap w-100">
              <div class="d-flex align-items-center flex-grow-1">
                <i class="fa fa-user-circle fa-2x text-primary mr-3"></i>
                <div>
                  <strong>${d.nombre} ${d.apellido}</strong> &nbsp;
                  <small class="text-muted">(${d.puesto})</small><br>
                  <span class="text-muted">
                    <i class="fa fa-envelope"></i> ${d.correo} &nbsp;|&nbsp;
                    <i class="fa fa-phone"></i> ${d.telefono} &nbsp;|&nbsp;
                    <strong>$${d.salario}</strong>
                  </span><br>
                  <span class="text-muted">
                    <i class="fa fa-map-marker-alt"></i> ${d.direccion} &nbsp;|&nbsp;
                    <i class="fa fa-birthday-cake"></i> ${formatDate(d.fecha_nacimiento)}
                  </span>
                </div>
              </div>
              <div class="text-right mt-2 mt-md-0">
                <button class="btn btn-sm btn-outline-warning btn-editar" data-id="${d.docente_id}">
                  <i class="fa fa-edit"></i> Editar
                </button>
                <button class="btn btn-sm btn-outline-danger btn-eliminar" data-id="${d.docente_id}">
                  <i class="fa fa-trash"></i> Eliminar
                </button>
              </div>
            </div>`;
          lista.appendChild(li);
        });

        document.querySelectorAll('.btn-editar').forEach(btn =>
          btn.addEventListener('click', () => editarDocente(btn.dataset.id))
        );
        document.querySelectorAll('.btn-eliminar').forEach(btn =>
          btn.addEventListener('click', () => eliminarDocente(btn.dataset.id))
        );
      })
      .catch(err => console.error('Error al cargar los docentes:', err));
  }

  // 2) Formatea fecha a YYYY-MM-DD
  function formatDate(dateString) {
    const d = new Date(dateString);
    return `${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,'0')}-${String(d.getDate()).padStart(2,'0')}`;
  }

  // 3) Mostrar modal con datos (sin tocar el file-input)
  function editarDocente(id) {
    fetch(`../php/profesorId.php?id=${id}`)
      .then(res => res.json())
      .then(data => {
        if (!data) return console.error('No llegan datos');

        document.getElementById('edit-docente-id').value        = data.docente_id;
        document.getElementById('edit-nombre').value           = data.nombre;
        document.getElementById('edit-apellido').value         = data.apellido;
        document.getElementById('edit-telefono').value         = data.telefono;
        document.getElementById('edit-correo').value           = data.correo;
        document.getElementById('edit-activo').value           = data.activo;
        document.getElementById('edit-puesto').value           = data.puesto;
        document.getElementById('edit-genero').value           = data.genero;
        document.getElementById('edit-fecha-nacimiento').value = formatDate(data.fecha_nacimiento);
        document.getElementById('edit-salario').value          = data.salario;
        document.getElementById('edit-direccion').value        = data.direccion;

        // SOLO así mostramos la imagen existente, en un <img id="preview-foto">
        const preview = document.getElementById('preview-foto');
        if (preview) preview.src = data.foto_url;

        // **NO** toques nunca el input file: 
        //—> document.getElementById('edit-foto').value = ...

        $('#modalEditarProfesor').modal('show');
      })
      .catch(err => console.error('Error al obtener los datos del docente:', err));
  }

  // 4) Eliminar docente
  function eliminarDocente(id) {
    if (!confirm('¿Seguro de eliminar?')) return;
    fetch('../php/eliminar_docentes.php', {
      method: 'POST',
      headers: {'Content-Type':'application/json'},
      body: JSON.stringify({ id })
    })
    .then(r => r.json())
    .then(resp => {
      if (resp.success) {
        alert('Docente eliminado.');
        cargarDocentes();
      } else {
        alert('Error al eliminar: '+(resp.message||''));
      }
    })
    .catch(e => {
      console.error('Error al eliminar:', e);
      alert('No se pudo eliminar.');
    });
  }

  // 5) Submit del form de editar con FormData
  const formEditar = document.getElementById('formEditarProfesor');
  formEditar.addEventListener('submit', function(e) {
    e.preventDefault();
    const fd = new FormData(this);
    fetch(this.action, { method: 'POST', body: fd })
      .then(r => r.json())
      .then(resp => {
        if (resp.success) {
          alert('Docente actualizado.');
          $('#modalEditarProfesor').modal('hide');
          cargarDocentes();
        } else {
          alert('Error al actualizar: '+(resp.message||''));
        }
      })
      .catch(e => {
        console.error('Error enviando datos:', e);
        alert('No se pudo conectar.');
      });
  });

  // 6) Carga inicial
  cargarDocentes();
});
