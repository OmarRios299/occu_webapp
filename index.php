<?php

//Controllers
require_once "controllers/controller_template.php";
require_once "controllers/controller_general.php";
require_once "controllers/controller_login.php";

//Administración
require_once "controllers/controller_admin_usuarios.php";
require_once "controllers/controller_admin_paises.php";

//Cafeterías
require_once "controllers/controller_cafeterias.php";
require_once "controllers/controller_cafeterias_mapa.php";
require_once "controllers/controller_cafeterias_lista.php";
require_once "controllers/controller_cafeterias_servicios.php";

//Menu
require_once "controllers/controller_menu_categorias.php";

//Models
require_once "models/model_login.php";
require_once "models/model_general.php";

//Administración
require_once "models/model_admin_usuarios.php";
require_once "models/model_admin_paises.php";

//Cafeterías
require_once "models/model_cafeterias.php";
require_once "models/model_cafeterias_mapa.php";
require_once "models/model_cafeterias_lista.php";
require_once "models/model_cafeterias_servicios.php";

//Menu
require_once "models/model_menu_categorias.php";

$template = new TemplateController();
$template -> template();