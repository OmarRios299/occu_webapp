<?php


class MisPedidosController
{
    // Obtener pedido actual y pedidos anteriores con paginación
    static public function obtenerMisPedidosController($datos = [])
    {
        
        if (!isset($_SESSION['id']) || empty($_SESSION['id'])) {
            return json_encode([
                "error" => true,
                "message" => "Debes iniciar sesión para ver tus pedidos."
            ]);
        }

        $id_usuario = $_SESSION['id'];
        $pagina = isset($datos['pagina']) ? intval($datos['pagina']) : 1;
        $por_pagina = isset($datos['por_pagina']) ? intval($datos['por_pagina']) : 10;
        
        $pedido_actual = MisPedidosModel::obtenerPedidoActualModel($id_usuario);
        $pedidos_anteriores = MisPedidosModel::obtenerPedidosAnterioresModel($id_usuario, $pagina, $por_pagina);
        $total_pedidos = MisPedidosModel::contarPedidosAnterioresModel($id_usuario);
        $total_paginas = ceil($total_pedidos / $por_pagina);

        // Calcular minutos transcurridos para el pedido actual (si existe)
        if ($pedido_actual) {
            $zona_horaria = MisPedidosModel::obtenerZonaHorariaCafeteriaModel($pedido_actual['id_cafeteria']);
            if (!$zona_horaria) {
                $zona_horaria = "America/Tijuana";
            }
            
            if ($pedido_actual['fecha_alta']) {
                $timezone_cafeteria = new DateTimeZone($zona_horaria);
                $fecha_alta = new DateTime($pedido_actual['fecha_alta'], $timezone_cafeteria);
                $ahora = new DateTime('now', $timezone_cafeteria);
                $diferencia = $ahora->diff($fecha_alta);
                $pedido_actual['minutos_transcurridos'] = ($diferencia->days * 24 * 60) + ($diferencia->h * 60) + $diferencia->i;
            } else {
                $pedido_actual['minutos_transcurridos'] = 0;
            }
        }

        // Calcular minutos transcurridos para pedidos anteriores
        foreach ($pedidos_anteriores as &$pedido) {
            $zona_horaria = MisPedidosModel::obtenerZonaHorariaCafeteriaModel($pedido['id_cafeteria']);
            if (!$zona_horaria) {
                $zona_horaria = "America/Tijuana";
            }
            
            // Para pedidos anteriores, calcular desde fecha_alta hasta fecha_entragado o fecha_rechazo, o hasta ahora si aún está en proceso
            $fecha_fin = $pedido['fecha_entragado'] ?? $pedido['fecha_rechazo'] ?? null;
            
            if ($pedido['fecha_alta']) {
                $timezone_cafeteria = new DateTimeZone($zona_horaria);
                $fecha_alta = new DateTime($pedido['fecha_alta'], $timezone_cafeteria);
                
                if ($fecha_fin) {
                    $fecha_final = new DateTime($fecha_fin, $timezone_cafeteria);
                } else {
                    $fecha_final = new DateTime('now', $timezone_cafeteria);
                }
                
                $diferencia = $fecha_final->diff($fecha_alta);
                $pedido['minutos_transcurridos'] = ($diferencia->days * 24 * 60) + ($diferencia->h * 60) + $diferencia->i;
            } else {
                $pedido['minutos_transcurridos'] = 0;
            }
        }

        return json_encode([
            "pedido_actual" => $pedido_actual ? $pedido_actual : null,
            "pedidos_anteriores" => $pedidos_anteriores ? $pedidos_anteriores : [],
            "paginacion" => [
                "pagina_actual" => $pagina,
                "total_paginas" => $total_paginas,
                "total_pedidos" => $total_pedidos,
                "por_pagina" => $por_pagina
            ]
        ]);
    }

    // Obtener detalle completo de un pedido
    static public function obtenerDetallePedidoController($datos)
    {
        
        if (!isset($_SESSION['id']) || empty($_SESSION['id'])) {
            return json_encode([
                "error" => true,
                "message" => "Debes iniciar sesión para ver el detalle del pedido."
            ]);
        }

        $id_usuario = $_SESSION['id'];
        $id_pedido = $datos['id_pedido'];

        $pedido = MisPedidosModel::obtenerDetallePedidoModel($id_pedido, $id_usuario);

        if (!$pedido) {
            return json_encode([
                "error" => true,
                "message" => "Pedido no encontrado."
            ]);
        }

        // Obtener la zona horaria del pedido y calcular minutos transcurridos
        $zona_horaria = MisPedidosModel::obtenerZonaHorariaPedidoModel($id_pedido);
        if (!$zona_horaria) {
            $zona_horaria = "America/Tijuana";
        }
        
        // Calcular minutos transcurridos desde fecha_alta hasta fecha_entragado, fecha_rechazo o ahora
        $fecha_fin = $pedido['fecha_entragado'] ?? $pedido['fecha_rechazo'] ?? null;
        
        if ($pedido['fecha_alta']) {
            $timezone_cafeteria = new DateTimeZone($zona_horaria);
            $fecha_alta = new DateTime($pedido['fecha_alta'], $timezone_cafeteria);
            
            if ($fecha_fin) {
                $fecha_final = new DateTime($fecha_fin, $timezone_cafeteria);
            } else {
                $fecha_final = new DateTime('now', $timezone_cafeteria);
            }
            
            $diferencia = $fecha_final->diff($fecha_alta);
            $pedido['minutos_transcurridos'] = ($diferencia->days * 24 * 60) + ($diferencia->h * 60) + $diferencia->i;
        } else {
            $pedido['minutos_transcurridos'] = 0;
        }

        // Obtener items del pedido
        $items = MisPedidosModel::obtenerItemsPedidoModel($id_pedido);

        // Obtener ingredientes de cada item
        foreach ($items as &$item) {
            $item['ingredientes'] = MisPedidosModel::obtenerIngredientesItemModel($item['id_item']);
        }

        return json_encode([
            "pedido" => $pedido,
            "items" => $items
        ]);
    }

    // Verificar si tiene pedido en proceso
    static public function verificarPedidoEnProcesoController()
    {
        
        if (!isset($_SESSION['id']) || empty($_SESSION['id'])) {
            return json_encode([
                "tiene_pedido" => false
            ]);
        }

        $id_usuario = $_SESSION['id'];
        $tiene_pedido = MisPedidosModel::tienePedidoEnProcesoModel($id_usuario);

        return json_encode([
            "tiene_pedido" => $tiene_pedido
        ]);
    }
}

