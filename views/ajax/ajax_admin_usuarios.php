<?php require_once '../../controllers/controller_admin_usuarios.php';
require_once '../../controllers/controller_general.php';
require_once '../../controllers/controller_template.php';
require_once '../../models/model_admin_usuarios.php';
require_once '../../models/model_general.php';

session_start();

if(isset($_SESSION['iniciarSesion']) && $_SESSION['iniciarSesion'] == 'ok'){

    if(isset($_POST['registrar_usuario'])){

        /*  */
        $datos = array(
            "id"             => isset($_POST['id_usuario']) ? $_POST['id_usuario'] : false,
            "nombre"                => $_POST['nombre'],
            "apellido"              => isset($_POST['apellido']) ? $_POST['apellido'] : '',
            "correo"                => isset($_POST['correo']) ? $_POST['correo'] : '',
            "contrasena"            => isset($_POST['contrasena']) ? $_POST['contrasena'] : '',
            "telefono"              => isset($_POST['telefono']) ? $_POST['telefono'] : '',
            "nivel"                 => $_POST['nivel'],
            "ciudad"                => isset($_POST['ciudad']) ? $_POST['ciudad'] : '',
            "imagen_subir"          => isset($_FILES["imagen_usuario_subir"]) ? $_FILES['imagen_usuario_subir'] : false,
            // "imagen_captura"         => isset($_POST["imagen_usuario_captura"]) ? $_POST['imagen_usuario_captura'] : false,
        );
        $controller = "registrarUsuarioController";

    }else if(isset($_GET['tabla_usuarios'])){

        $datos = array(
            'ciudad' => $_GET['ciudad'],
            'entidad' =>  $_GET['entidad'],
            'estatus' =>  $_GET['estatus'],
            'nivel' =>  $_GET['nivel'],
        );
        $controller = 'obtenerUsuariosController';

    }else{

        $datos = false;

    }

    echo ($datos) ? AdminUsuariosController::$controller($datos) : "error";

}else{
    echo "session_expired";
} ?>