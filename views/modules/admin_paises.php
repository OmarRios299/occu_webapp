<?php
if (!isset($action[1])) {
    include "admin_paises/tabla_paises.php";
} else {
    if (is_numeric($action[1])) {
        // var_dump($action[1]);
        $pais = AdminPaisesController::obtenerInfoPaisController($action[1]);
        if ($pais) {
            include "admin_paises/ver_pais.php";
        } else {
            include "404.php";
        }
    } else {
        include "404.php";
    }
}
