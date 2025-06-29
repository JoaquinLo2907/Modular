document.addEventListener("DOMContentLoaded", function () {
  fetch("../php/estudiantes.php")
    .then(response => response.json())
    .then(data => {
      const select = document.getElementById("estudianteSelect");
      select.innerHTML = ""; // Limpiar opciones anteriores

      // ✅ Agregar la opción por defecto
      const defaultOption = document.createElement("option");
      defaultOption.value = "";
      defaultOption.textContent = "Selecciona un estudiante";
      defaultOption.disabled = true;
      defaultOption.selected = true;
      select.appendChild(defaultOption);

      // ✅ Agregar estudiantes si hay datos
      if (Array.isArray(data) && data.length > 0) {
        data.forEach(estudiante => {
          const option = document.createElement("option");
          option.value = estudiante.estudiante_id;
          option.textContent = `${estudiante.nombre} ${estudiante.apellido} - ${estudiante.grado}°${estudiante.grupo}`;
          select.appendChild(option);
        });
      } else {
        const option = document.createElement("option");
        option.textContent = "No hay estudiantes disponibles";
        option.disabled = true;
        select.appendChild(option);
      }
    })
    .catch(error => {
      console.error("Error al cargar estudiantes:", error);
      const select = document.getElementById("estudianteSelect");
      select.innerHTML = "<option>Error al cargar</option>";
    });
});
