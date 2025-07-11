// scripts/registrarTutor.js
document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('registerForm');

  // Expresiones regulares y reglas
  const nameRegex   = /^[A-Za-zÁÉÍÓÚÑáéíóúñ ]{2,}$/;
  const phoneRegex  = /^\d{3}-\d{3}-\d{4}$/;
  const emailRegex  = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

  const rules = {
    nombre: {
      test: v => nameRegex.test(v),
      msg: 'Nombre inválido (mín. 2 letras).'
    },
    apellido: {
      test: v => nameRegex.test(v),
      msg: 'Apellido inválido (mín. 2 letras).'
    },
    telefono: {
      test: v => phoneRegex.test(v),
      msg: 'Teléfono inválido (formato 555-000-0000).'
    },
    correo: {
      test: v => emailRegex.test(v),
      msg: 'Correo inválido.'
    },
    estudiante_id: {
      test: v => v !== '',
      msg: 'Selecciona un estudiante.'
    },
    direccion: {
      test: v => v.trim().length >= 5,
      msg: 'Dirección muy corta (min. 5 caracteres).'
    },
    password: {
      test: v => v.length >= 8,
      msg: 'Contraseña mín. 8 caracteres.'
    },
    password2: {
      test: (v, f) => v === f.password.value,
      msg: 'Las contraseñas no coinciden.'
    }
  };

  form.addEventListener('submit', e => {
    e.preventDefault();

    // Limpia estados previos
    Object.keys(rules).forEach(name => {
      form.elements[name].classList.remove('is-invalid');
    });

    // Recorre reglas
    let firstInvalid = null;
    for (let name in rules) {
      const fld = form.elements[name];
      const val = fld.value.trim();
      if (!rules[name].test(val, form.elements)) {
        fld.classList.add('is-invalid');
        if (!firstInvalid) firstInvalid = fld;
      }
    }

    if (firstInvalid) {
      firstInvalid.focus();
      Swal.fire({
        toast: true,
        position: 'top-end',
        icon: 'error',
        title: rules[firstInvalid.name].msg,
        showConfirmButton: false,
        timer: 2000
      });
    } else {
      form.submit();
    }
  });
});
