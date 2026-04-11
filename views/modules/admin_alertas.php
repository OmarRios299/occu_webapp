<?php
if(!isset($action[1])){
    include "admin_alertas/tabla_alertas.php";
}else{
    if($action[1]=="agregar"){
        // Modo agregar - no hay alerta
        $alerta = null;
        include "admin_alertas/formulario_alerta.php";
    }else if(is_numeric($action[1])){
        // Obtener información de la alerta
        $alerta = AdminAlertasController::obtenerInfoAlertaController($action[1]);
        if($alerta){
            if(isset($action[2]) && $action[2]=="editar"){
                // Modo edición
                include "admin_alertas/formulario_alerta.php";
            }else{
                include "admin_alertas/ver_alerta.php";
            }
        }else{
            include "404.php";
        }
    }
}

