document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('registerForm');
  form.addEventListener('submit', function(e) {
    e.preventDefault(); // evita recarga
    const data = new FormData(this);

    fetch(this.action, {
      method: 'POST',
      body: data
    })
    .then(res => res.text())
    .then(msg => {
      // Detecta si el mensaje contiene la palabra "error" (puedes ajustarlo según tu PHP)
      const isError = msg.toLowerCase().includes('error');
      Swal.fire({
        toast: true,
        position: 'top-end',
        icon: isError ? 'error' : 'success',
        title: msg,
        showConfirmButton: false,
        timer: 2500,
      });
      if (!isError) form.reset();
    })
    .catch(() => {
      Swal.fire({
        toast: true,
        position: 'top-end',
        icon: 'error',
        title: 'No se pudo conectar con el servidor',
        showConfirmButton: false,
        timer: 2500,
      });
    });
  });
});
