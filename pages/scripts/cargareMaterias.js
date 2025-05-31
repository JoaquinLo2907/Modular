document.addEventListener("DOMContentLoaded", function () {
  const materiasContainer = document.querySelector("#materias-container");

  fetch("../php/obtener_materias.php")
    .then(res => res.json())
    .then(data => {
      materiasContainer.innerHTML = '';

      data.forEach(materia => {
        const tarjeta = document.createElement("div");
        tarjeta.classList.add("col-lg-4", "col-md-6", "col-sm-12", "mb-4");

        tarjeta.innerHTML = `
          <div class="card h-100">
            <img src="../../${materia.foto_url || 'assets/img/default.jpg'}" class="card-img-top" alt="${materia.nombre}" style="max-height: 200px; object-fit: cover;">
            <div class="card-body">
              <h5 class="card-title">${materia.nombre}</h5>
              <p class="card-text"><strong>Nivel:</strong> ${materia.nivel_grado}</p>
              <p class="card-text"><strong>Descripción:</strong> ${materia.descripcion || 'Sin descripción'}</p>
            </div>
          </div>
        `;

        materiasContainer.appendChild(tarjeta);
      });
    })
    .catch(err => console.error("Error al cargar las materias:", err));
});
