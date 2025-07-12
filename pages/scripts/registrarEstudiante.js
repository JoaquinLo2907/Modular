document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('formEstudiante');

  form.addEventListener('submit', function(e) {
    e.preventDefault(); // nunca dejamos que el navegador haga validación nativa

    // 1) Validación HTML5 simplona
    if (!form.checkValidity()) {
      Swal.fire({
        icon: 'error',
        title: 'Corrige los campos',
        text: 'Rellena correctamente todos los campos obligatorios.',
        timer: 2000,
        showConfirmButton: false
      });
      return; // aquí salimos sin añadir clases ni inline feedback
    }

    // 2) Si llega aquí, todo válido: enviamos por AJAX
    const formData = new FormData(form);
    fetch('../php/registrar_estudiante.php', {
      method: 'POST',
      body: formData
    })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        Swal.fire({
          icon: 'success',
          title: '¡Estudiante registrado!',
          timer: 2000,
          showConfirmButton: false
        }).then(() => {
          form.reset();
          form.nombre.focus();
        });
      } else {
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: data.message || 'No se pudo registrar el estudiante.'
        });
      }
    })
    .catch(() => {
      Swal.fire({
        icon: 'error',
        title: 'Error de conexión',
        text: 'No se pudo conectar con el servidor.'
      });
    });
  });
});
