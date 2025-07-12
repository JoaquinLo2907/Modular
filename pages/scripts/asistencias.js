document.addEventListener("DOMContentLoaded", () => {
  const materiaSelect = document.getElementById("materia-select");
  const tbody = document.getElementById("asistencias-body");
  const form = document.getElementById("form-asistencias");

  // 1. Cargar materias del docente
  fetch("../php/materias-docente.php")
    .then(res => res.json())
    .then(materias => {
      materias.forEach(mat => {
        const option = document.createElement("option");
        option.value = mat.materia_id;
        option.textContent = `${mat.nombre} (${mat.nivel_grado}, Ciclo ${mat.ciclo}) - ID ${mat.materia_id}`;



        materiaSelect.appendChild(option);
      });
    });

  // 2. Cuando el docente selecciona una materia
  materiaSelect.addEventListener("change", () => {
    const materiaId = materiaSelect.value;

    if (!materiaId) return;

    // Limpiar tabla
    tbody.innerHTML = "";

    // Cargar estudiantes de esa materia
    fetch(`../php/estudiantes-materia.php?materia_id=${materiaId}`)
      .then(res => res.json())
      .then(estudiantes => {
        if (!Array.isArray(estudiantes)) {
          console.error("Error: respuesta no es array:", estudiantes);
          return;
        }

        estudiantes.forEach(est => {
          const tr = document.createElement("tr");
          tr.innerHTML = `
            <td>${est.nombre}</td>
            <td>${est.apellido}</td>
            <td>
              <select class="form-control asistencia-select" data-id="${est.estudiante_id}">
                <option value="presente">Presente</option>
                <option value="ausente">Ausente</option>
              </select>
            </td>
          `;
          tbody.appendChild(tr);
        });
      })
      .catch(err => {
        console.error("Error al cargar estudiantes:", err);
      });
  });

  // 3. Guardar asistencias
  if (form) {
    form.addEventListener("submit", function (e) {
      e.preventDefault();

      const materiaId = materiaSelect.value;
      if (!materiaId) {
        Swal.fire("Atención", "Debes seleccionar una materia", "warning");
        return;
      }

      const asistencias = [];

      document.querySelectorAll(".asistencia-select").forEach(select => {
        const estudianteId = select.dataset.id;
        const valor = select.value;

        asistencias.push({
          estudiante_id: estudianteId,
          estado: valor
        });
      });

fetch('../../pages/php/guardar_asistencias.php', {
  method: 'POST',
  headers: { 'Content-Type': 'application/json' },
  body: JSON.stringify({ materia_id: materiaId, asistencias })
})
  .then(res => res.text()) // OBTENEMOS texto crudo primero
  .then(text => {
    console.log("Respuesta cruda del servidor:", text);
    const data = JSON.parse(text); // ahora sí parseamos
    if (data.success) {
      Swal.fire("Guardado", "Asistencias guardadas exitosamente", "success");
      document.querySelectorAll(".asistencia-select").forEach(select => {
        select.value = "presente";
      });
    } else {
      Swal.fire("Error", data.error || "No se pudo guardar", "error");
    }
  })
  .catch(err => {
    console.error("Error al guardar asistencias:", err);
  });


    });
  }
});
