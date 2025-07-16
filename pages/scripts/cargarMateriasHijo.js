document.addEventListener('DOMContentLoaded', () => {
  const accordion = document.getElementById('accordionHijos');

  fetch('../php/obtener_materias_hijo.php')
    .then(res => res.json())
    .then(json => {
      if (!json || !json.data) return;

      const hijosAgrupados = {};

      json.data.forEach(m => {
        const id = m.estudiante_id;
        const nombre = m.nombre_estudiante || m.nombre || 'Desconocido';
        const apellido = m.apellido_estudiante || m.apellido || '';

        if (!hijosAgrupados[id]) {
          hijosAgrupados[id] = {
            nombre: `${nombre} ${apellido}`,
            materias: []
          };
        }

        hijosAgrupados[id].materias.push(m);
      });

      accordion.innerHTML = ''; // Limpiar

      let index = 0;
      for (const [id, hijo] of Object.entries(hijosAgrupados)) {
        const collapseId = `collapseHijo${index}`;

        const materiasHtml = hijo.materias.map(m =>
          `<div class="materia-item d-flex justify-content-between align-items-start">
             <div>
               <h6><i class="fas fa-book mr-2"></i>${m.nombre_materia}</h6>
               <small class="text-muted"><i class="fas fa-layer-group mr-1"></i> Nivel: ${m.nivel_grado || '-'}</small><br>
               <small class="text-muted"><i class="fas fa-chalkboard mr-1"></i> Clase: ${m.grado}° ${m.grupo}</small>
             </div>
             <a href="materias-informacion.php?materia_id=${m.materia_id}" class="btn btn-outline-primary btn-sm ml-3">
               Ver más
             </a>
           </div>`
        ).join('');

        const card = `
          <div class="card hijo-card">
            <div class="card-header" id="heading${index}">
              <h5 class="mb-0">
                <button class="btn btn-link text-left w-100 collapsed"
                        type="button"
                        data-toggle="collapse" data-target="#${collapseId}" 
                        aria-expanded="${index === 0}" aria-controls="${collapseId}">
                  <span class="text-orange-custom">${hijo.nombre}</span>
                </button>
              </h5>
            </div>
            <div id="${collapseId}" class="collapse ${index === 0 ? 'show' : ''}" 
                 aria-labelledby="heading${index}" data-parent="#accordionHijos">
              <div class="card-body">
                ${materiasHtml}
              </div>
            </div>
          </div>
        `;

        accordion.innerHTML += card;
        index++;
      }
    })
    .catch(err => console.error('Error cargando materias:', err));
});