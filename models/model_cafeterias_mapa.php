<?php 

require_once "conexion.php";

class CafeteriasMapaModel extends Conexion {

    
    /* OBTENER CAFETERIAS */
    
    static public function obtenerCafeteriasModel(){
    
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
            AND ciudades.estado = 0");
        
        $stmt -> execute();
    
        return $stmt -> fetchAll();
    
        $stmt = null;
    
    }
    
    /* OBTENER CAFETERIAS */
    

}