<?php 

require_once __DIR__ . '/../config/conexion.php';

class AdminPaisesModel extends Conexion {

/* OBTENER PAISES */

static public function obtenerPaisesModel(){

    $stmt = Conexion::conectar()->prepare("SELECT paises.*, CONCAT(admin_usuarios.nombre, ' ', admin_usuarios.apellido) AS usuario_alta
    FROM paises 
    INNER JOIN admin_usuarios ON paises.id_alta = admin_usuarios.id 
    WHERE paises.estado!=2");

    $stmt -> execute();

    return $stmt -> fetchAll();

    $stmt = null;

}

/* OBTENER PAISES */


/* OBTENER INFO DE PAIS */

static public function obtenerInfoPaisModel($pais){

    $stmt = Conexion::conectar()->prepare("SELECT paises.* FROM paises WHERE id=:id");

    $stmt->bindParam(':id', $pais,PDO::PARAM_INT);

    $stmt -> execute();

    return $stmt -> fetch();

    $stmt = null;

}

/* OBTENER INFO DE PAIS */


/* OBTENER INFO DE CIUDAD */

static public function obtenerInfoCiudadModel($id){

    $stmt = Conexion::conectar()->prepare("SELECT ciudades.*, entidades_federativas.nombre AS entidad_federativa FROM ciudades INNER JOIN entidades_federativas ON ciudades.id_entidad_federativa = entidades_federativas.id WHERE ciudades.id=:id");

    $stmt->bindParam(':id', $id,PDO::PARAM_INT);

    $stmt -> execute();

    return $stmt -> fetch();

    $stmt = null;

}

/* OBTENER INFO DE CIUDAD */



/* AGREGAR PAIS */

static public function agregarPaisModel($datos){

    $stmt = Conexion::conectar()->prepare("INSERT INTO paises(
    nombre,
    id_alta,
    fecha_alta
    )
    VALUES(
        :nombre,
        :id_alta,
        :fecha_alta
    )");

    $stmt->bindParam(':nombre', $datos['nombre'], PDO::PARAM_STR);
    $stmt->bindParam(':id_alta', $datos['id_alta'], PDO::PARAM_INT);
    $stmt->bindParam(':fecha_alta', $datos['fecha_alta'], PDO::PARAM_STR);

    if($stmt->execute()){
        return 'success';
    }else{
        return 'error';
    }
    $stmt = null;
}

/* AGREGAR PAIS */


/* EDITAR PAIS */

static public function editarPaisModel($datos){

    $stmt = Conexion::conectar()->prepare("UPDATE paises SET nombre=:nombre WHERE id = :id");

    $stmt->bindParam(":id", $datos['id'], PDO::PARAM_INT);
    $stmt->bindParam(":nombre", $datos['nombre'], PDO::PARAM_STR);

    if($stmt->execute()){
        return 'success';
    }else{
        return 'error';
    }

    $stmt = null;

}

/* EDITAR PAIS */


/* OBTENER CIUDADES POR PAIS */

static public function obtenerCiudadesPorPaisModel($pais){

    $stmt = Conexion::conectar()->prepare("SELECT 
    ciudades.*,
    entidades_federativas.nombre AS entidad_federativa,
    CONCAT(admin_usuarios.nombre, ' ', admin_usuarios.apellido) AS usuario_alta,
    COUNT(CASE WHEN cafeterias.estado != 2 THEN cafeterias.id END) AS total_cafeterias
    FROM ciudades
    INNER JOIN admin_usuarios ON ciudades.id_alta = admin_usuarios.id
    INNER JOIN entidades_federativas ON ciudades.id_entidad_federativa = entidades_federativas.id
    LEFT JOIN cafeterias ON cafeterias.id_ciudad = ciudades.id
    WHERE entidades_federativas.id_pais = :id AND ciudades.estado != 2
    GROUP BY ciudades.id, entidades_federativas.nombre, admin_usuarios.nombre, admin_usuarios.apellido;

    ");

   $stmt->bindParam(':id', $pais,PDO::PARAM_INT);

    $stmt -> execute();

    return $stmt -> fetchAll();

    $stmt = null;

}

/* OBTENER CIUDADES POR PAIS */


/* AGREGAR CIUDAD */

static public function agregarCiudadModel($datos) {
    $stmt = Conexion::conectar()->prepare("INSERT INTO ciudades(
        nombre,
        id_entidad_federativa,
        coordenadas,
        id_alta,
        fecha_alta
    ) VALUES (
        :nombre,
        :id_entidad_federativa,
        :coordenadas,
        :id_alta,
        :fecha_alta
    )");

    $stmt->bindParam(':nombre', $datos['nombre'], PDO::PARAM_STR);
    $stmt->bindParam(':id_entidad_federativa', $datos['entidad_federativa'], PDO::PARAM_INT);
    
    // Manejar coordenadas vacías o null
    $coordenadas = !empty($datos['coordenadas']) ? $datos['coordenadas'] : null;
    $stmt->bindParam(':coordenadas', $coordenadas, PDO::PARAM_STR);
    $stmt->bindParam(':id_alta', $datos['id_alta'], PDO::PARAM_INT);
    $stmt->bindParam(':fecha_alta', $datos['fecha_alta'], PDO::PARAM_STR);

    if ($stmt->execute()) {
        return 'success';
    } else {
        return 'error';
    }

    $stmt = null;
}


/* AGREGAR CIUDAD */


/* EDITAR CIUDAD  */

static public function editarCiudadModel($datos){

    $stmt = Conexion::conectar()->prepare("UPDATE ciudades SET nombre=:nombre, id_entidad_federativa=:entidad_federativa, coordenadas=:coordenadas WHERE id = :id");

    $stmt->bindParam(":id", $datos['id'], PDO::PARAM_INT);
    $stmt->bindParam(":nombre", $datos['nombre'], PDO::PARAM_STR);
    $stmt->bindParam(":entidad_federativa", $datos['entidad_federativa'], PDO::PARAM_INT);
    
    // Manejar coordenadas vacías o null
    $coordenadas = !empty($datos['coordenadas']) ? $datos['coordenadas'] : null;
    $stmt->bindParam(":coordenadas", $coordenadas, PDO::PARAM_STR);

    if($stmt->execute()){
        return 'success';
    }else{
        return 'error';
    }

    $stmt = null;

}

/* EDITAR CIUDAD  */


/* ACTUALIZAR COORDENADAS DE CIUDAD */

static public function actualizarCoordenadasCiudadModel($datos){

    $stmt = Conexion::conectar()->prepare("UPDATE ciudades SET coordenadas=:coordenadas WHERE id = :id");

    $stmt->bindParam(":id", $datos['id'], PDO::PARAM_INT);
    
    // Manejar coordenadas vacías o null
    $coordenadas = !empty($datos['coordenadas']) ? $datos['coordenadas'] : null;
    $stmt->bindParam(":coordenadas", $coordenadas, PDO::PARAM_STR);

    if($stmt->execute()){
        return 'success';
    }else{
        return 'error';
    }

    $stmt = null;

}

/* ACTUALIZAR COORDENADAS DE CIUDAD */


/* OBTENER CAFETERíAS */

static public function obtenerCafeteriasModel($id){

    $stmt = Conexion::conectar()->prepare("SELECT
    cafeterias.*,
    CONCAT(
        admin_usuarios.nombre,
        ' ',
        admin_usuarios.apellido
    ) AS usuario_alta,
    ciudades.nombre AS ciudad,
    entidades_federativas.nombre AS entidad_federativa,
    paises.nombre AS pais
    FROM
        cafeterias
    INNER JOIN admin_usuarios ON admin_usuarios.id = cafeterias.id_usuario
    INNER JOIN ciudades ON admin_usuarios.id_ciudad = ciudades.id
    INNER JOIN entidades_federativas ON ciudades.id_entidad_federativa = entidades_federativas.id
    INNER JOIN paises ON paises.id = entidades_federativas.id_pais
    WHERE cafeterias.estado !=2 AND cafeterias.id_ciudad=:id");

    $stmt->bindParam(':id', $id,PDO::PARAM_INT);

    $stmt -> execute();

    return $stmt -> fetchAll();

    $stmt = null;

}

/* OBTENER CAFETERíAS */



/* OBTENER DATOS DE CONTADORES */

static public function obtenerDatosContadoresPaisesModel(){

    $stmt = Conexion::conectar()->prepare("SELECT 
    (SELECT COUNT(*) FROM cafeterias WHERE estado != 2) AS total_cafeterias,
    (SELECT COUNT(*) FROM ciudades WHERE estado != 2) AS total_ciudades;
    ");

    //$stmt->bindParam(':', ,PDO::PARAM_STR);

    $stmt -> execute();

    return $stmt -> fetch();

    $stmt = null;

}

/* OBTENER DATOS DE CONTADORES */


}