document.addEventListener('DOMContentLoaded', function () {
  fetch('../php/tutores_opciones.php')
    .then(res => res.json())
    .then(data => {
      const select = document.getElementById('tutorSelect');
      let options = '<option value="">Seleccione un tutor</option>';
      data.forEach(tutor => {
        options += `<option value="${tutor.tutor_id}">${tutor.nombre} ${tutor.apellido}</option>`;
      });
      select.innerHTML = options;
    })
    .catch(err => {
      console.error("Error al cargar tutores:", err);
      alert("No se pudieron cargar los tutores.");
    });
});
