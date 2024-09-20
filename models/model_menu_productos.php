<?php

require_once "conexion.php";

class MenuProductosModel extends Conexion
{


    /* OBTENER PRODUCTOS */

    static public function obtenerProductosModel()
    {

        $stmt = Conexion::conectar()->prepare("SELECT
        cafeterias_menu_productos.*,
        CONCAT(
            admin_usuarios.nombre,
            ' ',
            admin_usuarios.apellido
        ) AS usuario_alta,
        cafeterias_menu_categorias.nombre AS categoria
        FROM
            cafeterias_menu_productos
        INNER JOIN admin_usuarios ON cafeterias_menu_productos.id_alta = admin_usuarios.id
        INNER JOIN cafeterias_menu_categorias ON cafeterias_menu_productos.id_categoria = cafeterias_menu_categorias.id
        WHERE cafeterias_menu_productos.estado !=2
        GROUP BY
            cafeterias_menu_productos.id
        ORDER BY
            cafeterias_menu_productos.id;
        ");

        //$stmt->bindParam(':', ,PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->fetchAll();

        $stmt = null;
    }

    /* OBTENER PRODUCTOS */

    /* OBTENER PRODUCTOS */

    static public function obtenerCategoriasModel()
    {
        $stmt = Conexion::conectar()->prepare("SELECT
            cafeterias_menu_categorias.*
            FROM
                cafeterias_menu_categorias
            WHERE cafeterias_menu_categorias.estado !=2
            ");

        //$stmt->bindParam(':', ,PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->fetchAll();

        $stmt = null;
    }

    /* OBTENER PRODUCTOS */


    /* AGREGAR PRODUCTOS */

    static public function agregarProductoModel($datos)
    {

        $conexion = Conexion::conectar();
        $stmt = $conexion->prepare("INSERT INTO cafeterias_menu_productos(nombre, id_categoria, imagen, id_alta, fecha_alta) 
        VALUES (:nombre, :id_categoria, 'views/assets/img/cafeteria_default.png', :id_alta, :fecha_alta)");

        $stmt->bindParam(':nombre', $datos['nombre'], PDO::PARAM_STR);
        $stmt->bindParam(':id_categoria', $datos['id_categoria'], PDO::PARAM_INT);
        $stmt->bindParam(':id_alta', $datos['id_alta'], PDO::PARAM_INT);
        $stmt->bindParam(':fecha_alta', $datos['fecha_alta'], PDO::PARAM_STR);

        if ($stmt->execute()) {
            return $conexion->lastInsertId();
        } else {
            return "error";
        }
        $stmt = null;
    }

    /* AGREGAR PRODUCTOS */


    /* EDITAR IMAGEN */

    static public function editarImagenModel($datos)
    {

        $stmt = Conexion::conectar()->prepare("UPDATE cafeterias_menu_productos SET imagen=:imagen WHERE id = :id");

        $stmt->bindParam(":id", $datos['id'], PDO::PARAM_INT);
        $stmt->bindParam(":imagen", $datos['imagen'], PDO::PARAM_STR);

        if ($stmt->execute()) {
            return 'success';
        } else {
            return 'error';
        }

        $stmt = null;
    }

    /* EDITAR IMAGEN */


    /* BUSCAR PRODUCTO */

    static public function buscarProductoModel($id)
    {

        $stmt = Conexion::conectar()->prepare("SELECT cafeterias_menu_productos.* FROM cafeterias_menu_productos WHERE cafeterias_menu_productos.id=:id");

        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetch();

        $stmt = null;
    }

    /* BUSCAR PRODUCTO */


    /* EDITAR PRODUCTO */

    static public function editarProductoModel($datos)
    {

        $stmt = Conexion::conectar()->prepare("UPDATE cafeterias_menu_productos SET nombre=:nombre, id_categoria=:id_categoria WHERE id = :id");

        $stmt->bindParam(":id", $datos['id'], PDO::PARAM_INT);
        $stmt->bindParam(":id_categoria", $datos['id_categoria'], PDO::PARAM_INT);
        $stmt->bindParam(":nombre", $datos['nombre'], PDO::PARAM_STR);

        if ($stmt->execute()) {
            return 'success';
        } else {
            return 'error';
        }

        $stmt = null;
    }

    /* EDITAR PRODUCTO */
}
