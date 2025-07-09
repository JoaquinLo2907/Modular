// alertaImportacionAlumnos.js
document.addEventListener('DOMContentLoaded', () => {
  const params = new URLSearchParams(window.location.search);
  const imp    = params.get('import');
  if (!imp) return;

  const cnt = parseInt(params.get('count') || '0', 10);

  // Usamos un toast de SweetAlert2
  const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
  });

  if (imp === 'ok' && cnt > 0) {
    Toast.fire({
      icon: 'success',
      title: `Importación exitosa: se agregaron ${cnt} estudiante${cnt > 1 ? 's' : ''}.`
    });
  } else if (imp === 'none') {
    Toast.fire({
      icon: 'info',
      title: 'Importación Fallida: no se encontraron filas válidas para insertar.'
    });
  } else if (imp === 'error') {
    Toast.fire({
      icon: 'error',
      title: 'Ocurrió un error al intentar importar. Verifica el archivo e inténtalo de nuevo.'
    });
  }

  // Limpiamos la query string para que no vuelva a saltar al refrescar
  history.replaceState(null, '', window.location.pathname);
});
