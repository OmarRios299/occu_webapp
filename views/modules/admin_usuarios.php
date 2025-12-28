<?php
if(!isset($action[1])){
include "admin_usuarios/tabla_usuarios.php";
}else{
    if($action[1]=="agregar"){
        // Modo agregar - no hay usuario
        $usuario = null;
        include "admin_usuarios/formulario_usuario.php";
    }else if(is_numeric($action[1])){
        // Obtener información del usuario
        $usuario=AdminUsuariosController::obtenerInfoUsuarioController($action[1]);
        if($usuario){
            if(isset($action[2]) && $action[2]=="editar"){
                // Modo edición
                include "admin_usuarios/formulario_usuario.php";
            }else{
                include "admin_usuarios/ver_usuario.php";
            }
        }else{
            include "404.php";
        }
    }
}