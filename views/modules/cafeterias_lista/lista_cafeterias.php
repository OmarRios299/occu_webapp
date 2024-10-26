
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
        <button class="btn btn-custom me-2" id="abrir_filtros">
            <i class="bi bi-funnel"></i> filtros
        </button>
        <a class="btn btn-custom" href="<?= $url ?>cafeterias_mapa">
            <i class="bi bi-map"></i> mapa
        </a>
    </div>

    <form onsubmit="return false;" id="aplicar_filtros">
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
                                        <input class="form-check-input" type="radio" name="horario_filtro" value="todos" checked>
                                        <label class="form-check-label" for="flexRadioDefault1">
                                            Todos los horarios
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="horario_filtro" value="abierto" >
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
                                <select class="form-control select2" id_pais='<?= $_SESSION['ciudad'] ?>' id="select_ciudades_filtro">
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
            <div class="col-md-12" style="display: none;" id="div_servicios">
                <div class="caja mb-2">
                    <div class="row" id="caja_servicios">

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



    <!-- Contenedor de búsqueda y paginación -->
    <div class="d-flex justify-content-between">
        <!-- Input de Filtrado -->
        <div class="col-md-6">
            <input type="text" id="filtro-input" class="form-control w-80" placeholder="Buscar cafeterías..." style="display: none;">
        </div>
        <!-- Botones de Paginación -->
        <div class="col-md-3 text-end">
            <button id="boton-anterior" class="btn btn-primary" data-pagina="1">
                << </button>
                    <button id="boton-siguiente" class="btn btn-primary" data-pagina="1">>></button>
        </div>
    </div>

    <!-- Contenedor para Listado de Cafeterías -->
    <div class="row mt-3" id="div_lista_cafeterias">
        <!-- Aquí se cargarán las cafeterías dinámicamente -->
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