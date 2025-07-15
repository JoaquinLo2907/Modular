// File: cargarClases.js

$(document).ready(() => {
  const $selectCiclo  = $('#selectCiclo');
  const $selectGrado  = $('#selectGrado');
  const $selectGrupo  = $('#selectGrupo');
  const $modal        = $('#claseModal');
  const $form         = $('#claseForm');
  const $asigCont     = $('#asignacionesContainer');

  let materiasAll = [];
  let docentesAll = [];

  // ───── 1️⃣ Carga inicial de ciclos, materias y docentes ─────
  function cargarSelects() {
    const pCiclos = fetch('../php/obtener_ciclos.php')
      .then(r => r.json())
      .then(data => {
        $selectCiclo.empty().append('<option value="">Seleccione ciclo</option>');
        // Sólo ciclos con estado 'activo'
        data
          .filter(c => c.estado === 'activo')
          .forEach(c => {
            $selectCiclo.append(
              `<option value="${c.ciclo_id}">
                 ${c.nombre} (${c.fecha_inicio} → ${c.fecha_fin})
               </option>`
            );
          });
      }); // :contentReference[oaicite:0]{index=0}

    const pMaterias = fetch('../php/obtener_materias.php')
      .then(r => r.json())
      .then(data => materiasAll = data);

    const pDocs = fetch('../php/obtener_profesores.php')
      .then(r => r.json())
      .then(data => docentesAll = data);

    return Promise.all([pCiclos, pMaterias, pDocs]);
  }

  // ───── 2️⃣ Render dinámico de asignaciones según grado ─────
  function renderAsignaciones() {
    const grado = +$selectGrado.val();
    $asigCont.empty();
    if (!grado) return;

    const nivel = grado <= 6 ? 'primaria' : 'secundaria';
    const mats  = materiasAll.filter(m => m.nivel_grado === nivel);

    if (grado <= 6) {
      // Primaria: checkbox para varias materias + un solo docente
      $asigCont.append(`
        <div class="form-group">
          <label>Materias</label>
          <div id="checkboxMaterias">
            ${mats.map(m => `
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="mat-${m.materia_id}" value="${m.materia_id}">
                <label class="form-check-label" for="mat-${m.materia_id}">${m.nombre}</label>
              </div>
            `).join('')}
          </div>
        </div>
        <div class="form-group">
          <label for="selectDocenteAll">Docente (todas)</label>
          <select id="selectDocenteAll" class="form-control">
            <option value="">Seleccione docente</option>
            ${docentesAll.map(d =>
              `<option value="${d.docente_id}">${d.nombre} ${d.apellido}</option>`
            ).join('')}
          </select>
        </div>
      `);
    } else {
      // Secundaria: checkbox + select de docente por materia
      $asigCont.append(`
        <table class="table">
          <thead>
            <tr><th>Incluir</th><th>Materia</th><th>Docente</th></tr>
          </thead>
          <tbody>
            ${mats.map(m => `
              <tr>
                <td>
                  <input class="form-check-input checkMateriaSec" type="checkbox" data-m="${m.materia_id}">
                </td>
                <td>${m.nombre}</td>
                <td>
                  <select class="form-control selectDocentePorMateria" data-m="${m.materia_id}">
                    <option value="">Seleccione docente</option>
                    ${docentesAll.map(d =>
                      `<option value="${d.docente_id}">${d.nombre} ${d.apellido}</option>`
                    ).join('')}
                  </select>
                </td>
              </tr>
            `).join('')}
          </tbody>
        </table>
      `);
    }
  }

  // ───── 3️⃣ Botón “Nueva Clase” ─────
  $('#newClaseBtn').click(() => {
    $form[0].reset();
    $form.removeData('id');
    renderAsignaciones();
    $modal.find('.modal-title').text('Nueva Clase');
    $modal.modal('show');
  });

  // ───── 4️⃣ Al cambiar el grado ─────
  $selectGrado.on('change', renderAsignaciones);

  // ───── 5️⃣ Crear o actualizar clase ─────
  $form.submit(function(e) {
    e.preventDefault();
    const isEdit = !!$(this).data('id');
    const url    = isEdit
      ? '../php/actualizar_clase.php'
      : '../php/crear_clase.php';

    const payload = {
      clase_id:     $(this).data('id') || null,
      ciclo_id:     $selectCiclo.val(),
      grado:        $selectGrado.val(),
      grupo:        $selectGrupo.val(),
      asignaciones: []
    };

    if (!payload.ciclo_id || !payload.grado || !payload.grupo) {
      return alert('Complete ciclo, grado y grupo.');
    }

    if (payload.grado <= 6) {
      // Primaria: obtiene materias checked
      const materias = $('#checkboxMaterias input:checked')
                         .map((_,el) => el.value).get();
      const doc      = $('#selectDocenteAll').val();
      if (!materias.length || !doc) {
        return alert('Elija al menos una materia y un docente.');
      }
      materias.forEach(mid => {
        payload.asignaciones.push({ materia_id: mid, docente_id: doc });
      });
    } else {
      // Secundaria: solo materias checked
      const checked = $('.checkMateriaSec:checked');
      if (!checked.length) {
        return alert('Seleccione al menos una materia de secundaria.');
      }
      let ok = true;
      checked.each((_, chk) => {
        const mid = $(chk).data('m');
        const doc = $(`.selectDocentePorMateria[data-m="${mid}"]`).val();
        if (!doc) ok = false;
        payload.asignaciones.push({ materia_id: mid, docente_id: doc });
      });
      if (!ok) return alert('Elija un docente para cada materia seleccionada.');
    }

    fetch(url, {
      method:  'POST',
      headers: { 'Content-Type': 'application/json' },
      body:    JSON.stringify(payload)
    })
    .then(r => r.json())
    .then(json => {
      if (json.success) {
        $modal.modal('hide');
        cargarClases();
      } else {
        alert(json.message || 'Error al guardar');
      }
    })
    .catch(err => {
      console.error(err);
      alert('Error procesando la petición.');
    });
  });

  // ───── 6️⃣ Editar clase existente ─────
  $('#tabla-clases').on('click', '.btn-editar-clase', function() {
    const id = $(this).data('id');
    fetch(`../php/obtener_clase.php?clase_id=${id}`)
      .then(r => r.json())
      .then(json => {
        const c = json.clase;
        $form.data('id', id);
        $selectCiclo.val(c.ciclo_id);
        $selectGrado.val(c.grado);
        $selectGrupo.val(c.grupo);
        renderAsignaciones();
        if (c.grado <= 6) {
          // Marcar checkboxes y docente
          json.asignaciones.forEach(a => {
            $(`#checkboxMaterias input[value="${a.materia_id}"]`).prop('checked', true);
          });
          $('#selectDocenteAll').val(json.asignaciones[0].docente_id);
        } else {
          // Marcar checkboxes y selects de secundaria
          json.asignaciones.forEach(a => {
            $(`.checkMateriaSec[data-m="${a.materia_id}"]`).prop('checked', true);
            $(`.selectDocentePorMateria[data-m="${a.materia_id}"]`).val(a.docente_id);
          });
        }
        $modal.find('.modal-title').text('Editar Clase');
        $modal.modal('show');
      })
      .catch(err => {
        console.error(err);
        alert('No se pudo cargar la clase.');
      });
  });

  // ───── 7️⃣ Eliminar clase ─────
  $('#tabla-clases').on('click', '.btn-eliminar-clase', function() {
    if (!confirm('¿Eliminar esta clase?')) return;
    const id = $(this).data('id');
    fetch('../php/eliminar_clase.php', {
      method:  'POST',
      headers: { 'Content-Type': 'application/json' },
      body:    JSON.stringify({ clase_id: id })
    })
    .then(r => r.json())
    .then(json => {
      if (json.success) cargarClases();
      else alert(json.message || 'Error al eliminar');
    })
    .catch(err => {
      console.error(err);
      alert('No se pudo eliminar.');
    });
  });

  // ───── 8️⃣ Listar e inicializar DataTable ─────
  function cargarClases() {
    fetch('../php/obtener_clases.php')
      .then(r => r.json())
      .then(data => {
        if ($.fn.dataTable.isDataTable('#tabla-clases')) {
          $('#tabla-clases').DataTable().destroy();
        }
        const $tbody = $('#tabla-clases tbody').empty();
        data.forEach(c => {
          $tbody.append(`
            <tr>
              <td>${c.ciclo}</td>
              <td>${c.materia}</td>
              <td>${c.docente}</td>
              <td>${c.grado}</td>
              <td>${c.grupo}</td>
              <td>
                <button class="btn btn-sm btn-warning btn-editar-clase" data-id="${c.clase_id}">
                  <i class="fa fa-edit"></i>
                </button>
                <button class="btn btn-sm btn-danger btn-eliminar-clase" data-id="${c.clase_id}">
                  <i class="fa fa-trash"></i>
                </button>
              </td>
            </tr>
          `);
        });
        $('#tabla-clases').DataTable({ pageLength: 10, autoWidth: false });
      })
      .catch(console.error);
  }

  // ───── 9️⃣ Inicialización ─────
  cargarSelects().then(cargarClases);
});
