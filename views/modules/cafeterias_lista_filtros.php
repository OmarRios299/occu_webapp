<!-- Offcanvas de Filtros para Desktop (lateral desde la derecha) -->
<div class="offcanvas offcanvas-end filtros-offcanvas filtros-offcanvas-desktop" tabindex="-1" id="offcanvasFiltros" aria-labelledby="offcanvasFiltrosLabel">
    <div class="offcanvas-header filtros-offcanvas-header">
        <div class="filtros-header-content">
            <h5 id="offcanvasFiltrosLabel" class="offcanvas-title-filtros">
                <i class="bi bi-funnel-fill"></i>
                <span>Filtros de Búsqueda</span>
            </h5>
            <button type="button" class="btn-close-offcanvas" data-bs-dismiss="offcanvas" aria-label="Cerrar">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
    </div>
    <div class="offcanvas-body filtros-offcanvas-body">
        <form onsubmit="return false;" id="aplicar_filtros_offcanvas">
            <input type="hidden" id="ciudades_filtro_offcanvas" value="">
            
            <div class="filtros-content-wrapper">
                <!-- Filtro de Calificación -->
                <!-- <div class="filtro-section">
                    <div class="filtro-section-header">
                        <i class="bi bi-star-fill"></i>
                        <h6>Calificación Mínima</h6>
                    </div>
                    <div class="star-rating-modern">
                        <input type="radio" id="star5-filtros" name="rating" value="5" />
                        <label for="star5-filtros" title="5 estrellas">★</label>
                        <input type="radio" id="star4-filtros" name="rating" value="4" />
                        <label for="star4-filtros" title="4 estrellas">★</label>
                        <input type="radio" id="star3-filtros" name="rating" value="3" checked />
                        <label for="star3-filtros" title="3 estrellas">★</label>
                        <input type="radio" id="star2-filtros" name="rating" value="2" />
                        <label for="star2-filtros" title="2 estrellas">★</label>
                        <input type="radio" id="star1-filtros" name="rating" value="1" />
                        <label for="star1-filtros" title="1 estrella">★</label>
                    </div>
                </div> -->

                <div class="filtro-divider-section"></div>

                <!-- Filtro de Horario -->
                <div class="filtro-section">
                    <div class="filtro-section-header">
                        <i class="bi bi-clock-fill"></i>
                        <h6>Horario</h6>
                    </div>
                    <div class="horario-buttons-vertical">
                        <label class="horario-btn-vertical">
                            <input type="radio" name="horario_filtro" value="todos" checked>
                            <span class="horario-btn-content-vertical">
                                <i class="bi bi-calendar3"></i>
                                <div>
                                    <strong>Todos los horarios</strong>
                                    <span>Verás todas las cafeterías</span>
                                </div>
                            </span>
                        </label>
                        <label class="horario-btn-vertical">
                            <input type="radio" name="horario_filtro" value="abierto">
                            <span class="horario-btn-content-vertical">
                                <i class="bi bi-circle-fill"></i>
                                <div>
                                    <strong>Abiertas ahora</strong>
                                    <span>Solo cafeterías abiertas</span>
                                </div>
                            </span>
                        </label>
                    </div>
                </div>

                <div class="filtro-divider-section"></div>

                <!-- Filtro de Ciudad -->
                <div class="filtro-section">
                    <div class="filtro-section-header">
                        <i class="bi bi-geo-alt-fill"></i>
                        <h6>Ciudad</h6>
                    </div>
                    <select class="select-modern select2 select_ciudades_filtro" id_pais='<?= $_SESSION['ciudad'] ?>'>
                        <option value="" selected disabled>Selecciona una ciudad</option>
                        <option value="">Todas las ciudades</option>
                        <?php foreach (GeneralController::obtenerCiudadesController() as $ciudad) { ?>
                            <option value="<?= $ciudad['id'] ?>" coordenadas='<?= isset($ciudad['coordenadas']) ? $ciudad['coordenadas'] : '' ?>'><?= $ciudad['nombre'] ?></option>
                        <?php }  ?>
                    </select>
                </div>

                <div class="filtro-divider-section"></div>

                <!-- Filtro de Servicios -->
                <div class="filtro-section">
                    <div class="filtro-section-header">
                        <i class="bi bi-grid-3x3-fill"></i>
                        <h6>Servicios</h6>
                    </div>
                    <label class="switch-modern-large">
                        <input type="checkbox" class="check_servicios" value="">
                        <span class="switch-slider-large"></span>
                        <div class="switch-label-content">
                            <strong>Buscar por servicios</strong>
                            <span>Filtrar cafeterías por servicios que ofrecen</span>
                        </div>
                    </label>
                </div>

                <!-- Sección de Servicios expandida -->
                <div class="div_servicios filtros-servicios-expandidos" style="display: none;">
                    <div class="filtro-divider-section"></div>
                    <div class="filtro-section">
                        <div class="filtro-section-header">
                            <i class="bi bi-tags-fill"></i>
                            <h6>Selecciona los Servicios</h6>
                        </div>
                        <div class="servicios-grid-filtros caja_servicios">
                            <!-- Los servicios se cargarán aquí dinámicamente -->
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    
    <!-- Footer fijo del offcanvas -->
    <div class="filtros-offcanvas-footer">
        <button type="button" class="btn-clear-filters" onclick="limpiarFiltros();">
            <i class="bi bi-arrow-clockwise"></i>
            <span>Limpiar</span>
        </button>
        <button type="button" class="btn-apply-filters-offcanvas" onclick="aplicarFiltros();">
            <i class="bi bi-funnel-fill"></i>
            <span>Aplicar Filtros</span>
        </button>
    </div>
