<?php

include '../conexion.php';
include '../clases.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombres = $_POST['nombres'];
    $apellidos = $_POST['apellidos'];
    $documento = $_POST['documento'];
    $telefono = $_POST['telefono'];
    $correo = $_POST['correo'];
    $contrasena = $_POST['contrasena'];
    $rol = $_POST['rol'];

    $contrasenaEncrip = password_hash($contrasena, PASSWORD_DEFAULT);

    try {
        $usuarios = crearUsuarios($rol, $nombres, $apellidos, $documento, $telefono, $correo, $contrasenaEncrip);
        $usuarios->guardar($conexion);

        echo "<link rel='stylesheet' href='procesar.css'>";
        echo '<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>';
        echo '<script>
                document.addEventListener("DOMContentLoaded", function() {
                    swal({
                        title: "¡Usuario creado con éxito!",
                        text: "Te has registrado como ' . ucfirst($rol) . '. Redirigiendo al menú en 3 segundos...",
                        icon: "success",
                        button: "Aceptar",
                        closeOnClickOutside: false,
                        closeOnEsc: false
                    });

                    setTimeout(function() {
                        window.location.href = "/menu.html";
                    }, 3000);
                });
              </script>';
        exit;

    } catch (Exception $e) {
        echo "<link rel='stylesheet' href='procesar.css'>";
        echo '<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>';
        echo '<script>
                document.addEventListener("DOMContentLoaded", function() {
                    swal({
                        title: "¡Error al crear el usuario!",
                        text: "Verifica los datos ingresados. ' . $e->getMessage() . '",
                        icon: "error",
                        button: "Aceptar",
                        closeOnClickOutside: false,
                        closeOnEsc: false
                    });

                    setTimeout(function() {
                        window.location.href = "/menu.html";
                    }, 3000);
                });
              </script>';
    }
}
?>
