<?php
require_once '../../controllers/controller_cafeteria_pedidos.php';
require_once '../../controllers/controller_general.php';
require_once '../../controllers/controller_template.php';
require_once '../../models/model_cafeteria_pedidos.php';
require_once '../../models/model_general.php';

session_start();

if (isset($_SESSION['iniciarSesion']) && $_SESSION['iniciarSesion'] == 'ok') {

    if (isset($_GET['obtenerPedidos'])) {

        $datos = array(
            "id_cafeteria" => $_GET['id_cafeteria'],
            "estado_pedido" => isset($_GET['estado_pedido']) ? $_GET['estado_pedido'] : null
        );
        $controller = "obtenerPedidosController";

    } else if (isset($_GET['obtenerDetallePedido'])) {

        $datos = array(
            "id_pedido" => $_GET['id_pedido']
        );
        $controller = "obtenerDetallePedidoController";

    } else if (isset($_POST['aceptarPedido'])) {

        $datos = array(
            "id_pedido" => $_POST['id_pedido']
        );
        $controller = "aceptarPedidoController";

    } else if (isset($_POST['rechazarPedido'])) {

        $datos = array(
            "id_pedido" => $_POST['id_pedido'],
            "motivo" => $_POST['motivo']
        );
        $controller = "rechazarPedidoController";

    } else if (isset($_POST['entregarPedido'])) {

        $datos = array(
            "id_pedido" => $_POST['id_pedido']
        );
        $controller = "entregarPedidoController";

    } else if (isset($_POST['cancelarPedido'])) {

        $datos = array(
            "id_pedido" => $_POST['id_pedido']
        );
        $controller = "cancelarPedidoController";

    } else {

        $datos = false;

    }

    echo ($datos) ? CafeteriaPedidosController::$controller($datos) : "error";

} else {
    echo "session_expired";
}

