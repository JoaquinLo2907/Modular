// File: cargarCiclos.js

$(document).ready(() => {
  const $periodosContainer = $('#periodosContainer');
  const tpl = $('#periodoTemplate').html();

  // 1) Dibuja el mini-resumen de periodos bajo el repeater
  function renderSummary() {
    const $sum = $('#periodosSummary').empty();
    $periodosContainer.find('.periodo-row').each((i, el) => {
      const $el  = $(el);
      const nom  = $el.find('.periodo-nombre').val() || '[sin nombre]';
      const ini  = $el.find('.periodo-inicio').val();
      const fin  = $el.find('.periodo-fin').val();
      $sum.append(`
        <li class="list-group-item px-2 py-1">
          <strong>${i+1}. ${nom}</strong> — ${ini} → ${fin}
        </li>`);
    });
  }

  // 2) Botón “+ Agregar periodo”
  $('#addPeriodoBtn').click(() => {
    $periodosContainer.append(tpl);
    renderSummary();
  });

  // 3) Delegación: quitar un periodo
  $periodosContainer.on('click', '.btn-remove-periodo', function(){
    $(this).closest('.periodo-row').remove();
    renderSummary();
  });

  // 4) Cada cambio en un campo de periodo refresca el resumen
  $periodosContainer.on('change', '.periodo-nombre, .periodo-inicio, .periodo-fin', renderSummary);

  // 5) “Nuevo Ciclo” resetea el form y abre el modal
  $('#newCicloBtn').click(() => {
    $('#cicloForm')[0].reset();
    $('#cicloForm').removeData('id');
    $periodosContainer.empty();
    $('#periodosSummary').empty();
    $('#cicloModalLabel').text('Nuevo Ciclo Escolar');
    $('#cicloModal').modal('show');
  });

  // 6) Envío de formulario: validaciones front-end y fetch
  $('#cicloForm').submit(function(e){
    e.preventDefault();

    // 6.1) Validación básica de fechas del ciclo
    let errores = [];
    const iniC = $('#fechaInicioCiclo').val();
    const finC = $('#fechaFinCiclo').val();
    $('#fechaInicioCiclo, #fechaFinCiclo').removeClass('is-invalid');
    if (!iniC || !finC || iniC >= finC) {
      errores.push('Rango del ciclo inválido (inicio ≥ fin).');
      $('#fechaInicioCiclo, #fechaFinCiclo').addClass('is-invalid');
    }

    // 6.2) Recolectar y validar periodos
    const periodos = [];
    $periodosContainer.find('.periodo-row').each((_, row) => {
      const $r   = $(row);
      const nom  = $r.find('.periodo-nombre').val().trim();
      const ini  = $r.find('.periodo-inicio').val();
      const fin  = $r.find('.periodo-fin').val();
      $r.find('.form-control').removeClass('is-invalid');

      if (!nom) {
        errores.push('Nombre de algún periodo vacío.');
        $r.find('.periodo-nombre').addClass('is-invalid');
      }
      if (!ini || !fin || ini >= fin) {
        errores.push(`Periodo “${nom||'?'}” con rango inválido.`);
        $r.find('.periodo-inicio, .periodo-fin').addClass('is-invalid');
      }
      if (ini < iniC || fin > finC) {
        errores.push(`Periodo “${nom}” fuera del rango del ciclo.`);
        $r.find('.periodo-inicio, .periodo-fin').addClass('is-invalid');
      }
      periodos.push({ ini, fin, row: $r });
    });

    // 6.3) Comprobar solapamientos entre periodos
    periodos.sort((a,b) => a.ini.localeCompare(b.ini));
    for (let i = 1; i < periodos.length; i++) {
      if (periodos[i].ini < periodos[i-1].fin) {
        errores.push('Al menos dos periodos se solapan.');
        periodos[i].row.find('.periodo-inicio, .periodo-fin').addClass('is-invalid');
      }
    }

    if (errores.length) {
      return alert(errores.join('\n'));
    }

    // 6.4) Preparar payload JSON
    const payload = {
      ciclo_id:     $(this).data('id') || null,
      nombre:       $('#nombreCiclo').val().trim(),
      fecha_inicio: iniC,
      fecha_fin:    finC,
      estado:       $('#estadoCiclo').val(),
      observaciones: $('#observacionesCiclo').val().trim(),
      periodos:     []
    };
    $periodosContainer.find('.periodo-row').each((_, row) => {
      const $r = $(row);
      payload.periodos.push({
        periodo_id:   $r.find('.periodo-id').val() || null,
        nombre:       $r.find('.periodo-nombre').val().trim(),
        fecha_inicio: $r.find('.periodo-inicio').val(),
        fecha_fin:    $r.find('.periodo-fin').val()
      });
    });

    // 6.5) Enviar a crear o actualizar
    const url = payload.ciclo_id
      ? '../php/actualizar_ciclo.php'
      : '../php/crear_ciclo.php';

    fetch(url, {
      method: 'POST',
      headers: {'Content-Type':'application/json'},
      body: JSON.stringify(payload)
    })
    .then(res => {
      if (!res.ok) throw new Error(`HTTP ${res.status}`);
      return res.json();
    })
    .then(json => {
      if (json.success) {
        $('#cicloModal').modal('hide');
        cargarCiclos();
      } else {
        alert(json.message || 'Error al guardar ciclo');
      }
    })
    .catch(err => {
      console.error('Formulario error:', err);
      alert('No se pudo procesar el ciclo. Revisa la consola.');
    });
  });

  // 7) Editar ciclo: cargar datos + sus periodos
  $('#tabla-ciclos').on('click', '.btn-editar', function(){
    const id = $(this).data('id');
    fetch(`../php/obtener_ciclo.php?id=${id}`)
      .then(res => res.json())
      .then(json => {
        const c = json.ciclo;
        $('#cicloForm').data('id', id);
        $('#nombreCiclo').val(c.nombre);
        $('#fechaInicioCiclo').val(c.fecha_inicio);
        $('#fechaFinCiclo').val(c.fecha_fin);
        $('#estadoCiclo').val(c.estado);
        $('#observacionesCiclo').val(c.observaciones);
        $periodosContainer.empty();
        json.periodos.forEach(p => {
          const $row = $(tpl);
          $row.find('.periodo-id').val(p.periodo_id);
          $row.find('.periodo-nombre').val(p.nombre);
          $row.find('.periodo-inicio').val(p.fecha_inicio);
          $row.find('.periodo-fin').val(p.fecha_fin);
          $periodosContainer.append($row);
        });
        renderSummary();
        $('#cicloModalLabel').text('Editar Ciclo Escolar');
        $('#cicloModal').modal('show');
      })
      .catch(err => console.error('Carga ciclo error:', err));
  });

  // 8) Cerrar/Reabrir ciclo (toggle estado)
  $('#tabla-ciclos').on('click', '.btn-toggle-estado', function(){
    const id = $(this).data('id');
    fetch('../php/toggle_estado_ciclo.php', {
      method: 'POST',
      headers: {'Content-Type':'application/json'},
      body: JSON.stringify({ ciclo_id: id })
    })
    .then(res => {
      if (!res.ok) throw new Error(`HTTP ${res.status}`);
      return res.json();
    })
    .then(json => {
      if (json.success) cargarCiclos();
      else alert(json.message);
    })
    .catch(err => {
      console.error('Toggle estado error:', err);
      alert('No se pudo cambiar el estado del ciclo.');
    });
  });

  // 9) Duplicar ciclo: clona y abre modal
  $('#tabla-ciclos').on('click', '.btn-duplicar', function(){
    const id = $(this).data('id');
    fetch(`../php/obtener_ciclo.php?id=${id}`)
      .then(res => res.json())
      .then(json => {
        const c = json.ciclo;
        $('#cicloForm')[0].reset();
        $('#cicloForm').removeData('id');
        $('#nombreCiclo').val(c.nombre + ' (Copia)');
        $('#fechaInicioCiclo').val(c.fecha_inicio);
        $('#fechaFinCiclo').val(c.fecha_fin);
        $('#estadoCiclo').val('activo');
        $('#observacionesCiclo').val(c.observaciones);
        $periodosContainer.empty();
        json.periodos.forEach(p => {
          const $row = $(tpl);
          $row.find('.periodo-nombre').val(p.nombre);
          $row.find('.periodo-inicio').val(p.fecha_inicio);
          $row.find('.periodo-fin').val(p.fecha_fin);
          $periodosContainer.append($row);
        });
        renderSummary();
        $('#cicloModalLabel').text('Duplicar Ciclo Escolar');
        $('#cicloModal').modal('show');
      })
      .catch(err => console.error('Duplicar ciclo error:', err));
  });

  // 10) Función para listar y renderizar todos los ciclos
  function cargarCiclos() {
    fetch('../php/obtener_ciclos.php')
      .then(res => res.json())
      .then(data => {
        // destruir DataTable si ya existía
        if ($.fn.dataTable && $.fn.dataTable.isDataTable('#tabla-ciclos')) {
          $('#tabla-ciclos').DataTable().destroy();
        }
        const $tbody = $('#tabla-ciclos tbody').empty();
        data.forEach(c => {
          $tbody.append(`
            <tr>
              <td>${c.nombre}</td>
              <td>${c.fecha_inicio}</td>
              <td>${c.fecha_fin}</td>
              <td>${c.estado}</td>
              <td>${c.observaciones||''}</td>
              <td>
                <button class="btn btn-sm btn-warning btn-editar" data-id="${c.ciclo_id}">
                  <i class="fa fa-edit"></i>
                </button>
                <button class="btn btn-sm btn-secondary btn-toggle-estado"
                        data-id="${c.ciclo_id}"
                        title="${c.estado==='activo'? 'Cerrar ciclo':'Reabrir ciclo'}">
                  ${c.estado==='activo'
                    ? '<i class="fa fa-lock-open"></i>'
                    : '<i class="fa fa-lock"></i>'}
                </button>
                <button class="btn btn-sm btn-info btn-duplicar" data-id="${c.ciclo_id}">
                  <i class="fa fa-clone"></i>
                </button>
              </td>
            </tr>`);
        });

        // inicializar DataTable
        $('#tabla-ciclos').DataTable({
          pageLength: 10,
          autoWidth: false
        });
      })
      .catch(err => console.error('Lista ciclos error:', err));
  }

  // 11) Carga inicial al abrir la página
  cargarCiclos();
});
