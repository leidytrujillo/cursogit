<?php
include '../conexion.php';

// Valor por defecto cuando se abra reportes
$filtro = 'todos';

// Verificamos si el usuario envió el formulario
if (isset($_GET['tipo'])) {
    $filtro = $_GET['tipo'];
}

// Estilos y botón "Volver al menú"
echo "<style>
    body {
        font-family: sans-serif;
    }
    h1 {
        text-align: center;
    }
    h2 {
        text-align: center;
        margin-top: 40px;
    }
    table {
        width: 40%;
        border-collapse: collapse;
        margin: 20px auto;
        font-size: 16px;
        text-align: left;
    }
    th, td {
        border: 1px solid #ddd;
        padding: 8px;
    }
    th {
        background-color: #f4f4f4;
        font-weight: bold;
    }
    tr:nth-child(even) {
        background-color: #f9f9f9;
    }
    tr:hover {
        background-color: #f1f1f1;
    }
    header {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        padding: 10px;
    }
    .volver {
        text-align: center;
        padding: 10px;
        width: 150px;
    }
</style>";

echo '<header>
        <div class="volver">
            <a href="/menu.html">
                <button class="boton-v">
                    Volver al menú
                </button>
            </a>
        </div>
    </header>';

// Título y formulario
echo "<h1>Reporte de Usuarios</h1>";
echo '<form method="GET" action="reporte.php" style="text-align:center;">
        <label for="tipo">Filtrar por:</label>
        <select name="tipo" id="tipo">
            <option value="todos"';
if ($filtro == 'todos') {
    echo ' selected';
}
echo '>Todos</option>';

echo '<option value="cliente"';
if ($filtro == 'cliente') {
    echo ' selected';
}
echo '>Cliente</option>';

echo '<option value="vendedor"';
if ($filtro == 'vendedor') {
    echo ' selected';
}
echo '>Vendedor</option>';

echo '<option value="domiciliario"';
if ($filtro == 'domiciliario') {
    echo ' selected';
}
echo '>Domiciliario</option>';

echo '</select>
        <input type="submit" value="Filtrar">
      </form><br>';

// Función para mostrar tabla
function mostrarTabla($resultado, $titulo) {
    echo "<h2>$titulo</h2>";
    if ($resultado->num_rows > 0) {
        echo "<table>
                <tr><th>Nombres</th><th>Apellidos</th><th>Documento</th><th>Teléfono</th><th>Correo</th></tr>";
        while ($row = $resultado->fetch_assoc()) {
            echo "<tr>
                    <td>{$row['nombres']}</td>
                    <td>{$row['apellidos']}</td>
                    <td>{$row['documento']}</td>
                    <td>{$row['telefono']}</td>
                    <td>{$row['correo']}</td>
                  </tr>";
        }
        echo "</table>";
    } else {
        echo "<p style='text-align:center;'>No hay datos de $titulo.</p>";
    }
}

// Consultas según filtro
if ($filtro == 'cliente' || $filtro == 'todos') {
    $sqlCliente = "SELECT u.* FROM usuarios u INNER JOIN cliente c ON u.id = c.usuario_id";
    $resultCliente = $conexion->query($sqlCliente);
    mostrarTabla($resultCliente, "Cliente");
}

if ($filtro == 'vendedor' || $filtro == 'todos') {
    $sqlVendedor = "SELECT u.* FROM usuarios u INNER JOIN vendedor v ON u.id = v.usuario_id";
    $resultVendedor = $conexion->query($sqlVendedor);
    mostrarTabla($resultVendedor, "Vendedor");
}

if ($filtro == 'domiciliario' || $filtro == 'todos') {
    $sqlDomiciliario = "SELECT u.* FROM usuarios u INNER JOIN domiciliario d ON u.id = d.usuario_id";
    $resultDomiciliario = $conexion->query($sqlDomiciliario);
    mostrarTabla($resultDomiciliario, "Domiciliario");
}
?>
