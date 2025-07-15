<?php

require_once "conexion.php";

class CafeteriasMenuModel extends Conexion
{


    /* BUSCAR CAFETERÍA */

    static public function buscarCafeteriaModel($cafeteria)
    {
        $stmt = Conexion::conectar()->prepare("SELECT
            cafeterias.*
        FROM
            cafeterias
        WHERE id = :cafeteria");

        $stmt->bindParam(':cafeteria', $cafeteria, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetch();

        $stmt = null;
    }

    /* BUSCAR CAFETERÍA */


    /* OBTENER CATEGORIAS */

    static public function obtenerCategoriasModel($cafeteria)
    {

        $stmt = Conexion::conectar()->prepare("SELECT DISTINCT
            menu_categorias.*
        FROM
            menu_categorias
        INNER JOIN menu_subcategorias ON menu_subcategorias.id_categoria = menu_categorias.id
        INNER JOIN propietarios_menu_subcategorias ON propietarios_menu_subcategorias.id_subcategoria = menu_subcategorias.id
        INNER JOIN cafeterias ON cafeterias.id_usuario = propietarios_menu_subcategorias.id_propietario
        WHERE cafeterias.id = :cafeteria 
        AND cafeterias.estado=0
        AND propietarios_menu_subcategorias.estado = 1");

        $stmt->bindParam(':cafeteria', $cafeteria, PDO::PARAM_INT);

        $stmt->execute();

        $categorias = $stmt->fetchAll();

        return $categorias;
    }

    /* OBTENER CATEGORIAS */


    /* OBTENER SUBCATEGORIAS */

    static public function  obtenerSubcategoriasModel($cafeteria)
    {

        $stmt = Conexion::conectar()->prepare("SELECT 
        menu_subcategorias.*,
        menu_subcategorias.registro_occu,
        propietarios_menu_subcategorias.id_propietario,
        'No' AS extra
        FROM
            menu_subcategorias
        INNER JOIN 
            propietarios_menu_subcategorias ON propietarios_menu_subcategorias.id_subcategoria = menu_subcategorias.id
        INNER JOIN cafeterias ON cafeterias.id_usuario = propietarios_menu_subcategorias.id_propietario
        WHERE cafeterias.id = :cafeteria
        AND menu_subcategorias.estado = 0
        AND cafeterias.estado=0
        AND propietarios_menu_subcategorias.estado=1");

        $stmt->bindParam(':cafeteria', $cafeteria, PDO::PARAM_INT);

        $stmt->execute();

        $subcategorias = $stmt->fetchAll();

        return $subcategorias;
    }


    /* OBTENER SUBCATEGORIAS */


    /* OBTENER PRODUCTOS */

    static public function obtenerProductosModel($subcategoria, $cafeteria)
    {

        $stmt = Conexion::conectar()->prepare("SELECT
        menu_productos.id,
        menu_productos.nombre,
        COALESCE(propietarios_menu_cafeterias.estado, 'No') AS estado,
        menu_productos.imagen
        FROM
            menu_productos
        LEFT JOIN propietarios_menu_cafeterias ON propietarios_menu_cafeterias.id_producto = menu_productos.id
            AND propietarios_menu_cafeterias.id_cafeteria = :id
            AND id_tamano = 0
        WHERE menu_productos.estado = 0
        AND menu_productos.id_subcategoria = :subcategoria
        AND propietarios_menu_cafeterias.estado = 1
        ");

        $stmt->bindParam(':subcategoria', $subcategoria, PDO::PARAM_INT);
        $stmt->bindParam(':id', $cafeteria, PDO::PARAM_INT);

        $stmt->execute();
        $productos = $stmt->fetchAll();

        return $productos;
    }

    /* OBTENER PRODUCTOS */
}
