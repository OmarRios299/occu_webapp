<?php include 'views/modules/cafeterias_lista_filtros.php'; ?>

<!-- Incluir estilos de ver_cafeteria.php para el offcanvas -->
<?php 
// Incluir solo los estilos CSS de ver_cafeteria.php
$action_backup = isset($action) ? $action : null;
$action = [1 => 'temp']; // Temporal para que los estilos se carguen
ob_start();
include 'cafeterias_lista/ver_cafeteria.php';
$content = ob_get_clean();
$action = $action_backup;

// Extraer solo los estilos
preg_match('/<style>(.*?)<\/style>/s', $content, $matches);
if (isset($matches[1])) {
    echo '<style>' . $matches[1] . '</style>';
}
?>

<style>
/* ============================================
   Z-INDEX HIERARCHY (de mayor a menor):
   - Carrito: 1060 (más alto cuando está abierto)
   - Offcanvas filtros (desktop): 1056 (por encima del navbar en pantallas grandes)
   - Navbar: 1055 (siempre visible)
   - Offcanvas filtros/cafetería: 1045 (Bootstrap default)
   - Botón filtros: 1020-1030 (más bajo)
   ============================================ */

/* Carrito debe estar por encima de TODO cuando está abierto */
#offcanvasCarrito {
    z-index: 1060 !important; /* Por encima del navbar (1055) */
}

/* Offcanvas de filtros y cafetería deben estar por encima del navbar en pantallas grandes */
@media (min-width: 992px) {
    #offcanvasFiltros {
        z-index: 1056 !important; /* Por encima del navbar (1055) en desktop */
        top: 80px !important; /* Dejar espacio para el navbar */
        height: calc(100vh - 80px) !important; /* Altura ajustada para no tapar navbar */
    }
    
    #offcanvasCafeteria {
        z-index: 1056 !important; /* Por encima del navbar (1055) en desktop */
        top: 80px !important; /* Dejar espacio para el navbar */
        height: calc(100vh - 80px) !important; /* Altura ajustada para no tapar navbar */
        max-height: calc(100vh - 80px) !important;
    }
    
    #offcanvasCafeteria .offcanvas-header {
        padding-top: 1rem !important;
    }
}

/* Navbar debe estar por encima de offcanvas pero por debajo del carrito y filtros */
.mobile-logo-nav,
.mobile-bottom-nav,
.navbar.fixed-top {
    z-index: 1055 !important; /* Bootstrap offcanvas usa 1045, navbar debe estar por encima */
}

/* Desactivar hide-on-scroll en el módulo de mapa - navbar siempre visible */
.mobile-logo-nav.hide-on-scroll,
.mobile-logo-nav.hide-on-scroll.hidden {
    transform: translateY(0) !important;
    opacity: 1 !important;
    visibility: visible !important;
    transition: none !important;
}

/* Botón de filtros - posicionamiento y z-index base */
#btn_filtro_mapa {
    z-index: 1020 !important; /* Botón de filtros debe estar por debajo de todo */
}

/* En pantallas grandes, asegurar que el botón esté por debajo del navbar */
@media (min-width: 992px) {
    /* Sobrescribir el z-index global de bd-mode-toggle (1500) */
    .bd-mode-toggle {
        z-index: 1020 !important; /* Por debajo del navbar (1055) */
        top: 80px !important; /* Ajustar para que esté debajo del navbar */
        bottom: auto !important;
        margin-top: 0 !important;
    }
    
    #btn_filtro_mapa {
        position: relative !important;
        z-index: inherit !important;
    }
    
    /* Mapa debe llegar hasta abajo en pantallas grandes */
    #map {
        height: 100vh !important;
        min-height: 100vh !important;
    }
}

/* En móviles, el botón debe estar ARRIBA (igual que en desktop) */
@media (max-width: 991.98px) {
    /* Botón arriba a la derecha en móviles */
    .bd-mode-toggle {
        top: 80px !important; /* Debajo del navbar móvil */
        bottom: auto !important;
        right: 20px !important;
        left: auto !important;
        margin-top: 0 !important;
        margin-right: 0 !important;
        z-index: 1030 !important; /* Por debajo del carrito (1060) y navbar (1055) pero visible */
    }
    
    #btn_filtro_mapa {
        position: relative !important;
        z-index: inherit !important;
    }
    
    /* Mapa en móviles - ajustar altura considerando navbar y bottom nav */
    #map {
        height: calc(100vh - 60px - 60px) !important; /* 100vh - navbar - bottom nav */
        min-height: calc(100vh - 120px) !important;
    }
}

/* Estilos específicos para el offcanvas de cafetería - sobrescribir estilos de filtros */
#offcanvasCafeteria .offcanvas-body,
#offcanvasCafeteriaMobile .offcanvas-body {
    overflow-y: auto !important;
    overflow-x: hidden !important;
    height: auto !important;
    max-height: calc(100vh - 60px) !important;
    padding: 1rem !important;
    display: block !important;
    flex-direction: unset !important;
    position: relative !important;
}

