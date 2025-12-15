<?php

// Clase abstracta Usuarios que es la base para los distintos tipos de usuarios
abstract class Usuarios{

    // Atributos protegidos, accesibles solo dentro de la clase y sus subclases
    protected $nombres;
    protected $apellidos;
    protected $documento;
    protected $telefono;
    protected $correo;
    protected $contrasena;


    // Metodo constructor para inicializar los datos del usuario
    public function __construct($nombres, $apellidos, $documento, $telefono, $correo, $contrasena) {

        $this->nombres = $nombres;
        $this->apellidos = $apellidos;
        $this->documento = $documento;
        $this->telefono = $telefono;
        $this->correo = $correo;
        $this->contrasena = $contrasena;
    }


    // Metodos get para obtener los valores de los atributos
    public function getNombres() {
        return $this->nombres;
    }

    public function getApellidos() {
        return $this->apellidos;
    }

    public function getDocumento() {
        return $this->documento;
    }

    public function getTelefono() {
        return $this->telefono;
    }

    public function getCorreo() {
        return $this->correo;
    }

    public function getcontrasena() {
        return $this->contrasena;
    }


    // Metodos set para modificar los valores de los atributos
    public function setNombres($nombres){
        $this->nombres = $nombres;
    }

    public function setApellidos($apellidos){
        $this->apellidos = $apellidos;
    }

    public function setDocumento($documento){
        $this->documento = $documento;
    }

    public function setTelefono($telefono){
        $this->telefono = $telefono;
    }

    public function setCorreo($correo){
        $this->correo = $correo;
    }

    public function setcontrasena($contrasena){
        $this->contrasena = $contrasena;
    }

    // Metodo abstracto que cada subclase implementara para guardar el usuario en la base de datos
    abstract public function guardar($conexion);

    // Metodo abstracto que cada subclase implementara para borrar el usuario en la base de datos
    abstract public function borrar($conexion, $correo);

    // Metodo abstracto que cada subclase implementara para actualizar al usuario en la base de datos
    abstract public function actualizar($conexion, $id, $datos);


}


/* ===== Clase Cliente ===== */

class Cliente extends Usuarios{

    protected $puntos_lealtad;

    // Guarda el cliente en las tablas: usuarios y cliente
    public function guardar($conexion){
        
        // Insertar en la tabla usuarios
        $sql = "INSERT INTO usuarios (nombres, apellidos, documento, telefono, correo, contrasena, rol) 
        VALUES (?, ?, ?, ?, ?, ?, 'cliente')";

        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("sssiss", $this->nombres, $this->apellidos, $this->documento, $this->telefono, $this->correo, $this->contrasena);
        $stmt->execute();

        $id = $conexion->insert_id; // Obtiene el ID del usuario recien creado

        // Verifica si el usuario ya esta en la tabla cliente
        $sqlCheck = "SELECT COUNT(*) AS total FROM cliente WHERE usuario_id = ?";
        $stmtCheck = $conexion->prepare($sqlCheck);
        $stmtCheck->bind_param("i", $id);
        $stmtCheck->execute();

        $result = $stmtCheck->get_result();
        $row = $result->fetch_assoc();
        $count = $row['total']; 
        $stmtCheck->close();


        // Si el usuario no existe en cliente, lo agrega
        if ($count == 0) { 
            $sqlCliente = "INSERT INTO cliente(usuario_id, puntos_lealtad) VALUES (?, ?)";
            $stmtCliente = $conexion->prepare($sqlCliente);
            $stmtCliente->bind_param("ii", $id, $this->puntos_lealtad);
            $stmtCliente->execute();
            $stmtCliente->close();
        } else {
            echo "El usuario ya está registrado como cliente.";
        }
    
    }


