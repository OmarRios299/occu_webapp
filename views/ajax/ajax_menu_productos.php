<?php 

require_once '../../controllers/controller_menu_productos.php';
require_once '../../controllers/controller_template.php';
require_once '../../controllers/controller_general.php';
require_once '../../models/model_menu_productos.php';
require_once '../../models/model_general.php';

session_start();

if(isset($_SESSION['iniciarSesion']) && $_SESSION['iniciarSesion'] == 'ok'){

    if(isset($_GET['productos'])){

        $datos = array(
            'categoria' => $_GET['categoria'],
            'estatus' => $_GET['estatus']
        );
        $controller = "obtenerProductosController";

    }else if(isset($_POST['agregar_producto'])){

        /*  */
        $datos = array(
            "id"             => isset($_POST['id_producto']) ? $_POST['id_producto'] : false,
            "nombre"                => $_POST['nombre'],
            "id_categoria"                => $_POST['id_categoria'],
            "imagen_subir"          => isset($_FILES["imagen_producto"]) ? $_FILES['imagen_producto'] : false,
        );
        $controller = "agregarProductosController";

    }else{

        $datos = false;

    }

    echo ($datos) ? MenuProductosController::$controller($datos) : "error";

}else{
    echo "session_expired";
}