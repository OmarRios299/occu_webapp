<?php

class CafeteriaPedidosController
{

    /* ========== OBTENER PEDIDOS ========== */

    // Obtener todos los pedidos de una cafetería
    static public function obtenerPedidosController($datos)
    {
        $id_cafeteria = $datos['id_cafeteria'];
        $estado_pedido = isset($datos['estado_pedido']) ? $datos['estado_pedido'] : null;

        // Obtener la zona horaria de la cafetería
        $zona_horaria = CafeteriaPedidosModel::obtenerZonaHorariaCafeteriaModel($id_cafeteria);
        if (!$zona_horaria) {
            $zona_horaria = "America/Tijuana";
        }

        $pedidos = CafeteriaPedidosModel::obtenerPedidosModel($id_cafeteria, $estado_pedido);
        $contadores = CafeteriaPedidosModel::obtenerContadoresPedidosModel($id_cafeteria);

        // Crear objeto DateTimeZone para la cafetería
        $timezone_cafeteria = new DateTimeZone($zona_horaria);
        $ahora = new DateTime('now', $timezone_cafeteria);

        // Agregar items resumidos y calcular minutos transcurridos para cada pedido
        foreach ($pedidos as &$pedido) {
            // Calcular minutos transcurridos usando la zona horaria de la cafetería
            if ($pedido['fecha_alta']) {
                $fecha_alta = new DateTime($pedido['fecha_alta'], $timezone_cafeteria);
                $diferencia = $ahora->diff($fecha_alta);
                $pedido['minutos_transcurridos'] = ($diferencia->days * 24 * 60) + ($diferencia->h * 60) + $diferencia->i;
            } else {
                $pedido['minutos_transcurridos'] = 0;
            }

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
                'terminados' => intval($contadores['terminados'] ?? 0),
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

        // Obtener la zona horaria de la cafetería del pedido
        $zona_horaria = CafeteriaPedidosModel::obtenerZonaHorariaPedidoModel($datos['id_pedido']);
        if (!$zona_horaria) {
            $zona_horaria = "America/Tijuana";
        }

        // Calcular minutos transcurridos usando la zona horaria de la cafetería
        if ($pedido['fecha_alta']) {
            $timezone_cafeteria = new DateTimeZone($zona_horaria);
            $fecha_alta = new DateTime($pedido['fecha_alta'], $timezone_cafeteria);
            $ahora = new DateTime('now', $timezone_cafeteria);
            $diferencia = $ahora->diff($fecha_alta);
            $pedido['minutos_transcurridos'] = ($diferencia->days * 24 * 60) + ($diferencia->h * 60) + $diferencia->i;
        } else {
            $pedido['minutos_transcurridos'] = 0;
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
        // Obtener la zona horaria de la ciudad de la cafetería del pedido
        $zona_horaria = CafeteriaPedidosModel::obtenerZonaHorariaPedidoModel($datos['id_pedido']);
        
        // Si no se encuentra zona horaria, usar la por defecto
        if (!$zona_horaria) {
            $zona_horaria = "America/Tijuana";
        }

        // Establecer la zona horaria antes de obtener la fecha
        date_default_timezone_set($zona_horaria);

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
        // Obtener la zona horaria de la ciudad de la cafetería del pedido
        $zona_horaria = CafeteriaPedidosModel::obtenerZonaHorariaPedidoModel($datos['id_pedido']);
        
        // Si no se encuentra zona horaria, usar la por defecto
        if (!$zona_horaria) {
            $zona_horaria = "America/Tijuana";
        }

        // Establecer la zona horaria antes de obtener la fecha
        date_default_timezone_set($zona_horaria);

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
        // Validar que se proporcione el código
        if (empty($datos['codigo'])) {
            return json_encode([
                'error' => true,
                'message' => 'Debe proporcionar el código de verificación'
            ]);
        }

        // Obtener la zona horaria de la ciudad de la cafetería del pedido
        $zona_horaria = CafeteriaPedidosModel::obtenerZonaHorariaPedidoModel($datos['id_pedido']);
        
        // Si no se encuentra zona horaria, usar la por defecto
        if (!$zona_horaria) {
            $zona_horaria = "America/Tijuana";
        }

        // Establecer la zona horaria antes de obtener la fecha
        date_default_timezone_set($zona_horaria);

        $datos_pedido = [
            'id_pedido' => $datos['id_pedido'],
            'id_usuario' => $_SESSION['id'],
            'fecha' => date("Y-m-d H:i:s"),
            'codigo' => $datos['codigo']
        ];

        $resultado = CafeteriaPedidosModel::entregarPedidoModel($datos_pedido);

        if ($resultado == 'success') {
            return json_encode([
                'success' => true,
                'message' => '¡Pedido entregado!'
            ]);
        }

        if ($resultado == 'codigo_invalido') {
            return json_encode([
                'error' => true,
                'message' => 'El código de verificación es incorrecto'
            ]);
        }

        return json_encode([
            'error' => true,
            'message' => 'No se pudo marcar como entregado'
        ]);
    }

    // Terminar pedido
    static public function terminarPedidoController($datos)
    {
        // Obtener la zona horaria de la ciudad de la cafetería del pedido
        $zona_horaria = CafeteriaPedidosModel::obtenerZonaHorariaPedidoModel($datos['id_pedido']);
        
        // Si no se encuentra zona horaria, usar la por defecto
        if (!$zona_horaria) {
            $zona_horaria = "America/Tijuana";
        }

        // Establecer la zona horaria antes de obtener la fecha
        date_default_timezone_set($zona_horaria);

        $datos_pedido = [
            'id_pedido' => $datos['id_pedido'],
            'id_usuario' => $_SESSION['id'],
            'fecha' => date("Y-m-d H:i:s")
        ];

        $resultado = CafeteriaPedidosModel::terminarPedidoModel($datos_pedido);

        if ($resultado == 'success') {
            return json_encode([
                'success' => true,
                'message' => 'Pedido marcado como terminado'
            ]);
        }

        return json_encode([
            'error' => true,
            'message' => 'No se pudo marcar como terminado'
        ]);
    }

    // Cancelar pedido
    static public function cancelarPedidoController($datos)
    {
        // Obtener la zona horaria de la ciudad de la cafetería del pedido
        $zona_horaria = CafeteriaPedidosModel::obtenerZonaHorariaPedidoModel($datos['id_pedido']);
        
        // Si no se encuentra zona horaria, usar la por defecto
        if (!$zona_horaria) {
            $zona_horaria = "America/Tijuana";
        }

        // Establecer la zona horaria antes de obtener la fecha
        date_default_timezone_set($zona_horaria);

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

