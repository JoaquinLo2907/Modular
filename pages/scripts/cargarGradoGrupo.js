document.addEventListener("DOMContentLoaded", function () {
  const gradoSelect = document.getElementById("grado");
  const grupoSelect = document.getElementById("grupo");

  if (!gradoSelect || !grupoSelect) {
    console.error("No se encontraron los selects de grado o grupo.");
    return;
  }

  // Genera grados del 1 al 12
  const grados = Array.from({ length: 12 }, (_, i) => (i + 1).toString());
  const grupos = ["A", "B", "C", "D"];

  gradoSelect.innerHTML = '<option value="">Seleccione un grado</option>';
  grados.forEach(grado => {
    const option = document.createElement("option");
    option.value = grado;
    option.textContent = grado;
    gradoSelect.appendChild(option);
  });

  grupoSelect.innerHTML = '<option value="">Seleccione un grupo</option>';
  grupos.forEach(grupo => {
    const option = document.createElement("option");
    option.value = grupo;
    option.textContent = grupo;
    grupoSelect.appendChild(option);
  });
});
