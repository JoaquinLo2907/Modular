document.addEventListener("DOMContentLoaded", function () {
  const tabla = document.querySelector("#tabla-receipt tbody");
  if (!tabla) {
    console.error("No se encontró el tbody de la tabla de recibos.");
    return;
  }

  fetch("../../pages/php/obtener_pagos.php")
    .then(res => res.json())
    .then(data => {
      tabla.innerHTML = "";

      data.forEach((pago, index) => {
        const fila = document.createElement("tr");
        fila.innerHTML = `
          <td>${index + 1}</td>
          <td>${pago.tipo || 'General'}</td>
          <td>${pago.nombre} ${pago.apellido}</td>
          <td>
  <span class="badge badge-${pago.estado === 'pagado' ? 'success' : 'warning'}">
    ${pago.estado === 'pagado' ? 'Pagado' : 'Pendiente'}
  </span>
</td>


          <td>${pago.fecha_pago}</td>
          <td>#RCPT${pago.pago_id}</td>
          <td>$${parseFloat(pago.monto).toFixed(2)}</td>
        `;
        tabla.appendChild(fila);
      });
    })
    .catch(error => {
      console.error("Error al cargar recibos:", error);
    });
});
