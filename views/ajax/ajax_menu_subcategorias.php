<?php 
require_once '../../controllers/controller_menu_subcategorias.php';
require_once '../../controllers/controller_template.php';
require_once '../../controllers/controller_general.php';
require_once '../../models/model_menu_subcategorias.php';
require_once '../../models/model_general.php';

session_start();

if(isset($_SESSION['iniciarSesion']) && $_SESSION['iniciarSesion'] == 'ok'){

    if(isset($_GET['subcategorias'])){

        $datos = true;
        $controller = "obtenerSubcategoriasController";

    }else if(isset($_POST['agregar_subcategoria'])){

        /*  */
        $datos = array(
            "id"             => isset($_POST['id']) ? $_POST['id'] : false,
            "nombre"                => $_POST['nombre'],
            "id_categoria"                => $_POST['id_categoria'],
        );
        $controller = "agregarSubcategoriaController";

    }else{

        $datos = false;

    }

    echo ($datos) ? MenuSubcategoriasController::$controller($datos) : "error";

}else{
    echo "session_expired";
}