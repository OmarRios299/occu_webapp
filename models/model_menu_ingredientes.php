<?php

require_once "conexion.php";

class MenuIngredientesModel extends Conexion
{

    	static public function obtenerCategoriasModel(){

		$stmt = Conexion::conectar()->prepare("SELECT menu_ingredientes_categorias.* 
        FROM menu_ingredientes_categorias 
        WHERE menu_ingredientes_categorias.estado=0");
		
		$stmt -> execute();
	
		return $stmt -> fetchAll();
	
		$stmt = null;
	
	}

    static public function obtenerIngredientesModel()
    {

        $stmt = Conexion::conectar()->prepare("SELECT
        menu_ingredientes.*,
        menu_ingredientes_categorias.nombre AS categoria
        FROM
            menu_ingredientes
        INNER JOIN menu_ingredientes_categorias ON menu_ingredientes_categorias.id = menu_ingredientes.id_ingrediente_categoria
        WHERE
            menu_ingredientes.estado != 2
        GROUP BY
            menu_ingredientes.id
        ");

        //$stmt->bindParam(':', ,PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->fetchAll();

        $stmt = null;
    }



    static public function agregarIngredienteModel($datos)
    {

        $conexion = Conexion::conectar();
        $stmt = $conexion->prepare("INSERT INTO menu_ingredientes(nombre, id_ingrediente_categoria) 
        VALUES (:nombre, :id_ingrediente_categoria)");

        $stmt->bindParam(':nombre', $datos['nombre'], PDO::PARAM_STR);
        $stmt->bindParam(':id_ingrediente_categoria', $datos['id_ingrediente_categoria'], PDO::PARAM_INT);

        if ($stmt->execute()) {
            return $conexion->lastInsertId();
        } else {
            return "error";
        }
        $stmt = null;
    }


    static public function buscarIngredienteModel($id)
    {

        $stmt = Conexion::conectar()->prepare("SELECT menu_ingredientes.* FROM menu_ingredientes WHERE menu_ingredientes.id=:id");

        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetch();

        $stmt = null;
    }


    static public function editarIngredienteModel($datos)
    {

        $stmt = Conexion::conectar()->prepare("UPDATE menu_ingredientes SET nombre=:nombre, id_ingrediente_categoria=:id_ingrediente_categoria WHERE id = :id");

        $stmt->bindParam(":id", $datos['id'], PDO::PARAM_INT);
        $stmt->bindParam(":nombre", $datos['nombre'], PDO::PARAM_STR);
        $stmt->bindParam(":id_ingrediente_categoria", $datos['id_ingrediente_categoria'], PDO::PARAM_INT);

        if ($stmt->execute()) {
            return 'success';
        } else {
            return 'error';
        }

        $stmt = null;
    }
}
