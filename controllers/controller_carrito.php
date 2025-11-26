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
}
