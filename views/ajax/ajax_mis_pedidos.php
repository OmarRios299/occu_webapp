<?php 
require_once '../../controllers/controller_mis_pedidos.php';
require_once '../../controllers/controller_template.php';
require_once '../../models/model_mis_pedidos.php';

session_start();

if(isset($_SESSION['iniciarSesion']) && $_SESSION['iniciarSesion'] == 'ok'){

    if(isset($_GET['obtenerMisPedidos'])){

        $datos = array(
            "pagina" => isset($_GET['pagina']) ? $_GET['pagina'] : 1,
            "por_pagina" => isset($_GET['por_pagina']) ? $_GET['por_pagina'] : 10
        );
        $controller = "obtenerMisPedidosController";

    }else if(isset($_GET['obtenerDetallePedido'])){

        $datos = array(
            "id_pedido" => $_GET['id_pedido']
        );
        $controller = "obtenerDetallePedidoController";

    }else if(isset($_GET['verificarPedidoEnProceso'])){

        $datos = true;
        $controller = "verificarPedidoEnProcesoController";

    }else{

        $datos = false;

    }

    echo ($datos) ? MisPedidosController::$controller($datos) : "error";

}else{
    echo "session_expired";
} ?>

