document.addEventListener('DOMContentLoaded', function () {
  function cargarDocentes() {
    fetch('../php/obtener_profesores.php')
      .then(response => response.json())
      .then(data => {
        const listaDocentes = document.getElementById('docentes-lista');
        listaDocentes.innerHTML = '';

        if (!Array.isArray(data)) {
          console.error('Los datos recibidos no son un arreglo válido');
          return;
        }

        data.forEach(docente => {
          const li = document.createElement('li');
          li.classList.add('list-group-item', 'd-flex', 'justify-content-between', 'align-items-center');

          li.innerHTML = `
  <div class="d-flex justify-content-between align-items-center flex-wrap w-100">
    <div class="d-flex align-items-center flex-grow-1">
      <i class="fa fa-user-circle fa-2x text-primary mr-3"></i>
      <div>
        <strong>${docente.nombre} ${docente.apellido}</strong> &nbsp;
        <small class="text-muted">(${docente.puesto})</small><br>
        <span class="text-muted">
          <i class="fa fa-envelope"></i> ${docente.correo} &nbsp;|&nbsp;
          <i class="fa fa-phone"></i> ${docente.telefono} &nbsp;|&nbsp;
          <strong>$${docente.salario}</strong>
        </span><br>
        <span class="text-muted">
          <i class="fa fa-map-marker-alt"></i> ${docente.direccion} &nbsp;|&nbsp;
          <i class="fa fa-birthday-cake"></i> ${docente.fecha_nacimiento}
        </span>
      </div>
    </div>
    <div class="text-right mt-2 mt-md-0">
      <button class="btn btn-sm btn-outline-warning btn-editar" data-id="${docente.docente_id}">
        <i class="fa fa-edit"></i> Editar
      </button>
      <button class="btn btn-sm btn-outline-danger btn-eliminar" data-id="${docente.docente_id}">
        <i class="fa fa-trash"></i> Eliminar
      </button>
    </div>
  </div>
`;



          listaDocentes.appendChild(li);
        });

        document.querySelectorAll('.btn-editar').forEach(btn => {
          btn.addEventListener('click', function () {
            const id = this.dataset.id;
            editarDocente(id);
          });
        });

        document.querySelectorAll('.btn-eliminar').forEach(btn => {
          btn.addEventListener('click', function () {
            const id = this.dataset.id;
            eliminarDocente(id);
          });
        });
      })
      .catch(error => {
        console.error('Error al cargar los docentes:', error);
      });
  }

function formatDate(dateString) {
  const date = new Date(dateString);
  const year = date.getFullYear();
  const month = String(date.getMonth() + 1).padStart(2, '0'); // Los meses son 0-indexados, así que sumamos 1
  const day = String(date.getDate()).padStart(2, '0');

  return `${year}-${month}-${day}`;
}


  
function editarDocente(id) {
  fetch(`../php/profesorId.php?id=${id}`)
    .then(response => response.json())
    .then(data => {
      if (data) {
        console.log("Fecha de nacimiento recibida:", data.fecha_nacimiento); // Verifica la fecha recibida

        // ✅ Define la variable antes de usarla
        const fechaNacimiento = data.fecha_nacimiento;
        console.log("Fecha a enviar:", fechaNacimiento);

        // Rellenamos todos los campos del formulario
        document.getElementById('edit-docente-id').value = data.docente_id;
        document.getElementById('edit-nombre').value = data.nombre;
        document.getElementById('edit-apellido').value = data.apellido;
        document.getElementById('edit-telefono').value = data.telefono;
        document.getElementById('edit-correo').value = data.correo;
        document.getElementById('edit-activo').value = data.activo;
        document.getElementById('edit-puesto').value = data.puesto;
        document.getElementById('edit-genero').value = data.genero;

        document.getElementById('edit-fecha-nacimiento').value = fechaNacimiento;

        document.getElementById('edit-salario').value = data.salario;
        document.getElementById('edit-direccion').value = data.direccion;
        document.getElementById('edit-foto').value = data.foto_url;
        document.getElementById('edit-creado-en').value = data.creado_en;
        document.getElementById('edit-actualizado-en').value = data.actualizado_en;

        // Mostrar el modal
        $('#modalEditarProfesor').modal('show');
      }
    })
    .catch(error => {
      console.error('Error al obtener los datos del docente:', error);
    });
}





function eliminarDocente(id) {
  if (confirm("¿Estás seguro de eliminar este docente?")) {
    fetch('../php/eliminar_docentes.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({ id: id }) // enviamos solo un ID individual
    })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        alert("Docente eliminado (lógicamente) con éxito.");
        cargarDocentes(); // vuelve a cargar la lista
      } else {
        alert("Error al eliminar: " + (data.message || "Desconocido."));
        
      }
    })
    .catch(error => {
      console.error("Error al eliminar:", error);
      alert("Hubo un problema al intentar eliminar el docente.");
    });
  }
}


const formEditar = document.getElementById('formEditarProfesor');
formEditar.addEventListener('submit', function(event) {
    event.preventDefault();

    const id = document.getElementById('edit-docente-id').value;
    const nombre = document.getElementById('edit-nombre').value;
    const apellido = document.getElementById('edit-apellido').value;
    const telefono = document.getElementById('edit-telefono').value;
    const correo = document.getElementById('edit-correo').value;
    const activo = document.getElementById('edit-activo').value;
    const puesto = document.getElementById('edit-puesto').value;
    const genero = document.getElementById('edit-genero').value;
    const fechaNacimiento = document.getElementById('edit-fecha-nacimiento').value;
    const salario = document.getElementById('edit-salario').value;
    const direccion = document.getElementById('edit-direccion').value;
    const fotoUrl = document.getElementById('edit-foto').value;

    const nuevaContraseña = document.getElementById('edit-password').value;
    const confirmarContraseña = document.getElementById('edit-password2').value;

    if (!fechaNacimiento) {
        alert("Por favor, ingresa una fecha de nacimiento.");
        return;
    }

    if ((nuevaContraseña || confirmarContraseña) && nuevaContraseña !== confirmarContraseña) {
        alert("Las contraseñas no coinciden.");
        return;
    }

    const datos = {
        id,
        nombre,
        apellido,
        telefono,
        correo,
        activo,
        puesto,
        genero,
        fecha_nacimiento: fechaNacimiento,
        salario,
        direccion,
        foto_url: fotoUrl
    };

    if (nuevaContraseña && confirmarContraseña) {
        datos.nueva_contraseña = nuevaContraseña;
        datos.confirmar_contraseña = confirmarContraseña;
    }

    fetch('../php/editar_docentes.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(datos)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert("Docente actualizado correctamente.");
            $('#modalEditarProfesor').modal('hide');
            cargarDocentes();
        } else {
            alert("Hubo un error al actualizar el docente: " + (data.message || ""));
        }
    })
    .catch(error => {
        console.error('Error al enviar los datos:', error);
    });
});


cargarDocentes();
});
