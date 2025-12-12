<?php

require_once __DIR__ . '/../config/conexion.php';

class LoginModel extends Conexion{

	/* INGRESO */
	
	static public function ingresoModel($datosModel){

		$stmt = Conexion::conectar()->prepare("SELECT
        admin_usuarios.*,
        CONCAT(
            admin_usuarios.nombre,
            ' ',
            admin_usuarios.apellido
        ) AS nombre_usuario,
        paises.id AS id_pais
        FROM
            admin_usuarios
        LEFT JOIN ciudades ON admin_usuarios.id_ciudad = ciudades.id
        LEFT JOIN entidades_federativas ON ciudades.id_entidad_federativa = entidades_federativas.id
        LEFT JOIN paises ON entidades_federativas.id_pais = paises.id
        WHERE admin_usuarios.correo_electronico = :correo_electronico");
        
        $stmt->bindParam(":correo_electronico", $datosModel, PDO::PARAM_STR);

        $stmt->execute();
       
        return $stmt->fetch();

        $stmt->close();

        $stmt = null;

	}
	
	/* End of INGRESO */

    /* ACTUALIZAR TOKEN SESSION */
    
    static public function actualizarTokenSession($id_usuario,$token_sesion){
        
        $stmt = Conexion::conectar()->prepare("UPDATE admin_usuarios 
        SET token_sesion=:token_sesion 
        WHERE id=:id");
    
        $stmt->bindParam(":id", $id_usuario, PDO::PARAM_INT);
        $stmt->bindParam(":token_sesion", $token_sesion, PDO::PARAM_STR);
    
        if($stmt->execute()){
            return 'success';
        }else{
            return 'error';
        }
    
        $stmt = null;
        
    }
    
    /* ACTUALIZAR TOKEN SESSION */

    /* INGRESO TOKEN */
	
	static public function ingresoTokenModel($token_sesion){
 
		$stmt = Conexion::conectar()->prepare("SELECT 
        admin_usuarios.*,
        CONCAT(
            admin_usuarios.nombre,
            ' ',
            admin_usuarios.apellido
        ) AS nombre_usuario,
        entidades_federativas.id_pais
        FROM admin_usuarios
        INNER JOIN ciudades ON ciudades.id = admin_usuarios.id_ciudad
        INNER JOIN entidades_federativas ON entidades_federativas.id = ciudades.id_entidad_federativa
        WHERE admin_usuarios.token_sesion = :token_sesion
        AND admin_usuarios.token_sesion != ''
        AND admin_usuarios.token_sesion != 0
        AND admin_usuarios.estado=0
        ");
        
        $stmt->bindParam(":token_sesion", $token_sesion, PDO::PARAM_STR);

        $stmt->execute();
       
        return $stmt->fetch();

        $stmt = null;

	}
	
	/* End of INGRESO TOKEN */

}