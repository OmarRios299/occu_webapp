<?php

require_once __DIR__ . '/../config/conexion.php';

class CafeteriaPedidosModel extends Conexion
{

    /* ========== OBTENER PEDIDOS ========== */

    // Obtener pedidos por cafetería y estado
    static public function obtenerPedidosModel($id_cafeteria, $estado_pedido = null)
    {
        $sql = "SELECT 
            v.id,
            v.id_cafeteria,
            v.id_cliente,
            CONCAT(u.nombre, ' ', u.apellido) AS nombre_cliente,
            u.telefono AS telefono_cliente,
            v.monto_total,
            v.estado_pedido,
            v.fecha_alta,
            v.fecha_aceptado,
            v.fecha_rechazo,
            v.motivo_rechazo,
            v.fecha_entragado
        FROM ventas v
        INNER JOIN admin_usuarios u ON u.id = v.id_cliente
        WHERE v.id_cafeteria = :id_cafeteria
        AND v.estado = 0";

        if ($estado_pedido !== null) {
            $sql .= " AND v.estado_pedido = :estado_pedido";
            
            // Filtrar por fecha según el estado del pedido
            if ($estado_pedido == 4) {
                // Entregados: solo los entregados hoy
                $sql .= " AND DATE(v.fecha_entragado) = CURDATE()";
            } elseif ($estado_pedido == 3) {
                // Rechazados: solo los rechazados hoy
                $sql .= " AND DATE(v.fecha_rechazo) = CURDATE()";
            } else {
                // Pendientes (1) y En preparación (2): solo los creados hoy
                $sql .= " AND DATE(v.fecha_alta) = CURDATE()";
            }
        } else {
            // Si no se especifica estado, filtrar todos los pedidos de hoy según su estado
            $sql .= " AND (
                (v.estado_pedido IN (1, 2) AND DATE(v.fecha_alta) = CURDATE()) OR
                (v.estado_pedido = 3 AND DATE(v.fecha_rechazo) = CURDATE()) OR
                (v.estado_pedido = 4 AND DATE(v.fecha_entragado) = CURDATE())
            )";
        }

        $sql .= " ORDER BY v.fecha_alta DESC";

        $stmt = Conexion::conectar()->prepare($sql);
        $stmt->bindParam(':id_cafeteria', $id_cafeteria, PDO::PARAM_INT);
        
