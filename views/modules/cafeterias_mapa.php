
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
    <button class="btn btn-icono btn-buscar py-2 mt-2" id="btn_filtro_mapa"></button>
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
                    <div class="row" id="filtros_div">
                        <div class="col-md-5 cambiar-clase mb-2">
                            <div class="caja">
                                <div class="row filtros_cafeterias">
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
                                                    <input class="form-check-input" type="radio" name="horario_filtro" value="todos" checked>
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
                                            <input class="form-check-input" type="checkbox" value="" id="check_servicios">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Ciudad</label>
                                            <select class="form-control" id_pais='<?= $_SESSION['ciudad'] ?>' id="select_ciudades_filtro">
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
                        <div class="col-md-12" style="display: none;" id="div_servicios">
                            <div class="caja mb-2">
                                <div class="row" id="caja_servicios">

                                </div>
                            </div>
                        </div>
                        <div class="col-md d-flex justify-content-center align-items-center h-100 mt-2">
                            <div class="caja">
                                <button type="submit" class="btn btn-icono btn-buscar" id="" data-bs-dismiss="modal"></button>
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