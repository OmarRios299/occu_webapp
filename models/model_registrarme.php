<?php 


require_once "conexion.php";

class RegistrarmeModel extends Conexion {
    /* INSERTAR USUARIO */
    
    static public function insertarUsuarioModel($datos){
    
        $conexion = Conexion::conectar();
        $stmt = $conexion->prepare("INSERT INTO admin_usuarios 
        (nombre, 
        apellido, 
        id_ciudad, 
        correo_electronico, 
        contrasena, 
        nivel, 
        imagen,
        id_alta, 
        fecha_alta) 
        VALUES (
        :nombre, 
        :apellido, 
        0,
        :correo_electronico, 
        :contrasena, 
        :nivel, 
        'views/assets/img/usuario_default.png',
        :id_alta, 
        :fecha_alta)");

        $stmt->bindParam(':nombre', $datos['nombre'], PDO::PARAM_STR);
        $stmt->bindParam(':apellido', $datos['apellido'], PDO::PARAM_STR);
        $stmt->bindParam(':correo_electronico', $datos['correo'], PDO::PARAM_STR);
        $stmt->bindParam(':contrasena', $datos['contrasena'], PDO::PARAM_STR);
        $stmt->bindParam(':nivel', $datos['nivel'], PDO::PARAM_STR);
       // $stmt->bindParam(':telefono', $datos['telefono'], PDO::PARAM_STR);
        $stmt->bindParam(':id_alta', $datos['id_alta'], PDO::PARAM_INT);
        $stmt->bindParam(':fecha_alta', $datos['fecha_alta'], PDO::PARAM_STR);

        if($stmt->execute()){
            return $conexion-> lastInsertId();
        }else{
            return "error";
        }
        $stmt = null;

    }
    
    /* INSERTAR USUARIO */
}
?>