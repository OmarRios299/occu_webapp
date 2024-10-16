<?php 

require_once '../../controllers/controller_admin_pagina_incial.php';
require_once '../../models/model_admin_pagina_incial.php';
require_once '../../models/model_general.php';

session_start();

if(isset($_SESSION['iniciarSesion']) && $_SESSION['iniciarSesion'] == 'ok'){

    if(isset($_POST['registrar_caja'])){

        /*  */
        $datos = array(
        "nombre" => $_POST['nombre_caja'],
        );
        $controller = "controller";

    }else{

        $datos = false;

    }

    echo ($datos) ? AdminPaginaInicialController::$controller($datos) : "error";

}else{
    echo "session_expired";
}

?>