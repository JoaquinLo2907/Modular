document.addEventListener("DOMContentLoaded", function () {
  const formIndividual = document.querySelector("form[action*='agregar_pago.php']");
  const formMasivo = document.querySelector("form[action*='agregar_pago_masivo.php']");

  if (formIndividual) {
    formIndividual.addEventListener("submit", function (e) {
      e.preventDefault();
      const formData = new FormData(formIndividual);

      fetch("../../pages/php/agregar_pago.php", {
        method: "POST",
        body: formData
      })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            mostrarNotificacion("✅ Pago individual agregado exitosamente.", "success", formIndividual);
            formIndividual.reset();
          } else {
            mostrarNotificacion("❌ Error al agregar el pago individual.", "danger", formIndividual);
          }
        })
        .catch(error => {
          console.error("Error AJAX:", error);
          mostrarNotificacion("❌ Error de red o del servidor.", "danger", formIndividual);
        });
    });
  }

  if (formMasivo) {
    formMasivo.addEventListener("submit", function (e) {
      e.preventDefault();
      const formData = new FormData(formMasivo);

      fetch("../../pages/php/agregar_pago_masivo.php", {
        method: "POST",
        body: formData
      })
        .then(res => res.json())
        .then(data => {
          if (data.success) {
            mostrarNotificacion("✅ Pagos masivos asignados correctamente.", "success", formMasivo);
            formMasivo.reset();
          } else {
            mostrarNotificacion("❌ Error: " + (data.message || "No se pudo completar la operación."), "danger", formMasivo);
          }
        })
        .catch(error => {
          console.error("Error AJAX:", error);
          mostrarNotificacion("❌ Error de conexión con el servidor.", "danger", formMasivo);
        });
    });
  }

  function mostrarNotificacion(mensaje, tipo, formRef) {
    const alertDiv = document.createElement("div");
    alertDiv.className = `alert alert-${tipo}`;
    alertDiv.textContent = mensaje;
    alertDiv.style.marginTop = "1rem";
    alertDiv.style.textAlign = "center";

    const wrapper = formRef.closest(".ms-panel-body");
    if (wrapper) {
      wrapper.prepend(alertDiv);
      setTimeout(() => alertDiv.remove(), 5000);
    }
  }
});
