<?php 

require_once "conexion.php";

class CafeteriasMapaModel extends Conexion {

    
    /* OBTENER CAFETERIAS */
    
    static public function obtenerCafeteriasModel(){
    
        $stmt = Conexion::conectar()->prepare("SELECT * FROM cafeterias WHERE estado = 0");
        
        $stmt -> execute();
    
        return $stmt -> fetchAll();
    
        $stmt = null;
    
    }
    
    /* OBTENER CAFETERIAS */
    

}