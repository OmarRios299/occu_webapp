<?php

require_once '../../controllers/controller_cafeterias_mapa.php';
require_once '../../models/model_cafeterias_mapa.php';
require_once '../../models/model_general.php';

session_start();

if(isset($_SESSION['iniciarSesion']) && $_SESSION['iniciarSesion'] == 'ok'){

    if(isset($_POST['obtenerCafeterias'])){

        $datos = true;
        $controller = "obtenerCafeteriasController";

    }else{

        $datos = false;

    }

    echo ($datos) ? CafeteriasMapaController::$controller($datos) : "error";

}else{
    echo "session_expired";
}