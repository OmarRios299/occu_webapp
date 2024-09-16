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
    

    // Obtener los horarios simples de una cafetería
    static public function obtenerHorariosSimplesCafeteriaModel($id_cafeteria) {
        $conexion = Conexion::conectar();
        $stmt = $conexion->prepare("SELECT horario_apertura, horario_cierre FROM cafeterias WHERE id = :id_cafeteria");
        $stmt->bindParam(':id_cafeteria', $id_cafeteria, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Obtener los horarios detallados (JSON) de una cafetería
    static public function obtenerHorariosDetalladosCafeteriaModel($id_cafeteria) {
        $conexion = Conexion::conectar();
        $stmt = $conexion->prepare("SELECT dia, hora_apertura, hora_cierre, cerrado FROM cafeteria_horarios WHERE id_cafeteria = :id_cafeteria");
        $stmt->bindParam(':id_cafeteria', $id_cafeteria, PDO::PARAM_INT);
        $stmt->execute();
        
        $horarios = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $horarios[$row['dia']] = [
                'apertura' => $row['hora_apertura'],
                'cierre' => $row['hora_cierre'],
                'cerrado' => $row['cerrado']
            ];
        }

        return $horarios;
    }
    

}