<?php 
require_once '../../controllers/controller_menu_propietarios.php';
require_once '../../controllers/controller_template.php';
require_once '../../models/model_menu_propietarios.php';
require_once '../../models/model_general.php';

session_start();

if(isset($_SESSION['iniciarSesion']) && $_SESSION['iniciarSesion'] == 'ok'){

    if(isset($_POST['cargar_categorias'])){

        $datos = true;
        $controller = "obtenerCategoriasController";

    }else if(isset($_POST['agregar_subcategoria'])){

        $datos = array(
            "id_subcategoria"             => $_POST['id_subcategoria'],
            "estado"                => isset($_POST['estado']) ? $_POST['estado'] : false,
            "id_registro"                => isset($_POST['id_registro'])? $_POST['id_registro'] : false,
        );
        $controller = "agregarSubcategoriaController";

    }else if(isset($_POST['agregar_producto'])){

        $datos = array(
            "id_producto"             => $_POST['id_producto'],
            "estado"                => isset($_POST['estado']) ? $_POST['estado'] : false,
            "id_registro"                => isset($_POST['id_registro'])? $_POST['id_registro'] : false,
        );
        $controller = "agregarProductoController";

    }else if(isset($_GET['switch_vasos'])){

        $datos = array(
            "accion"                    => $_GET['switch_vasos'],
            "id_producto"               => $_GET['id_producto'],
            "id_tamano"                 => $_GET['id_tamano'],
            "precio"                    => $_GET['precio']
        );
        $controller = "activarTamanoController";

    }else{

        $datos = false;

    }

    echo ($datos) ? MenuPropietariosController::$controller($datos) : "error";

}else{
    echo "session_expired";
}