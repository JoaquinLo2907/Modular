$(document).ready(function() {
  let table;

  // 1) Poblar el SELECT de periodos (periodo_id & nombre)
  function cargaPeriodos() {
    return $.getJSON('../php/listar_periodos.php', function(periodos) {
      const $sel = $('#sel_periodo').empty();
      periodos.forEach(p => {
        $sel.append(`<option value="${p.periodo_id}">${p.nombre}</option>`);
      });
    });
  }

  // 2) Inicializar DataTable tras cargar los periodos
  cargaPeriodos().then(function() {
    table = $('#data-table-calificaciones').DataTable({
      ajax: {
        url: '../php/obtener_todas_calificaciones.php',
        data: function(d) {
          d.periodo_id = $('#sel_periodo').val();
        },
        dataSrc: ''
      },
      columns: [
        { data: 'alumno',       title: 'Alumno'      },
        { data: 'materia',      title: 'Materia'     },
        { data: 'calificacion', title: 'Calificación'},
        { data: 'periodo',      title: 'Periodo'     },
        {
          data: 'calificacion_id',
          title: 'Acciones',
          orderable: false,
          render: function(id) {
            return `
              <button class="btn btn-sm btn-warning btn-editar" data-id="${id}">
                <i class="fas fa-edit"></i>
              </button>
              <button class="btn btn-sm btn-danger btn-eliminar" data-id="${id}">
                <i class="fas fa-trash-alt"></i>
              </button>
            `;
          }
        }
      ],
      order: [[0, 'asc'], [1, 'asc']],
      responsive: true,
      language: {
        url: 'https://cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json'
      },
    });
  });

  // 3) Al cambiar el periodo, recarga la tabla
  $('#sel_periodo').on('change', function() {
    table.ajax.reload();
  });

  // 4) Nuevo Período
  $('#btnNuevoPeriodo').on('click', function() {
    $('#modalPeriodo').modal('show');
  });
  $('#formPeriodo').on('submit', function(e) {
    e.preventDefault();
    $.post('../php/crear_periodo.php', $(this).serialize(), function() {
      $('#modalPeriodo').modal('hide');
      cargaPeriodos().then(() => $('#sel_periodo').trigger('change'));
    });
  });

  // 5) Asignar Materia
  $('#btnAsignarMateria').on('click', function() {
    $.when(
      $.getJSON('../php/listar_estudiantes.php'),
      $.getJSON('../php/listar_materias.php')
    ).done(function(estudiantes, materias) {
      const $est = $('#asig_estudiante').empty();
      estudiantes[0].forEach(e =>
        $est.append(`<option value="${e.estudiante_id}">${e.alumno}</option>`)
      );
      const $mat = $('#asig_materia').empty();
      materias[0].forEach(m =>
        $mat.append(`<option value="${m.materia_id}">${m.nombre}</option>`)
      );
      $('#asig_periodo_id').val($('#sel_periodo').val());
      $('#modalAsignar').modal('show');
    });
  });
  $('#formAsignar').on('submit', function(e) {
    e.preventDefault();
    $.post('../php/asignar_materia.php', $(this).serialize(), function() {
      $('#modalAsignar').modal('hide');
      table.ajax.reload();
    });
  });

  // 6) Editar calificación
  $('#data-table-calificaciones tbody')
    .on('click', '.btn-editar', function() {
      const data = table.row($(this).closest('tr')).data();
      $('#edt_calif_id').val(data.calificacion_id);
      $('#edt_materia').val(data.materia);
      $('#edt_calificacion').val(data.calificacion === '—' ? '' : data.calificacion);
      $('#modalEditar').modal('show');
    });
  $('#formEditar').on('submit', function(e) {
    e.preventDefault();
    $.post('../php/actualizar_calificacion.php', $(this).serialize(), function() {
      $('#modalEditar').modal('hide');
      table.ajax.reload(null, false);
    });
  });

  // 7) Eliminar calificación
  $('#data-table-calificaciones tbody')
    .on('click', '.btn-eliminar', function() {
      const id = $(this).data('id');
      if (!confirm('¿Seguro que quieres quitar esta materia al alumno?')) return;
      $.post('../php/eliminar_calificacion.php', { calificacion_id: id }, function(resp) {
        if (resp.success) {
          table.ajax.reload(null, false);
        } else {
          alert('Error al eliminar: ' + resp.message);
        }
      }, 'json');
    });


});
