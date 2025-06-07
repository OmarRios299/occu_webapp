<?php

require_once "conexion.php";

class VerMenuModel extends Conexion
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

        $categorias = array_merge(
            $categorias,
            self::obtenerCategoriasExtraModel($cafeteria)
        );
        return $categorias;
    }

    static public function obtenerCategoriasExtraModel($cafeteria)
    {

        $stmt = Conexion::conectar()->prepare("SELECT DISTINCT
            menu_categorias.*
        FROM
            menu_categorias
        INNER JOIN propietarios_menu_subcategorias_extra ON propietarios_menu_subcategorias_extra.id_categoria = menu_categorias.id
        INNER JOIN cafeterias ON cafeterias.id_usuario = propietarios_menu_subcategorias_extra.id_propietario
        WHERE cafeterias.id = :cafeteria 
        AND cafeterias.estado=0
        AND propietarios_menu_subcategorias_extra.estado = 1");

        $stmt->bindParam(':cafeteria', $cafeteria, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetchAll();

        $stmt = null;
    }

    /* OBTENER CATEGORIAS */


    /* OBTENER SUBCATEGORIAS */

    static public function  obtenerSubcategoriasModel($cafeteria)
    {

        $stmt = Conexion::conectar()->prepare("SELECT 
        menu_subcategorias.*,
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

        $subcategorias = array_merge(
            $subcategorias,
            self::obtenerSubcategoriasExtraModel($subcategorias[0]['id_propietario'])
        );
        return $subcategorias;
    }


    static public function obtenerSubcategoriasExtraModel($id_propietario)
    {
        $sql = "SELECT 
                    propietarios_menu_subcategorias_extra.*,
                    'Si' AS extra
                FROM propietarios_menu_subcategorias_extra
                INNER JOIN menu_categorias ON menu_categorias.id = propietarios_menu_subcategorias_extra.id_categoria
                WHERE propietarios_menu_subcategorias_extra.id_propietario = :id_propietario
                 AND propietarios_menu_subcategorias_extra.estado = 1";


        $stmt = Conexion::conectar()->prepare($sql);
        $stmt->bindParam(':id_propietario', $id_propietario, PDO::PARAM_INT);

        $stmt->execute();
        return $stmt->fetchAll();
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

        $productos = array_merge(
            $productos,
            self::obtenerProductosExtraModel($subcategoria, $cafeteria)
        );
        return $productos;
    }

    static public function obtenerProductosExtraModel($subcategoria, $idCafeteria, $subcategoriaExtra = false)
    {
        $filtro = "  AND propietarios_menu_productos_extra.id_subcategoria = :subcategoria";

        if ($subcategoriaExtra) {
            $filtro = "  AND propietarios_menu_productos_extra.id_subcategoria_extra = :subcategoria";
        }

        $cafeteria = self::buscarCafeteriaModel($idCafeteria);

        $stmt = Conexion::conectar()->prepare("SELECT
        propietarios_menu_productos_extra.id,
        propietarios_menu_productos_extra.nombre,
        COALESCE(propietarios_menu_cafeterias.estado, 'No') AS estado,
        propietarios_menu_productos_extra.imagen
        FROM
            propietarios_menu_cafeterias
        INNER JOIN propietarios_menu_productos_extra ON propietarios_menu_cafeterias.id_producto_extra = propietarios_menu_productos_extra.id
            AND propietarios_menu_productos_extra.id_propietario = :id
            AND propietarios_menu_productos_extra.estado != 2
        WHERE propietarios_menu_cafeterias.estado = 1
        AND propietarios_menu_cafeterias.id_tamano = 0
        $filtro
        ");

        $stmt->bindParam(':subcategoria', $subcategoria, PDO::PARAM_INT);
        $stmt->bindParam(':id', $cafeteria['id_usuario'], PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetchAll();

        $stmt = null;
    }

    /* OBTENER PRODUCTOS */
}
