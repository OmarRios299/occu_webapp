<?php

require_once "conexion.php";

class MenuProductosModel extends Conexion
{

    /* OBTENER PRODUCTOS */

    static public function obtenerProductosModel($datos)
    {
        $subcategoria ='';
        $categoria = '';
        $estatus = 'WHERE menu_productos.estado != 2';
        if ($datos['id_subcategoria']!='') {
            $subcategoria = 'AND menu_productos.id_subcategoria = :subcategoria';
        }else if ($datos['id_categoria']!='') {
            $cate=$datos['id_categoria'];
            $categoria = "AND menu_subcategorias.id_categoria = '$cate'";
        }
        if ($datos['estatus']=='Activas') {
            $estatus = 'WHERE menu_productos.estado = 0';
        }else if($datos['estatus']=='Inactivas'){
            $estatus = 'WHERE menu_productos.estado = 1';
        }

        $stmt = Conexion::conectar()->prepare("SELECT
        menu_productos.*,
        CONCAT(
            admin_usuarios.nombre,
            ' ',
            admin_usuarios.apellido
        ) AS usuario_alta,
        menu_subcategorias.nombre as subcategoria,
        menu_categorias.nombre AS categoria
        FROM
            menu_productos
        INNER JOIN admin_usuarios ON menu_productos.id_alta = admin_usuarios.id
        INNER JOIN menu_subcategorias ON menu_productos.id_subcategoria = menu_subcategorias.id
        INNER JOIN menu_categorias ON menu_subcategorias.id_categoria = menu_categorias.id
        $estatus
        $subcategoria
        $categoria
        GROUP BY
            menu_productos.id
        ORDER BY
            menu_productos.id;
        ");

        if ($datos['id_subcategoria']!='') {
            $stmt->bindParam(':subcategoria', $datos['id_subcategoria'],PDO::PARAM_INT);
        }

        $stmt->execute();

        return $stmt->fetchAll();

        $stmt = null;
    }

    /* OBTENER PRODUCTOS */


    /* OBTENER CATEGORIAS */

    static public function obtenerCategoriasModel()
    {
        $stmt = Conexion::conectar()->prepare("SELECT
            menu_categorias.*
            FROM
                menu_categorias
            WHERE menu_categorias.estado !=2
            ");

        //$stmt->bindParam(':', ,PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->fetchAll();

        $stmt = null;
    }

    /* OBTENER CATEGORIAS */
    

    /* OBTENER SUBCATEGORIAS */

    static public function obtenerSubcategoriasModel()
    {
        $stmt = Conexion::conectar()->prepare("SELECT
        menu_subcategorias.*,
        menu_categorias.nombre AS categoria
        FROM
            menu_subcategorias
        INNER JOIN menu_categorias ON menu_categorias.id = menu_subcategorias.id_categoria
        WHERE
            menu_subcategorias.estado != 2
            ");

        //$stmt->bindParam(':', ,PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->fetchAll();

        $stmt = null;
    }

    /* OBTENER SUBCATEGORIAS */


    /* AGREGAR PRODUCTOS */

    static public function agregarProductoModel($datos)
    {

        $conexion = Conexion::conectar();
        $stmt = $conexion->prepare("INSERT INTO menu_productos(nombre, id_subcategoria, imagen, id_alta, fecha_alta, registro_occu) 
        VALUES (:nombre, :id_subcategoria, 'views/assets/img/cafeteria_default.png', :id_alta, :fecha_alta, 1)");

        $stmt->bindParam(':nombre', $datos['nombre'], PDO::PARAM_STR);
        $stmt->bindParam(':id_subcategoria', $datos['id_subcategoria'], PDO::PARAM_INT);
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

        $stmt = Conexion::conectar()->prepare("UPDATE menu_productos SET imagen=:imagen WHERE id = :id");

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

        $stmt = Conexion::conectar()->prepare("SELECT menu_productos.* FROM menu_productos WHERE menu_productos.id=:id");

        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetch();

        $stmt = null;
    }

    /* BUSCAR PRODUCTO */


    /* EDITAR PRODUCTO */

    static public function editarProductoModel($datos)
    {

        $stmt = Conexion::conectar()->prepare("UPDATE menu_productos SET nombre=:nombre, id_subcategoria=:id_subcategoria WHERE id = :id");

        $stmt->bindParam(":id", $datos['id'], PDO::PARAM_INT);
        $stmt->bindParam(":id_subcategoria", $datos['id_subcategoria'], PDO::PARAM_INT);
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
