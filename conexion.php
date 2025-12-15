<?php 

/*Parametros de a la conexion de base de datos

    - $host = "localhost", direccion del servidor MySQL para entornos locales
    - $dbName = "sbr", nombre de la base de datos que se va a usar
    - $userName = "root", nombre de usuario de MySQL
    - $password = ", contraseña del usuario
*/

$host = "localhost";
$dbName = "sbr";
$userName = "root";
$password = "";


//Creacion de la instancia de conexion utilizando la clase mysqli
$conexion = new mysqli($host, $userName, $password, $dbName);

/* Verificacion de errores de conexion

    - Si ocurre un error al conectar, se mostrara el mensaje de error
    - Si no ocurre error al conectar, se mostrara el mensaje de ejecución 
*/

if ($conexion->connect_error) {
    die("Error al conectar la base de datos". $conexion->connect_error);
} 


?>