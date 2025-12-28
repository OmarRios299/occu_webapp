<?php

require_once __DIR__ . '/../config/conexion.php';

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
        // Obtener id_propietario desde la cafetería
        $stmt_propietario = Conexion::conectar()->prepare("SELECT id_usuario AS id_propietario FROM cafeterias WHERE id = :id_cafeteria");
        $stmt_propietario->bindParam(':id_cafeteria', $cafeteria, PDO::PARAM_INT);
        $stmt_propietario->execute();
        $cafeteria_data = $stmt_propietario->fetch();
        
        if (!$cafeteria_data) {
            return array();
        }
        
        $id_propietario = $cafeteria_data['id_propietario'];

        $stmt = Conexion::conectar()->prepare("SELECT
        menu_productos.id,
        menu_productos.nombre,
        propietarios_productos.estado,
        menu_productos.imagen
        FROM
            menu_productos
        INNER JOIN propietarios_productos ON propietarios_productos.id_producto = menu_productos.id
            AND propietarios_productos.id_propietario = :id_propietario
            AND propietarios_productos.id_cafeteria = :id_cafeteria
            AND propietarios_productos.estado = 1
        WHERE menu_productos.estado = 0
        AND menu_productos.id_subcategoria = :subcategoria
        ");

        $stmt->bindParam(':subcategoria', $subcategoria, PDO::PARAM_INT);
        $stmt->bindParam(':id_propietario', $id_propietario, PDO::PARAM_INT);
        $stmt->bindParam(':id_cafeteria', $cafeteria, PDO::PARAM_INT);

        $stmt->execute();
        $productos = $stmt->fetchAll();

        return $productos;
    }


    static public function buscarProductoModel($datos)
    {
        // Obtener id_propietario desde la cafetería
        $stmt_propietario = Conexion::conectar()->prepare("SELECT id_usuario AS id_propietario FROM cafeterias WHERE id = :id_cafeteria");
        $stmt_propietario->bindParam(':id_cafeteria', $datos['id_cafeteria'], PDO::PARAM_INT);
        $stmt_propietario->execute();
        $cafeteria_data = $stmt_propietario->fetch();
        
        if (!$cafeteria_data) {
            return null;
        }
        
        $id_propietario = $cafeteria_data['id_propietario'];
        
        // Obtener el id_propietario_producto
        $stmt_producto = Conexion::conectar()->prepare("SELECT id FROM propietarios_productos
        WHERE id_producto = :id_producto
        AND id_propietario = :id_propietario
        AND id_cafeteria = :id_cafeteria");
        
        $stmt_producto->bindParam(':id_producto', $datos['id_producto'], PDO::PARAM_INT);
        $stmt_producto->bindParam(':id_propietario', $id_propietario, PDO::PARAM_INT);
        $stmt_producto->bindParam(':id_cafeteria', $datos['id_cafeteria'], PDO::PARAM_INT);
        $stmt_producto->execute();
        $producto = $stmt_producto->fetch();
        
        if (!$producto) {
            return null;
        }

        // Obtener datos del producto y precio_base (para alimentos sin tamaño)
        $stmt = Conexion::conectar()->prepare("SELECT
        mp.id, 
        mp.nombre, 
        mp.imagen,
        pp.precio_base AS precio
        FROM propietarios_productos pp
        INNER JOIN menu_productos mp ON pp.id_producto = mp.id
        WHERE pp.id_producto = :id_producto
        AND pp.id_propietario = :id_propietario
        AND pp.id_cafeteria = :id_cafeteria");

        $stmt->bindParam(":id_producto", $datos['id_producto'], PDO::PARAM_INT);
        $stmt->bindParam(":id_propietario", $id_propietario, PDO::PARAM_INT);
        $stmt->bindParam(":id_cafeteria", $datos['id_cafeteria'], PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetch();
    }


    static public function buscarCategoriasIngredientesModel($datos)
    {
        // Obtener id_propietario desde la cafetería
        $stmt_propietario = Conexion::conectar()->prepare("SELECT id_usuario AS id_propietario FROM cafeterias WHERE id = :id_cafeteria");
        $stmt_propietario->bindParam(':id_cafeteria', $datos['id_cafeteria'], PDO::PARAM_INT);
        $stmt_propietario->execute();
        $cafeteria_data = $stmt_propietario->fetch();
        
        if (!$cafeteria_data) {
            return array();
        }
        
        $id_propietario = $cafeteria_data['id_propietario'];
        
        // Obtener el id_propietario_producto
        $stmt_producto = Conexion::conectar()->prepare("SELECT id FROM propietarios_productos
        WHERE id_producto = :id_producto
        AND id_propietario = :id_propietario
        AND id_cafeteria = :id_cafeteria");
        
        $stmt_producto->bindParam(':id_producto', $datos['id_producto'], PDO::PARAM_INT);
        $stmt_producto->bindParam(':id_propietario', $id_propietario, PDO::PARAM_INT);
        $stmt_producto->bindParam(':id_cafeteria', $datos['id_cafeteria'], PDO::PARAM_INT);
        $stmt_producto->execute();
        $producto = $stmt_producto->fetch();
        
        if (!$producto) {
            return array();
        }
        
        $id_propietario_producto = $producto['id'];

        // Obtener el id_producto desde el id_propietario_producto
        $stmt_producto_id = Conexion::conectar()->prepare("SELECT id_producto FROM propietarios_productos WHERE id = :id_propietario_producto");
        $stmt_producto_id->bindParam(":id_propietario_producto", $id_propietario_producto, PDO::PARAM_INT);
        $stmt_producto_id->execute();
        $producto_data = $stmt_producto_id->fetch();
        
        if (!$producto_data) {
            return array();
        }
        
        $id_producto = $producto_data['id_producto'];

        $stmt = Conexion::conectar()->prepare("SELECT 
        mic.id, 
        mic.nombre,
        CASE 
            WHEN mpb.id_ingrediente_categoria IS NOT NULL THEN 'Si'
            ELSE 'No'
        END AS obligatoria
        FROM propietarios_ingredientes pi
            JOIN menu_ingredientes mi ON mi.id = pi.id_ingrediente
            JOIN menu_ingredientes_categorias mic ON mic.id = mi.id_ingrediente_categoria
            LEFT JOIN menu_productos_bases mpb ON mpb.id_producto = :id_producto 
                AND mpb.id_ingrediente_categoria = mic.id
        WHERE mi.estado = 0
            AND mic.estado = 0
            AND pi.estado = 1
            AND pi.id_propietario_producto = :id_propietario_producto
        GROUP BY mic.id");

        $stmt->bindParam(":id_propietario_producto", $id_propietario_producto, PDO::PARAM_INT);
        $stmt->bindParam(":id_producto", $id_producto, PDO::PARAM_INT);

        $stmt->execute();

        $categorias = $stmt->fetchAll();

        foreach ($categorias as &$cat) {
            $cat['ingredientes'] = self::buscarIngredientesModel($datos, $cat['id'], $id_propietario_producto);
        }

        return $categorias;
    }

    static public function buscarIngredientesModel($datos, $categoria, $id_propietario_producto)
    {
        $stmt = Conexion::conectar()->prepare("SELECT 
        mi.id, 
        mi.nombre, 
        pi.costo_extra, 
        pi.cantidad_gratis, 
        pi.precio
        FROM propietarios_ingredientes pi 
            JOIN menu_ingredientes mi ON mi.id = pi.id_ingrediente
        WHERE mi.estado = 0
            AND pi.estado = 1
            AND pi.id_propietario_producto = :id_propietario_producto
            AND mi.id_ingrediente_categoria = :id_categoria");

        $stmt->bindParam(":id_propietario_producto", $id_propietario_producto, PDO::PARAM_INT);
        $stmt->bindParam(":id_categoria", $categoria, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetchAll();
    }


    static public function buscarTamanosModel($datos)
    {
        // Obtener id_propietario desde la cafetería
        $stmt_propietario = Conexion::conectar()->prepare("SELECT id_usuario AS id_propietario FROM cafeterias WHERE id = :id_cafeteria");
        $stmt_propietario->bindParam(':id_cafeteria', $datos['id_cafeteria'], PDO::PARAM_INT);
        $stmt_propietario->execute();
        $cafeteria_data = $stmt_propietario->fetch();
        
        if (!$cafeteria_data) {
            return array();
        }
        
        $id_propietario = $cafeteria_data['id_propietario'];
        
        // Obtener el id_propietario_producto
        $stmt_producto = Conexion::conectar()->prepare("SELECT id FROM propietarios_productos
        WHERE id_producto = :id_producto
        AND id_propietario = :id_propietario
        AND id_cafeteria = :id_cafeteria");
        
        $stmt_producto->bindParam(':id_producto', $datos['id_producto'], PDO::PARAM_INT);
        $stmt_producto->bindParam(':id_propietario', $id_propietario, PDO::PARAM_INT);
        $stmt_producto->bindParam(':id_cafeteria', $datos['id_cafeteria'], PDO::PARAM_INT);
        $stmt_producto->execute();
        $producto = $stmt_producto->fetch();
        
        if (!$producto) {
            return array();
        }
        
        $id_propietario_producto = $producto['id'];

        $stmt = Conexion::conectar()->prepare("SELECT 
        pt.id,
        pt.nombre, 
        pt.unidad_medida, 
        pt.medida,
        ppt.precio
        FROM propietarios_productos_tamanos ppt 
            JOIN menu_productos_tamanos pt ON pt.id = ppt.id_tamano
        WHERE ppt.estado = 1
            AND pt.estado = 0
            AND ppt.id_propietario_producto = :id_propietario_producto
        ORDER BY pt.id ASC");

        $stmt->bindParam(":id_propietario_producto", $id_propietario_producto, PDO::PARAM_INT);

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

    /* OBTENER ZONA HORARIA DE LA CIUDAD */
    
    static public function obtenerZonaHorariaCiudadModel($id_ciudad)
    {
        $stmt = Conexion::conectar()->prepare("SELECT zona_horaria FROM ciudades WHERE id = :id_ciudad");

        $stmt->bindParam(':id_ciudad', $id_ciudad, PDO::PARAM_INT);

        $stmt->execute();

        $resultado = $stmt->fetch();

        return $resultado ? $resultado['zona_horaria'] : null;

        $stmt = null;
    }
}
