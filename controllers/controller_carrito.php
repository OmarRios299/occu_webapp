<?php

class CarritoController
{

    static public function abrirCarritoController($datos)
    {
        // date_default_timezone_set("America/Tijuana");
        $datos['id_usuario'] = $_SESSION['id'];
        // $datos['fecha_alta'] = date("Y-m-d H:i:s");

        $carrito = CarritoModel::buscarCarritoModel($datos);
        $datos['id_carrito'] = $carrito['id'] ?? "";

        $items = CarritoModel::buscarCarritoItemsModel($datos);

        if (!$items) {
            return json_encode([
                "error" => true,
                "message" => "Aún no tienes productos en tu carrito."
            ]);
        }

        foreach ($items as &$item) {
            $item['ingredientes'] = CarritoModel::buscarCarritoItemsIngredientesModel($item);
        }

        return json_encode([
            "carrito" => $carrito,
            "productos" => $items
        ]);
    }


    static public function actualizarItemCantidadController($datos)
    {
        return CarritoModel::actualizarItemCantidadModel($datos);
    }

    static public function eliminarItemCantidadController($datos)
    {
        return CarritoModel::eliminarItemCantidadModel($datos);
    }

    static public function carritoContadorItemsController()
    {
        $id_usuario = $_SESSION['id'];
        return json_encode(['cantidad' => CarritoModel::carritoContadorItemsModel($id_usuario)]);
    }


    /* ========== FUNCIONES PARA PROCESAR PAGO ========== */

    // Obtener resumen del carrito para la página de pago
    static public function obtenerResumenPagoController()
    {
        $id_usuario = $_SESSION['id'];
        
        // Obtener datos del carrito
        $carrito = CarritoModel::obtenerResumenCarritoModel($id_usuario);
        
        if (!$carrito) {
            return json_encode([
                "error" => true,
                "message" => "No tienes productos en tu carrito."
            ]);
        }

        // Obtener items del carrito
        $items = CarritoModel::obtenerItemsParaVentaModel($carrito['id_carrito'], $carrito['id_cafeteria']);
        
        $productos = [];
        $total_general = 0;

        foreach ($items as $item) {
            // Obtener ingredientes del item
            $ingredientes = CarritoModel::obtenerIngredientesParaVentaModel(
                $item['id_item'], 
                $item['id_producto'], 
                $carrito['id_cafeteria']
            );

            // Calcular total de ingredientes
            $total_ingredientes = 0;
            foreach ($ingredientes as $ing) {
                $total_ingredientes += floatval($ing['monto_total']);
            }

            // Calcular subtotal del item (precio unitario + ingredientes)
            $precio_unitario_total = floatval($item['precio_unitario']) + $total_ingredientes;
            $subtotal_item = $precio_unitario_total * $item['cantidad'];

            $productos[] = [
                'nombre' => $item['nombre_producto'],
                'cantidad' => $item['cantidad'],
                'precio_unitario' => $item['precio_unitario'],
                'total_ingredientes' => $total_ingredientes,
                'subtotal' => $subtotal_item
            ];

            $total_general += $subtotal_item;
        }

        return json_encode([
            "carrito" => $carrito,
            "productos" => $productos,
            "total" => $total_general
        ]);
    }

