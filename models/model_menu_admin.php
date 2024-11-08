<?php 


require_once "conexion.php";

class AdminMenuModel extends Conexion {

    
    /* OBTENER CATEGORIAS DE MENU */
    
    static public function obtenerCategoriasMenuModel(){
    
        $stmt = Conexion::conectar()->prepare("SELECT 
        cafeterias_menu_categorias.*,
        COUNT(cafeterias_menu_productos.id) AS total_productos
        FROM cafeterias_menu_productos
        INNER JOIN cafeterias_menu_categorias ON cafeterias_menu_productos.id_categoria = cafeterias_menu_categorias.id
        WHERE cafeterias_menu_categorias.estado=0
        AND cafeterias_menu_productos.estado=0
        GROUP BY cafeterias_menu_categorias.id");
    
        $stmt -> execute();
    
        return $stmt -> fetchAll();
    
        $stmt = null;
    
    }
    
    /* OBTENER CATEGORIAS DE MENU */
    

}