document.addEventListener('DOMContentLoaded', function() {
    function cargarMaterias() {
        fetch('../php/obtener_materias.php')
            .then(response => response.json())
            .then(data => {
                const listaMaterias = document.getElementById('materias-lista');
                listaMaterias.innerHTML = '';

                if (!Array.isArray(data)) {
                    console.error('Los datos recibidos no son un arreglo válido');
                    return;
                }

                data.forEach(materia => {
                    const materiaElement = document.createElement('li');
                    materiaElement.classList.add('list-group-item', 'd-flex', 'justify-content-between');

                    materiaElement.innerHTML = `
                        <div>
                            <strong>${materia.nombre}</strong> - Nivel: ${materia.nivel_grado}
                        </div>
                        <div>
                            <button class="btn btn-warning btn-sm btn-editar" data-id="${materia.materia_id}">Editar</button>
                            <button class="btn btn-danger btn-sm btn-eliminar" data-id="${materia.materia_id}">Eliminar</button>
                        </div>
                    `;

                    listaMaterias.appendChild(materiaElement);
                });

                // Asignar eventos a botones de editar
                document.querySelectorAll('.btn-editar').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const id = this.dataset.id;
                        editarMateria(id);
                    });
                });

                // Asignar eventos a botones de eliminar
                document.querySelectorAll('.btn-eliminar').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const id = this.dataset.id;
                        eliminarMateria(id);
                    });
                });
            })
            .catch(error => {
                console.error('Error al cargar las materias:', error);
            });
    }

    // Función para editar materia
    function editarMateria(id) {
        // Hacer una solicitud para obtener los detalles de la materia
        fetch(`../php/obtener_materia_por_id.php?materia_id=${id}`)
            .then(response => response.json())
            .then(data => {
                if (data) {
                    // Rellenar el formulario con los datos de la materia
                    document.getElementById('materiaId').value = data.materia_id;
                    document.getElementById('materiaNombre').value = data.nombre;
                    document.getElementById('materiaNivel').value = data.nivel_grado;
                    
                    // Mostrar el modal
                    $('#editModal').modal('show');
                }
            })
            .catch(error => {
                console.error('Error al obtener la materia:', error);
            });
    }

    // Función para eliminar materia
    function eliminarMateria(id) {
        if (confirm("¿Estás seguro de eliminar esta materia?")) {
            fetch('../php/materias_controller.php', {
                method: 'POST',
                body: new URLSearchParams({
                    accion: 'eliminar',
                    materia_id: id
                })
            })
            .then(res => res.json())
            .then(response => {
                if (response.success) {
                    alert('Materia eliminada con éxito');
                    cargarMaterias();
                } else {
                    alert('Error al eliminar: ' + response.error);
                }
            })
            .catch(error => console.error('Error en la solicitud:', error));
        }
    }

    // Llamar la función al cargar la página
    cargarMaterias();

    // Manejar la edición de materia
    const editForm = document.getElementById('editForm');
    editForm.addEventListener('submit', function(event) {
        event.preventDefault();

        const materiaId = document.getElementById('materiaId').value;
        const nombre = document.getElementById('materiaNombre').value;
        const nivel = document.getElementById('materiaNivel').value;

        // Realizar la solicitud para actualizar la materia
        fetch('../php/materias_controller.php', {
            method: 'POST',
            body: new URLSearchParams({
                accion: 'editar',
                materia_id: materiaId,
                nombre: nombre,
                nivel_grado: nivel
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Materia editada con éxito');
                $('#editModal').modal('hide'); // Cerrar el modal
                cargarMaterias(); // Recargar las materias
            } else {
                alert('Error al editar la materia: ' + data.error);
            }
        })
        .catch(error => {
            console.error('Error en la solicitud de edición:', error);
        });
    });
});
