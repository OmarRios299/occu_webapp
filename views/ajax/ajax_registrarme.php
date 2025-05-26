<?php

require_once "../../controllers/controller_registrarme.php";
require_once "../../controllers/controller_template.php";
require_once "../../models/model_registrarme.php";
require_once "../../models/model_general.php";
require_once '../../controllers/controller_phpMailer.php';
require_once "../../models/model_login.php";


if(isset($_POST['registrar_usuario'])){

    /*  */
    $datos = array(
        "nombre"                => $_POST['nombre'],
        "apellido"              => $_POST['apellido'],
        "correo"                => $_POST['correo'],
        "contrasena"            => $_POST['contrasena'],
        "nivel"                 => $_POST['nivel'],
        "ciudad"                => $_POST['ciudad'],
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

}else if (isset($_POST['usuarioVerificacion'])) {

    $datos = array(
        "usuario"                => $_POST['usuarioVerificacion'],
        "contrasena"              => $_POST['contrasena'],
        "pin"                => $_POST['pin'],
    );
    $controller = "validarUsuarioController";

}else if (isset($_POST['reenviar_codigo_correo'])) {

    $datos = array(
        "correo"                => $_POST['reenviar_codigo_correo'],
    );
    $controller = "reenviarCodigoController";
} else {
    $datos = false;
}

echo ($datos) ? RegistrarmeController::$controller($datos) : "error";