</div>

<!-- Offcanvas de Filtros para Móvil (inferior) -->
<div class="offcanvas offcanvas-bottom custom-offcanvas-height filtros-offcanvas-mobile" tabindex="-1" id="offcanvasFiltrosMobile" aria-labelledby="offcanvasFiltrosMobileLabel">
    <div class="offcanvas-header-modern">
        <h5 id="offcanvasFiltrosMobileLabel" class="offcanvas-title-modern">
            <i class="bi bi-funnel-fill"></i>
            <span>Filtros</span>
        </h5>
        <button type="button" class="btn-close-modern" data-bs-dismiss="offcanvas" aria-label="Cerrar">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>
    <div class="offcanvas-body-modern">
        <form onsubmit="return false;" id="aplicar_filtros_mobile">
            <input type="hidden" id="ciudades_filtro_mobile" value="">
            
            <!-- Ciudad -->
            <div class="offcanvas-filter-group">
                <label class="offcanvas-filter-label">
                    <i class="bi bi-geo-alt-fill"></i>
                    Ciudad
                </label>
                <select class="select-modern select2 select_ciudades_filtro" id_pais='<?= $_SESSION['ciudad'] ?>'>
                    <option value="" selected disabled>Selecciona una ciudad</option>
                    <option value="">Todas</option>
                    <?php foreach (GeneralController::obtenerCiudadesController() as $ciudad) { ?>
                        <option value="<?= $ciudad['id'] ?>" coordenadas='<?= isset($ciudad['coordenadas']) ? $ciudad['coordenadas'] : '' ?>'><?= $ciudad['nombre'] ?></option>
                    <?php }  ?>
                </select>
            </div>

            <!-- Calificación -->
            <!-- <div class="offcanvas-filter-group">
                <label class="offcanvas-filter-label">
                    <i class="bi bi-star-fill"></i>
                    Calificación mínima
                </label>
                <div class="star-rating-modern">
                    <input type="radio" id="star5-mobile" name="rating" value="5" />
                    <label for="star5-mobile" title="5 estrellas">★</label>
                    <input type="radio" id="star4-mobile" name="rating" value="4" />
                    <label for="star4-mobile" title="4 estrellas">★</label>
                    <input type="radio" id="star3-mobile" name="rating" value="3" checked />
                    <label for="star3-mobile" title="3 estrellas">★</label>
                    <input type="radio" id="star2-mobile" name="rating" value="2" />
                    <label for="star2-mobile" title="2 estrellas">★</label>
                    <input type="radio" id="star1-mobile" name="rating" value="1" />
                    <label for="star1-mobile" title="1 estrella">★</label>
                </div>
            </div> -->

            <!-- Horario -->
            <div class="offcanvas-filter-group">
                <label class="offcanvas-filter-label">
                    <i class="bi bi-clock-fill"></i>
                    Horario
                </label>
                <div class="horario-options">
                    <label class="radio-modern">
                        <input type="radio" name="horario_filtro" id="flexRadioDefault1-mobile" value="todos" checked>
                        <span class="radio-custom"></span>
                        <div class="radio-content">
                            <strong>Todas las cafeterías</strong>
                            <span>Verás todas las cafeterías</span>
                        </div>
                    </label>
                    <label class="radio-modern">
                        <input type="radio" name="horario_filtro" id="flexRadioDefault2-mobile" value="abierto">
                        <span class="radio-custom"></span>
                        <div class="radio-content">
                            <strong>Abiertas ahora</strong>
                            <span>No verás las cafeterías cerradas</span>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Servicios -->
            <div class="offcanvas-filter-group">
                <label class="offcanvas-filter-label">
                    <i class="bi bi-grid-fill"></i>
                    Servicios
                </label>
                <label class="checkbox-modern">
                    <input type="checkbox" class="check_servicios" id="listGroupRadioGrid3-mobile" value="">
                    <span class="checkbox-custom"></span>
                    <div class="checkbox-content">
                        <strong>Buscar por servicios</strong>
                        <span>Busca por servicios que ofrecen</span>
                    </div>
                </label>
            </div>

            <!-- Sección de Servicios -->
            <div class="div_servicios" style="display: none;">
                <div class="offcanvas-filter-group">
                    <label class="offcanvas-filter-label">
                        <i class="bi bi-tags-fill"></i>
                        Selecciona servicios
                    </label>
                    <div class="caja_servicios"></div>
                </div>
            </div>

            <!-- Botón de aplicar -->
            <div class="offcanvas-actions-menu">
                <button type="button" class="btn-apply-filters-offcanvas" onclick="aplicarFiltrosMobile();">
                    <i class="bi bi-search"></i>
                    <span>Aplicar filtros</span>
                </button>
            </div>
        </form>
    </div>
</div>