<?php

require_once "conexion.php";

class MenuCategoriasModel extends Conexion
{


    /* OBTENER CATEGORIAS */

    static public function obtenerCategoriasModel()
    {

        $stmt = Conexion::conectar()->prepare("SELECT
        menu_categorias.*,
        CONCAT(admin_usuarios.nombre,' ',admin_usuarios.apellido) AS usuario_alta,
        COUNT(menu_productos.id) AS total_productos,
        COUNT(menu_subcategorias.id) AS total_subcategorias
        FROM
            menu_categorias
        LEFT JOIN menu_subcategorias ON menu_categorias.id = menu_subcategorias.id_categoria
        LEFT JOIN menu_productos ON menu_subcategorias.id = menu_productos.id_subcategoria
        INNER JOIN admin_usuarios ON menu_categorias.id_alta = admin_usuarios.id
        WHERE
            menu_categorias.estado != 2
        GROUP BY
            menu_categorias.id
        ORDER BY
            menu_categorias.id;
        ");

        //$stmt->bindParam(':', ,PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->fetchAll();

        $stmt = null;
    }

    /* OBTENER CATEGORIAS */



    /* AGREGAR CATEGORIAS */

    static public function agregarCategoriaModel($datos)
    {

        $conexion = Conexion::conectar();
        $stmt = $conexion->prepare("INSERT INTO menu_categorias(nombre, imagen, id_alta, fecha_alta, es_bebida) 
        VALUES (:nombre, 'views/assets/img/cafeteria_default.png', :id_alta, :fecha_alta, :es_bebida)");

        $stmt->bindParam(':nombre', $datos['nombre'], PDO::PARAM_STR);
        $stmt->bindParam(':id_alta', $datos['id_alta'], PDO::PARAM_INT);
        $stmt->bindParam(':fecha_alta', $datos['fecha_alta'], PDO::PARAM_STR);
        $stmt->bindParam(':es_bebida', $datos['es_bebida'], PDO::PARAM_STR);
        
        if($stmt->execute()){
            return $conexion-> lastInsertId();
        }else{
            return "error";
        }
        $stmt = null;
        
    }

    /* AGREGAR CATEGORIAS */
    
    
    /* EDITAR IMAGEN */
    
    static public function editarImagenModel($datos){
    
        $stmt = Conexion::conectar()->prepare("UPDATE menu_categorias SET imagen=:imagen WHERE id = :id");
    
        $stmt->bindParam(":id", $datos['id'], PDO::PARAM_INT);
        $stmt->bindParam(":imagen", $datos['imagen'], PDO::PARAM_STR);
    
        if($stmt->execute()){
            return 'success';
        }else{
            return 'error';
        }
    
        $stmt = null;
    
    }
    
    /* EDITAR IMAGEN */
    
    
    /* BUSCAR CATEGORIA */
    
    static public function buscarCategoriaModel($id){
    
        $stmt = Conexion::conectar()->prepare("SELECT menu_categorias.* FROM menu_categorias WHERE menu_categorias.id=:id");
    
        $stmt->bindParam(':id', $id,PDO::PARAM_INT);
    
        $stmt -> execute();
    
        return $stmt -> fetch();
    
        $stmt = null;
    
    }
    
    /* BUSCAR CATEGORIA */
    
    
    /* EDITAR CATEGORIA */
    
    static public function editarCategoriaModel($datos){
    
        $stmt = Conexion::conectar()->prepare("UPDATE menu_categorias SET nombre=:nombre, es_bebida=:es_bebida WHERE id = :id");
    
        $stmt->bindParam(":id", $datos['id'], PDO::PARAM_INT);
        $stmt->bindParam(":nombre", $datos['nombre'], PDO::PARAM_STR);
        $stmt->bindParam(':es_bebida', $datos['es_bebida'], PDO::PARAM_STR);
    
        if($stmt->execute()){
            return 'success';
        }else{
            return 'error';
        }
    
        $stmt = null;
    
    }
    
    /* EDITAR CATEGORIA */
    
}
