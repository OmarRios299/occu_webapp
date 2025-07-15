<?php

require_once "conexion.php";

class MenuIngredientesCategoriasModel extends Conexion
{


    static public function obtenerCategoriasModel()
    {

        $stmt = Conexion::conectar()->prepare("SELECT
        menu_ingredientes_categorias.*
        FROM
            menu_ingredientes_categorias
        WHERE
            menu_ingredientes_categorias.estado != 2
        ");

        //$stmt->bindParam(':', ,PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->fetchAll();

        $stmt = null;
    }


    static public function agregarCategoriaModel($datos)
    {

        $conexion = Conexion::conectar();
        $stmt = $conexion->prepare("INSERT INTO menu_ingredientes_categorias(nombre) 
        VALUES (:nombre)");

        $stmt->bindParam(':nombre', $datos['nombre'], PDO::PARAM_STR);
        
        if($stmt->execute()){
            return $conexion-> lastInsertId();
        }else{
            return "error";
        }
        $stmt = null;
        
    }

    
    static public function buscarCategoriaModel($id){
    
        $stmt = Conexion::conectar()->prepare("SELECT menu_ingredientes_categorias.* FROM menu_ingredientes_categorias WHERE menu_ingredientes_categorias.id=:id");
    
        $stmt->bindParam(':id', $id,PDO::PARAM_INT);
    
        $stmt -> execute();
    
        return $stmt -> fetch();
    
        $stmt = null;
    
    }
    
    
    static public function editarCategoriaModel($datos){
    
        $stmt = Conexion::conectar()->prepare("UPDATE menu_ingredientes_categorias SET nombre=:nombre WHERE id = :id");
    
        $stmt->bindParam(":id", $datos['id'], PDO::PARAM_INT);
        $stmt->bindParam(":nombre", $datos['nombre'], PDO::PARAM_STR);
    
        if($stmt->execute()){
            return 'success';
        }else{
            return 'error';
        }
    
        $stmt = null;
    
    }
    
}
