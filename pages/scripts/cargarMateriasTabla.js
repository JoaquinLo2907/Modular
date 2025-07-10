document.addEventListener("click", function (e) {
  if (e.target.classList.contains("ver-detalles")) {
    const materiaId = e.target.dataset.id;

    // Aquí podrías hacer un fetch real si tienes más datos
    fetch(`../../pages/php/obtener_materia_detalle.php?materia_id=${materiaId}`)
      .then(res => res.json())
      .then(materia => {
        // Rellenar campos del modal
        document.getElementById("detalleNombreMateria").textContent = materia.nombre;
        document.getElementById("detalleNivelMateria").textContent = materia.nivel_grado;
        document.getElementById("detalleCicloMateria").textContent = materia.ciclo || "2024-2025";

        const tbodyGrupos = document.getElementById("tablaGruposMateria");
        tbodyGrupos.innerHTML = "";

        materia.grupos.forEach(grupo => {
          const fila = document.createElement("tr");
          fila.innerHTML = `
            <td>${grupo.grado}</td>
            <td>${grupo.grupo}</td>
            <td><button class="btn btn-outline-info btn-sm">Ver Calificaciones</button></td>
          `;
          tbodyGrupos.appendChild(fila);
        });

        $("#modalDetallesMateria").modal("show");
      })
      .catch(err => {
        console.error("Error al cargar detalles de la materia", err);
      });
  }
});
