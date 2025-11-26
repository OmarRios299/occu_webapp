<?php

require_once '../../controllers/controller_cafeterias_menu.php';
require_once '../../controllers/controller_general.php';
require_once '../../models/model_cafeterias_menu.php';
require_once '../../models/model_general.php';



    if(isset($_GET['obtenerProducto'])){

        $datos = array(
            'id_producto'=> $_GET['id_producto'],
            'id_cafeteria' => $_GET['id_cafeteria'],
        );
        $controller = "obtenerProductoController";

    }else if(isset($_POST['agregar_producto'])){

        $datos = array(
            'id_cafeteria'=> $_POST['id_cafeteria'],
            'id_producto'=> $_POST['id_producto'],
            'id_cafeteria' => $_POST['id_cafeteria'],
            'cantidad' => $_POST['cantidad'],
            'id_tamano' => $_POST['tamano'],
            'ingredientes' => json_decode($_POST["ingredientes"],true),
            'eliminarOtroCarrito' => $_POST['eliminarOtroCarrito'],
            
        );
        $controller = "agregarCarritoController";

    }else{

        $datos = false;

    }

    echo ($datos) ? CafeteriasMenuController::$controller($datos) : "error";
