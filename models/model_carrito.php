<?php 

require_once __DIR__ . '/../config/conexion.php';

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
        // Obtener id_propietario desde la cafetería del carrito
        $stmt_carrito = Conexion::conectar()->prepare("SELECT id_cafeteria FROM ventas_carrito WHERE id = :id_carrito");
        $stmt_carrito->bindParam(':id_carrito', $datos['id_carrito'], PDO::PARAM_INT);
        $stmt_carrito->execute();
        $carrito_data = $stmt_carrito->fetch();
        
        if (!$carrito_data) {
            return array();
        }
        
        $id_cafeteria = $carrito_data['id_cafeteria'];
        
        // Obtener id_propietario desde la cafetería
        $stmt_propietario = Conexion::conectar()->prepare("SELECT id_usuario AS id_propietario FROM cafeterias WHERE id = :id_cafeteria");
        $stmt_propietario->bindParam(':id_cafeteria', $id_cafeteria, PDO::PARAM_INT);
        $stmt_propietario->execute();
        $cafeteria_data = $stmt_propietario->fetch();
        
        if (!$cafeteria_data) {
            return array();
        }
        
        $id_propietario = $cafeteria_data['id_propietario'];
    
        $stmt = Conexion::conectar()->prepare("SELECT 
        vci.id AS id_item,
        vci.id_producto,
        mp.nombre AS nombre,
        mp.imagen AS imagen,
        vci.cantidad,
        mpt.nombre AS tamano,
        mpt.medida,
        mpt.unidad_medida,
        CASE 
            WHEN vci.id_tamano = 0 THEN pp.precio_base
            ELSE ppt.precio
        END AS precio
        FROM ventas_carrito_items vci
        INNER JOIN ventas_carrito vc ON vc.id = vci.id_carrito
        INNER JOIN menu_productos mp ON mp.id = vci.id_producto
        INNER JOIN propietarios_productos pp ON pp.id_producto = vci.id_producto
            AND pp.id_propietario = :id_propietario
            AND pp.id_cafeteria = :id_cafeteria
        LEFT JOIN propietarios_productos_tamanos ppt ON ppt.id_propietario_producto = pp.id
            AND ppt.id_tamano = vci.id_tamano
            AND ppt.estado = 1
        LEFT JOIN menu_productos_tamanos mpt ON mpt.id = vci.id_tamano
        WHERE vci.estado = 0
        AND vci.id_carrito = :id_carrito
        ;");
    
        $stmt->bindParam(':id_carrito', $datos['id_carrito'],PDO::PARAM_INT);
        $stmt->bindParam(':id_propietario', $id_propietario,PDO::PARAM_INT);
        $stmt->bindParam(':id_cafeteria', $id_cafeteria,PDO::PARAM_INT);

        $stmt -> execute();
    
        return $stmt -> fetchAll();
    
    }
    

    static public function buscarCarritoItemsIngredientesModel($datos){
        // Obtener id_producto y id_cafeteria desde el item del carrito
        $stmt_item = Conexion::conectar()->prepare("SELECT vci.id_producto, vc.id_cafeteria 
        FROM ventas_carrito_items vci
        INNER JOIN ventas_carrito vc ON vc.id = vci.id_carrito
        WHERE vci.id = :id_carrito_item");
        $stmt_item->bindParam(':id_carrito_item', $datos['id_item'], PDO::PARAM_INT);
        $stmt_item->execute();
        $item_data = $stmt_item->fetch();
        
        if (!$item_data) {
            return array();
        }
        
        // Obtener id_propietario desde la cafetería
        $stmt_propietario = Conexion::conectar()->prepare("SELECT id_usuario AS id_propietario FROM cafeterias WHERE id = :id_cafeteria");
        $stmt_propietario->bindParam(':id_cafeteria', $item_data['id_cafeteria'], PDO::PARAM_INT);
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
        
        $stmt_producto->bindParam(':id_producto', $item_data['id_producto'], PDO::PARAM_INT);
        $stmt_producto->bindParam(':id_propietario', $id_propietario, PDO::PARAM_INT);
        $stmt_producto->bindParam(':id_cafeteria', $item_data['id_cafeteria'], PDO::PARAM_INT);
        $stmt_producto->execute();
        $producto = $stmt_producto->fetch();
        
        if (!$producto) {
            return array();
        }
        
        $id_propietario_producto = $producto['id'];
    
        $stmt = Conexion::conectar()->prepare("SELECT 
        ii.id AS id_item_ingre, 
        ii.id_ingrediente, 
        mi.nombre,
        mi.id_ingrediente_categoria,
        ii.cantidad,
        CASE 
            WHEN pi.precio > 0 AND pi.cantidad_gratis > 0 THEN
                -- Solo aplicar cantidad_gratis si es el primer ingrediente insertado de su categoría (menor ID)
                CASE 
                    WHEN ii.id = (
                        SELECT MIN(ii2.id)
                        FROM ventas_carrito_items_ingredientes ii2
                        INNER JOIN menu_ingredientes mi2 ON mi2.id = ii2.id_ingrediente
                        WHERE ii2.id_carrito_item = :id_carrito_item
                        AND ii2.estado = 0
                        AND mi2.id_ingrediente_categoria = mi.id_ingrediente_categoria
                    )
                    THEN GREATEST(0, (ii.cantidad - pi.cantidad_gratis)) * pi.precio
                    ELSE ii.cantidad * pi.precio
                END
            WHEN pi.precio > 0 AND pi.cantidad_gratis = 0
                THEN ii.cantidad * pi.precio
            ELSE 0
        END AS total
        FROM ventas_carrito_items_ingredientes ii
        INNER JOIN menu_ingredientes mi ON mi.id = ii.id_ingrediente
        INNER JOIN propietarios_ingredientes pi ON pi.id_propietario_producto = :id_propietario_producto
            AND pi.id_ingrediente = ii.id_ingrediente
        WHERE ii.estado = 0
        AND ii.id_carrito_item = :id_carrito_item
        ;");
    
        $stmt->bindParam(':id_carrito_item', $datos['id_item'],PDO::PARAM_INT);
        $stmt->bindParam(':id_propietario_producto', $id_propietario_producto,PDO::PARAM_INT);

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
        // Obtener id_propietario desde la cafetería
        $stmt_propietario = Conexion::conectar()->prepare("SELECT id_usuario AS id_propietario FROM cafeterias WHERE id = :id_cafeteria");
        $stmt_propietario->bindParam(':id_cafeteria', $id_cafeteria, PDO::PARAM_INT);
        $stmt_propietario->execute();
        $cafeteria_data = $stmt_propietario->fetch();
        
        if (!$cafeteria_data) {
            return array();
        }
        
        $id_propietario = $cafeteria_data['id_propietario'];
        
        $stmt = Conexion::conectar()->prepare("SELECT 
            vci.id AS id_item,
            vci.id_producto,
            vci.id_tamano,
            vci.cantidad,
            mp.nombre AS nombre_producto,
            CASE 
                WHEN vci.id_tamano = 0 THEN COALESCE(pp.precio_base, 0)
                ELSE COALESCE(ppt.precio, 0)
            END AS precio_unitario
        FROM ventas_carrito_items vci
        INNER JOIN menu_productos mp ON mp.id = vci.id_producto
        INNER JOIN propietarios_productos pp ON pp.id_producto = vci.id_producto
            AND pp.id_propietario = :id_propietario
            AND pp.id_cafeteria = :id_cafeteria
        LEFT JOIN propietarios_productos_tamanos ppt ON ppt.id_propietario_producto = pp.id
            AND ppt.id_tamano = vci.id_tamano
            AND ppt.estado = 1
        WHERE vci.estado = 0
        AND vci.id_carrito = :id_carrito");

        $stmt->bindParam(':id_carrito', $id_carrito, PDO::PARAM_INT);
        $stmt->bindParam(':id_propietario', $id_propietario, PDO::PARAM_INT);
        $stmt->bindParam(':id_cafeteria', $id_cafeteria, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    // Obtener ingredientes con costo calculado para un item
    static public function obtenerIngredientesParaVentaModel($id_item, $id_producto, $id_cafeteria)
    {
        // Obtener id_propietario desde la cafetería
        $stmt_propietario = Conexion::conectar()->prepare("SELECT id_usuario AS id_propietario FROM cafeterias WHERE id = :id_cafeteria");
        $stmt_propietario->bindParam(':id_cafeteria', $id_cafeteria, PDO::PARAM_INT);
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
        
        $stmt_producto->bindParam(':id_producto', $id_producto, PDO::PARAM_INT);
        $stmt_producto->bindParam(':id_propietario', $id_propietario, PDO::PARAM_INT);
        $stmt_producto->bindParam(':id_cafeteria', $id_cafeteria, PDO::PARAM_INT);
        $stmt_producto->execute();
        $producto = $stmt_producto->fetch();
        
        if (!$producto) {
            return array();
        }
        
        $id_propietario_producto = $producto['id'];
        
        $stmt = Conexion::conectar()->prepare("SELECT 
            vcii.id AS id_item_ingrediente,
            vcii.id_ingrediente,
            mi.nombre AS nombre_ingrediente,
            mi.id_ingrediente_categoria,
            vcii.cantidad,
            pi.costo_extra,
            pi.cantidad_gratis,
            COALESCE(pi.precio, 0) AS precio,
            CASE 
                WHEN pi.precio > 0 AND pi.cantidad_gratis > 0 THEN
                    -- Solo aplicar cantidad_gratis si es el primer ingrediente insertado de su categoría (menor ID)
                    CASE 
                        WHEN vcii.id = (
                            SELECT MIN(vcii2.id)
                            FROM ventas_carrito_items_ingredientes vcii2
                            INNER JOIN menu_ingredientes mi2 ON mi2.id = vcii2.id_ingrediente
                            WHERE vcii2.id_carrito_item = :id_item
                            AND vcii2.estado = 0
                            AND mi2.id_ingrediente_categoria = mi.id_ingrediente_categoria
                        )
                        THEN GREATEST(0, (vcii.cantidad - pi.cantidad_gratis)) * pi.precio
                        ELSE vcii.cantidad * pi.precio
                    END
                WHEN pi.precio > 0 AND pi.cantidad_gratis = 0
                    THEN vcii.cantidad * pi.precio
                ELSE 0
            END AS monto_total
        FROM ventas_carrito_items_ingredientes vcii
        INNER JOIN menu_ingredientes mi ON mi.id = vcii.id_ingrediente
        INNER JOIN propietarios_ingredientes pi ON pi.id_propietario_producto = :id_propietario_producto
            AND pi.id_ingrediente = vcii.id_ingrediente
        WHERE vcii.estado = 0
        AND vcii.id_carrito_item = :id_item");

        $stmt->bindParam(':id_item', $id_item, PDO::PARAM_INT);
        $stmt->bindParam(':id_propietario_producto', $id_propietario_producto, PDO::PARAM_INT);
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