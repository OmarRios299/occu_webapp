<?php 


require_once "conexion.php";

class DashboardModel extends Conexion {

    /* OBTENER CIUDADES POR PAIS */

    static public function obtenerCiudadesPorPaisModel(){

        $stmt = Conexion::conectar()->prepare("SELECT
        ciudades.*,
        entidades_federativas.nombre AS entidad_federativa,
        -- Subconsulta para contar cafeterías
        (SELECT COUNT(*)
        FROM cafeterias 
        WHERE cafeterias.id_ciudad = ciudades.id 
        AND cafeterias.estado != 2) AS total_cafeterias,
        -- Subconsulta para contar propietarios
        (SELECT COUNT(*)
        FROM admin_usuarios 
        WHERE admin_usuarios.id_ciudad = ciudades.id 
        AND admin_usuarios.estado != 2 
        AND admin_usuarios.nivel = 'Propietario') AS total_propietarios,
        (SELECT COUNT(*)
        FROM admin_usuarios 
        WHERE admin_usuarios.id_ciudad = ciudades.id 
        AND admin_usuarios.estado != 2 
        AND admin_usuarios.nivel = 'Baristas') AS total_baristas,
        (SELECT COUNT(*)
        FROM admin_usuarios 
        WHERE admin_usuarios.id_ciudad = ciudades.id 
        AND admin_usuarios.estado != 2 
        AND admin_usuarios.nivel = 'Consumidores') AS total_consumidores
        FROM
            ciudades
        INNER JOIN entidades_federativas 
            ON ciudades.id_entidad_federativa = entidades_federativas.id
        WHERE
            entidades_federativas.id_pais = 1
            AND ciudades.estado != 2
        GROUP BY
            ciudades.id;


        ");

    //$stmt->bindParam(':id', $pais,PDO::PARAM_INT);

        $stmt -> execute();

        return $stmt -> fetchAll();

        $stmt = null;

    }

    /* OBTENER CIUDADES POR PAIS */

        
    /* OBTENER DATOS DE CONTADORES */

    static public function obtenerDatosContadoresModel(){

        $stmt = Conexion::conectar()->prepare("SELECT 
        (SELECT COUNT(*) FROM cafeterias WHERE estado != 2) AS total_cafeterias,
        (SELECT COUNT(*) FROM ciudades WHERE estado != 2) AS total_ciudades,
        (SELECT COUNT(*) FROM admin_usuarios WHERE estado != 2 AND nivel = 'Propietario') AS total_propietarios,
        (SELECT COUNT(*) FROM admin_usuarios WHERE estado != 2 AND nivel = 'Barista') AS total_baristas,
        (SELECT COUNT(*) FROM admin_usuarios WHERE estado != 2 AND nivel = 'Consumidor') AS total_consumidores
        ");

        //$stmt->bindParam(':', ,PDO::PARAM_STR);

        $stmt -> execute();

        return $stmt -> fetch();

        $stmt = null;

    }

    /* OBTENER DATOS DE CONTADORES */
}