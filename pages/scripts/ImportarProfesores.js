// importarCSV.js
document.addEventListener('DOMContentLoaded', function() {
  const inputFile  = document.getElementById('archivoCSV');
  const spanNombre = document.getElementById('nombreArchivo');

  if (!inputFile || !spanNombre) {
    console.warn('No se encontró #archivoCSV o #nombreArchivo en el DOM');
    return;
  }

  inputFile.addEventListener('change', function() {
    const fileName = this.files.length
      ? this.files[0].name
      : 'Ningún archivo seleccionado';
    spanNombre.textContent = fileName;
  });
});
