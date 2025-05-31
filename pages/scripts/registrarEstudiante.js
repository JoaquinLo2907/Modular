document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('formEstudiante');

  form.addEventListener('submit', function (e) {
    e.preventDefault(); // Evita el envío clásico

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
          title: 'Estudiante registrado',
          text: 'Puedes agregar otro estudiante.',
          timer: 2500,
          showConfirmButton: false
        }).then(() => {
          form.reset();
          form.querySelector('[name="nombre"]').focus();
        });
      } else {
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: data.message || 'No se pudo registrar el estudiante.'
        });
      }
    })
    .catch(error => {
      console.error('Error en la solicitud:', error);
      Swal.fire({
        icon: 'error',
        title: 'Error de conexión',
        text: 'No se pudo conectar con el servidor.'
      });
    });
  });
});
