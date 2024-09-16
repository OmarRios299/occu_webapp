<?php

require_once "conexion.php";

class CafeteriasListaModel extends Conexion {

    
    static public function obtenerCafeteriasModel($datos) {
        $pagina = $datos['pagina'];
        $limite = 9; 
        $offset = ($pagina - 1) * $limite;

        $busqueda = '%' . $datos['busqueda'] . '%';

        $stmt = Conexion::conectar()->prepare("SELECT
            cafeterias.*,
            ciudades.nombre AS ciudad,
            entidades_federativas.nombre AS entidad_federativa,
            paises.nombre AS pais
            FROM cafeterias
            INNER JOIN ciudades ON cafeterias.id_ciudad = ciudades.id
            INNER JOIN entidades_federativas ON ciudades.id_entidad_federativa = entidades_federativas.id
            INNER JOIN paises ON entidades_federativas.id_pais = paises.id
            WHERE cafeterias.estado = 0 
            AND paises.estado = 0 
            AND ciudades.estado = 0
            AND (cafeterias.nombre LIKE :busqueda OR ciudades.nombre LIKE :busqueda)
            LIMIT :offset, :limite");

        $stmt->bindParam(':busqueda', $busqueda, PDO::PARAM_STR);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindParam(':limite', $limite, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetchAll();
    }

    static public function contarTotalCafeterias($busqueda = '') {
        $busqueda = '%' . $busqueda . '%';

        $stmt = Conexion::conectar()->prepare("SELECT COUNT(*) as total FROM cafeterias 
            INNER JOIN ciudades ON cafeterias.id_ciudad = ciudades.id
            INNER JOIN entidades_federativas ON ciudades.id_entidad_federativa = entidades_federativas.id
            INNER JOIN paises ON entidades_federativas.id_pais = paises.id
            WHERE cafeterias.estado = 0 
            AND paises.estado = 0 
            AND ciudades.estado = 0
            AND (cafeterias.nombre LIKE :busqueda OR ciudades.nombre LIKE :busqueda)");

        $stmt->bindParam(':busqueda', $busqueda, PDO::PARAM_STR);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }

    /* OBTENER CAFETERIAS */
    
    static public function obtenerDatosCafeteriaModel($id){
    
        $stmt = Conexion::conectar()->prepare("SELECT
        cafeterias.*,
        ciudades.nombre AS ciudad,
        entidades_federativas.nombre AS entidad_federativa,
        paises.nombre AS pais
        FROM
            cafeterias
        INNER JOIN ciudades ON cafeterias.id_ciudad = ciudades.id
        INNER JOIN entidades_federativas ON ciudades.id_entidad_federativa = entidades_federativas.id
        INNER JOIN paises ON entidades_federativas.id_pais = paises.id
        WHERE
            cafeterias.id = :id");
    
        $stmt->bindParam(':id', $id,PDO::PARAM_STR);
    
        $stmt -> execute();
    
        return $stmt -> fetch();
    
        $stmt = null;
    
    }
    
    /* OBTENER CAFETERIAS */
    
    
    /* OBTENER LAS IMAGENES DEL CAROUSEL */
    
    static public function obtenerImagenesModel($id){
    
        $stmt = Conexion::conectar()->prepare("SELECT cafeterias_imagenes.* FROM cafeterias_imagenes 
        WHERE id_cafeteria = :id
        AND estado =0
        ");
    
        $stmt->bindParam(':id', $id,PDO::PARAM_INT);
    
        $stmt -> execute();
    
        return $stmt -> fetchAll();
    
        $stmt = null;
    
    }
    
    /* OBTENER LAS IMAGENES DEL CAROUSEL */
    

}