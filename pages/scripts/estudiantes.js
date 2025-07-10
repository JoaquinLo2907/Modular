document.addEventListener("DOMContentLoaded", () => {
  // 1. Cargar estudiantes y agregarlos a la tabla
  fetch('../../pages/php/obtener_estudiante.php')
    .then(res => res.json())
    .then(estudiantes => {
      const tbody = document.querySelector("#tabla-estudiantes tbody");

      if (!tbody) {
        console.error("No se encontró el tbody de la tabla");
        return;
      }

      estudiantes.forEach(est => {
        const tr = document.createElement("tr");
        tr.innerHTML = `
          <td>${est.nombre}</td>
          <td>${est.apellido}</td>
          <td>${est.grado}</td>
          <td>${est.grupo}</td>
          <td>
            <button class="btn btn-info btn-sm ver-calificaciones" 
              data-id="${est.estudiante_id}" 
              data-nombre="${est.nombre} ${est.apellido}">
              Ver Calificaciones
            </button>
          </td>
        `;
        tbody.appendChild(tr);
      });
    })
    .catch(err => {
      console.error("Error al cargar estudiantes:", err);
    });

  // 2. Escuchar los clics en el botón de calificaciones (de forma global)
  document.addEventListener("click", function (e) {
    if (e.target.classList.contains("ver-calificaciones")) {
      const id = e.target.dataset.id;
      const nombre = e.target.dataset.nombre;

      // Mostrar nombre del alumno en el modal
      document.getElementById("nombreAlumno").textContent = nombre;

      // 3. Obtener calificaciones
      
      fetch(`../../pages/php/obtener_calificaciones.php?id=${id}`)
        
        .then(res => res.json())
        .then(data => {
          const tbody = document.getElementById("tablaCalificaciones");
          tbody.innerHTML = "";

          if (!Array.isArray(data) || data.length === 0) {
            tbody.innerHTML = `<tr><td colspan="2">No hay calificaciones registradas.</td></tr>`;
            return;
          }

          data.forEach(item => {
            console.log("ID capturado:", id);

            const fila = document.createElement("tr");
            fila.innerHTML = `<td>${item.materia}</td><td>${item.calificacion}</td>`;
            tbody.appendChild(fila);
          });

          // Mostrar el modal
          $('#modalCalificaciones').modal('show');
        })
        .catch(() => {
          alert("Error al obtener calificaciones");
        });
    }
  });
});
