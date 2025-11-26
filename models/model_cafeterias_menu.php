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


    static public function buscarProductoModel($datos)
    {

        $stmt = Conexion::conectar()->prepare("SELECT
        mp.id, 
        mp.nombre, 
        mp.imagen,
        pmc.precio
        FROM propietarios_menu_cafeterias pmc
        INNER JOIN menu_productos mp ON pmc.id_producto = mp.id
        WHERE pmc.id_producto = :id_producto
        AND pmc.id_cafeteria = :id_cafeteria");

        $stmt->bindParam(":id_producto", $datos['id_producto'], PDO::PARAM_INT);
        $stmt->bindParam(":id_cafeteria", $datos['id_cafeteria'], PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetch();
    }


    static public function buscarCategoriasIngredientesModel($datos)
    {

        $stmt = Conexion::conectar()->prepare("SELECT 
        mic.id, 
        mic.nombre,
        mic.obligatoria
        FROM propietarios_menu_ingredientes pmi 
            JOIN menu_ingredientes mi ON mi.id = pmi.id_ingrediente
            JOIN menu_ingredientes_categorias mic ON mic.id = mi.id_ingrediente_categoria
        WHERE mi.estado = 0
            AND mic.estado = 0
            AND pmi.estado = 1
            AND pmi.id_cafeteria = :id_cafeteria
            AND pmi.id_producto = :id_producto
        GROUP BY mic.id");

        $stmt->bindParam(":id_producto", $datos['id_producto'], PDO::PARAM_INT);
        $stmt->bindParam(":id_cafeteria", $datos['id_cafeteria'], PDO::PARAM_INT);

        $stmt->execute();

        $categorias = $stmt->fetchAll();

        foreach ($categorias as &$cat) {
            $cat['ingredientes'] = self::buscarIngredientesModel($datos, $cat['id']);
        }

        return $categorias;
    }

    static public function buscarIngredientesModel($datos, $categoria)
    {

        $stmt = Conexion::conectar()->prepare("SELECT 
        mi.id, 
        mi.nombre, 
        pmi.costo_extra, 
        pmi.cantidad_gratis, 
        pmi.precio
        FROM propietarios_menu_ingredientes pmi 
            JOIN menu_ingredientes mi ON mi.id = pmi.id_ingrediente
        WHERE mi.estado = 0
            AND pmi.estado = 1
            AND pmi.id_cafeteria = :id_cafeteria
            AND pmi.id_producto = :id_producto
            AND mi.id_ingrediente_categoria = :id_categoria");

        $stmt->bindParam(":id_producto", $datos['id_producto'], PDO::PARAM_INT);
        $stmt->bindParam(":id_cafeteria", $datos['id_cafeteria'], PDO::PARAM_INT);
        $stmt->bindParam(":id_categoria", $categoria, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetchAll();
    }


    static public function buscarTamanosModel($datos)
    {

        $stmt = Conexion::conectar()->prepare("SELECT 
        pt.id,
        pt.nombre, 
        pt.unidad_medida, 
        pt.medida,
        pm.precio
        FROM propietarios_menu_cafeterias pm 
            JOIN menu_productos_tamanos pt ON pt.id = pm.id_tamano
        WHERE pm.id_tamano != 0
            AND pm.estado = 1
            AND pt.estado = 0
            AND pm.id_cafeteria = :id_cafeteria
            AND pm.id_producto = :id_producto
        ORDER BY pt.id ASC");

        $stmt->bindParam(":id_producto", $datos['id_producto'], PDO::PARAM_INT);
        $stmt->bindParam(":id_cafeteria", $datos['id_cafeteria'], PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetchAll();
    }

    static public function buscarCarritoModel($datos)
    {

        $stmt = Conexion::conectar()->prepare("SELECT vc.*, 
        c.imagen AS imagen_cafeteria,
        c.nombre
        FROM ventas_carrito vc
        INNER JOIN cafeterias c ON c.id = vc.id_cafeteria
        WHERE vc.estado = 0
        AND vc.id_usuario = :id_usuario
       -- AND vc.id_cafeteria = :id_cafeteria
        ");

        $stmt->bindParam(":id_usuario", $datos['id_usuario'], PDO::PARAM_INT);
        //$stmt->bindParam(":id_cafeteria", $datos['id_cafeteria'], PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetch();
    }



    static public function crearCarritoModel($datos)
    {
        $conexion = Conexion::conectar();
        $stmt = $conexion->prepare("INSERT INTO ventas_carrito(
            id_cafeteria,
            id_usuario,
            fecha_alta
        )
        VALUES(
            :id_cafeteria,
            :id_usuario,
            :fecha_alta
        )");

        $stmt->bindParam(':id_cafeteria', $datos['id_cafeteria'], PDO::PARAM_INT);
        $stmt->bindParam(':id_usuario', $datos['id_usuario'], PDO::PARAM_INT);
        $stmt->bindParam(':fecha_alta', $datos['fecha_alta'], PDO::PARAM_STR);

        if ($stmt->execute()) {
            return $conexion->lastInsertId();
        } else {
            return "error";
        }
        $stmt = null;
    }


    static public function carritoItemsModel($datos, $id_carrito)
    {

        $conexion = Conexion::conectar();
        $stmt = $conexion->prepare("INSERT INTO ventas_carrito_items(
            id_carrito,
            id_producto,
            id_tamano,
            cantidad,
            fecha_alta
        )
        VALUES(
            :id_carrito,
            :id_producto,
            :id_tamano,
            :cantidad,
            :fecha_alta
        )");

        $stmt->bindParam(':id_carrito', $id_carrito, PDO::PARAM_INT);
        $stmt->bindParam(':id_producto', $datos['id_producto'], PDO::PARAM_INT);
        $stmt->bindParam(':id_tamano', $datos['id_tamano'], PDO::PARAM_INT);
        $stmt->bindParam(':cantidad', $datos['cantidad'], PDO::PARAM_STR);
        $stmt->bindParam(':fecha_alta', $datos['fecha_alta'], PDO::PARAM_STR);

        if ($stmt->execute()) {
            return $conexion->lastInsertId();
        } else {
            return "error";
        }
        $stmt = null;
    }


    static public function carritoItemsIngredientesModel($datos, $ingrediente, $id_item)
    {

        $stmt = Conexion::conectar()->prepare("INSERT INTO ventas_carrito_items_ingredientes(
            id_carrito_item,
            id_ingrediente,
            cantidad,
            fecha_alta
        )
        VALUES(
            :id_carrito_item,
            :id_ingrediente,
            :cantidad,
            :fecha_alta
        )");

        $stmt->bindParam(':id_carrito_item', $id_item, PDO::PARAM_INT);
        $stmt->bindParam(':id_ingrediente', $ingrediente['id'], PDO::PARAM_INT);
        $stmt->bindParam(':cantidad', $ingrediente['cantidad'], PDO::PARAM_STR);
        $stmt->bindParam(':fecha_alta', $datos['fecha_alta'], PDO::PARAM_STR);

        if ($stmt->execute()) {
            return 'success';
        } else {
            return 'error';
        }
        $stmt = null;
    }

    static public function eliminarCarritoCompletoModel($id_usuario, $id_carrito)
    {
        $db = Conexion::conectar();

        $db->beginTransaction();

        // 1. Desactivar carrito
        $sql1 = "UPDATE ventas_carrito 
                 SET estado = 2 
                 WHERE id_usuario = :id_usuario";
        $stmt = $db->prepare($sql1);
        $stmt->execute([':id_usuario' => $id_usuario]);

        // 2. Desactivar items
        $sql2 = "UPDATE ventas_carrito_items 
                 SET estado = 2 
                 WHERE id_carrito = :id_carrito";
        $stmt = $db->prepare($sql2);
        $stmt->execute([':id_carrito' => $id_carrito]);

        // 3. Desactivar ingredientes
        $sql3 = "UPDATE ventas_carrito_items_ingredientes ing
                 INNER JOIN ventas_carrito_items item 
	                 ON item.id = ing.id_carrito_item
                 SET ing.estado = 2
                 WHERE item.id_carrito = :id_carrito";
        $stmt = $db->prepare($sql3);
        $stmt->execute([':id_carrito' => $id_carrito]);

        $db->commit();
        return "success";
    }

    static public function carritoTieneItemsModel($id_carrito)
    {
        $stmt = Conexion::conectar()->prepare("SELECT COUNT(*) AS total 
        FROM ventas_carrito_items 
        WHERE id_carrito = :id_carrito AND estado = 0
        ");

        $stmt->bindParam(":id_carrito", $id_carrito, PDO::PARAM_INT);
        $stmt->execute();
        $res = $stmt->fetch();

        return $res['total'] > 0;
    }

    static public function buscarItemRepetidoModel($id_carrito, $id_producto, $id_tamano, $ingredientes)
    {
        $conexion = Conexion::conectar();

        // 🔥 Convertir ingredientes front si llegan como JSON string
        if (!is_array($ingredientes)) {
            $ingredientes = json_decode($ingredientes, true);
        }

        // 🔥 Ordenar ingredientes del front por ID
        usort($ingredientes, function ($a, $b) {
            return $a['id'] <=> $b['id'];
        });

        // Buscar items existentes con mismo producto y tamaño
        $stmt = $conexion->prepare("
        SELECT id 
        FROM ventas_carrito_items
        WHERE id_carrito = :id_carrito
          AND id_producto = :id_producto
          AND id_tamano = :id_tamano
          AND estado = 0
    ");

        $stmt->bindParam(":id_carrito", $id_carrito, PDO::PARAM_INT);
        $stmt->bindParam(":id_producto", $id_producto, PDO::PARAM_INT);
        $stmt->bindParam(":id_tamano", $id_tamano, PDO::PARAM_INT);
        $stmt->execute();

        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (!$items) return false;

        foreach ($items as $item) {

            // Ingredientes del item en BD
            $stmtIng = $conexion->prepare("
            SELECT id_ingrediente AS id, cantidad
            FROM ventas_carrito_items_ingredientes
            WHERE id_carrito_item = :item_id
            ORDER BY id_ingrediente ASC
        ");
            $stmtIng->bindParam(":item_id", $item['id'], PDO::PARAM_INT);
            $stmtIng->execute();
            $ingredientesBD = $stmtIng->fetchAll(PDO::FETCH_ASSOC);

            // 🔥 Normalizar tipos (int) para evitar diferencias por strings
            $ingredientesBD = array_map(function ($ing) {
                return [
                    'id' => (int)$ing['id'],
                    'cantidad' => (int)$ing['cantidad']
                ];
            }, $ingredientesBD);

            $ingredientesFront = array_map(function ($ing) {
                return [
                    'id' => (int)$ing['id'],
                    'cantidad' => (int)$ing['cantidad']
                ];
            }, $ingredientes);

            // 🔥 Ordenar ambos para garantizar igualdad exacta
            usort($ingredientesBD, fn($a, $b) => $a['id'] <=> $b['id']);
            usort($ingredientesFront, fn($a, $b) => $a['id'] <=> $b['id']);

            // 🔥 Comparación REAL
            if ($ingredientesBD == $ingredientesFront) {
                return $item['id']; // ✔ COINCIDENCIA EXACTA
            }
        }

        return false;
    }


    static public function aumentarCantidadItemModel($id_item)
    {
        $stmt = Conexion::conectar()->prepare("UPDATE ventas_carrito_items
        SET cantidad = cantidad + 1
        WHERE id = :id_item");

        $stmt->bindParam(":id_item", $id_item, PDO::PARAM_INT);

        return $stmt->execute() ? "success" : "error";
    }
}
