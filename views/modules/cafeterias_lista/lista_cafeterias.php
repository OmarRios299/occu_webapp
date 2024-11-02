<div class="container mt-4">
    <!-- Encabezado -->
    <div class="text-center mb-4">
        <h2>¡Bienvenido!</h2>
        <p>Comienza a explorar y encuentra cafés cerca de ti.</p>
    </div>

    <!-- Barra de botones -->
    <div class="d-flex justify-content-center mb-4">
        <button class="btn btn-custom me-2" id="buscar_filtro">
            <i class="bi bi-search"></i> buscar
        </button>
        <button class="btn btn-custom me-2 menu_offcanvas" id="abrir_filtros">
            <i class="bi bi-funnel"></i> filtros
        </button>
        <a class="btn btn-custom" href="<?= $url ?>cafeterias_mapa">
            <i class="bi bi-map"></i> mapa
        </a>
    </div>

    <form onsubmit="return false;" id="aplicar_filtros">
        <input type="hidden" id="ciudades_filtro">
        <div class="row filtros_cafeterias" id="filtros_div" style="display: none;">
            <div class="col-md-5 cambiar-clase mb-2">
                <div class="caja">
                    <div class="row ">
                        <div class="col-md-6 d-flex justify-content-center align-items-center">
                            <div class="row">
                                <div class="col-md-12 text-center">
                                    <label for="">Calificación</label>
                                    <div class="star-rating">
                                        <input type="radio" id="star5" name="rating" value="5" />
                                        <label for="star5" title="5 estrellas">★</label>
                                        <input type="radio" id="star4" name="rating" value="4" />
                                        <label for="star4" title="4 estrellas">★</label>
                                        <input type="radio" id="star3" name="rating" value="3" checked />
                                        <label for="star3" title="3 estrellas">★</label>
                                        <input type="radio" id="star2" name="rating" value="2" />
                                        <label for="star2" title="2 estrellas">★</label>
                                        <input type="radio" id="star1" name="rating" value="1" />
                                        <label for="star1" title="1 estrella">★</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 d-flex justify-content-center align-items-center">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="horario_filtro" value="todos">
                                        <label class="form-check-label" for="flexRadioDefault1">
                                            Todos los horarios
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="horario_filtro" value="abierto">
                                        <label class="form-check-label" for="flexRadioDefault2">
                                            Abiertas ahora
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-5 cambiar-clase mb-2">
                <div class="caja h-100">
                    <div class="row">
                        <div class="col-md-6 mt-0 d-flex justify-content-center align-items-center">
                            <div class="form-check">
                                <label class="form-check-label" for="flexCheckDefault">
                                    Buscar por servicios
                                </label>
                                <input class="form-check-input check_servicios" type="checkbox" value="">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Ciudad</label>
                                <select class="form-control select2 select_ciudades_filtro" id_pais='<?= $_SESSION['ciudad'] ?>'>
                                    <option value="" selected disabled>Selecciona una ciudad</option>
                                    <option value="">Todas</option>
                                    <?php foreach (GeneralController::obtenerCiudadesController() as $ciudad) { ?>
                                        <option value="<?= $ciudad['id'] ?>"><?= $ciudad['nombre'] ?></option>
                                    <?php }  ?>

                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12 div_servicios" style="display: none;" id="">
                <div class="caja mb-2">
                    <div class="row caja_servicios">

                    </div>
                </div>
            </div>
            <div class="col-md d-flex justify-content-center align-items-center h-100 mt-2">
                <div class="caja">
                    <button type="submit" class="btn btn-icono btn-buscar" id=""></button>
                </div>
            </div>
        </div>
    </form>

    <div class="row">
        <!-- Input de Filtrado -->
        <div class="col-md-12">
            <input type="text" id="filtro-input" class="form-control" placeholder="Buscar cafeterías..." style="display: none;">
        </div>
    </div>
    <!-- Contenedor para Listado de Cafeterías -->
    <div class="row mt-3" id="div_lista_cafeterias">
        <!-- Aquí se cargarán las cafeterías dinámicamente -->
    </div>
    <!-- Contenedor de búsqueda y paginación -->
    <div class="row">
        <!-- Botones de Paginación -->
        <div class="col-md-12 text-end">
            <button id="boton-anterior" class="btn btn-primary" data-pagina="1">
                << </button>
                    <button id="boton-siguiente" class="btn btn-primary" data-pagina="1">>></button>
        </div>
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

