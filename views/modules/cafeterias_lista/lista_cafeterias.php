<style>
    .btn-custom {
        background-color: #FF6B6B;
        /* Color personalizado de los botones */
        color: white;
    }

    .btn-custom:hover {
        background-color: #FF3B3B;
        /* Color hover */
    }

    .card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .card-img-top {
        height: 200px;
        /* Ajusta el tamaño de las imágenes */
        object-fit: cover;
    }

    .status-open {
        color: green;
    }

    .favorite-icon {
        color: red;
        font-size: 1.2rem;
    }

    /* estilos para el filtro y estrallas */

    .star-rating {
        direction: rtl;
        /* De derecha a izquierda para mejor UX */
        font-size: 1.5rem;
        unicode-bidi: bidi-override;
        /* Reversión de texto */
        display: flex;
    }

    .star-rating input {
        display: none;
        /* Ocultar los inputs */
    }

    .star-rating label {
        cursor: pointer;
        color: #ccc;
        /* Color de las estrellas no seleccionadas */
        font-size: inherit;
        /* Hereda el tamaño del contenedor */

    }

    .star-rating input:checked~label {
        color: #ffc107;
        /* Color de las estrellas seleccionadas */
    }

    .star-rating label:hover,
    .star-rating label:hover~label {
        color: #ffc107;
        /* Cambiar el color al pasar el mouse sobre una estrella */
    }
</style>
<div class="container mt-4">
    <!-- Encabezado -->
    <div class="text-center mb-4">
        <h2>¡Bienvenido!</h2>
        <p>Comienza a explorar y encuentra cafés cerca de ti.</p>
    </div>

    <!-- Barra de botones -->
    <div class="d-flex justify-content-center mb-4">
        <button class="btn btn-custom me-2">
            <i class="bi bi-search"></i> buscar
        </button>
        <button class="btn btn-custom me-2" id="abrir_filtros">
            <i class="bi bi-funnel"></i> filtros
        </button>
        <a class="btn btn-custom" href="<?= $url ?>cafeterias_mapa">
            <i class="bi bi-map"></i> mapa
        </a>
    </div>


    <div class="row" id="filtros_div" style="display: none;">
        <div class="col-md-5">
            <div class="caja">
                <div class="row ">
                    <div class="col-md-5 d-flex justify-content-center align-items-center">
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
                    <div class="col-md-7 d-flex justify-content-center align-items-center">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="flexRadioDefault" id="flexRadioDefault1">
                                    <label class="form-check-label" for="flexRadioDefault1">
                                        Todos los horarios
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="flexRadioDefault" id="flexRadioDefault2" checked>
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
        <div class="col-md-6">
            <div class="caja">
                <div class="row">
                    <div class="col-md-6 mt-0 d-flex justify-content-center align-items-center">
                        <div class="form-check">
                            <label class="form-check-label" for="flexCheckDefault">
                                Drive Thru
                            </label>
                            <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault">

                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Ciudad</label>
                            <select class="form-control select_ciudad" id_pais=<?=$_SESSION['ciudad']?> id="select_ciudades_filtro">
                                <option value="" disabled>Selecciona una opción</option>

                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-1 d-flex justify-content-center align-items-center h-100 mt-1">
        <div class="caja">
        <button type="button" class="btn btn-icono btn-buscar"></button>
        </div>
        </div>
    </div>

    <!-- Listado de cafeterías -->
    <div class="row mt-3" id="div_lista_cafeterias">
        <!-- Tarjeta de ejemplo -->
        <div class="col-12 col-md-6 col-lg-4 mb-4">
            <div class="card">
                <img src="cafe1.jpg" class="card-img-top" alt="Cafetería 1">
                <div class="card-body">
                    <div class="card-header">
                        <h5 class="card-title">Fraternos Coffee Bar</h5>
                        <i class="bi bi-heart favorite-icon"></i>
                    </div>
                    <p class="card-text">Dirección</p>
                    <div class="d-flex justify-content-between">
                        <span class="status-open">abierto</span>
                        <span>1.2km</span>
                    </div>
                </div>
            </div>
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