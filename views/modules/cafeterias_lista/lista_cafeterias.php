<?php include 'views/modules/cafeterias_lista_filtros.php'; ?>
<div class="container mt-4 lista-cafeterias-modern">
    <!-- Encabezado Moderno -->
    <!-- <div class="header-modern mb-5">
        <div class="text-center">
            <h1 class="titulo-principal">¡Bienvenido!</h1>
            <p class="subtitulo-principal">Descubre los mejores cafés cerca de ti</p>
        </div>
    </div> -->

    <!-- Barra de acciones moderna -->
    <div class="actions-bar-menu mb-4">
        <div class="search-container" id="search-container" style="display: none;">
            <div class="search-input-wrapper">
                <i class="bi bi-search search-icon"></i>
                <input type="text" id="filtro-input" class="search-input-modern" placeholder="Buscar cafeterías por nombre...">
            </div>
        </div>
        <div class="actions-buttons-menu">
            <button class="btn-action-menu btn-search" id="buscar_filtro" title="Buscar">
                <i class="bi bi-search"></i>
                <span class="btn-text">Buscar</span>
            </button>
            <button class="btn-action-menu btn-filter menu_offcanvas" id="abrir_filtros" title="Filtros">
                <i class="bi bi-funnel"></i>
                <span class="btn-text">Filtros</span>
            </button>
            <a class="btn-action-menu btn-map" href="<?= $url ?>cafeterias_mapa" title="Ver en mapa">
                <i class="bi bi-map"></i>
                <span class="btn-text">Mapa</span>
            </a>
        </div>
    </div>

    <form onsubmit="return false;" id="aplicar_filtros">
        <input type="hidden" id="ciudades_filtro">
    </form>
    <!-- Contenedor para Listado de Cafeterías -->
    <div class="cafeterias-grid" id="div_lista_cafeterias">
        <!-- Aquí se cargarán las cafeterías dinámicamente -->
    </div>

    <!-- Paginación Moderna -->
    <div class="pagination-modern">
        <button id="boton-anterior" class="btn-pagination" data-pagina="1" disabled>
            <i class="bi bi-chevron-left"></i>
            <span>Anterior</span>
        </button>
        <div class="pagination-info">
            <span id="pagination-text">Página 1</span>
        </div>
        <button id="boton-siguiente" class="btn-pagination" data-pagina="1">
            <span>Siguiente</span>
            <i class="bi bi-chevron-right"></i>
        </button>
    </div>

</div>

<div class="modal fade" id="modal_cafeteria" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalLabel">Selecciona un ubicación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="map" style="height: 500px; width: 100%;"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="submit" class="btn btn-primary" id="btn_guardar_ubicacion" data-bs-dismiss="modal">Aceptar</button>
            </div>
        </div>
    </div>
</div>
