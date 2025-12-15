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

