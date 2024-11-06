<!-- Menu de mapa -->
<div class="dropdown position-fixed top-0 end-0 mt-3 me-3 bd-mode-toggle">
    <button class="btn btn-icono btn-lista py-2 dropdown-toggle d-flex align-items-center"
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
    </ul>
    <button class="btn btn-icono btn-buscar py-2 mt-2 menu_offcanvas" id="btn_filtro_mapa"></button>
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

<div class="modal fade" id="modal_filtro_mapa" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <input type="hidden" id="id_cafeteria">
                <h5 class="modal-title" id="modalLabel"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body modal_mapa">
                <form onsubmit="return false;" id="aplicar_filtros_mapa">
                    <input type="hidden" id="ciudades_filtro">
                    <div class="row filtros_cafeterias" id="filtros_div">
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
                                            <select class="form-control select_ciudades_filtro" id_pais='<?= $_SESSION['ciudad'] ?>'>
                                                <option value="" selected disabled>Selecciona una ciudad</option>
                                                <option value="">Todas</option>
                                                <?php foreach (GeneralController::obtenerCiudadesController() as $ciudad) { 
                                                    
                                                    if ($_SESSION['ciudad']==$ciudad['id']) {
                                                        ?>
                                                            <option value="<?= $ciudad['id']; ?>" coordenadas='<?= $ciudad['coordenadas']; ?>' selected><?= $ciudad['nombre'] ?></option>
                                                        <?php
                                                    }else{?>
                                                        <option value="<?= $ciudad['id']; ?>" coordenadas='<?= $ciudad['coordenadas']; ?>'><?= $ciudad['nombre'] ?></option>
                                                <?php } } ?>

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
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

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
                            <?php foreach (GeneralController::obtenerCiudadesController() as $ciudad) { 
                                                    
                                if ($_SESSION['ciudad']==$ciudad['id']) {
                                    ?>
                                        <option value="<?= $ciudad['id']; ?>" coordenadas='<?= $ciudad['coordenadas']; ?>' selected><?= $ciudad['nombre'] ?></option>
                                    <?php
                                }else{?>
                                    <option value="<?= $ciudad['id']; ?>" coordenadas='<?= $ciudad['coordenadas']; ?>'><?= $ciudad['nombre'] ?></option>
                            <?php } } ?>

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
                        <span class="d-block small opacity-75">Busca por servicios que ofrecen</span>
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