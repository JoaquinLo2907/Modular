// assets/js/editar_docente.js
document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('formEditarProfesor');
  form.addEventListener('submit', function(e) {
    e.preventDefault();           // ¡muy importante!
    e.stopImmediatePropagation();  

    const data = new FormData(this);
    fetch(this.action, {
      method: 'POST',
      body: data
    })
    .then(res => {
      // Si status >=400, seguimos parseando para leer el mensaje de error
      return res.json().then(payload => {
        if (!res.ok) throw new Error(payload.message || 'Error en la petición');
        return payload;
      });
    })
    .then(response => {
      // response.success === true aquí
      Swal.fire({
        toast: true,
        position: 'top-end',
        icon: 'success',
        title: response.message || 'Docente actualizado',
        showConfirmButton: false,
        timer: 2000,
      });

      // ejemplo: cerrar modal y recargar tabla
      $('#formEditarProfesor').closest('.modal').modal('hide');
      if (typeof cargarDocentes === 'function') cargarDocentes();
    })
    .catch(err => {
      // aquí caen errores de HTTP o de red
      Swal.fire({
        toast: true,
        position: 'top-end',
        icon: 'error',
        title: err.message.includes('conectar') 
               ? 'No se pudo conectar con el servidor' 
               : err.message,
        showConfirmButton: false,
        timer: 2000,
      });
    });
  });
});
