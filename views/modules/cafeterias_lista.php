<input type="hidden" id="aux_validacion">
<?php 
if (!isset($action[1])) {
    include 'cafeterias_lista/lista_cafeterias.php';
} else {
    if (is_numeric($action[1])) {
        include 'cafeterias_lista/ver_cafeteria.php';
    }else{
        include '404.php';
    }
}

?>