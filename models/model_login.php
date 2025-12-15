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

	/* BUSCAR USUARIO POR PROVEEDOR */
	
	static public function buscarUsuarioPorProveedorModel($proveedor, $id_usuario_proveedor){


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
        WHERE admin_usuarios.proveedor = :proveedor 
        AND admin_usuarios.id_usuario_proveedor = :id_usuario_proveedor
        AND admin_usuarios.estado = 0");
        
        $stmt->bindParam(":proveedor", $proveedor, PDO::PARAM_STR);
        $stmt->bindParam(":id_usuario_proveedor", $id_usuario_proveedor, PDO::PARAM_STR);

        $stmt->execute();
       
        return $stmt->fetch();

        $stmt->close();

        $stmt = null;

	}
	
	/* End of BUSCAR USUARIO POR PROVEEDOR */

	/* CREAR USUARIO CON GOOGLE */
	
	static public function crearUsuarioGoogleModel($datos){

		$conexion = Conexion::conectar();
		$stmt = $conexion->prepare("INSERT INTO admin_usuarios 
		(nombre, 
		apellido, 
		id_ciudad, 
		correo_electronico, 
		nivel, 
		imagen,
		proveedor,
		id_usuario_proveedor,
		verificado,
		estado,
		id_alta, 
		fecha_alta) 
		VALUES (
		:nombre, 
		:apellido, 
		:id_ciudad, 
		:correo_electronico, 
		:nivel, 
		:imagen,
		'google',
		:id_usuario_proveedor,
		'No',
		0,
		:id_alta, 
		:fecha_alta)");

		$stmt->bindParam(':nombre', $datos['nombre'], PDO::PARAM_STR);
		$stmt->bindParam(':apellido', $datos['apellido'], PDO::PARAM_STR);
		$stmt->bindParam(':id_ciudad', $datos['id_ciudad'], PDO::PARAM_INT);
		$stmt->bindParam(':correo_electronico', $datos['correo_electronico'], PDO::PARAM_STR);
		$stmt->bindParam(':nivel', $datos['nivel'], PDO::PARAM_STR);
		$stmt->bindParam(':imagen', $datos['imagen'], PDO::PARAM_STR);
		$stmt->bindParam(':id_usuario_proveedor', $datos['id_usuario_proveedor'], PDO::PARAM_STR);
		$stmt->bindParam(':id_alta', $datos['id_alta'], PDO::PARAM_INT);
		$stmt->bindParam(':fecha_alta', $datos['fecha_alta'], PDO::PARAM_STR);

		if($stmt->execute()){
			return $conexion->lastInsertId();
		}else{
			return "error";
		}
		$stmt = null;

	}
	
	/* End of CREAR USUARIO CON GOOGLE */

	/* OBTENER USUARIO POR ID CON JOIN */
	
	static public function obtenerUsuarioPorIdModel($id_usuario){

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
        WHERE admin_usuarios.id = :id");
        
        $stmt->bindParam(":id", $id_usuario, PDO::PARAM_INT);

        $stmt->execute();
       
        return $stmt->fetch();

        $stmt->close();

        $stmt = null;

	}
	
	/* End of OBTENER USUARIO POR ID CON JOIN */

	/* COMPLETAR INFORMACION USUARIO GOOGLE */
	
	static public function completarInfoUsuarioGoogleModel($id_usuario, $id_ciudad, $nivel){

		$stmt = Conexion::conectar()->prepare("UPDATE admin_usuarios SET 
		id_ciudad = :id_ciudad, 
		nivel = :nivel,
		verificado = 'Si'
		WHERE id = :id");

		$stmt->bindParam(':id_ciudad', $id_ciudad, PDO::PARAM_INT);
		$stmt->bindParam(':nivel', $nivel, PDO::PARAM_STR);
		$stmt->bindParam(':id', $id_usuario, PDO::PARAM_INT);

		if($stmt->execute()){
			return 'success';
		} else {
			return 'error';
		}

		$stmt = null;

	}
	
	/* End of COMPLETAR INFORMACION USUARIO GOOGLE */

}