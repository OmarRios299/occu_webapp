<?php

require_once __DIR__ . '/../config/conexion.php';

class MisPedidosModel extends Conexion
{
    // Obtener pedido actual (en proceso: estado_pedido 1 o 2)
    static public function obtenerPedidoActualModel($id_usuario)
    {
        $stmt = Conexion::conectar()->prepare("SELECT 
            v.id,
            v.id_cafeteria,
            v.id_cliente,
            v.monto_total,
            v.estado_pedido,
            v.fecha_alta,
            v.fecha_aceptado,
            v.fecha_entragado,
            c.nombre AS nombre_cafeteria,
            c.imagen AS imagen_cafeteria,
            c.direccion AS direccion_cafeteria
        FROM ventas v
        INNER JOIN cafeterias c ON c.id = v.id_cafeteria
        WHERE v.id_cliente = :id_usuario
        AND v.estado = 0
        AND v.estado_pedido IN (1, 2)
        ORDER BY v.fecha_alta DESC
        LIMIT 1");

        $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch();
    }

    // Obtener pedidos anteriores (estado_pedido 3 o 4) con paginación
    static public function obtenerPedidosAnterioresModel($id_usuario, $pagina = 1, $por_pagina = 10)
    {
        $offset = ($pagina - 1) * $por_pagina;
        
        $stmt = Conexion::conectar()->prepare("SELECT 
            v.id,
            v.id_cafeteria,
            v.id_cliente,
            v.monto_total,
            v.estado_pedido,
            v.fecha_alta,
            v.fecha_aceptado,
            v.fecha_entragado,
            v.fecha_rechazo,
            v.motivo_rechazo,
            c.nombre AS nombre_cafeteria,
            c.imagen AS imagen_cafeteria,
            c.direccion AS direccion_cafeteria
        FROM ventas v
        INNER JOIN cafeterias c ON c.id = v.id_cafeteria
        WHERE v.id_cliente = :id_usuario
        AND v.estado = 0
        AND v.estado_pedido IN (3, 4)
        ORDER BY v.fecha_alta DESC
        LIMIT :por_pagina OFFSET :offset");

        $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        $stmt->bindParam(':por_pagina', $por_pagina, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }

    // Contar total de pedidos anteriores para paginación
    static public function contarPedidosAnterioresModel($id_usuario)
    {
        $stmt = Conexion::conectar()->prepare("SELECT COUNT(*) as total
        FROM ventas v
        WHERE v.id_cliente = :id_usuario
        AND v.estado = 0
        AND v.estado_pedido IN (3, 4)");

        $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        $stmt->execute();
        
        $result = $stmt->fetch();
        return $result['total'] ?? 0;
    }

    // Obtener detalle completo de un pedido
    static public function obtenerDetallePedidoModel($id_pedido, $id_usuario)
    {
        $stmt = Conexion::conectar()->prepare("SELECT 
            v.*,
            c.nombre AS nombre_cafeteria,
            c.imagen AS imagen_cafeteria,
            c.direccion AS direccion_cafeteria,
            c.telefono AS telefono_cafeteria,
            c.correo_electronico AS correo_cafeteria
        FROM ventas v
        INNER JOIN cafeterias c ON c.id = v.id_cafeteria
        WHERE v.id = :id_pedido
        AND v.id_cliente = :id_usuario
        AND v.estado = 0");

        $stmt->bindParam(':id_pedido', $id_pedido, PDO::PARAM_INT);
        $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch();
    }

    // Obtener items de un pedido
    static public function obtenerItemsPedidoModel($id_pedido)
    {
        $stmt = Conexion::conectar()->prepare("SELECT 
            vi.id AS id_item,
            vi.id_producto,
            mp.nombre AS nombre_producto,
            mp.imagen AS imagen_producto,
            vi.id_tamano,
            mpt.nombre AS nombre_tamano,
            mpt.medida,
            mpt.unidad_medida,
            vi.monto_unitario,
            vi.cantidad,
            vi.monto_subtotal,
            vi.monto_total
        FROM ventas_items vi
        INNER JOIN menu_productos mp ON mp.id = vi.id_producto
        LEFT JOIN menu_productos_tamanos mpt ON mpt.id = vi.id_tamano
        WHERE vi.id_venta = :id_pedido
        AND vi.estado = 0
        ORDER BY vi.id");

        $stmt->bindParam(':id_pedido', $id_pedido, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }

    // Obtener ingredientes de un item del pedido
    static public function obtenerIngredientesItemModel($id_venta_item)
    {
        $stmt = Conexion::conectar()->prepare("SELECT 
            vii.id,
            vii.id_ingrediente,
            mi.nombre AS nombre_ingrediente,
            mic.nombre AS categoria_ingrediente,
            vii.costo_extra,
            vii.cantidad_gratis,
            vii.cantidad,
            vii.precio,
            vii.monto_total
        FROM ventas_items_ingredientes vii
        INNER JOIN menu_ingredientes mi ON mi.id = vii.id_ingrediente
        INNER JOIN menu_ingredientes_categorias mic ON mic.id = mi.id_ingrediente_categoria
        WHERE vii.id_venta_item = :id_venta_item
        AND vii.estado = 0
        ORDER BY mic.nombre, mi.nombre");

        $stmt->bindParam(':id_venta_item', $id_venta_item, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }

    // Verificar si el usuario tiene un pedido en proceso
    static public function tienePedidoEnProcesoModel($id_usuario)
    {
        $stmt = Conexion::conectar()->prepare("SELECT COUNT(*) as total
        FROM ventas
        WHERE id_cliente = :id_usuario
        AND estado = 0
        AND estado_pedido IN (1, 2)");

        $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        $stmt->execute();
        
        $result = $stmt->fetch();
        return $result['total'] > 0;
    }

    /* OBTENER ZONA HORARIA DE LA CAFETERÍA */
    
    static public function obtenerZonaHorariaCafeteriaModel($id_cafeteria)
    {
        $stmt = Conexion::conectar()->prepare("SELECT ciudades.zona_horaria 
        FROM cafeterias c
        INNER JOIN ciudades ON ciudades.id = c.id_ciudad
        WHERE c.id = :id_cafeteria");

        $stmt->bindParam(':id_cafeteria', $id_cafeteria, PDO::PARAM_INT);

        $stmt->execute();

        $resultado = $stmt->fetch();

        return $resultado ? $resultado['zona_horaria'] : null;

        $stmt = null;
    }

    /* OBTENER ZONA HORARIA DEL PEDIDO */
    
    static public function obtenerZonaHorariaPedidoModel($id_pedido)
    {
        $stmt = Conexion::conectar()->prepare("SELECT ciudades.zona_horaria 
        FROM ventas v
        INNER JOIN cafeterias c ON c.id = v.id_cafeteria
        INNER JOIN ciudades ON ciudades.id = c.id_ciudad
        WHERE v.id = :id_pedido");

        $stmt->bindParam(':id_pedido', $id_pedido, PDO::PARAM_INT);

        $stmt->execute();

        $resultado = $stmt->fetch();

        return $resultado ? $resultado['zona_horaria'] : null;

        $stmt = null;
    }
}

