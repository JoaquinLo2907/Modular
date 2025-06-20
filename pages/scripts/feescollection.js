document.addEventListener("DOMContentLoaded", function () {
  fetch("../../pages/php/obtener_pagos.php")
    .then(response => response.json())
    .then(data => {
      const tbody = document.querySelector("#tabla-fees tbody");
      if (!tbody) {
        console.error("No se encontró el tbody de la tabla");
        return;
      }
      tbody.innerHTML = "";

      if (!Array.isArray(data)) {
        console.error("Respuesta no válida:", data);
        return;
      }

      data.forEach(fee => {
        const fila = document.createElement("tr");
        fila.innerHTML = `
          <td>${fee.pago_id}</td>
          <td>${fee.nombre} ${fee.apellido}</td>
          <td>${fee.grado}</td>
          <td>${fee.grupo}</td>
          <td>$${parseFloat(fee.monto).toFixed(2)}</td>
          <td>${fee.fecha_pago}</td>
          <td>${fee.fecha_vencimiento}</td>
          <td><span class="badge badge-${fee.estado === "pagado" ? "success" : "warning"}">${fee.estado}</span></td>
          <td>${fee.creado_en}</td>
          <td>${fee.actualizado_en}</td>
          <td>
            <button class="btn btn-sm btn-outline-primary actualizar-btn" data-id="${fee.pago_id}" data-estado="${fee.estado}">Cambiar</button>
          </td>
        `;
        tbody.appendChild(fila);
      });

      const tabla = $("#tabla-fees").DataTable({
        pageLength: 50,
        orderCellsTop: true,
        fixedHeader: true,
        language: {
          url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
        }
      });

      $('#tabla-fees thead tr:eq(1) th').each(function (i) {
        const input = $(this).find("input, select");
        if (input.length) {
          input.on('keyup change', function () {
            if (tabla.column(i).search() !== this.value) {
              tabla.column(i).search(this.value).draw();
            }
          });
        }
      });
    })
    .catch(error => {
      console.error("Error al cargar fees:", error);
    });

  document.addEventListener("click", function (e) {
    if (e.target.classList.contains("actualizar-btn")) {
      const pagoId = e.target.dataset.id;
      const estadoActual = e.target.dataset.estado;

      document.getElementById("modal-pago-id").value = pagoId;
      document.getElementById("modal-estado").value = estadoActual;

      $("#modalActualizarEstado").modal("show");
    }
  });

  const btnGuardar = document.getElementById("btnGuardarEstado");
  if (btnGuardar) {
    btnGuardar.addEventListener("click", () => {
      const pagoId = document.getElementById("modal-pago-id").value;
      const nuevoEstado = document.getElementById("modal-estado").value;

      fetch("../../pages/php/actualizar_estado_pago.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ pago_id: pagoId, estado: nuevoEstado })
      })
        .then(res => res.json())
        .then(data => {
          if (data.success) {
            $("#modalActualizarEstado").modal("hide");
            const alerta = document.createElement("div");
            alerta.className = "alert alert-success text-center mt-3";
            alerta.textContent = "✔️ Estado actualizado exitosamente.";
            const wrapper = document.querySelector(".ms-content-wrapper .card-body") || document.body;
            wrapper.prepend(alerta);
            setTimeout(() => location.reload(), 1500);
          } else {
            Swal.fire("Error", data.message || "No se pudo actualizar.", "error");
          }
        })
        .catch(() => {
          Swal.fire("Error", "Error de red o servidor.", "error");
        });
    });
  }
});