    // Borrar un cliente
    public function borrar($conexion, $correo) {

        // Buscar ID del usuario y poder borrarlo por su correo electronico
        $sqlGetUserId = "SELECT id FROM usuarios WHERE correo = ?";
        $stmtGetUserId = $conexion->prepare($sqlGetUserId);
        $stmtGetUserId->bind_param("s", $correo);
        $stmtGetUserId->execute();
        $result = $stmtGetUserId->get_result();
        $row = $result->fetch_assoc();
        $stmtGetUserId->close();
    
        if (!$row) {
            echo "Error: No se encontro un usuario con ese correo.";
            return false;
        }
    
        $usuario_id = $row['id'];
    
        // Eliminar el cliente de la tabla cliente
        $sqlCliente = "DELETE FROM cliente WHERE usuario_id = ?";
        $stmtCliente = $conexion->prepare($sqlCliente);
        $stmtCliente->bind_param("i", $usuario_id);
        $stmtCliente->execute();
        $clienteEliminado = ($stmtCliente->affected_rows > 0);
        $stmtCliente->close();
    
        // Si se elimino el cliente, eliminar en la tabla usuario
        $usuarioEliminado = false;
        if ($clienteEliminado) {
            $sqlUsuario = "DELETE FROM usuarios WHERE id = ?";
            $stmtUsuario = $conexion->prepare($sqlUsuario);
            $stmtUsuario->bind_param("i", $usuario_id);
            $stmtUsuario->execute();
            $usuarioEliminado = ($stmtUsuario->affected_rows > 0);
            $stmtUsuario->close();
        }
    
        return ($clienteEliminado && $usuarioEliminado);
    }
    
    
    // Actualizar datos de usuarios y cliente
    public function actualizar($conexion, $id, $datos) {

        //Actualizar atributos
        $sqlUsuario = "UPDATE usuarios SET nombres=?, apellidos=?, documento=?, telefono=?, correo=?, contrasena=? WHERE id=?";

        $stmtUsuario = $conexion->prepare($sqlUsuario);
        $stmtUsuario->bind_param("sssissi", $datos['nombres'], $datos['apellidos'], $datos['documento'], $datos['telefono'], $datos['correo'], $datos['contrasena'], $id);
        $stmtUsuario->execute();

        $sqlCliente = "UPDATE cliente SET puntos_lealtad=? WHERE usuario_id=?";
        $stmtCliente = $conexion->prepare($sqlCliente);
        $stmtCliente->bind_param("ii", $datos['puntos_lealtad'], $id);
        $stmtCliente->execute();
    
        return ($stmtUsuario->affected_rows > 0 || $stmtCliente->affected_rows > 0);
    }
    

}

/* ===== Clase vendedor ===== */
class Vendedor extends Usuarios{


    protected $negocio_id;

    // Guarda el vendedor en las tablas: usuarios y vendedor
    public function guardar($conexion){

        // Insertar en la tabla usuarios
        $sql = "INSERT INTO usuarios (nombres, apellidos, documento, telefono, correo, contrasena, rol) 
        VALUES (?, ?, ?, ?, ?, ?, 'vendedor')";

        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("sssiss", $this->nombres, $this->apellidos, $this->documento, $this->telefono, $this->correo, $this->contrasena);
        $stmt->execute();

        $id = $conexion->insert_id; // Obtener el ID del usuario recién insertado

        // Verificar si el usuario ya está en la tabla vendedor
        $sqlCheck = "SELECT COUNT(*) AS total FROM vendedor WHERE usuario_id = ?";
        $stmtCheck = $conexion->prepare($sqlCheck);
        $stmtCheck->bind_param("i", $id);
        $stmtCheck->execute();

        $result = $stmtCheck->get_result();
        $row = $result->fetch_assoc();
        $count = $row['total']; 
        $stmtCheck->close();

        // Si el usuario no existe en vendedor, lo agrega
        if ($count == 0) { 
            $sqlVendedor = "INSERT INTO vendedor(usuario_id, negocio_id) VALUES (?, ?)";
            $stmtVendedor = $conexion->prepare($sqlVendedor);
            $stmtVendedor->bind_param("ii", $id, $this->negocio_id );
            $stmtVendedor->execute();
            $stmtVendedor->close();
        } else {
            echo "El usuario ya está registrado como vendedor";
        }
    
    }

