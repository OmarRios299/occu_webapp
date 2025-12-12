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
            'id_subcategoria' => $_GET['id_subcategoria'],
            'id_categoria' => $_GET['id_categoria'],
            'estatus' => $_GET['estatus']
        );
        $controller = "obtenerProductosController";

    }else if(isset($_POST['agregar_producto'])){

        /*  */
        $datos = array(
            "id"             => isset($_POST['id_producto']) ? $_POST['id_producto'] : false,
            "nombre"                => $_POST['nombre'],
            "id_subcategoria"                => $_POST['id_subcategoria'],
            "imagen_subir"          => isset($_FILES["imagen_producto"]) ? $_FILES['imagen_producto'] : false,
        );
        $controller = "agregarProductosController";

    }else if(isset($_GET['obtener_categorias_bases'])){

        $id_producto = isset($_GET['id_producto']) ? $_GET['id_producto'] : null;
        echo json_encode(MenuProductosController::obtenerCategoriasIngredientesBaseController($id_producto));

    }else if(isset($_GET['obtener_bases_producto'])){

        $id_producto = $_GET['id_producto'];
        echo json_encode(MenuProductosController::obtenerBasesProductoController($id_producto));

    }else if(isset($_POST['guardar_bases_producto'])){

        $categorias = [];
        if(isset($_POST['categorias']) && is_array($_POST['categorias'])){
            $categorias = $_POST['categorias'];
        }
        
        $datos = array(
            'id_producto' => $_POST['id_producto'],
            'categorias' => $categorias
        );
        echo MenuProductosController::guardarBasesProductoController($datos);

    }else{

        $datos = false;

    }

    if(isset($controller)){
        echo ($datos) ? MenuProductosController::$controller($datos) : "error";
    }

}else{
    echo "session_expired";
}