<?php
// Incluir conexión a la base de datos
include '../conexion.php';

// Obtener el rol seleccionado a traves de la URL (si existe) y asignarlo a la variable $rol_seleccionado
$rol_seleccionado = isset($_GET['rol']) ? $_GET['rol'] : '';

// Consulta para obtener la lista de usuarios, filtrando por rol si se selecciona uno
$query = "SELECT id, nombres, apellidos FROM usuarios";
if (!empty($rol_seleccionado)) {
    $query .= " WHERE rol = '$rol_seleccionado'";
}

// Ejecutar la consulta y almacenar el resultado en la variable $resultado.
$resultado = $conexion->query($query);

// Inicializamos un array vacio para almacenar los usuarios obtenidos
$usuarios = [];

// Si se encuentran usuarios, se recorren y se agregan al array $usuarios.
if ($resultado->num_rows > 0) {
    while ($row = $resultado->fetch_assoc()) {
        $usuarios[] = $row;
    }
}

// Procesar la actualizacion cuando el formulario se envie
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST["id"];
    $nombres = $_POST["nombres"];
    $apellidos = $_POST["apellidos"];
    $documento = $_POST["documento"];
    $telefono = $_POST["telefono"];
    $correo = $_POST["correo"];
    $contrasena = !empty($_POST["contrasena"]) ? password_hash($_POST["contrasena"], PASSWORD_DEFAULT) : null;

    // Consulta para actualizar los datos del usuario en la base de datos.
    $updateQuery = "UPDATE usuarios SET 
                    nombres='$nombres', 
                    apellidos='$apellidos', 
                    documento='$documento', 
                    telefono='$telefono', 
                    correo='$correo'";

    // Si hay una nueva contraseña, se agrega a la consulta de actualizacion
    if ($contrasena) {
        $updateQuery .= ", contrasena='$contrasena'";
    }

    // Completamos la consulta de actualizacion con el id del usuario
    $updateQuery .= " WHERE id=$id";

    // Ejecutar la consulta de actualizacion
    $conexion->query($updateQuery);

    // Mensaje de exito.
    echo '<script>
        localStorage.setItem("actualizacionExitosa", "true");
        window.location.href = "actualizar.php";
    </script>';
    exit;
}

// Cerrar conexión a la base de datos
$conexion->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Actualizar Usuario</title>
    <script src="script.js" defer></script>
    <link rel="stylesheet" href="/Update/actualizar.css">
</head>
<body>
    
    <header>
        <div class="volver">
            <a href="/menu.html">
                <button class="boton-v">
                    Volver al menu
                </button>
            </a>
    </header>

    <main>
        <div class="titulo">
            <h1>Actualizar Usuario</h1>
        </div>

        <div class="formulario">
            <!-- Selector de rol para filtrar los usuarios -->
        <label for="rol">Selecciona un Rol:</label>
        <select id="rol" name="rol" required>
            <option value="" disabled selected>--Selecciona un Rol--</option>
            <option value="cliente">Cliente</option>
            <option value="vendedor">Vendedor</option>
            <option value="domiciliario">Domiciliario</option>
        </select>
    
        <!-- Formulario para actualizar los datos del usuario -->
        <form method="post">
            <label for="id">Selecciona el Usuario:</label>
            <select name="id" id="id" required>
                <option value="" disabled selected>--Selecciona un Usuario--</option>
                <?php foreach ($usuarios as $usuario): ?>
                    <option value="<?= $usuario['id']; ?>">
                        ID: <?= $usuario['id']; ?> - <?= $usuario['nombres']; ?> <?= $usuario['apellidos']; ?>
                    </option>
                <?php endforeach; ?>
            </select>
    
            <label for="nombres">Nombres:</label>
            <input type="text" name="nombres" id="nombres" required>
    
            <label for="apellidos">Apellidos:</label>
            <input type="text" name="apellidos" id="apellidos" required>
    
            <label for="documento">Documento:</label>
            <input type="number" name="documento" id="documento" required>
    
            <label for="telefono">Teléfono:</label>
            <input type="number" name="telefono" id="telefono" required>
    
            <label for="correo">Correo:</label>
            <input type="email" name="correo" id="correo" required>
    
            <label for="contrasena">Nueva Contraseña:</label>
            <input type="password" name="contrasena" id="contrasena">

            <button class="actualizar_b" type="submit">Actualizar</button>
        </form>
        </div>
    </main>

    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script src="/Update/script.js" defer></script>

</body>
</html>