    // Borrar un vendedor
    public function borrar($conexion, $correo) {

        // Buscar ID del usuario y poder borrarlo por su correo electronico
        $sqlGetUserId = "SELECT id FROM usuarios WHERE correo = ?";
        $stmtGetUserId = $conexion->prepare($sqlGetUserId);
        $stmtGetUserId->bind_param("s", $correo);
        $stmtGetUserId->execute();
        $result = $stmtGetUserId->get_result();
        $row = $result->fetch_assoc();
        $stmtGetUserId->close();
    
        if (!$row) {
            echo "Error: No se encontro un usuario con ese correo.";
            return false;
        }
        
        $usuario_id = $row['id'];
    
        // Eliminar relaciones en negocio_vendedor
        // $sqlNegocioVendedor = "DELETE FROM negocio_vendedor WHERE usuario_id = ?";
        // $stmtNegocioVendedor = $conexion->prepare($sqlNegocioVendedor);
        // $stmtNegocioVendedor->bind_param("i", $usuario_id);
        // $stmtNegocioVendedor->execute();
    
        // // Verifica si se eliminó alguna fila
        // if ($stmtNegocioVendedor->error) {
        //     echo "Error al eliminar relación en negocio_vendedor: " . $stmtNegocioVendedor->error;
        //     return false;
        // }
        
        // Eliminar el vendedor de la tabla vendedor
        $sqlVendedor = "DELETE FROM vendedor WHERE usuario_id = ?";
        $stmtVendedor = $conexion->prepare($sqlVendedor);
        $stmtVendedor->bind_param("i", $usuario_id);
        $stmtVendedor->execute();
        $vendedorEliminado = ($stmtVendedor->affected_rows > 0);
    
        // Verifica si se elimino alguna fila
        if ($stmtVendedor->error) {
            echo "Error al eliminar vendedor: " . $stmtVendedor->error;
            return false;
        }
        
        $stmtVendedor->close();
    
        // Si se elimino el vendedor, eliminar en la tabla usuario
        $usuarioEliminado = false;
        if ($vendedorEliminado) {
            $sqlUsuario = "DELETE FROM usuarios WHERE id = ?";
            $stmtUsuario = $conexion->prepare($sqlUsuario);
            $stmtUsuario->bind_param("i", $usuario_id);
            $stmtUsuario->execute();
    
            // Verifica si se elimino alguna fila
            if ($stmtUsuario->error) {
                echo "Error al eliminar usuario: " . $stmtUsuario->error;
                return false;
            }
    
            $usuarioEliminado = ($stmtUsuario->affected_rows > 0);
            $stmtUsuario->close();
        }
    
        return ($vendedorEliminado && $usuarioEliminado);
    }
    
    
    // Actualizar datos de usuarios y vendedor
    public function actualizar($conexion, $id, $datos) {

        //Actualizar atributos
        $sqlUsuario = "UPDATE usuarios SET nombres=?, apellidos=?, documento=?, telefono=?, correo=?, contrasena=? WHERE id=?";

        $stmtUsuario = $conexion->prepare($sqlUsuario);
        $stmtUsuario->bind_param("sssissi", $datos['nombres'], $datos['apellidos'], $datos['documento'], $datos['telefono'], $datos['correo'], $datos['contrasena'], $id);
        $stmtUsuario->execute();
    
        $sqlVendedor = "UPDATE vendedor SET negocio_id=? WHERE usuario_id=?";
        $stmtVendedor = $conexion->prepare($sqlVendedor);
        $stmtVendedor->bind_param("ii", $datos['negocio_id'], $id);
        $stmtVendedor->execute();
    
        return ($stmtUsuario->affected_rows > 0 || $stmtVendedor->affected_rows > 0);
    }
    
    

}

/* ===== Clase Domiciliario ===== */
class Domiciliario extends Usuarios{

    protected $medio_transporte_id;


    public function guardar($conexion){

        // Guarda el vendedor en las tablas: usuarios y vendedor
        $sql = "INSERT INTO usuarios (nombres, apellidos, documento, telefono, correo, contrasena, rol) 
        VALUES (?, ?, ?, ?, ?, ?, 'domiciliario')";

        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("sssiss", $this->nombres, $this->apellidos, $this->documento, $this->telefono, $this->correo, $this->contrasena);
        $stmt->execute();

        $id = $conexion->insert_id; // Obtener el ID del usuario recien insertado

        // Verifica si el usuario ya esta en la tabla domiciliaio
        $sqlCheck = "SELECT COUNT(*) AS total FROM domiciliario WHERE usuario_id = ?";
        $stmtCheck = $conexion->prepare($sqlCheck);
        $stmtCheck->bind_param("i", $id);
        $stmtCheck->execute();

        $result = $stmtCheck->get_result();
        $row = $result->fetch_assoc();
        $count = $row['total']; 
        $stmtCheck->close();

        // Si el usuario no existe en domiciliario, lo agrega
        if ($count == 0) { 
            $sqlDomiciliario = "INSERT INTO domiciliario(usuario_id, medio_transporte_id ) VALUES (?, ?)";
            $stmtDomiciliario = $conexion->prepare($sqlDomiciliario);
            $stmtDomiciliario->bind_param("ii", $id, $this->medio_transporte_id);
            $stmtDomiciliario->execute();
            $stmtDomiciliario->close();
        } else {
            echo "El usuario ya está registrado como domiciliario.";
        }
    
    }

