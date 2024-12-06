<?php 
require_once '../../controllers/controller_menu_propietarios.php';
require_once '../../models/model_menu_propietarios.php';
require_once '../../models/model_general.php';

session_start();

if(isset($_SESSION['iniciarSesion']) && $_SESSION['iniciarSesion'] == 'ok'){

    if(isset($_POST['cargar_categorias'])){

        $datos = true;
        $controller = "obtenerCategoriasController";

    }else if(isset($_POST['cambiar_estado'])){

        $datos = array(
            "id_subcategoria"             => $_POST['id_subcategoria'],
            "estado"                => isset($_POST['estado']) ? $_POST['estado'] : false,
            "id_registro"                => isset($_POST['id_registro'])? $_POST['id_registro'] : false,
        );
        $controller = "agregarSubcategoriaController";

    }else{

        $datos = false;

    }

    echo ($datos) ? MenuPropietariosController::$controller($datos) : "error";

}else{
    echo "session_expired";
}