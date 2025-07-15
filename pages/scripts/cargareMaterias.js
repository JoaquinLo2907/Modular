document.addEventListener("DOMContentLoaded", () => {
  const contenedor = document.getElementById("materias-container");
  const buscarInput = document.getElementById("buscar-materia");
  const filtroSelect = document.getElementById("filtrar-nivel");
  let materias = [];
  let rolUsuario = null;

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
        wrapper.className = "col-12 col-sm-6 col-md-4 col-xl-2 mb-4";

        // Cambiar la ruta según el rol
  const rutaDetalle = `../courses/detalle-materia.php?id=${m.materia_id}`;


        wrapper.innerHTML = `
        <div class="card h-100 shadow-sm">
          <img
            src="../../${m.foto_url || 'assets/img/default.jpg'}"
            class="card-img-top"
            alt="${m.nombre}">
          <div class="card-body d-flex flex-column">
            <h5 class="card-title">${m.nombre}</h5>
            <span class="badge bg-primary mb-2">${m.nivel_grado}</span>
            <p class="card-text flex-grow-1">
              ${m.descripcion || 'Sin descripción'}
            </p>
            <a href="${rutaDetalle}" class="btn btn-outline-primary mt-auto">
              Ver detalle
            </a>
          </div>
        </div>
      `;
        contenedor.appendChild(wrapper);
      });
  }

  buscarInput.addEventListener("input", renderMaterias);
  filtroSelect.addEventListener("change", renderMaterias);

  fetch("../php/obtener_materias.php")
    .then(res => res.json())
    .then(data => {
      materias = data.materias;
      rolUsuario = data.rol;
      renderMaterias();
    })
    .catch(err => console.error("Error al cargar materias:", err));
});