    // Borrar un domiciliario
    public function borrar($conexion, $correo) {

        // Buscar ID del usuario y poder borrarlo por su correo electronico
        $sqlGetUserId = "SELECT id FROM usuarios WHERE correo = ?";
        $stmtGetUserId = $conexion->prepare($sqlGetUserId);
        $stmtGetUserId->bind_param("s", $correo);
        $stmtGetUserId->execute();
        $result = $stmtGetUserId->get_result();
        $row = $result->fetch_assoc();
        $stmtGetUserId->close();
    
        if (!$row) {
            echo "Error: No se encontro un domiciliario con ese correo.";
            return false;
        }
    
        $usuario_id = $row['id'];
    
        // Eliminar el domiciliario de la tabla domiciliario
        $sqlDomiciliario = "DELETE FROM domiciliario WHERE usuario_id = ?";
        $stmtDomiciliario = $conexion->prepare($sqlDomiciliario);
        $stmtDomiciliario->bind_param("i", $usuario_id);
        $stmtDomiciliario->execute();
        $domiciliarioEliminado = ($stmtDomiciliario->affected_rows > 0);
        $stmtDomiciliario->close();
    
        // Si se elimino el domiciliario, elimina en la tabla usuario
        $usuarioEliminado = false;
        if ($domiciliarioEliminado) {
            $sqlUsuario = "DELETE FROM usuarios WHERE id = ?";
            $stmtUsuario = $conexion->prepare($sqlUsuario);
            $stmtUsuario->bind_param("i", $usuario_id);
            $stmtUsuario->execute();
            $usuarioEliminado = ($stmtUsuario->affected_rows > 0);
            $stmtUsuario->close();
        }
    
        return ($domiciliarioEliminado && $usuarioEliminado);
    }
    
    // Actualizar datos de usuarios y domiciliario
    public function actualizar($conexion, $id, $datos) {
        
        // Actualizar atributos
        $sqlUsuario = "UPDATE usuarios SET nombres=?, apellidos=?, documento=?, telefono=?, correo=?, contrasena=? WHERE id=?";

        $stmtUsuario = $conexion->prepare($sqlUsuario);
        $stmtUsuario->bind_param("sssissi", $datos['nombres'], $datos['apellidos'], $datos['documento'], $datos['telefono'], $datos['correo'], $datos['contrasena'], $id);
        $stmtUsuario->execute();
    
        $sqlDomiciliario = "UPDATE domiciliario SET medio_transporte_id=? WHERE usuario_id=?";
        $stmtDomiciliario = $conexion->prepare($sqlDomiciliario);
        $stmtDomiciliario->bind_param("ii", $datos['medio_transporte_id'], $id);
        $stmtDomiciliario->execute();
    
        return ($stmtUsuario->affected_rows > 0 || $stmtDomiciliario->affected_rows > 0);
    }
    
    
    
    
    

}


// Funcion para crear instancias nuevas cada vez que se registran por diferente rol
function crearUsuarios($rol, $nombres, $apellidos, $documento, $telefono, $correo, $contrasena){

    //Se crea nueva instancia cada vez que se eliga algun rol
    switch ($rol) {
        case 'cliente':
            return new Cliente($nombres, $apellidos, $documento, $telefono, $correo, $contrasena);
            break;

        case 'vendedor':
            return new Vendedor($nombres, $apellidos, $documento, $telefono, $correo, $contrasena);
            break;

        case 'domiciliario':
            return new Domiciliario($nombres, $apellidos, $documento, $telefono, $correo, $contrasena);
            break;
        
        default:
            echo "Rol no valido";
            break;
    }
}



?>