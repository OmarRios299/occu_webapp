<?php 
require_once '../../controllers/controller_menu_ingredientes_categorias.php';
require_once '../../controllers/controller_template.php';
require_once '../../controllers/controller_general.php';
require_once '../../models/model_menu_ingredientes_categorias.php';
require_once '../../models/model_general.php';

session_start();

if(isset($_SESSION['iniciarSesion']) && $_SESSION['iniciarSesion'] == 'ok'){

    if(isset($_GET['categorias'])){

        $datos = true;
        $controller = "obtenerCategoriasController";

    }else if(isset($_POST['agregar_categoria'])){

        /*  */
        $datos = array(
            "id"             => isset($_POST['id']) ? $_POST['id'] : false,
            "nombre"                => $_POST['nombre'],
        );
        $controller = "agregarCategoriaController";

    }else{

        $datos = false;

    }

    echo ($datos) ? MenuIngredientesCategoriasController::$controller($datos) : "error";

}else{
    echo "session_expired";
}