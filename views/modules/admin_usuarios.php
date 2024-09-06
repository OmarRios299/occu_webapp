<?php
if(!isset($action[1])){
include "admin_usuarios/tabla_usuarios.php";
}else{
    if($action[1]=="agregar"){
        include "admin_usuarios/agregar_usuario.php";
    }else if(is_numeric($action[1])){
        // var_dump($action[1]);
        $usuario=AdminUsuariosController::obtenerInfoUsuarioController($action[1]);
        if($usuario){
            if(isset($action[2]) && $action[2]=="editar")
                include "admin_usuarios/editar_usuario.php";
            else{
                include "admin_usuarios/ver_usuario.php";
            }
        }else{
            include "404.php";
        }
    }
}