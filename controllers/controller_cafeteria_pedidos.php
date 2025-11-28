<?php

class CafeteriaPedidosController
{

    /* ========== OBTENER PEDIDOS ========== */

    // Obtener todos los pedidos de una cafetería
    static public function obtenerPedidosController($datos)
    {
        $id_cafeteria = $datos['id_cafeteria'];
        $estado_pedido = isset($datos['estado_pedido']) ? $datos['estado_pedido'] : null;

        $pedidos = CafeteriaPedidosModel::obtenerPedidosModel($id_cafeteria, $estado_pedido);
        $contadores = CafeteriaPedidosModel::obtenerContadoresPedidosModel($id_cafeteria);

        // Agregar items resumidos a cada pedido
        foreach ($pedidos as &$pedido) {
            $items = CafeteriaPedidosModel::obtenerItemsPedidoModel($pedido['id']);
            $pedido['items_resumen'] = [];
            $pedido['total_items'] = 0;

            foreach ($items as $item) {
                $pedido['items_resumen'][] = [
                    'cantidad' => $item['cantidad'],
                    'nombre' => $item['nombre_producto'],
                    'tamano' => $item['nombre_tamano'] ? $item['medida'] . ' ' . $item['unidad_medida'] : null
                ];
                $pedido['total_items'] += $item['cantidad'];
            }
        }

        return json_encode([
            'pedidos' => $pedidos,
            'contadores' => [
                'pendientes' => intval($contadores['pendientes'] ?? 0),
                'preparando' => intval($contadores['preparando'] ?? 0),
                'entregados_hoy' => intval($contadores['entregados_hoy'] ?? 0),
                'rechazados_hoy' => intval($contadores['rechazados_hoy'] ?? 0)
            ]
        ]);
    }

    // Obtener detalle completo de un pedido
    static public function obtenerDetallePedidoController($datos)
    {
        $pedido = CafeteriaPedidosModel::obtenerDetallePedidoModel($datos['id_pedido']);

        if (!$pedido) {
            return json_encode(['error' => true, 'message' => 'Pedido no encontrado']);
        }

        // Obtener items del pedido
        $items = CafeteriaPedidosModel::obtenerItemsPedidoModel($datos['id_pedido']);

        // Agregar ingredientes a cada item
        foreach ($items as &$item) {
            $item['ingredientes'] = CafeteriaPedidosModel::obtenerIngredientesItemModel($item['id_item']);
        }

        return json_encode([
            'pedido' => $pedido,
            'items' => $items
        ]);
    }


    /* ========== ACCIONES SOBRE PEDIDOS ========== */

    // Aceptar pedido
    static public function aceptarPedidoController($datos)
    {
        date_default_timezone_set("America/Tijuana");

        $datos_pedido = [
            'id_pedido' => $datos['id_pedido'],
            'id_usuario' => $_SESSION['id'],
            'fecha' => date("Y-m-d H:i:s")
        ];

        $resultado = CafeteriaPedidosModel::aceptarPedidoModel($datos_pedido);

        if ($resultado == 'success') {
            return json_encode([
                'success' => true,
                'message' => 'Pedido aceptado correctamente'
            ]);
        }

        return json_encode([
            'error' => true,
            'message' => 'No se pudo aceptar el pedido'
        ]);
    }

    // Rechazar pedido
    static public function rechazarPedidoController($datos)
    {
        date_default_timezone_set("America/Tijuana");

        if (empty($datos['motivo'])) {
            return json_encode([
                'error' => true,
                'message' => 'Debe proporcionar un motivo de rechazo'
            ]);
        }

        $datos_pedido = [
            'id_pedido' => $datos['id_pedido'],
            'id_usuario' => $_SESSION['id'],
            'fecha' => date("Y-m-d H:i:s"),
            'motivo' => $datos['motivo']
        ];

        $resultado = CafeteriaPedidosModel::rechazarPedidoModel($datos_pedido);

        if ($resultado == 'success') {
            return json_encode([
                'success' => true,
                'message' => 'Pedido rechazado'
            ]);
        }

        return json_encode([
            'error' => true,
            'message' => 'No se pudo rechazar el pedido'
        ]);
    }

    // Entregar pedido
    static public function entregarPedidoController($datos)
    {
        date_default_timezone_set("America/Tijuana");

        $datos_pedido = [
            'id_pedido' => $datos['id_pedido'],
            'id_usuario' => $_SESSION['id'],
            'fecha' => date("Y-m-d H:i:s")
        ];

        $resultado = CafeteriaPedidosModel::entregarPedidoModel($datos_pedido);

        if ($resultado == 'success') {
            return json_encode([
                'success' => true,
                'message' => '¡Pedido entregado!'
            ]);
        }

        return json_encode([
            'error' => true,
            'message' => 'No se pudo marcar como entregado'
        ]);
    }

    // Cancelar pedido
    static public function cancelarPedidoController($datos)
    {
        date_default_timezone_set("America/Tijuana");

        $datos_pedido = [
            'id_pedido' => $datos['id_pedido'],
            'id_usuario' => $_SESSION['id'],
            'fecha' => date("Y-m-d H:i:s")
        ];

        $resultado = CafeteriaPedidosModel::cancelarPedidoModel($datos_pedido);

        if ($resultado == 'success') {
            return json_encode([
                'success' => true,
                'message' => 'Pedido cancelado'
            ]);
        }

        return json_encode([
            'error' => true,
            'message' => 'No se pudo cancelar el pedido'
        ]);
    }
}

