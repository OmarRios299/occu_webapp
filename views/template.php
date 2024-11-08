<?php

session_start();

if (isset($_GET['action'])) {
    $action = explode("/", $_GET['action']);
    $moduloActual = $action[0];
} else {
    $moduloActual = "dashboard";
}

if ($moduloActual == "salir") {
    setcookie('token_session', '', time(), '/');
    session_destroy();
    echo '<script>window.location="login"</script>';
}

//verificamos si hay session y una cookie del token
if (!isset($_SESSION['iniciarSesion']) && isset($_COOKIE['token_session'])) {
    //buscamos el usuario al que pertenece el usuario para hacer login
    LoginController::ingresoTokenController($_COOKIE['token_session']);
}

$template = new TemplateController();
$url = $template->obtenerUrlController();
$v = "1.0.7";

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>OCCU</title>
    <meta name="theme-color" content="#000000">
    <meta name="msapplication-navbutton-color" content="#000000">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">

    <link rel="icon" sizes="192x192" href="<?php echo $url; ?>views/assets/img/logo_1.png">

    <!-- Bootstrap CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <!-- DataTables -->
    <link rel="stylesheet" type="text/css" href="<?php echo $url; ?>views/assets/plugins/DataTables/datatables.min.css" />

    <!-- Iconos -->
    <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <!-- <link rel="stylesheet" type="text/css" href="<?php echo $url; ?>views/assets/css/css/fontawesome/all.min.css"> -->

    <!-- Estilos internos -->
    <link rel="stylesheet" type="text/css" href="<?php echo $url; ?>views/assets/css/css/style.css">

    <!-- Scrollbar Custom CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/malihu-custom-scrollbar-plugin/3.1.5/jquery.mCustomScrollbar.min.css">

    <!-- Select2 stylesheet -->
    <link href="<?php echo $url; ?>views/assets/plugins/select2/dist/css/select2.min.css" rel="stylesheet">
    <!-- Select2 stylesheet -->

    <!-- Incluir Font Awesome (iconos) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <!-- Splide.js (para los carouseles) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@splidejs/splide@latest/dist/css/splide.min.css">

</head>

<body id="body">

    <input type="hidden" class="url" value="<?php echo $url; ?>">

    <input type="hidden" class="moduloActual" value="<?php echo $moduloActual; ?>">

    <?php
    if (isset($_SESSION['iniciarSesion']) && $_SESSION['iniciarSesion'] == 'ok') {
        // Determina si la página actual es "cafeterias_mapa" y estructura el layout en consecuencia
        if ($moduloActual == "cafeterias_mapa") {
            echo '<div style="width: 100%; margin: 0; height:100vh;"><div>';
        } else {
            echo '<div id="sistema">';
            echo '<div class="contenido">';
            include "modules/sections/navbar.php";
            echo '<div class="modulos">';
        }

        // Determina el módulo a incluir basado en el valor de $action[0]
        if (isset($action[0])) {
            // Lista blanca de módulos permitidos sin importar el nivel
            $modulosPermitidos = [
                "dashboard",
                "404",
                "mantenimiento",
                "cafeterias_lista",
                "cafeterias_mapa",
                "registrarme",
                "salir"
            ];

            if (in_array($action[0], $modulosPermitidos)) {
                echo '<div class="modulo-' . htmlspecialchars($action[0]) . '">';
                include "modules/" . htmlspecialchars($action[0]) . ".php";
                echo '</div>';
            } else {
                $modulo = GeneralController::buscarModuloSistema($action[0]);
                if ($modulo && $modulo['permiso_modulo'] == 1) {
                    echo '<div class="modulo-' . htmlspecialchars($action[0]) . '">';
                    include "modules/" . htmlspecialchars($action[0]) . ".php";
                    echo '</div>';
                } else {
                    echo '<div class="modulo-404">';
                    include "modules/404.php";
                    echo '</div>';
                }
            }
        } else {
            echo '<div class="modulo-dashboard">';
            include "modules/dashboard.php";
            echo '</div>';
        }

        echo '</div></div></div><div class="overlay"></div>';
    } else if (isset($action[0])) {
        // Layout para las páginas públicas o sin sesión iniciada
        if ($moduloActual == "cafeterias_mapa") {
            echo '<div style="width: 100%; margin: 0; height:100vh;"><div>';
        } else if ($moduloActual == "inicio") {
            echo '<div id="pagina-inicial">';
            echo '<div class="contenido">';
            include "modules/sections/sidebar.php";
            echo '<div>';
        } else if ($moduloActual == "login" || $moduloActual == "registrarme") {
            echo '<div id="sistema">';
            echo '<div class="content-login">';
            include "modules/sections/sidebar.php";
            echo '<div>';
        }  else {
            echo '<div id="sistema">';
            echo '<div class="contenido">';
            include "modules/sections/sidebar.php";
            echo '<div class="modulos">';
        }

        // Lista blanca de módulos públicos permitidos
        $modulosPublicos = [
            "404",
            "mantenimiento",
            "cafeterias_lista",
            "cafeterias_mapa",
            "registrarme",
            "inicio"
        ];

        if (in_array($action[0], $modulosPublicos)) {
            echo '<div class="modulo-' . htmlspecialchars($action[0]) . '">';
            include "modules/" . htmlspecialchars($action[0]) . ".php";
            echo '</div>';
        } else {
            echo '<div class="modulo-404">';
            include "modules/login.php";
            echo '</div>';
        }

        echo '</div></div></div><div class="overlay"></div>';
    } else {
        echo '<div class="contenido">';
        include "modules/sections/sidebar.php";
        echo '<div class="modulos">';
        include "modules/inicio.php";
        echo '</div></div></div><div class="overlay"></div>';
    }
    ?>



    <!-- jQuery     <script async defer src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&libraries=geometry,drawing&callback=initMap"></script>