<style>
    .custom-offcanvas-height {
        height: 50% !important;
        /* Ajusta el porcentaje para mostrar el menú más arriba */

    }

    .list-group {
        width: 100%;
        max-width: 460px;
        margin-inline: 1.5rem;
    }

    .form-check-input:checked+.form-checked-content {
        opacity: .5;
    }

    .form-check-input-placeholder {
        border-style: dashed;
    }

    [contenteditable]:focus {
        outline: 0;
    }

    .list-group-checkable .list-group-item {
        cursor: pointer;
    }

    .list-group-item-check {
        position: absolute;
        clip: rect(0, 0, 0, 0);
    }

    .list-group-item-check:hover+.list-group-item {
        background-color: var(--bs-secondary-bg);
    }

    .list-group-item-check:checked+.list-group-item {
        color: #fff;
        background-color: var(--bs-primary);
        border-color: var(--bs-primary);
    }

    .list-group-item-check[disabled]+.list-group-item,
    .list-group-item-check:disabled+.list-group-item {
        pointer-events: none;
        filter: none;
        opacity: .5;
    }

    .list-group-radio .list-group-item {
        cursor: pointer;
        border-radius: .5rem;
    }

    .list-group-radio .form-check-input {
        z-index: 2;
        margin-top: -.5em;
    }

    .list-group-radio .list-group-item:hover,
    .list-group-radio .list-group-item:focus {
        background-color: var(--bs-secondary-bg);
    }

    .list-group-radio .form-check-input:checked+.list-group-item {
        background-color: var(--bs-body);
        border-color: var(--bs-primary);
        box-shadow: 0 0 0 2px var(--bs-primary);
    }

    .list-group-radio .form-check-input[disabled]+.list-group-item,
    .list-group-radio .form-check-input:disabled+.list-group-item {
        pointer-events: none;
        filter: none;
        opacity: .5;
    }

    .offcanvas {
        border-top-left-radius: 15px;
        /* Ajusta el radio de la esquina superior izquierda */
        border-top-right-radius: 15px;
        /* Ajusta el radio de la esquina superior derecha */
        overflow: hidden;
        /* Evita que el contenido sobresalga de los bordes redondeados */
    }
</style>

<!-- Contenedor del offcanvas -->
<div class="offcanvas offcanvas-bottom custom-offcanvas-height" tabindex="-1" id="offcanvasBottom" aria-labelledby="offcanvasBottomLabel">
    <div class="offcanvas-header">
        <h5 id="offcanvasBottomLabel">Filtrar</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <div class="d-flex flex-column flex-md-row p-4 gap-4 py-md-5 align-items-center justify-content-center">
            <div class="list-group list-group-radio d-grid gap-2 border-0">
                <div class="position-relative">
                    <div class="form-group">
                        <label>Ciudad</label>
                        <select class="form-control select_ciudades_filtro" id_pais='<?= $_SESSION['ciudad'] ?>'>
                            <option value="" selected disabled>Selecciona una ciudad</option>
                            <option value="">Todas</option>
                            <?php foreach (GeneralController::obtenerCiudadesController() as $ciudad) { ?>
                                <option value="<?= $ciudad['id'] ?>"><?= $ciudad['nombre'] ?></option>
                            <?php }  ?>

                        </select>
                    </div>
                </div>
                <div class="position-relative d-flex justify-content-center borde">
                    <div class="row">
                        <div class="col-md-12 text-center">
                            <label for="">Calificación</label>
                            <div class="star-rating">
                                <input type="radio" id="star5" name="rating" value="5" />
                                <label for="star5" title="5 estrellas">★</label>
                                <input type="radio" id="star4" name="rating" value="4" />
                                <label for="star4" title="4 estrellas">★</label>
                                <input type="radio" id="star3" name="rating" value="3" checked />
                                <label for="star3" title="3 estrellas">★</label>
                                <input type="radio" id="star2" name="rating" value="2" />
                                <label for="star2" title="2 estrellas">★</label>
                                <input type="radio" id="star1" name="rating" value="1" />
                                <label for="star1" title="1 estrella">★</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="position-relative mt-2">
                    <input class="form-check-input position-absolute top-50 end-0 me-3 fs-5" type="radio" name="horario_filtro" id="flexRadioDefault1" value="todos">
                    <label class="list-group-item py-3 pe-5" for="flexRadioDefault1">
                        <strong class="fw-semibold">Todas las cafeterías</strong>
                        <span class="d-block small opacity-75">Verás todas las cafeterías</span>
                    </label>
                </div>

                <div class="position-relative">
                    <input class="form-check-input position-absolute top-50 end-0 me-3 fs-5" type="radio" name="horario_filtro" id="flexRadioDefault2" value="abierto">
                    <label class="list-group-item py-3 pe-5" for="flexRadioDefault2">
                        <strong class="fw-semibold">Abiertas ahora</strong>
                        <span class="d-block small opacity-75">No verás las cafeterías que esten cerradas</span>
                    </label>
                </div>

                <div class="position-relative mt-2">
                    <input class="form-check-input position-absolute top-50 end-0 me-3 fs-5 check_servicios" type="checkbox" name="" id="listGroupRadioGrid3">
                    <label class="list-group-item py-3 pe-5" for="listGroupRadioGrid3">
                        <strong class="fw-semibold">Buscar por servicios</strong>
                        <span class="d-block small opacity-75">No verás las cafeterías que esten cerradas</span>
                    </label>
                </div>

            </div>
            <div class="col-12 div_servicios" style="display: none;" id="">
                <div class="d-flex flex-wrap justify-content-start caja_servicios">
                    <!-- Elemento de servicio -->

                </div>
            </div>
        </div>


    </div>
</div>