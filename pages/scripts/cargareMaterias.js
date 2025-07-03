document.addEventListener("DOMContentLoaded", () => {
  const contenedor = document.getElementById("materias-container");
  const buscarInput = document.getElementById("buscar-materia");
  const filtroSelect = document.getElementById("filtrar-nivel");
  let materias = [];

  // 1) Renderiza las tarjetas según estado de búsqueda/filtro
  function renderMaterias() {
    const texto = buscarInput.value.toLowerCase();
    const nivel = filtroSelect.value;
    contenedor.innerHTML = "";

    materias
      .filter(m =>
        m.nombre.toLowerCase().includes(texto) &&
        (nivel === "" || m.nivel_grado === nivel)
      )
      .forEach(m => {
        const wrapper = document.createElement("div");
        wrapper.classList.add("col");             // row-cols se encarga de la cantidad de columnas

        wrapper.innerHTML = `
          <div class="card h-100 shadow-sm">
            <img src="../../${m.foto_url || 'assets/img/default.jpg'}"
                 class="card-img-top"
                 alt="${m.nombre}">
            <div class="card-body d-flex flex-column">
              <h5 class="card-title">${m.nombre}</h5>
              <span class="badge bg-primary mb-2">${m.nivel_grado}</span>
              <p class="card-text flex-grow-1">
                ${m.descripcion || 'Sin descripción'}
              </p>
              <a href="detalle-materia.php?id=${m.materia_id}"
                 class="btn btn-outline-primary mt-auto">
                Ver detalle
              </a>
            </div>
          </div>
        `;
        contenedor.appendChild(wrapper);
      });
  }

  // 2) Eventos de búsqueda y filtro
  buscarInput.addEventListener("input", renderMaterias);
  filtroSelect.addEventListener("change", renderMaterias);

  // 3) Carga inicial de datos
  fetch("../php/obtener_materias.php")
    .then(res => res.json())
    .then(data => {
      materias = data;
      renderMaterias();
    })
    .catch(err => console.error("Error al cargar materias:", err));
});
