<?php 
require_once '../../controllers/controller_propietarios_menu.php';
require_once '../../controllers/controller_template.php';
require_once '../../controllers/controller_general.php';
require_once '../../models/model_propietarios_menu.php';
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
            "cafeteria"                => isset($_POST['cafeteria'])? $_POST['cafeteria'] : false,
        );
        $controller = "agregarProductoController";

    }else if(isset($_GET['switch_vasos'])){

        $datos = array(
            "accion"                    => $_GET['switch_vasos'],
            "id_producto"               => $_GET['id_producto'],
            "id_tamano"                 => $_GET['id_tamano'],
            "precio"                    => $_GET['precio'],
            "cafeteria"                => isset($_GET['cafeteria'])? $_GET['cafeteria'] : false,
        );
        $controller = "activarTamanoController";

    }else if(isset($_GET['buscarProducto'])){

        $datos = array(
            "id_producto"               => $_GET['id_producto'],
            "cafeteria"                => isset($_GET['cafeteria'])? $_GET['cafeteria'] : false,
        );
        $controller = "buscarProductoController";

    }else if(isset($_GET['actualizarMenu'])){

        $datos = array(
            "actualizar"               => $_GET['actualizarMenu'],
        );
        $controller = "actualizarMenuController";

    }else if(isset($_POST['agregar_subcategoria_extra'])){

        $datos = array(
            "nombre"                => $_POST['nombre'],
            "id_categoria"                => $_POST['id_categoria'],
        );
        $controller = "registrarSubcategoriaController";

    }else if(isset($_POST['agregar_producto_extra'])){

        $datos = array(
            "nombre"                => $_POST['nombre'],
            "id_subcategoria"                => $_POST['id_subcategoria'],
            "imagen_subir"          => isset($_FILES["imagen_producto"]) ? $_FILES['imagen_producto'] : false,
            "campo"                => $_POST['campo'],
        );
        $controller = "agregarProductosExtraController";

    }else{

        $datos = false;

    }

    echo ($datos) ? PropietariosMenuController::$controller($datos) : "error";

}else{
    echo "session_expired";
}