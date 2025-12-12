<?php 

require_once __DIR__ . '/../config/conexion.php';

class CafeteriasMapaModel extends Conexion {

    
    /* OBTENER CAFETERIAS */
    
    static public function obtenerCafeteriasModel($datos){

        $ciudad='';
        if($datos['ciudad']!=''){
            $ciudad ='AND cafeterias.id_ciudad = :ciudad';
        }
        
        $servicios = ''; 
        if (isset($datos['servicios']) && !empty($datos['servicios'])) {
            $servicios_ids = array_map(function($servicio) {
                return $servicio['id'];
            }, $datos['servicios']);
            
            $servicios_ids_str = implode(',', $servicios_ids);
            
            $servicios = " AND cafeterias.id IN (
                            SELECT id_cafeteria 
                            FROM cafeterias_servicios 
                            WHERE estado = 0 AND id_servicio IN ($servicios_ids_str))";
        }

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
            $ciudad
            $servicios");

        if($datos['ciudad']!=''){
            $stmt->bindParam(':ciudad', $datos['ciudad'], PDO::PARAM_INT);
        }

        $stmt->execute();

        return $stmt->fetchAll();
    
    }
    
    /* OBTENER CAFETERIAS */
    

}