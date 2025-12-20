<?php
if(!isset($action[1])){
$ocultar = ($_SESSION['nivel']=='Administrador') ? "" : "style='display:none;'";
include "cafeterias/tabla_cafeterias.php";
}else{
    if($action[1]=="agregar"){
        if (isset($action[3]) && $action[3]=='imagenes') {
            include "cafeterias/imagenes_cafeteria.php";
        } else {
            // Modo agregar - no hay $cafeteria definido
            $cafeteria = null;
            include "cafeterias/form_cafeteria.php";
        }
    }else if(is_numeric($action[1])){
        $data=json_decode(CafeteriasController::obtenerDatosCafeteriaController($action[1]), true);
        $cafeteria=$data['data'];
        if($cafeteria){
            if(isset($action[2]) && $action[2]=="editar"){
                // Modo edición - $cafeteria está definido
                include "cafeterias/form_cafeteria.php";
            }else{
                include "cafeterias/ver_cafeteria.php";
            }
        }else{
            include "404.php";
        }
    }
}