    // Procesar el pago y crear la venta
    static public function procesarPagoController($datos)
    {
        $id_usuario = $_SESSION['id'];

        // Verificar si tiene un pedido en proceso
        $tiene_pedido = CarritoModel::tienePedidoEnProcesoModel($id_usuario);
        
        if ($tiene_pedido) {
            return json_encode([
                "error" => true,
                "message" => "Tienes un pedido en proceso. No puedes realizar otro pedido hasta que se complete o cancele el actual.",
                "tiene_pedido_en_proceso" => true
            ]);
        }

        // Obtener datos del carrito
        $carrito = CarritoModel::obtenerResumenCarritoModel($id_usuario);

        if (!$carrito) {
            return json_encode([
                "error" => true,
                "message" => "No tienes productos en tu carrito."
            ]);
        }

        // Obtener la zona horaria de la ciudad de la cafetería
        $zona_horaria = CarritoModel::obtenerZonaHorariaCafeteriaModel($carrito['id_cafeteria']);
        
        // Si no se encuentra zona horaria, usar la por defecto
        if (!$zona_horaria) {
            $zona_horaria = "America/Tijuana";
        }

        // Establecer la zona horaria antes de obtener la fecha
        date_default_timezone_set($zona_horaria);
        $fecha_actual = date("Y-m-d H:i:s");

        // Obtener items del carrito
        $items = CarritoModel::obtenerItemsParaVentaModel($carrito['id_carrito'], $carrito['id_cafeteria']);

        if (empty($items)) {
            return json_encode([
                "error" => true,
                "message" => "Tu carrito está vacío."
            ]);
        }

        // Calcular monto total de la venta
        $monto_total_venta = 0;
        $items_procesados = [];

        foreach ($items as $item) {
            $ingredientes = CarritoModel::obtenerIngredientesParaVentaModel(
                $item['id_item'], 
                $item['id_producto'], 
                $carrito['id_cafeteria']
            );

            $total_ingredientes = 0;
            foreach ($ingredientes as $ing) {
                $total_ingredientes += floatval($ing['monto_total']);
            }

            $monto_unitario = floatval($item['precio_unitario']);
            $monto_subtotal = $monto_unitario * $item['cantidad']; // Solo producto
            $monto_total_item = ($monto_unitario + $total_ingredientes) * $item['cantidad']; // Producto + ingredientes

            $items_procesados[] = [
                'item' => $item,
                'ingredientes' => $ingredientes,
                'monto_unitario' => $monto_unitario,
                'monto_subtotal' => $monto_subtotal,
                'monto_total' => $monto_total_item
            ];

            $monto_total_venta += $monto_total_item;
        }

        // 1. Insertar venta principal
        $datos_venta = [
            'id_cafeteria' => $carrito['id_cafeteria'],
            'id_cliente' => $id_usuario,
            'monto_total' => $monto_total_venta,
            'estado_pedido' => 1, // 1 = Pendiente
            'id_alta' => $id_usuario,
            'fecha_alta' => $fecha_actual
        ];

        $id_venta = CarritoModel::insertarVentaModel($datos_venta);

        if (!$id_venta) {
            return json_encode([
                "error" => true,
                "message" => "Error al crear la venta."
            ]);
        }

        // 2. Insertar items de la venta
        foreach ($items_procesados as $item_proc) {
            $datos_item = [
                'id_venta' => $id_venta,
                'id_producto' => $item_proc['item']['id_producto'],
                'id_tamano' => $item_proc['item']['id_tamano'],
                'monto_unitario' => $item_proc['monto_unitario'],
                'cantidad' => $item_proc['item']['cantidad'],
                'monto_subtotal' => $item_proc['monto_subtotal'],
                'monto_total' => $item_proc['monto_total'],
                'id_alta' => $id_usuario,
                'fecha_alta' => $fecha_actual
            ];

            $id_venta_item = CarritoModel::insertarVentaItemModel($datos_item);

            if (!$id_venta_item) {
                continue; // Si falla un item, continuar con los demás
            }

            // 3. Insertar ingredientes del item
            foreach ($item_proc['ingredientes'] as $ing) {
                $datos_ingrediente = [
                    'id_venta_item' => $id_venta_item,
                    'id_ingrediente' => $ing['id_ingrediente'],
                    'costo_extra' => $ing['costo_extra'] == 'Si' ? 1 : 0,
                    'cantidad_gratis' => $ing['cantidad_gratis'],
                    'cantidad' => $ing['cantidad'],
                    'precio' => $ing['precio'],
                    'monto_total' => $ing['monto_total'],
                    'id_alta' => $id_usuario,
                    'fecha_alta' => $fecha_actual
                ];

                CarritoModel::insertarVentaItemIngredienteModel($datos_ingrediente);
            }
        }

        // 4. Cerrar carrito (marcar como procesado)
        CarritoModel::cerrarCarritoItemsIngredientesModel($carrito['id_carrito']);
        CarritoModel::cerrarCarritoItemsModel($carrito['id_carrito']);
        CarritoModel::cerrarCarritoModel($carrito['id_carrito']);

        return json_encode([
            "success" => true,
            "message" => "¡Pedido realizado con éxito!",
            "id_venta" => $id_venta,
            "total" => $monto_total_venta
        ]);
    }
}
