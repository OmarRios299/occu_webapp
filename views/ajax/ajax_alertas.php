<?php

require_once "../../controllers/controller_alertas.php";
require_once "../../models/model_alertas.php";

/*===============================
=     MARCAR ALERTA COMO VISTA   =
===============================*/

if(isset($_POST['marcarVista']) && isset($_POST['id_alerta'])){
    
    session_start();
    
    if (!isset($_SESSION['iniciarSesion']) || $_SESSION['iniciarSesion'] != 'ok') {
        echo json_encode([
            'success' => false,
            'mensaje' => 'Sesión no iniciada'
        ]);
        exit;
    }
    
    $id_usuario = $_SESSION['id'];
    $id_alerta = intval($_POST['id_alerta']);
    
    $respuesta = AlertaController::marcarAlertaVistaController($id_usuario, $id_alerta);
    
    echo $respuesta;
    
}

/*=====  End of MARCAR ALERTA COMO VISTA  ======*/

/*===============================
=     OBTENER CONTADOR DE IGNORAR   =
===============================*/

if(isset($_GET['obtener_contador_ignorar']) && isset($_GET['id_alerta'])){
    
    session_start();
    
    if (!isset($_SESSION['iniciarSesion']) || $_SESSION['iniciarSesion'] != 'ok') {
        echo json_encode([
            'success' => false,
            'mensaje' => 'Sesión no iniciada'
        ]);
        exit;
    }
    
    $id_usuario = $_SESSION['id'];
    $id_alerta = intval($_GET['id_alerta']);
    
    $respuesta = AlertaController::obtenerContadorIgnorarController($id_usuario, $id_alerta);
    
    echo $respuesta;
    
}

/*=====  End of OBTENER CONTADOR DE IGNORAR  ======*/

/*===============================
=     INCREMENTAR CONTADOR DE IGNORAR   =
===============================*/

if(isset($_POST['incrementar_contador_ignorar']) && isset($_POST['id_alerta'])){
    
    session_start();
    
    if (!isset($_SESSION['iniciarSesion']) || $_SESSION['iniciarSesion'] != 'ok') {
        echo json_encode([
            'success' => false,
            'mensaje' => 'Sesión no iniciada'
        ]);
        exit;
    }
    
    $id_usuario = $_SESSION['id'];
    $id_alerta = intval($_POST['id_alerta']);
    
    $respuesta = AlertaController::incrementarContadorIgnorarController($id_usuario, $id_alerta);
    
    echo $respuesta;
    
}

/*=====  End of INCREMENTAR CONTADOR DE IGNORAR  ======*/

