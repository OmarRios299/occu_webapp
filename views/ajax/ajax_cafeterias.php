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
            "nombre"         => $_POST['nombre'],
            "correo"         => $_POST['correo'],
            "telefono"       => $_POST['telefono'],
            "direccion"      => $_POST['direccion'],
            "ciudad"         => $_POST['ciudad'],
            "latitud"        => $_POST['latitud'],
            "longitud"       => $_POST['longitud'],
            "imagen_subir"   => isset($_FILES["imagen_cafeteria"]) ? $_FILES['imagen_cafeteria'] : false
        );
        
        if (isset($_POST['horario_apertura']) && isset($_POST['horario_cierre'])) {
            $datos["horario_apertura"] = $_POST['horario_apertura'];
            $datos["horario_cierre"] = $_POST['horario_cierre'];
        }
        
        if (isset($_POST['horarios'])) {
            $datos["horarios"] = json_decode($_POST['horarios'], true);
        }
        
        $controller = "registrarCafeteriaController";        

    }else{

        $datos = false;

    }

    echo ($datos) ? CafeteriasController::$controller($datos) : "error";

}else{
    echo "session_expired";
}
?>