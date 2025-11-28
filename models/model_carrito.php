<?php 

require_once "conexion.php";

class CarritoModel extends Conexion {

    
    static public function buscarCarritoModel($datos){
    
        $stmt = Conexion::conectar()->prepare("SELECT vc.*, 
        c.imagen AS imagen_cafeteria 
        FROM ventas_carrito vc
        INNER JOIN cafeterias c ON c.id = vc.id_cafeteria
        WHERE vc.estado = 0
        AND vc.id_usuario = :id_usuario
       -- AND vc.id_cafeteria = :id_cafeteria
        ;");
    
        $stmt->bindParam(':id_usuario', $datos['id_usuario'],PDO::PARAM_INT);
       // $stmt->bindParam(':id_cafeteria', $datos['id_cafeteria'],PDO::PARAM_INT);

        $stmt -> execute();
    
        return $stmt -> fetch();
    
    }

    
    static public function buscarCarritoItemsModel($datos){
    
        $stmt = Conexion::conectar()->prepare("SELECT 
        vci.id AS id_item,
        vci.id_producto,
        mp.nombre AS nombre,
        mp.imagen AS imagen,
        vci.cantidad,
        mpt.nombre AS tamano,
        mpt.medida,
        mpt.unidad_medida,
        pmc.precio
        FROM ventas_carrito_items vci
        INNER JOIN ventas_carrito vc ON vc.id = vci.id_carrito
        INNER JOIN menu_productos mp ON mp.id = vci.id_producto
        LEFT JOIN propietarios_menu_cafeterias pmc ON pmc.id_producto = vci.id_producto
        AND pmc.id_tamano = vci.id_tamano AND pmc.id_cafeteria = vc.id_cafeteria 
        LEFT JOIN menu_productos_tamanos mpt ON mpt.id = vci.id_tamano
        WHERE vci.estado = 0
        AND vci.id_carrito = :id_carrito
        ;");
    
        $stmt->bindParam(':id_carrito', $datos['id_carrito'],PDO::PARAM_INT);

        $stmt -> execute();
    
        return $stmt -> fetchAll();
    
    }
    

    static public function buscarCarritoItemsIngredientesModel($datos){
    
        $stmt = Conexion::conectar()->prepare("SELECT 
        ii.id AS id_item_ingre, 
        ii.id_ingrediente, 
        mi.nombre,
        ii.cantidad,
        CASE 
            WHEN pmi.costo_extra = 'Si' 
                THEN (ii.cantidad * pmi.precio) - (pmi.cantidad_gratis * pmi.precio)
            ELSE 0
        END AS total
        FROM ventas_carrito_items_ingredientes ii
        INNER JOIN menu_ingredientes mi ON mi.id = ii.id_ingrediente
        INNER JOIN ventas_carrito_items vci ON vci.id = ii.id_carrito_item
        INNER JOIN ventas_carrito vc ON vc.id = vci.id_carrito
        INNER JOIN propietarios_menu_ingredientes pmi ON pmi.id_cafeteria = vc.id_cafeteria
        AND pmi.id_producto = vci.id_producto AND pmi.id_ingrediente = ii.id_ingrediente
        WHERE ii.estado = 0
        AND ii.id_carrito_item = :id_carrito_item
        ;");
    
        $stmt->bindParam(':id_carrito_item', $datos['id_item'],PDO::PARAM_INT);

        $stmt -> execute();
    
        return $stmt -> fetchAll();
    
    }
    
    
    static public function actualizarItemCantidadModel($datos){
    
        $stmt = Conexion::conectar()->prepare("UPDATE ventas_carrito_items SET cantidad = :cantidad WHERE id = :id_item");
    
        $stmt->bindParam(":id_item", $datos['id_item'], PDO::PARAM_INT);
         $stmt->bindParam(":cantidad", $datos['cantidad'], PDO::PARAM_INT);
    
        if($stmt->execute()){
            return 'success';
        }else{
            return 'error';
        }
    
        $stmt = null;
    
    }
    
    static public function eliminarItemCantidadModel($datos){
    
        $stmt = Conexion::conectar()->prepare("UPDATE ventas_carrito_items SET estado = 2 WHERE id = :id_item");
    
        $stmt->bindParam(":id_item", $datos['id_item'], PDO::PARAM_INT);

        if($stmt->execute()){
            return 'success';
        }else{
            return 'error';
        }
    
        $stmt = null;
    
    }

    static public function carritoContadorItemsModel($id_usuario)
    {
        $stmt = Conexion::conectar()->prepare("SELECT COUNT(*) AS total
        FROM ventas_carrito_items ci
        INNER JOIN ventas_carrito vc ON vc.id = ci.id_carrito
        WHERE vc.id_usuario = :id_usuario
            AND vc.estado = 0
            AND ci.estado = 0
        ");

        $stmt->bindParam(":id_usuario", $id_usuario, PDO::PARAM_INT);
        $stmt->execute();
        $res = $stmt->fetch();

        return $res['total'];
    }


    /* ========== FUNCIONES PARA PROCESAR PAGO ========== */

    // Obtener datos completos del carrito para el resumen
    static public function obtenerResumenCarritoModel($id_usuario)
    {
        $stmt = Conexion::conectar()->prepare("SELECT 
            vc.id AS id_carrito,
            vc.id_cafeteria,
            c.nombre AS nombre_cafeteria,
            c.imagen AS imagen_cafeteria
        FROM ventas_carrito vc
        INNER JOIN cafeterias c ON c.id = vc.id_cafeteria
        WHERE vc.estado = 0
        AND vc.id_usuario = :id_usuario");

        $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch();
    }

    // Obtener items con precio calculado para la venta
    static public function obtenerItemsParaVentaModel($id_carrito, $id_cafeteria)
    {
        $stmt = Conexion::conectar()->prepare("SELECT 
            vci.id AS id_item,
            vci.id_producto,
            vci.id_tamano,
            vci.cantidad,
            mp.nombre AS nombre_producto,
            COALESCE(pmc.precio, 0) AS precio_unitario
        FROM ventas_carrito_items vci
        INNER JOIN menu_productos mp ON mp.id = vci.id_producto
        LEFT JOIN propietarios_menu_cafeterias pmc ON pmc.id_producto = vci.id_producto
            AND pmc.id_tamano = vci.id_tamano 
            AND pmc.id_cafeteria = :id_cafeteria
        WHERE vci.estado = 0
        AND vci.id_carrito = :id_carrito");

        $stmt->bindParam(':id_carrito', $id_carrito, PDO::PARAM_INT);
        $stmt->bindParam(':id_cafeteria', $id_cafeteria, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    // Obtener ingredientes con costo calculado para un item
    static public function obtenerIngredientesParaVentaModel($id_item, $id_producto, $id_cafeteria)
    {
        $stmt = Conexion::conectar()->prepare("SELECT 
            vcii.id AS id_item_ingrediente,
            vcii.id_ingrediente,
            mi.nombre AS nombre_ingrediente,
            vcii.cantidad,
            pmi.costo_extra,
            pmi.cantidad_gratis,
            COALESCE(pmi.precio, 0) AS precio,
            CASE 
                WHEN pmi.costo_extra = 'Si' 
                    THEN GREATEST(0, (vcii.cantidad - pmi.cantidad_gratis)) * pmi.precio
                ELSE 0
            END AS monto_total
        FROM ventas_carrito_items_ingredientes vcii
        INNER JOIN menu_ingredientes mi ON mi.id = vcii.id_ingrediente
        INNER JOIN propietarios_menu_ingredientes pmi ON pmi.id_cafeteria = :id_cafeteria
            AND pmi.id_producto = :id_producto 
            AND pmi.id_ingrediente = vcii.id_ingrediente
        WHERE vcii.estado = 0
        AND vcii.id_carrito_item = :id_item");

        $stmt->bindParam(':id_item', $id_item, PDO::PARAM_INT);
        $stmt->bindParam(':id_producto', $id_producto, PDO::PARAM_INT);
        $stmt->bindParam(':id_cafeteria', $id_cafeteria, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    // Insertar venta principal
    static public function insertarVentaModel($datos)
    {
        $conexion = Conexion::conectar();
        $stmt = $conexion->prepare("INSERT INTO ventas (
            id_cafeteria, 
            id_cliente, 
            monto_total, 
            estado_pedido, 
            estado, 
            id_alta, 
            fecha_alta
        ) VALUES (
            :id_cafeteria, 
            :id_cliente, 
            :monto_total, 
            :estado_pedido, 
            0, 
            :id_alta, 
            :fecha_alta
        )");

        $stmt->bindParam(':id_cafeteria', $datos['id_cafeteria'], PDO::PARAM_INT);
        $stmt->bindParam(':id_cliente', $datos['id_cliente'], PDO::PARAM_INT);
        $stmt->bindParam(':monto_total', $datos['monto_total'], PDO::PARAM_STR);
        $stmt->bindParam(':estado_pedido', $datos['estado_pedido'], PDO::PARAM_INT);
        $stmt->bindParam(':id_alta', $datos['id_alta'], PDO::PARAM_INT);
        $stmt->bindParam(':fecha_alta', $datos['fecha_alta'], PDO::PARAM_STR);

        if ($stmt->execute()) {
            return $conexion->lastInsertId();
        }
        return false;
    }

    // Insertar item de venta
    static public function insertarVentaItemModel($datos)
    {
        $conexion = Conexion::conectar();
        $stmt = $conexion->prepare("INSERT INTO ventas_items (
            id_venta, 
            id_producto, 
            id_tamano, 
            monto_unitario, 
            cantidad, 
            monto_subtotal, 
            monto_total, 
            estado, 
            id_alta, 
            fecha_alta
        ) VALUES (
            :id_venta, 
            :id_producto, 
            :id_tamano, 
            :monto_unitario, 
            :cantidad, 
            :monto_subtotal, 
            :monto_total, 
            0, 
            :id_alta, 
            :fecha_alta
        )");

        $stmt->bindParam(':id_venta', $datos['id_venta'], PDO::PARAM_INT);
        $stmt->bindParam(':id_producto', $datos['id_producto'], PDO::PARAM_INT);
        $stmt->bindParam(':id_tamano', $datos['id_tamano'], PDO::PARAM_INT);
        $stmt->bindParam(':monto_unitario', $datos['monto_unitario'], PDO::PARAM_STR);
        $stmt->bindParam(':cantidad', $datos['cantidad'], PDO::PARAM_INT);
        $stmt->bindParam(':monto_subtotal', $datos['monto_subtotal'], PDO::PARAM_STR);
        $stmt->bindParam(':monto_total', $datos['monto_total'], PDO::PARAM_STR);
        $stmt->bindParam(':id_alta', $datos['id_alta'], PDO::PARAM_INT);
        $stmt->bindParam(':fecha_alta', $datos['fecha_alta'], PDO::PARAM_STR);

        if ($stmt->execute()) {
            return $conexion->lastInsertId();
        }
        return false;
    }

    // Insertar ingrediente de item de venta
    static public function insertarVentaItemIngredienteModel($datos)
    {
        $stmt = Conexion::conectar()->prepare("INSERT INTO ventas_items_ingredientes (
            id_venta_item, 
            id_ingrediente, 
            costo_extra, 
            cantidad_gratis, 
            cantidad, 
            precio, 
            monto_total, 
            estado, 
            id_alta, 
            fecha_alta
        ) VALUES (
            :id_venta_item, 
            :id_ingrediente, 
            :costo_extra, 
            :cantidad_gratis, 
            :cantidad, 
            :precio, 
            :monto_total, 
            0, 
            :id_alta, 
            :fecha_alta
        )");

        $stmt->bindParam(':id_venta_item', $datos['id_venta_item'], PDO::PARAM_INT);
        $stmt->bindParam(':id_ingrediente', $datos['id_ingrediente'], PDO::PARAM_INT);
        $stmt->bindParam(':costo_extra', $datos['costo_extra'], PDO::PARAM_STR);
        $stmt->bindParam(':cantidad_gratis', $datos['cantidad_gratis'], PDO::PARAM_INT);
        $stmt->bindParam(':cantidad', $datos['cantidad'], PDO::PARAM_INT);
        $stmt->bindParam(':precio', $datos['precio'], PDO::PARAM_STR);
        $stmt->bindParam(':monto_total', $datos['monto_total'], PDO::PARAM_STR);
        $stmt->bindParam(':id_alta', $datos['id_alta'], PDO::PARAM_INT);
        $stmt->bindParam(':fecha_alta', $datos['fecha_alta'], PDO::PARAM_STR);

        return $stmt->execute();
    }

    // Marcar carrito como procesado (estado = 2)
    static public function cerrarCarritoModel($id_carrito)
    {
        $stmt = Conexion::conectar()->prepare("UPDATE ventas_carrito SET estado = 2 WHERE id = :id_carrito");
        $stmt->bindParam(':id_carrito', $id_carrito, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // Marcar items del carrito como procesados (estado = 2)
    static public function cerrarCarritoItemsModel($id_carrito)
    {
        $stmt = Conexion::conectar()->prepare("UPDATE ventas_carrito_items SET estado = 2 WHERE id_carrito = :id_carrito AND estado = 0");
        $stmt->bindParam(':id_carrito', $id_carrito, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // Marcar ingredientes de items como procesados (estado = 2)
    static public function cerrarCarritoItemsIngredientesModel($id_carrito)
    {
        $stmt = Conexion::conectar()->prepare("UPDATE ventas_carrito_items_ingredientes vcii
        INNER JOIN ventas_carrito_items vci ON vci.id = vcii.id_carrito_item
        SET vcii.estado = 2 
        WHERE vci.id_carrito = :id_carrito AND vcii.estado = 0");
        $stmt->bindParam(':id_carrito', $id_carrito, PDO::PARAM_INT);
        return $stmt->execute();
    }
}