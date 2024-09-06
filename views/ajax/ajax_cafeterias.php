<?php 
require_once '../../controllers/controller_cafeterias.php';
require_once '../../controllers/controller_general.php';
require_once '../../models/model_cafeterias.php';
require_once '../../models/model_general.php';

session_start();

if(isset($_SESSION['iniciarSesion']) && $_SESSION['iniciarSesion'] == 'ok'){

    if(isset($_POST['registrar_cafeteria'])){

        $datos = array(
            "id"             => isset($_POST['id_cafeteria']) ? $_POST['id_cafeteria'] : false,
            "nombre"                => $_POST['nombre'],
            "correo"                => $_POST['correo'],
            "telefono"              => $_POST['telefono'],
            "direccion"                => $_POST['direccion'],
            "horario_apertura"                => $_POST['horario_apertura'],
            "horario_cierre"                => $_POST['horario_cierre'],
            "ciudad"                => $_POST['ciudad'],
            "latitud"                => $_POST['latitud'],
            "longitud"                => $_POST['longitud'],
            "imagen_subir"          => isset($_FILES["imagen_cafeteria"]) ? $_FILES['imagen_cafeteria'] : false
        );
        $controller = "registrarCafeteriaController";

    }else{

        $datos = false;

    }

    echo ($datos) ? CafeteriasController::$controller($datos) : "error";

}else{
    echo "session_expired";
}
?>