-->
    <script src="<?php echo $url; ?>views/assets/js/jquery-3.7.1.min.js"></script>

    <!-- Mapa google -->
    <script async src="https://maps.googleapis.com/maps/api/js?key=<?=TemplateController::obtenerKeyGoogle()?>&libraries=geometry,drawing&v=beta" defer></script>

    <!-- Popper.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js" integrity="sha384-cs/chFZiN24E4KMATLdqdvsezGxaGsi4hLGOzlXwp5UZB1LY//20VyM2taTB4QvJ" crossorigin="anonymous"></script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

    <!-- SweetAlert -->
    <script src="<?php echo $url; ?>views/assets/plugins/sweetalert/dist/sweetalert.min.js"></script>

    <!-- jQuery Custom Scroller CDN -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/malihu-custom-scrollbar-plugin/3.1.5/jquery.mCustomScrollbar.concat.min.js"></script>

    <!-- scripts -->
    <script src="<?php echo $url; ?>views/assets/plugins/chartjs/chart.js"></script>
    <script src="<?php echo $url; ?>views/assets/plugins/moment/min/moment.min.js"></script>
    <script src="<?php echo $url; ?>views/assets/css/js/main.js"></script>
    <script src="<?php echo $url; ?>views/assets/css/js/color-modes.js"></script>
    <script src="<?php echo $url; ?>views/assets/plugins/select2/dist/js/select2.full.min.js"></script>
    <script src="<?php echo $url; ?>views/assets/plugins/editable-select/jquery-editable-select.min.js"></script>
    <script src="<?php echo $url; ?>views/assets/plugins/jqueryLoading/loading.js"></script>
    <script type="text/javascript" src="<?php echo $url; ?>views/assets/plugins/DataTables/datatables.min.js"></script>
    <!-- Custom scripts -->
    <script src="<?php echo $url; ?>views/assets/js/scripts/general.js?v='<?php echo $v; ?>'"></script>
    <script src="<?php echo $url; ?>views/assets/js/scripts/login.js?v='<?php echo $v; ?>'"></script>
    <script src="<?php echo $url; ?>views/assets/js/scripts/registrarme.js?v='<?php echo $v; ?>'"></script>
    <!-- Administración -->
    <script src="<?php echo $url; ?>views/assets/js/scripts/admin_usuarios.js?v='<?php echo $v; ?>'"></script>
    <script src="<?php echo $url; ?>views/assets/js/scripts/admin_paises.js?v='<?php echo $v; ?>'"></script>
    <script src="<?php echo $url; ?>views/assets/js/scripts/admin_pagina_inicial.js?v='<?php echo $v; ?>'"></script>
    <!-- Cafeterías -->
    <script src="<?php echo $url; ?>views/assets/js/scripts/cafeterias.js?v='<?php echo $v; ?>'"></script>
    <script src="<?php echo $url; ?>views/assets/js/scripts/cafeterias_mapa.js?v='<?php echo $v; ?>'"></script>
    <script src="<?php echo $url; ?>views/assets/js/scripts/cafeterias_lista.js?v='<?php echo $v; ?>'"></script>
    <script src="<?php echo $url; ?>views/assets/js/scripts/cafeterias_servicios.js?v='<?php echo $v; ?>'"></script>
    <!-- Menu -->
    <script src="<?php echo $url; ?>views/assets/js/scripts/menu_categorias.js?v='<?php echo $v; ?>'"></script>
    <script src="<?php echo $url; ?>views/assets/js/scripts/menu_subcategorias.js?v='<?php echo $v; ?>'"></script>
    <script src="<?php echo $url; ?>views/assets/js/scripts/menu_productos.js?v='<?php echo $v; ?>'"></script>
    <script src="<?php echo $url; ?>views/assets/js/scripts/menu_admin.js?v='<?php echo $v; ?>'"></script>
    <!-- Dashboard -->
    <script src="<?php echo $url; ?>views/assets/js/scripts/dashboard.js?v='<?php echo $v; ?>'"></script>

    <!-- Splide.js (para los carouseles) -->
    <script src="https://cdn.jsdelivr.net/npm/@splidejs/splide@latest/dist/js/splide.min.js"></script>
</body>

</html>