        if ($estado_pedido !== null) {
            $stmt->bindParam(':estado_pedido', $estado_pedido, PDO::PARAM_INT);
        }

        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Obtener contadores de pedidos por estado
    static public function obtenerContadoresPedidosModel($id_cafeteria)
    {
        $stmt = Conexion::conectar()->prepare("SELECT 
            SUM(CASE WHEN estado_pedido = 1 AND DATE(fecha_alta) = CURDATE() THEN 1 ELSE 0 END) AS pendientes,
            SUM(CASE WHEN estado_pedido = 2 AND DATE(fecha_alta) = CURDATE() THEN 1 ELSE 0 END) AS preparando,
            SUM(CASE WHEN estado_pedido = 4 AND DATE(fecha_entragado) = CURDATE() THEN 1 ELSE 0 END) AS entregados_hoy,
            SUM(CASE WHEN estado_pedido = 3 AND DATE(fecha_rechazo) = CURDATE() THEN 1 ELSE 0 END) AS rechazados_hoy
        FROM ventas
        WHERE id_cafeteria = :id_cafeteria
        AND estado = 0");

        $stmt->bindParam(':id_cafeteria', $id_cafeteria, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch();
    }

    // Obtener detalle de un pedido
    static public function obtenerDetallePedidoModel($id_pedido)
    {
        $stmt = Conexion::conectar()->prepare("SELECT 
            v.*,
            CONCAT(u.nombre, ' ', u.apellido) AS nombre_cliente,
            u.telefono AS telefono_cliente,
            u.correo_electronico AS correo_cliente,
            c.nombre AS nombre_cafeteria
        FROM ventas v
        INNER JOIN admin_usuarios u ON u.id = v.id_cliente
        INNER JOIN cafeterias c ON c.id = v.id_cafeteria
        WHERE v.id = :id_pedido");

        $stmt->bindParam(':id_pedido', $id_pedido, PDO::PARAM_INT);
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
        AND vi.estado = 0");

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
        AND vii.estado = 0");

        $stmt->bindParam(':id_venta_item', $id_venta_item, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }

    /* OBTENER ZONA HORARIA DE LA CIUDAD DEL PEDIDO */
    
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


    /* ========== ACCIONES SOBRE PEDIDOS ========== */

    // Aceptar pedido (estado 1 -> 2)
    static public function aceptarPedidoModel($datos)
    {
        $stmt = Conexion::conectar()->prepare("UPDATE ventas 
            SET estado_pedido = 2,
                id_aceptado = :id_usuario,
                fecha_aceptado = :fecha
            WHERE id = :id_pedido
            AND estado_pedido = 1");

        $stmt->bindParam(':id_pedido', $datos['id_pedido'], PDO::PARAM_INT);
        $stmt->bindParam(':id_usuario', $datos['id_usuario'], PDO::PARAM_INT);
        $stmt->bindParam(':fecha', $datos['fecha'], PDO::PARAM_STR);

        if ($stmt->execute() && $stmt->rowCount() > 0) {
            return 'success';
        }
        return 'error';
    }

    // Rechazar pedido (estado 1 -> 3)
    static public function rechazarPedidoModel($datos)
    {
        $stmt = Conexion::conectar()->prepare("UPDATE ventas 
            SET estado_pedido = 3,
                id_rechazo = :id_usuario,
                fecha_rechazo = :fecha,
                motivo_rechazo = :motivo
            WHERE id = :id_pedido
            AND estado_pedido = 1");

        $stmt->bindParam(':id_pedido', $datos['id_pedido'], PDO::PARAM_INT);
        $stmt->bindParam(':id_usuario', $datos['id_usuario'], PDO::PARAM_INT);
        $stmt->bindParam(':fecha', $datos['fecha'], PDO::PARAM_STR);
        $stmt->bindParam(':motivo', $datos['motivo'], PDO::PARAM_STR);

        if ($stmt->execute() && $stmt->rowCount() > 0) {
            return 'success';
        }
        return 'error';
    }

    // Marcar como entregado (estado 2 -> 4)
    static public function entregarPedidoModel($datos)
    {
        $stmt = Conexion::conectar()->prepare("UPDATE ventas 
            SET estado_pedido = 4,
                id_entregado = :id_usuario,
                fecha_entragado = :fecha
            WHERE id = :id_pedido
            AND estado_pedido = 2");

        $stmt->bindParam(':id_pedido', $datos['id_pedido'], PDO::PARAM_INT);
        $stmt->bindParam(':id_usuario', $datos['id_usuario'], PDO::PARAM_INT);
        $stmt->bindParam(':fecha', $datos['fecha'], PDO::PARAM_STR);

        if ($stmt->execute() && $stmt->rowCount() > 0) {
            return 'success';
        }
        return 'error';
    }

    // Cancelar pedido (estado -> 5)
    static public function cancelarPedidoModel($datos)
    {
        $stmt = Conexion::conectar()->prepare("UPDATE ventas 
            SET estado_pedido = 5,
                id_cancelado = :id_usuario,
                fecha_cancelado = :fecha
            WHERE id = :id_pedido
            AND estado_pedido IN (1, 2)");

        $stmt->bindParam(':id_pedido', $datos['id_pedido'], PDO::PARAM_INT);
        $stmt->bindParam(':id_usuario', $datos['id_usuario'], PDO::PARAM_INT);
        $stmt->bindParam(':fecha', $datos['fecha'], PDO::PARAM_STR);

        if ($stmt->execute() && $stmt->rowCount() > 0) {
            return 'success';
        }
        return 'error';
    }
}

