<?php
require_once '../../controllers/controller_admin_paises.php';
require_once '../../models/model_admin_paises.php';
require_once '../../models/model_general.php';

session_start();

if(isset($_SESSION['iniciarSesion']) && $_SESSION['iniciarSesion'] == 'ok'){

    if(isset($_POST['agregarPais'])){

        $datos = array(
        "nombre" => $_POST['nombre'],
        "id" => isset($_POST['id'])?$_POST['id']:false,
        );
        $controller = "agregarEditarPaisController";

    }else if(isset($_GET['cargarCiudades'])){

        $datos = array(
        "id_pais" => $_GET['id_pais'],
        );
        $controller = "obtenerCiudadesController";

    }else if(isset($_POST['cargarMetricos'])){

        $datos = array(
        "id_pais" => $_POST['pais'],
        );
        $controller = "obtenerDatosMetricosController";

    }else if(isset($_POST['agregar_ciudad'])){

        $datos = array(
            "id" => isset($_POST['id'])?$_POST['id']:false,
            "nombre" => $_POST['nombre'],
            "entidad_federativa" => $_POST['entidad_federativa'],
            "coordenadas" => $_POST['coordenadas']
        );
        $controller = "agregarEditarCiudadController";

    }else{

        $datos = false;

    }

    echo ($datos) ? AdminPaisesController::$controller($datos) : "error";

}else{
    echo "session_expired";
}