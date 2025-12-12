<?php

require_once __DIR__ . '/../config/conexion.php';

class CafeteriasListaModel extends Conexion {

    
    static public function obtenerCafeteriasModel($datos) {
        $pagina = $datos['pagina'];
        $limite = 9; 
        $offset = ($pagina - 1) * $limite;

        $busqueda = '%' . $datos['busqueda'] . '%';
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
            AND (cafeterias.nombre LIKE :busqueda OR ciudades.nombre LIKE :busqueda)
            $ciudad
            $servicios
            LIMIT :offset, :limite");

        $stmt->bindParam(':busqueda', $busqueda, PDO::PARAM_STR);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindParam(':limite', $limite, PDO::PARAM_INT);

        if($datos['ciudad']!=''){
            $stmt->bindParam(':ciudad', $datos['ciudad'], PDO::PARAM_INT);
        }

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


    /* BUSCAR SERVICIOS DE CAFETERIAS */
    
    static public function buscarServiciosCafeteriasModel($id){
    
        $stmt = Conexion::conectar()->prepare("SELECT
            servicios.*
        FROM
            servicios
        INNER JOIN cafeterias_servicios ON cafeterias_servicios.id_servicio = servicios.id  
        WHERE cafeterias_servicios.estado =0 AND cafeterias_servicios.id_cafeteria=:id
        AND servicios.estado =0");
    
        $stmt->bindParam(':id', $id,PDO::PARAM_INT);
    
        $stmt -> execute();
    
        return $stmt -> fetchAll();
    
        $stmt = null;
    
    }
    
    /* BUSCAR SERVICIOS DE CAFETERIAS */
    
    
    
    /* BUSCAR COMENTARIOS */

    static public function buscarComentariosModel($offset, $comentariosPorPagina, $id) {
        $stmt = Conexion::conectar()->prepare("SELECT cafeterias_comentarios.*, 
        CONCAT(
            admin_usuarios.nombre,
            ' ',
            admin_usuarios.apellido
        ) AS nombre_usuario
        FROM cafeterias_comentarios
        INNER JOIN admin_usuarios ON cafeterias_comentarios.id_usuario = admin_usuarios.id 
        WHERE cafeterias_comentarios.estado = 0
        AND cafeterias_comentarios.id_cafeteria = :id
        ORDER BY cafeterias_comentarios.fecha_alta DESC
        LIMIT :offset, :limite
        ");

        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->bindParam(":offset", $offset, PDO::PARAM_INT);
        $stmt->bindParam(":limite", $comentariosPorPagina, PDO::PARAM_INT);

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    static public function contarTotalComentariosModel($cafeteria) {
        $stmt = Conexion::conectar()->prepare("SELECT COUNT(*) AS total FROM cafeterias_comentarios WHERE estado = 0 AND id_cafeteria = :id"); // Asegúrate de agregar las condiciones necesarias
        $stmt->bindParam("id", $cafeteria, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }
    
    
    // static public function buscarComentariosModel(){
    
    //     $stmt = Conexion::conectar()->prepare("SELECT cafeterias_comentarios.*, 
    //     CONCAT(
    //         admin_usuarios.nombre,
    //         ' ',
    //         admin_usuarios.apellido
    //     ) AS nombre_usuario
    //     FROM cafeterias_comentarios
    //     INNER JOIN admin_usuarios ON cafeterias_comentarios.id_usuario = admin_usuarios.id
    //     ");
    
    //     // $stmt->bindParam(':', ,PDO::PARAM_STR);
    
    //     $stmt -> execute();
    
    //     return $stmt -> fetchAll();
    
    //     $stmt = null;
    
    // }
    
    /* BUSCAR COMENTARIOS */
    
    
    /* REGISTRAR COMENTARIOS */
    
    static public function registrarComentarioModel($datos){
    
        $stmt = Conexion::conectar()->prepare("INSERT INTO cafeterias_comentarios (comentario, id_usuario, id_cafeteria, fecha_alta) VALUES (:comentario, :id_usuario, :id_cafeteria, :fecha_alta)");
    
        $stmt->bindParam(':id_cafeteria', $datos['id_cafeteria'], PDO::PARAM_INT);
        $stmt->bindParam(':id_usuario', $datos['id_usuario'], PDO::PARAM_INT);
        $stmt->bindParam(':comentario', $datos['comentario'], PDO::PARAM_STR);
        $stmt->bindParam(':fecha_alta', $datos['fecha_alta'], PDO::PARAM_STR);
    
        if($stmt->execute()){
            return 'success';
        }else{
            return 'error';
        }
        $stmt = null;
    }
    
    /* REGISTRAR COMENTARIOS */

    
    /* OBTENER SERVICIOS */
    
    static public function obtenerServiciosModel(){
    
        $stmt = Conexion::conectar()->prepare("SELECT * FROM servicios WHERE estado=0 ");
        
        $stmt -> execute();
    
        return $stmt -> fetchAll();
    
        $stmt = null;
    
    }
    
    /* OBTENER SERVICIOS */
    
    

}