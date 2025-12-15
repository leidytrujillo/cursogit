<?php
require_once '../conexion.php'; 
require_once '../clases.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Validar campos
    if (!empty($_POST['correo']) && !empty($_POST['tipo_usuario'])) {

        $correo = $_POST['correo'];
        $tipo = $_POST['tipo_usuario'];

        $mensaje = "";
        $tipo_alerta = "error";

        // Eliminar según el tipo seleccionado
        if ($tipo == 'cliente') {
            $cliente = new Cliente("", "", "", "", "", ""); 
            if ($cliente->borrar($conexion, $correo)) {
                $mensaje = "Cliente y usuario eliminados correctamente.";
                $tipo_alerta = "success";
            } else {
                $mensaje = "No se encontró cliente con ese correo.";
            }

        } elseif ($tipo == 'vendedor') {
            $vendedor = new Vendedor("", "", "", "", "", ""); 
            if ($vendedor->borrar($conexion, $correo)) {
                $mensaje = "Vendedor y usuario eliminados correctamente.";
                $tipo_alerta = "success";
            } else {
                $mensaje = "No se encontró vendedor con ese correo.";
            }

        } elseif ($tipo == 'domiciliario') {
            $domiciliario = new Domiciliario("", "", "", "", "", ""); 
            if ($domiciliario->borrar($conexion, $correo)) {
                $mensaje = "Domiciliario y usuario eliminados correctamente.";
                $tipo_alerta = "success";
            } else {
                $mensaje = "No se encontró domiciliario con ese correo.";
            }

        } else {
            $mensaje = "Tipo de usuario no válido.";
        }

    } else {
        $mensaje = "Por favor, complete todos los campos.";
    }
} else {
    $mensaje = "Método de solicitud no permitido.";
    $tipo_alerta = "error";
}

// Mostrar alerta y redirigir
echo "
<!DOCTYPE html>
<html lang='es'>
<head>
    <meta charset='UTF-8'>
    <title>Resultado</title>
    <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
</head>
<body>
    <script>
        Swal.fire({
            icon: '$tipo_alerta',
            title: '$mensaje',
            showConfirmButton: false,
            timer: 5000
        });

        setTimeout(() => {
            window.location.href = '/menu.html';
        }, 5000);
    </script>
</body>
</html>";
?>
