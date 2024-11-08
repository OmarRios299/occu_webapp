<?php

require_once "conexion.php";

class MenuSubcategoriasModel extends Conexion
{


    /* OBTENER SUBCATEGORIAS */

    static public function obtenerSubcategoriasModel()
    {

        $stmt = Conexion::conectar()->prepare("SELECT
        menu_subcategorias.*,
        menu_categorias.nombre AS categoria,
        COUNT(menu_productos.id) AS total_productos
        FROM
            menu_subcategorias
        LEFT JOIN menu_categorias ON menu_categorias.id = menu_subcategorias.id_categoria
        LEFT JOIN menu_productos ON menu_subcategorias.id = menu_productos.id_subcategoria
        WHERE
            menu_subcategorias.estado != 2
        GROUP BY
            menu_subcategorias.id
        ");

        //$stmt->bindParam(':', ,PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->fetchAll();

        $stmt = null;
    }

    /* OBTENER SUBCATEGORIAS */



    /* AGREGAR SUBCATEGORIAS */

    static public function agregarSubcategoriaModel($datos)
    {

        $conexion = Conexion::conectar();
        $stmt = $conexion->prepare("INSERT INTO menu_subcategorias(nombre, id_categoria) 
        VALUES (:nombre, :id_categoria)");

        $stmt->bindParam(':nombre', $datos['nombre'], PDO::PARAM_STR);
        $stmt->bindParam(':id_categoria', $datos['id_categoria'], PDO::PARAM_INT);
        
        if($stmt->execute()){
            return $conexion-> lastInsertId();
        }else{
            return "error";
        }
        $stmt = null;
        
    }

    /* AGREGAR SUBCATEGORIAS */
    
    
    /* BUSCAR SUBCATEGORIAS */
    
    static public function buscarSubcategoriaModel($id){
    
        $stmt = Conexion::conectar()->prepare("SELECT menu_subcategorias.* FROM menu_subcategorias WHERE menu_subcategorias.id=:id");
    
        $stmt->bindParam(':id', $id,PDO::PARAM_INT);
    
        $stmt -> execute();
    
        return $stmt -> fetch();
    
        $stmt = null;
    
    }
    
    /* BUSCAR SUBCATEGORIAS */
    
    
    /* EDITAR SUBCATEGORIAS */
    
    static public function editarSubcategoriaModel($datos){
    
        $stmt = Conexion::conectar()->prepare("UPDATE menu_subcategorias SET nombre=:nombre, id_categoria=:id_categoria WHERE id = :id");
    
        $stmt->bindParam(":id", $datos['id'], PDO::PARAM_INT);
        $stmt->bindParam(":nombre", $datos['nombre'], PDO::PARAM_STR);
        $stmt->bindParam(":id_categoria", $datos['id_categoria'], PDO::PARAM_INT);
    
        if($stmt->execute()){
            return 'success';
        }else{
            return 'error';
        }
    
        $stmt = null;
    
    }
    
    /* EDITAR SUBCATEGORIAS */
    
}
