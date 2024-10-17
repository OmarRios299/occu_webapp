<?php 

require_once '../../controllers/controller_admin_pagina_inicial.php';
require_once '../../controllers/controller_general.php';
require_once '../../models/model_admin_pagina_inicial.php';
require_once '../../models/model_general.php';

session_start();

if(isset($_SESSION['iniciarSesion']) && $_SESSION['iniciarSesion'] == 'ok'){

    if(isset($_POST['registrar_imagen'])){

        $datos = array(
            "id"                    => isset($_POST['id']) ? $_POST['id'] : false,
            "titulo"                => $_POST['titulo'],
            "descripcion"           => $_POST['descripcion'],
            "enlace"                => $_POST['enlace'],
            "area"                  => $_POST['area'],
            "nombre_enlace"         => $_POST['nombre_enlace'],
            "imagen_subir"          => isset($_FILES["imagen_subir"]) ? $_FILES['imagen_subir'] : false,
        );
        $controller = "registrarImagenController";

    }else{

        $datos = false;

    }

    echo ($datos) ? AdminPaginaInicialController::$controller($datos) : "error";

}else{
    echo "session_expired";
}

?>