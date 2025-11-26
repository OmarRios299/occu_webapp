<?php require_once '../../controllers/controller_carrito.php';
require_once '../../controllers/controller_general.php';
require_once '../../controllers/controller_template.php';
require_once '../../models/model_carrito.php';
require_once '../../models/model_general.php';

session_start();

if(isset($_SESSION['iniciarSesion']) && $_SESSION['iniciarSesion'] == 'ok'){

    if(isset($_GET['abrirCarrito'])){

        $datos = array(
            "id_cafeteria"                => $_GET['id_cafeteria'],
        );
        $controller = "abrirCarritoController";

    }else if(isset($_POST['actualizarCantidad'])){

        $datos = array(
            "id_item"                => $_POST['id_item'],
            "cantidad"                => $_POST['cantidad'],
        );
        $controller = "actualizarItemCantidadController";

    }else if(isset($_POST['eliminarItem'])){

        $datos = array(
            "id_item"                => $_POST['id_item'],
        );
        $controller = "eliminarItemCantidadController";

    }else if(isset($_POST['obtener_carrito'])){

        $datos = true;
        $controller = "carritoContadorItemsController";

    }else{

        $datos = false;

    }

    echo ($datos) ? CarritoController::$controller($datos) : "error";

}else{
    echo "session_expired";
} ?>