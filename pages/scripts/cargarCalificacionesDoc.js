$(document).ready(function () {
  let table;

  function cargarPeriodos() {
    return $.getJSON('../php/listar_periodos.php', function (periodos) {
      const $sel = $('#sel_periodo').empty();
      periodos.forEach(p => {
        $sel.append(`<option value="${p.periodo_id}">${p.nombre}</option>`);
      });
    });
  }

  cargarPeriodos().then(function () {
    table = $('#tabla-calificaciones-docente').DataTable({
      ajax: {
        url: '../php/obtener_calificaciones_docente.php',
        data: function (d) {
          d.periodo_id = $('#sel_periodo').val();
          console.log("📦 Enviando periodo_id a PHP:", d.periodo_id);
        },
        dataSrc: function (json) {
          console.log("📥 Datos recibidos de PHP:", json);
          return json;
        }
      },
      columns: [
        { data: 'alumno', title: 'Alumno' },
        { data: 'materia', title: 'Materia' },
        { data: 'calificacion1', title: 'Calificación 1' },
        { data: 'calificacion2', title: 'Calificación 2' },
        { data: 'calificacion3', title: 'Calificación 3' },
        { data: 'promedio', title: 'Promedio' },
        {
          data: 'calificacion_id',
          title: 'Acciones',
          orderable: false,
          render: function (id, type, row) {
            return `
              <button class="btn btn-sm btn-warning btn-editar"
                      data-id="${id}"
                      data-materia="${row.materia}"
                      data-cal1="${row.calificacion1}"
                      data-cal2="${row.calificacion2}"
                      data-cal3="${row.calificacion3}"
                      data-estudiante="${row.estudiante_id}"
                      data-materia-id="${row.materia_id}">
                <i class="fas fa-edit"></i>
              </button>
            `;
          }
        }
      ],
      responsive: true,
      language: {
        url: 'https://cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json'
      }
    });
  });

  $('#sel_periodo').on('change', function () {
    table.ajax.reload();
  });

  $('#tabla-calificaciones-docente tbody').on('click', '.btn-editar', function () {
    $('#edit_id').val($(this).data('id'));
    $('#edit_materia').val($(this).data('materia'));
    $('#edit_calificacion1').val($(this).data('cal1'));
    $('#edit_calificacion2').val($(this).data('cal2'));
    $('#edit_calificacion3').val($(this).data('cal3'));
    $('#edit_estudiante').val($(this).data('estudiante'));
    $('#edit_materia_id').val($(this).data('materia-id'));
    $('#edit_periodo').val($('#sel_periodo').val());

    $('#modalEditar').modal('show');
  });

  $('#formEditar').on('submit', function (e) {
    e.preventDefault();
    $.post('../php/actualizar_calificacionDoc.php', $(this).serialize(), function () {
      $('#modalEditar').modal('hide');
      table.ajax.reload(null, false);
    });
  });
});
