document.addEventListener("DOMContentLoaded", function () {
    cargarDocentes();
});

function cargarDocentes() {
    fetch("../php/obtener_profesores.php")
        .then(response => {
            if (!response.ok) {
                throw new Error("Error en la respuesta del servidor");
            }
            return response.json();
        })
        .then(data => {
            const contenedor = document.getElementById("contenedor-profesores");
            contenedor.innerHTML = ""; // Limpiamos el contenedor por si acaso

            // Ruta base de tus imágenes en el servidor local
           const rutaBase = 'http://localhost/dashboard/Modular/';

            // Recorremos cada docente y generamos las tarjetas
            data.forEach(docente => {
                console.log(docente.foto_url); // Para verificar qué ruta te está llegando

                // Verificamos si la URL de la imagen ya tiene la ruta base. Si no, concatenamos.
                let foto = docente.foto_url;

                if (foto && !foto.startsWith('http')) {
                    // Si la foto no tiene 'http', se considera una ruta relativa
                    foto = rutaBase + foto; // Concatenamos correctamente la ruta base
                }

                // Usamos una imagen por defecto si no hay foto disponible
                foto = foto || 'https://via.placeholder.com/100';

                // Creamos la tarjeta para cada docente
                const card = document.createElement("div");
                card.classList.add("col-md-4", "mb-4");

                card.innerHTML = `
                    <div class="card h-100 shadow-sm">
                        <img src="${foto}" alt="${docente.nombre} ${docente.apellido}" class="card-img-top rounded-circle" style="width: 100px; height: 100px; object-fit: cover; margin: 0 auto; margin-top: 10px;">
                        <div class="card-body text-center">
                            <h5 class="card-title">${docente.nombre} ${docente.apellido}</h5>
                            <h6 class="card-subtitle mb-2 text-muted">ID: ${docente.docente_id}</h6>
                            <p class="card-text">
                                <strong>Teléfono:</strong> ${docente.telefono}<br>
                                <strong>Correo:</strong> ${docente.correo}<br>
                                <strong>Puesto:</strong> ${docente.puesto}
                            </p>
                            <a href="mailto:${docente.correo}" class="btn btn-info">Contactar</a>
                        </div>
                    </div>
                `;

                // Añadimos la tarjeta al contenedor
                contenedor.appendChild(card);
            });
        })
        .catch(error => {
            console.error("Error al cargar los datos:", error);
        });
}
