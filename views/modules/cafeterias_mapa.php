<?php include 'views/modules/cafeterias_lista_filtros.php'; ?>
<!-- Menu de mapa -->
<div class="dropdown position-fixed end-0 mt-5 me-3 bd-mode-toggle">
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
    <button class="btn btn-icono btn-buscar py-2 mt-3 menu_offcanvas" id="btn_filtro_mapa"></button>
</div>

<div id="map" style="height: 100vh; width: 100%;"></div>
<div class="modal fade" id="modal_cafeteria" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <input type="hidden" id="id_cafeteria">
                <h5 class="modal-title" id="modalLabel"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body modal_mapa">
                <?php
                include 'cafeterias_lista/ver_cafeteria.php';
                ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="submit" class="btn btn-primary">Aceptar</button>
            </div>
        </div>
    </div>
</div>