/* Offcanvas móvil - dejar espacio para el navbar */
#offcanvasCafeteriaMobile {
    max-height: calc(100vh - 60px) !important; /* 100vh - altura del navbar */
    height: calc(100vh - 60px) !important;
    top: 60px !important; /* Empezar debajo del navbar */
    border-top-left-radius: 20px !important;
    border-top-right-radius: 20px !important;
}

#offcanvasCafeteriaMobile .offcanvas-body {
    max-height: calc(100vh - 120px) !important; /* 100vh - navbar - header offcanvas */
    height: calc(100vh - 120px) !important;
}

/* Asegurar que el contenido pueda hacer scroll */
#contenido_cafeteria_mapa,
#contenido_cafeteria_mapa_mobile {
    width: 100%;
    height: auto;
    min-height: 100%;
    padding-bottom: 2rem;
}

/* Scrollbar personalizada para el offcanvas de cafetería */
#offcanvasCafeteria .offcanvas-body::-webkit-scrollbar,
#offcanvasCafeteriaMobile .offcanvas-body::-webkit-scrollbar {
    width: 10px;
}

#offcanvasCafeteria .offcanvas-body::-webkit-scrollbar-track,
#offcanvasCafeteriaMobile .offcanvas-body::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 5px;
}

#offcanvasCafeteria .offcanvas-body::-webkit-scrollbar-thumb,
#offcanvasCafeteriaMobile .offcanvas-body::-webkit-scrollbar-thumb {
    background: var(--principal);
    border-radius: 5px;
    border: 2px solid #f1f1f1;
}

#offcanvasCafeteria .offcanvas-body::-webkit-scrollbar-thumb:hover,
#offcanvasCafeteriaMobile .offcanvas-body::-webkit-scrollbar-thumb:hover {
    background: #e65a4a;
}
</style>

<!-- Menu de mapa -->
<div class="dropdown position-fixed end-0 mt-2 me-3 bd-mode-toggle">
    <!-- <button class="btn btn-icono btn-lista py-2 dropdown-toggle d-flex align-items-center"
        type="button"
        aria-expanded="false"
        data-bs-toggle="dropdown"></button>
    <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="bd-theme-text">
        <li>
            <a type="button" class="dropdown-item d-flex align-items-center" href="<?= $url ?>" data-bs-theme-value="dark" aria-pressed="false">
                Inicio
            </a>
        </li>
        <li>
            <a type="button" class="dropdown-item d-flex align-items-center" href="<?= $url ?>cafeterias_lista" data-bs-theme-value="auto" aria-pressed="true">
                Lista de cafeterías
            </a>
        </li>
    </ul> -->
    <button class="btn btn-icono btn-buscar menu_offcanvas" id="btn_filtro_mapa"></button>
</div>

<div id="map" style="width: 100%;"></div>

<!-- Offcanvas para mostrar información de cafetería (Desktop - desde la derecha) -->
<div class="offcanvas offcanvas-end filtros-offcanvas filtros-offcanvas-desktop" tabindex="-1" id="offcanvasCafeteria" aria-labelledby="offcanvasCafeteriaLabel" style="width: 650px; max-width: 90vw; top: 80px; height: calc(100vh - 80px);">
    <div class="offcanvas-header filtros-offcanvas-header">
        <div class="filtros-header-content">
            <h5 id="offcanvasCafeteriaLabel" class="offcanvas-title-filtros">
                <i class="bi bi-cup-hot-fill"></i>
                <span id="titulo_cafeteria_ver_mapa">Información de Cafetería</span>
            </h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Cerrar"></button>
        </div>
    </div>
    <div class="offcanvas-body filtros-offcanvas-body">
        <input type="hidden" id="id_cafeteria_mapa">
        <div id="contenido_cafeteria_mapa">
            <?php include 'cafeterias_lista/ver_cafeteria_contenido.php'; ?>
        </div>
    </div>
</div>

<!-- Offcanvas para mostrar información de cafetería (Mobile - desde abajo) -->
<div class="offcanvas offcanvas-bottom filtros-offcanvas filtros-offcanvas-mobile" tabindex="-1" id="offcanvasCafeteriaMobile" aria-labelledby="offcanvasCafeteriaMobileLabel" style="max-height: 98vh; height: 98vh;">
    <div class="offcanvas-header filtros-offcanvas-header">
        <div class="filtros-header-content">
            <h5 id="offcanvasCafeteriaMobileLabel" class="offcanvas-title-filtros">
                <i class="bi bi-cup-hot-fill"></i>
                <span id="titulo_cafeteria_ver_mapa_mobile">Información de Cafetería</span>
            </h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Cerrar"></button>
        </div>
    </div>
    <div class="offcanvas-body filtros-offcanvas-body">
        <input type="hidden" id="id_cafeteria_mapa_mobile">
        <div id="contenido_cafeteria_mapa_mobile">
            <?php include 'cafeterias_lista/ver_cafeteria_contenido.php'; ?>
        </div>
    </div>
</div>
