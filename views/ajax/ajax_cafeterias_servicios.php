<?php 

require_once '../../controllers/controller_cafeterias_servicios.php';
require_once '../../controllers/controller_template.php';
require_once '../../controllers/controller_general.php';
require_once '../../models/model_cafeterias_servicios.php';
require_once '../../models/model_general.php';

session_start();

if(isset($_SESSION['iniciarSesion']) && $_SESSION['iniciarSesion'] == 'ok'){

    if(isset($_GET['servicios'])){

        $datos = true;
        $controller = "obtenerServiciosController";

    }else if(isset($_POST['agregar_servicio'])){

        /*  */
        $datos = array(
            "id"             => isset($_POST['id_servicio']) ? $_POST['id_servicio'] : false,
            "nombre"                => $_POST['nombre'],
            "imagen_subir"          => isset($_FILES["imagen_servicio"]) ? $_FILES['imagen_servicio'] : false,
        );
        $controller = "agregarServiciosController";

    }else{

        $datos = false;

    }

    echo ($datos) ? CafeteriasServiciosController::$controller($datos) : "error";

}else{
    echo "session_expired";
}