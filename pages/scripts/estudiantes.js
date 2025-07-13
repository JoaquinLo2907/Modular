document.addEventListener("DOMContentLoaded", () => {
  // Determinar la URL según el rol del usuario
  const url = (typeof userRol !== 'undefined' && userRol == 1)
    ? '../../pages/php/estudiantes-doc.php'
    : '../../pages/php/obtener_estudiante.php';

  // Comentamos todo lo que carga y dibuja estudiantes
  /*
  fetch(url)
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
  */

  // También eliminamos el listener de los botones
  /*
  document.addEventListener("click", function (e) {
    if (e.target.classList.contains("ver-calificaciones")) {
      const id = e.target.dataset.id;
      const nombre = e.target.dataset.nombre;

      document.getElementById("nombreAlumno").textContent = nombre;

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
            const fila = document.createElement("tr");
            fila.innerHTML = `<td>${item.materia}</td><td>${item.calificacion}</td>`;
            tbody.appendChild(fila);
          });

          $('#modalCalificaciones').modal('show');
        })
        .catch(() => {
          alert("Error al obtener calificaciones");
        });
    }
  });
  */
});
