        // Manejador de cambio de rol
document.addEventListener("DOMContentLoaded", function () {
    let rolSelect = document.getElementById("rol");
    if (rolSelect) {
        rolSelect.addEventListener("change", function () {
            let rolSeleccionado = rolSelect.value;
            window.location.href = "actualizar.php?rol=" + rolSeleccionado;
        });
    }

    // Mostrar alerta si hubo actualización exitosa
    if (localStorage.getItem("actualizacionExitosa") === "true") {
        swal({
            title: "¡Usuario actualizado con éxito!",
            text: "Redirigiendo al menú en 5 segundos...",
            icon: "success",
            button: "Aceptar",
            closeOnClickOutside: false,
            closeOnEsc: false
        });

        // Limpiar el indicador y redirigir después de 5 segundos
        localStorage.removeItem("actualizacionExitosa");

        setTimeout(() => {
            window.location.href = "/menu.html";
        }, 5000);
    }
});
