<?php 
require_once '../../controllers/controller_cafeterias.php';
require_once '../../controllers/controller_general.php';
require_once '../../controllers/controller_template.php';
require_once '../../models/model_cafeterias.php';
require_once '../../models/model_general.php';

session_start();

if(isset($_SESSION['iniciarSesion']) && $_SESSION['iniciarSesion'] == 'ok'){

    if(isset($_POST['registrar_cafeteria'])){

        $datos = array(
            "id"             => isset($_POST['id_cafeteria']) ? $_POST['id_cafeteria'] : false,
            "nombre"         => $_POST['nombre'],
            "correo"             => isset($_POST['correo']) ? $_POST['correo'] : false,
            "telefono"       => $_POST['telefono'],
            "direccion"      => $_POST['direccion'],
            "ciudad"         => $_POST['ciudad'],
            "latitud"        => $_POST['latitud'],
            "longitud"       => $_POST['longitud'],
            "horario_diferente" => $_POST['horario_diferente'],
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

    }else if(isset($_GET['imagenes_cafeteria'])){

        $datos = $_GET['imagenes_cafeteria'];

        $controller = 'cargarTablaImagenesController';

    }else if(isset($_POST['subir_imagen'])){
        $datos = array(
            'id' => $_POST['id'],
            'contador' => $_POST['contador'],
            'imagen_subir' => $_FILES["imagen"],
        );
        $controller = 'subirImagenCafeteriaController';

    }else if(isset($_POST['cargar_servicios'])){

        $datos = $_POST['id_cafeteria'];
        $controller = 'obtenerServicioController';

    }else if(isset($_POST['registrar_servicios'])){

        $datos = array(
            'id_cafeteria' => $_POST['id_cafeteria'],
            'servicios' =>  json_decode($_POST['servicios'], true),
        );
        $controller = 'registrarServiciosController';

    }else if(isset($_GET['tabla_cafeterias'])){

        $datos = array(
            'ciudad' => $_GET['ciudad'],
            'entidad' =>  $_GET['entidad'],
            'estatus' =>  $_GET['estatus'],
        );
        $controller = 'obtenerCafeteriasController';

    }else{

        $datos = false;

    }

    echo ($datos) ? CafeteriasController::$controller($datos) : "error";

}else{
    echo "session_expired";
}
?>