<?php
require_once "conexion.php";

class AdminUsuariosModel extends Conexion
{


    /* OBTENER USUARIOS */

    static public function obtenerUsuariosModel()
    {

        $stmt = Conexion::conectar()->prepare("SELECT
        admin_usuarios.*,
        CONCAT(
            admin_usuarios.nombre,
            ' ',
            admin_usuarios.apellido
        ) AS nombre_usuario,
        ciudades.nombre AS ciudad,
        entidades_federativas.nombre AS entidad_federativa,
        paises.nombre AS pais
        FROM
            admin_usuarios
        INNER JOIN ciudades ON admin_usuarios.id_ciudad = ciudades.id
        INNER JOIN entidades_federativas ON ciudades.id_entidad_federativa = entidades_federativas.id
        INNER JOIN paises ON paises.id = entidades_federativas.id_pais WHERE admin_usuarios.estado !=2
        ");

        //$stmt->bindParam(':', ,PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->fetchAll();

        $stmt = null;
    }

    /* OBTENER USUARIOS */


    /* INSERTAR USUARIOS */

    static public function insertarUsuariosModel($datos)
    {

        $stmt = Conexion::conectar()->prepare("INSERT INTO admin_usuarios(
            nombre,
            apellido,
            id_ciudad,
            correo_electronico,
            contrasena,
            nivel,
            telefono,
            imagen,
            id_alta,
            fecha_alta
        )
        VALUES(
            :nombre,
            :apellido,
            :id_ciudad,
            :correo_electronico,
            :contrasena,
            :nivel,
            :telefono,
            :imagen,
            :id_alta,
            :fecha_alta
        )");

        $stmt->bindParam(':nombre', $datos['nombre'], PDO::PARAM_STR);
        $stmt->bindParam(':apellido', $datos['apellido'], PDO::PARAM_STR);
        $stmt->bindParam(':id_ciudad', $datos['id_ciudad'], PDO::PARAM_INT);
        $stmt->bindParam(':correo_electronico', $datos['correo_electronico'], PDO::PARAM_STR);
        $stmt->bindParam(':contrasena', $datos['contrasena'], PDO::PARAM_STR);
        $stmt->bindParam(':nivel', $datos['nivel'], PDO::PARAM_INT);
        $stmt->bindParam(':telefono', $datos['telefono'], PDO::PARAM_STR);
        $stmt->bindParam(':imagen', $datos['imagen'], PDO::PARAM_STR);
        $stmt->bindParam(':id_alta', $datos['id_alta'], PDO::PARAM_INT);
        $stmt->bindParam(':fecha_alta', $datos['fecha_alta'], PDO::PARAM_STR);

        if ($stmt->execute()) {
            return 'success';
        } else {
            return 'error';
        }
        $stmt = null;
    }

    /* INSERTAR USUARIOS */

    
    /* SELECCIONAR USUARIO */
    
    static public function obtenerInfoUsuarioModel($id){
    
        $stmt = Conexion::conectar()->prepare("SELECT
        admin_usuarios.*,
        CONCAT(
            admin_usuarios.nombre,
            ' ',
            admin_usuarios.apellido
        ) AS nombre_usuario,
        ciudades.id AS id_ciudad,
        ciudades.nombre AS ciudad,
        entidades_federativas.nombre AS entidad_federativa,
        entidades_federativas.id AS id_entidad_federativa,
        paises.id AS id_pais,
        paises.nombre AS pais
        FROM
            admin_usuarios
        INNER JOIN ciudades ON admin_usuarios.id_ciudad = ciudades.id
        INNER JOIN entidades_federativas ON ciudades.id_entidad_federativa = entidades_federativas.id
        INNER JOIN paises ON paises.id = entidades_federativas.id_pais WHERE admin_usuarios.id=:id");
    
        $stmt->bindParam(':id', $id,PDO::PARAM_STR);
    
        $stmt -> execute();
    
        return $stmt -> fetch();
    
        $stmt = null;
    
    }
    
    /* SELECCIONAR USUARIO */


    
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
        telefono, 
        imagen,
        id_alta, 
        fecha_alta) 
        VALUES (
        :nombre, 
        :apellido, 
        :id_ciudad, 
        :correo_electronico, 
        :contrasena, 
        :nivel, 
        :telefono, 
        'views/assets/img/usuario_default.png',
        :id_alta, 
        :fecha_alta)");

        $stmt->bindParam(':nombre', $datos['nombre'], PDO::PARAM_STR);
        $stmt->bindParam(':apellido', $datos['apellido'], PDO::PARAM_STR);
        $stmt->bindParam(':id_ciudad', $datos['ciudad'], PDO::PARAM_INT);
        $stmt->bindParam(':correo_electronico', $datos['correo'], PDO::PARAM_STR);
        $stmt->bindParam(':contrasena', $datos['contrasena'], PDO::PARAM_STR);
        $stmt->bindParam(':nivel', $datos['nivel'], PDO::PARAM_STR);
        $stmt->bindParam(':telefono', $datos['telefono'], PDO::PARAM_STR);
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

    
    /* EDITAR USUARIO */
    
    static public function editarUsuarioModel($datos){

        $contrasena = $datos['contrasena'] ? "contrasena=:contrasena," : ""; 
        $stmt = Conexion::conectar()->prepare("UPDATE admin_usuarios SET 
        nombre = :nombre, 
        apellido = :apellido, 
        id_ciudad = :id_ciudad, 
        correo_electronico = :correo_electronico, 
        $contrasena
        nivel = :nivel, 
        telefono = :telefono
        WHERE id = :id");

        $stmt->bindParam(':nombre', $datos['nombre'], PDO::PARAM_STR);
        $stmt->bindParam(':apellido', $datos['apellido'], PDO::PARAM_STR);
        $stmt->bindParam(':id_ciudad', $datos['ciudad'], PDO::PARAM_INT);
        $stmt->bindParam(':correo_electronico', $datos['correo'], PDO::PARAM_STR);
        if($datos['contrasena']) $stmt->bindParam(":contrasena",$datos['contrasena'],PDO::PARAM_STR);
        $stmt->bindParam(':nivel', $datos['nivel'], PDO::PARAM_STR);
        $stmt->bindParam(':telefono', $datos['telefono'], PDO::PARAM_STR);
        $stmt->bindParam(':id', $datos['id'], PDO::PARAM_INT);

        if($stmt->execute()){
            return 'success';
        } else {
            return 'error';
        }

        $stmt = null;

    
    }
    
    /* EDITAR USUARIO */
    
    /* BUSCAR USUARIO */

    static public function buscarUsuarioModel($id_usuario){

        $stmt = Conexion::conectar()->prepare("SELECT 
        admin_usuarios.*
        FROM admin_usuarios 
        WHERE estado!=2
        AND id=:id");
    
        $stmt->bindParam(':id',$id_usuario,PDO::PARAM_INT);
    
        $stmt -> execute();
    
        return $stmt -> fetch();
    
        $stmt = null;
    
    }
    
    /* BUSCAR USUARIO */

    /* EDITAR IMAGEN USUARIO */
    
    static public function editarImagenUsuarioModel($datos){
    
        $stmt = Conexion::conectar()->prepare("UPDATE admin_usuarios SET imagen=:imagen WHERE id=:id");
    
        $stmt->bindParam(":imagen", $datos['imagen'], PDO::PARAM_STR);
        $stmt->bindParam(":id", $datos['id'], PDO::PARAM_INT);
    
        if($stmt->execute()){
            return 'success';
        }else{
            return 'error';
        }
    
        $stmt = null;
    
    }
    
    /* EDITAR IMAGEN USUARIO */
    
}
