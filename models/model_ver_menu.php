<?php 

require_once "conexion.php";

class VerMenuModel extends Conexion {

    /* OBTENER CATEGORIAS */
    
    static public function obtenerCategoriasModel($cafeteria){
    
        $stmt = Conexion::conectar()->prepare("SELECT DISTINCT
            menu_categorias.*
        FROM
            menu_categorias
        INNER JOIN menu_subcategorias ON menu_subcategorias.id_categoria = menu_categorias.id
        INNER JOIN cafeterias_menu_subcategorias ON cafeterias_menu_subcategorias.id_subcategoria = menu_subcategorias.id
        INNER JOIN cafeterias ON cafeterias.id_usuario = cafeterias_menu_subcategorias.id_propietario
        WHERE cafeterias.id = :cafeteria 
        AND cafeterias.estado=0
        AND cafeterias_menu_subcategorias.estado = 1");
    
        $stmt->bindParam(':cafeteria', $cafeteria,PDO::PARAM_INT);
    
        $stmt -> execute();
    
        return $stmt -> fetchAll();
    
        $stmt = null;
    
    }
    
    /* OBTENER CATEGORIAS */

    /* OBTENER SUBCATEGORIAS */

    static public function obtenerSubcategoriasModel($cafeteria){

        $stmt = Conexion::conectar()->prepare("SELECT 
        menu_subcategorias.*
        FROM
            menu_subcategorias
        INNER JOIN 
            cafeterias_menu_subcategorias ON cafeterias_menu_subcategorias.id_subcategoria = menu_subcategorias.id
        INNER JOIN cafeterias ON cafeterias.id_usuario = cafeterias_menu_subcategorias.id_propietario
        WHERE cafeterias.id = :cafeteria
        AND menu_subcategorias.estado = 0
        AND cafeterias.estado=0
        AND cafeterias_menu_subcategorias.estado=1");

        $stmt->bindParam(':cafeteria', $cafeteria,PDO::PARAM_INT);
    
        $stmt -> execute();
    
        return $stmt -> fetchAll();
    
        $stmt = null;
    
    }
    
    /* OBTENER SUBCATEGORIAS */


    /* OBTENER PRODUCTOS */

    static public function obtenerProductosModel($subcategoria, $cafeteria){

        $stmt = Conexion::conectar()->prepare("SELECT
        menu_productos.id,
        menu_productos.nombre,
        COALESCE(cafeterias_menu_sucursales.estado, 'No') AS estado,
        COALESCE(cafeterias_menu_sucursales.id, 'No') AS id_registro,
        menu_productos.imagen
        FROM
            menu_productos
        LEFT JOIN cafeterias_menu_sucursales ON cafeterias_menu_sucursales.id_producto = menu_productos.id
            AND cafeterias_menu_sucursales.id_cafeteria = :id
            AND id_tamano = 0
        WHERE menu_productos.estado = 0
        AND menu_productos.id_subcategoria = :subcategoria
        AND cafeterias_menu_sucursales.estado = 1
        ");

        $stmt->bindParam(':subcategoria', $subcategoria,PDO::PARAM_INT);
        $stmt->bindParam(':id', $cafeteria,PDO::PARAM_INT);

        $stmt -> execute();
    
        return $stmt -> fetchAll();
    
        $stmt = null;
    
    }
    
    /* OBTENER PRODUCTOS */
}

?>