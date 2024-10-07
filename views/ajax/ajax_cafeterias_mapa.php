<?php

require_once '../../controllers/controller_cafeterias_mapa.php';
require_once '../../controllers/controller_general.php';
require_once '../../models/model_cafeterias_mapa.php';
require_once '../../models/model_general.php';



    if(isset($_POST['obtenerCafeterias'])){

        $datos = array(
            'horario'=> isset($_POST['horario']) ? $_POST['horario']:'',
            'ciudad' => isset($_POST['ciudad']) ? $_POST['ciudad'] : '',
            'servicios' => isset($_POST['servicios']) ? json_decode($_POST['servicios'], true) : '', 
        );
        $controller = "obtenerCafeteriasController";

    }else{

        $datos = false;

    }

    echo ($datos) ? CafeteriasMapaController::$controller($datos) : "error";
