<?php 


require_once "conexion.php";

class AdminMenuModel extends Conexion {

    
    /* OBTENER CATEGORIAS DE MENU */
    
    static public function obtenerCategoriasModel(){
    
        $stmt = Conexion::conectar()->prepare("SELECT * FROM menu_categorias WHERE estado=0;");
    
        $stmt -> execute();
    
        return $stmt -> fetchAll();
    
        $stmt = null;
    
    }
    
    /* OBTENER CATEGORIAS DE MENU */
    
    
    /* OBTENER SUBCATEGORIAS */
    
    static public function obtenerSubcategoriasModel(){
    
        $stmt = Conexion::conectar()->prepare("SELECT * FROM menu_subcategorias WHERE estado=0");
    
        $stmt -> execute();
    
        return $stmt -> fetchAll();
    
        $stmt = null;
    
    }
    
    /* OBTENER SUBCATEGORIAS */
    
    
    /* OBTENER PRODUCTOS */
    
    static public function obtenerProductosModel(){
    
        $stmt = Conexion::conectar()->prepare("SELECT * FROM menu_productos WHERE estado=0");
    
        $stmt -> execute();
    
        return $stmt -> fetchAll();
    
        $stmt = null;
    
    }
    
    /* OBTENER PRODUCTOS */
    
}