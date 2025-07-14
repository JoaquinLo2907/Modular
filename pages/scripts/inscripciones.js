// File: inscripciones.js

$(function() {
  const $selClase = $('#selectClase');
  const $tblIns   = $('#tablaInscritos');
  const $tblOut   = $('#tablaNoInscritos');
  const $titulo   = $('#tituloClase');
  // Las flechas ya invierten su contenido con CSS .flip en el HTML
  const $btnDer   = $('#btnPasarADerecha');
  const $btnIzq   = $('#btnPasarAIzquierda');
  const $form     = $('#formInscripciones');

  let todos       = [];
  let inscritos   = [];
  let noInscritos = [];

  // ── 1️⃣ Carga todas las clases (array) o detalle si se pasa clase_id :contentReference[oaicite:0]{index=0}
  function cargarClases() {
    fetch('../php/obtener_clase.php')
      .then(r => r.json())
      .then(data => {
        $selClase
          .empty()
          .append('<option value="">Seleccione clase</option>');
        data.forEach(c => {
          $selClase.append(
            `<option value="${c.clase_id}">
               ${c.ciclo} • Grado ${c.grado}${c.grupo}
             </option>`
          );
        });
      })
      .catch(err => console.error('Error cargando clases:', err));
  }

  // ── 2️⃣ Al cambiar de clase, trae estudiantes + inscripciones de clase + globales :contentReference[oaicite:1]{index=1} :contentReference[oaicite:2]{index=2}
  function cargarDatos(claseId) {
    if (!claseId) {
      $titulo.text('');
      inscritos   = [];
      noInscritos = [];
      renderTablas();
      return;
    }
    Promise.all([
      fetch('../php/obtener_estudiantes.php').then(r => r.json()),
      fetch(`../php/obtener_inscripciones.php?clase_id=${claseId}`).then(r => r.json()),
      fetch('../php/obtener_inscripciones.php').then(r => r.json())
    ])
    .then(([allEst, insClase, insTodas]) => {
      todos = allEst.filter(e => e.activo == 1);
      const setClase = new Set(insClase.map(i => +i.estudiante_id));
      const setTodas = new Set(insTodas.map(i => +i.estudiante_id));

      // 🔹 Inscritos en la clase actual
      inscritos = todos.filter(e => setClase.has(+e.estudiante_id));
      // 🔹 Los "no inscritos" serán sólo los que NO están en ninguna inscripción
      noInscritos = todos.filter(e => !setTodas.has(+e.estudiante_id));

      $titulo.text( $selClase.find(':selected').text() );
      renderTablas();
    })
    .catch(err => console.error('Error cargando datos:', err));
  }

  // ── 3️⃣ Dibuja TK con checkbox en la primera columna ──
  function renderTablas() {
    const $inB  = $tblIns.find('tbody').empty();
    const $outB = $tblOut.find('tbody').empty();

    inscritos.forEach(e => {
      $inB.append(`
        <tr data-id="${e.estudiante_id}">
          <td><input type="checkbox" class="chkIns" data-id="${e.estudiante_id}"></td>
          <td>${e.nombre} ${e.apellido}</td>
        </tr>
      `);
    });

    noInscritos.forEach(e => {
      $outB.append(`
        <tr data-id="${e.estudiante_id}">
          <td><input type="checkbox" class="chkOut" data-id="${e.estudiante_id}"></td>
          <td>${e.nombre} ${e.apellido}</td>
        </tr>
      `);
    });
  }

  // ── 4️⃣ Botón “>”: mueve solo los checkeds de No inscritos → Inscritos ──
  $btnDer.on('click', () => {
    $tblOut.find('tbody .chkOut:checked').each(function() {
      const id = +$(this).data('id');
      const idx = noInscritos.findIndex(e => +e.estudiante_id === id);
      if (idx > -1) {
        inscritos.push(noInscritos[idx]);
        noInscritos.splice(idx, 1);
      }
    });
    renderTablas();
  });

  // ── 5️⃣ Botón “<”: mueve solo los checkeds de Inscritos → No inscritos ──
  $btnIzq.on('click', () => {
    $tblIns.find('tbody .chkIns:checked').each(function() {
      const id = +$(this).data('id');
      const idx = inscritos.findIndex(e => +e.estudiante_id === id);
      if (idx > -1) {
        noInscritos.push(inscritos[idx]);
        inscritos.splice(idx, 1);
      }
    });
    renderTablas();
  });

  // ── 6️⃣ Guardar cambios: envía solo los IDs de 'inscritos' actuales :contentReference[oaicite:3]{index=3}
  $form.on('submit', function(e) {
    e.preventDefault();
    const claseId = +$selClase.val();
    if (!claseId) {
      return alert('Seleccione una clase primero.');
    }
    fetch('../php/guardar_inscripciones.php', {
      method:  'POST',
      headers: {'Content-Type':'application/json'},
      body:    JSON.stringify({
        clase_id:    claseId,
        estudiantes: inscritos.map(e => e.estudiante_id)
      })
    })
    .then(r => r.json())
    .then(resp => {
      if (resp.success) {
        alert('Inscripciones guardadas correctamente.');
      } else {
        alert(resp.message || 'Error al guardar inscripciones.');
      }
    })
    .catch(err => {
      console.error('Error guardando inscripciones:', err);
      alert('Ocurrió un error al guardar.');
    });
  });

  // ── 7️⃣ Inicialización: carga ciclo y configura el cambio ──
  cargarClases();
  $selClase.on('change', () => cargarDatos($selClase.val()));
});
