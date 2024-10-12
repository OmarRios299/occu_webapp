<?php

require_once "../../controllers/controller_registrarme.php";
require_once "../../models/model_registrarme.php";
require_once "../../models/model_general.php";


if(isset($_POST['registrar_usuario'])){

    /*  */
    $datos = array(
        "nombre"                => $_POST['nombre'],
        "apellido"              => $_POST['apellido'],
        "correo"                => $_POST['correo'],
        "contrasena"            => $_POST['contrasena'],
        "nivel"                 => $_POST['nivel']
    );
    $controller = "registrarUsuarioController";

}else if (isset($_POST['valorCampo'])) {

    /* VALIDAR CAMPO */
    $datos = array(
        "valor" => $_POST['valorCampo'],
        "columna" => $_POST['columnaCampo'],
        "tabla" => $_POST['tablaCampo']
    );
    $controller = "validarCampoController";
} else {
    $datos = false;
}

echo ($datos) ? RegistrarmeController::$controller($datos) : "error";