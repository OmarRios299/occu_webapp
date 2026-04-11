<?php
require_once 'config/env.php';
loadEnv(__DIR__ . '/.env');

//loadEnv(__DIR__ . '/../.env');
//require_once 'config/googleApiKey.php';

//Controllers
require_once "controllers/controller_template.php";
require_once "controllers/controller_general.php";
require_once "controllers/controller_login.php";
require_once "controllers/controller_registrarme.php";
require_once "controllers/controller_pagina_inicial.php";
require_once "controllers/controller_cafeterias_menu.php";

//Administración
require_once "controllers/controller_admin_usuarios.php";
require_once "controllers/controller_admin_paises.php";
require_once "controllers/controller_admin_pagina_inicial.php";
require_once "controllers/controller_admin_alertas.php";

//Cafeterías
require_once "controllers/controller_cafeterias.php";
require_once "controllers/controller_cafeterias_mapa.php";
require_once "controllers/controller_cafeterias_lista.php";
require_once "controllers/controller_cafeterias_servicios.php";

//Menu
require_once "controllers/controller_menu_categorias.php";
require_once "controllers/controller_menu_subcategorias.php";
require_once "controllers/controller_menu_productos.php";
require_once "controllers/controller_menu.php";
require_once "controllers/controller_propietarios_menu.php";
require_once "controllers/controller_menu_ingredientes_categorias.php";
require_once "controllers/controller_menu_ingredientes.php";
require_once "controllers/controller_carrito.php";
require_once "controllers/controller_mis_pedidos.php";

//Dashboard
require_once "controllers/controller_dashboard.php";

//Alertas
require_once "controllers/controller_alertas.php";

//Models
require_once "models/model_login.php";
require_once "models/model_general.php";
require_once "models/model_registrarme.php";
require_once "models/model_pagina_inicial.php";
require_once "models/model_cafeterias_menu.php";

//Administración
require_once "models/model_admin_usuarios.php";
require_once "models/model_admin_paises.php";
require_once "models/model_admin_pagina_inicial.php";

//Cafeterías
require_once "models/model_cafeterias.php";
require_once "models/model_cafeterias_mapa.php";
require_once "models/model_cafeterias_lista.php";
require_once "models/model_cafeterias_servicios.php";

//Menu
require_once "models/model_menu_categorias.php";
require_once "models/model_menu_subcategorias.php";
require_once "models/model_menu_productos.php";
require_once "models/model_menu.php";
require_once "models/model_propietarios_menu.php";
require_once "models/model_menu_ingredientes_categorias.php";
require_once "models/model_menu_ingredientes.php";
require_once "models/model_carrito.php";
require_once "models/model_mis_pedidos.php";

//Dashboard
require_once "models/model_dashboard.php";

//Alertas
require_once "models/model_alertas.php";

$template = new TemplateController();
$template -> template();