// alertaImportacionProfesores.js
document.addEventListener('DOMContentLoaded', () => {
  const params = new URLSearchParams(window.location.search);
  const imp    = params.get('import');
  if (!imp) return;

  const cnt = parseInt(params.get('count') || '0', 10);
  const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
  });

  if (imp === 'profok' && cnt > 0) {
    Toast.fire({
      icon: 'success',
      title: `Importación exitosa: se agregaron ${cnt} docente${cnt>1?'s':''}.`
    });
  } else if (imp === 'profnone') {
    Toast.fire({
      icon: 'info',
      title: 'Importación completada: no se encontraron filas válidas.'
    });
  } else {
    Toast.fire({
      icon: 'error',
      title: 'Error al importar docentes. Verifica el archivo e inténtalo de nuevo.'
    });
  }

  history.replaceState(null, '', window.location.pathname);
});
