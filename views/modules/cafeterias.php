<?php
if(!isset($action[1])){
$ocultar = ($_SESSION['nivel']=='Administrador') ? "" : "style='display:none;'";
include "cafeterias/tabla_cafeterias.php";
}else{
    if($action[1]=="agregar"){
        if (isset($action[3]) && $action[3]=='imagenes') {
            include "cafeterias/imagenes_cafeteria.php";
        } else {
            include "cafeterias/agregar_cafeteria.php";
        }
        
        
    }else if(is_numeric($action[1])){
        // var_dump($action[1]);
        $cafeteria=CafeteriasController::obtenerDatosCafeteriaController($action[1]);
        if($cafeteria){
            if(isset($action[2]) && $action[2]=="editar")
                include "cafeterias/editar_cafeteria.php";
            else{
                include "cafeterias/ver_cafeteria.php";
            }
        }else{
            include "404.php";
        }
    }
}