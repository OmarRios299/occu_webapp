<?php 
require_once '../../controllers/controller_menu_ingredientes.php';
require_once '../../controllers/controller_template.php';
require_once '../../controllers/controller_general.php';
require_once '../../models/model_menu_ingredientes.php';
require_once '../../models/model_general.php';

session_start();

if(isset($_SESSION['iniciarSesion']) && $_SESSION['iniciarSesion'] == 'ok'){

    if(isset($_GET['ingredientes'])){

        $datos = true;
        $controller = "obtenerIngredientesController";

    }else if(isset($_POST['agregar_ingrediente'])){

        /*  */
        $datos = array(
            "id"             => isset($_POST['id']) ? $_POST['id'] : false,
            "nombre"                => $_POST['nombre'],
            "id_ingrediente_categoria"                => $_POST['id_ingrediente_categoria'],
        );
        $controller = "agregarIngredienteController";

    }else{

        $datos = false;

    }

    echo ($datos) ? MenuIngredientesController::$controller($datos) : "error";

}else{
    echo "session_expired";
}