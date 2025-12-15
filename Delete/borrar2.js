document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("form-eliminar");

    form.addEventListener("submit", function (e) {
        e.preventDefault(); // Evita que el formulario se envíe inmediatamente

        Swal.fire({
            title: '¿Estás seguro?',
            text: "Esta acción no se puede deshacer.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit(); // Envía el formulario solo si confirma
            }
        });
    });
});
