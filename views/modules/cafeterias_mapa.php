<?php include 'views/modules/cafeterias_lista_filtros.php'; ?>

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
    z-index: 1075 !important; /* Por encima del navbar (1055), otros offcanvas (1060) y Select2 (1070) */
}

/* Offcanvas de filtros debe estar por encima del navbar en pantallas grandes */
@media (min-width: 992px) {
    #offcanvasFiltros {
        z-index: 1060 !important; /* Igual que el carrito y cafetería - por encima de todo */
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

/* Estilos para el tooltip del mapa */
.leaflet-tooltip.custom-tooltip-mapa {
    background: #fff !important;
    border: none !important;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15) !important;
    border-radius: 12px !important;
    padding: 0 !important;
}

.leaflet-tooltip.custom-tooltip-mapa:before {
    border-top-color: #fff !important;
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

<!-- Incluir offcanvas y estilos unificados -->
<?php include 'cafeterias_lista/ver_cafeteria_contenido.php'; ?>
