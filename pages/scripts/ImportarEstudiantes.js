// importarCSV.js
document.addEventListener('DOMContentLoaded', function() {
  const inputFile = document.getElementById('csvEstudiantes');
  if (!inputFile) return; // Si no está el input, salimos

  inputFile.addEventListener('change', function() {
    // Si en el futuro quieres mostrar el nombre en pantalla, aquí lo recogerías:
    console.log('Archivo seleccionado:', this.files[0]?.name ?? 'Ningún archivo seleccionado');
  });
});
