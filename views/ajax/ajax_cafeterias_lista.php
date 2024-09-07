<?php 
require_once '../../controllers/controller_cafeterias_lista.php';
require_once '../../controllers/controller_template.php';
require_once '../../models/model_cafeterias_lista.php';
require_once '../../models/model_general.php';

session_start();

if(isset($_SESSION['iniciarSesion']) && $_SESSION['iniciarSesion'] == 'ok'){

    if(isset($_POST['cargar_lista'])){

        $datos = true;
        $controller = "obtenerCafeteriasController";

    }else if(isset($_POST['cargar_datos'])){

        $datos = $_POST['id'];
        $controller = "obtenerDatosCafeteriaController";

    }else{

        $datos = false;

    }

    echo ($datos) ? CafeteriasListaController::$controller($datos) : "error";

}else{
    echo "session_expired";
}