<div class="titulo-boton">
    <h1 class="titulo-modulo">Cafeterías</h1>
    <div class="row tetxt-end">
        <div class="col-md">
            <button type="button" class="btn con-icono btn-buscar" id="filtro_busqueda">Filtrar</button>
        </div>
        <div class="col-md">
            <a class="btn btn-agregar con-icono" href="<?= $url . 'cafeterias/agregar' ?>">Agregar</a>
        </div>
    </div>
</div>
<h6 class="subtitulo filtro_busqueda" style="display: none;">Filtro de búsqueda</h6>
<form onsubmit="return false;" id="form_filtro_cafeterias">
    <div class="caja filtro_busqueda" style="display: none;">
        <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    <label>Entidad federativa:</label>
                    <select class="form-control select2 select_estado filtro" opcion_todos='SI' id="entidad_filtro">
                        <option value="" selected>Todos los estados</option>
                        <?php foreach (GeneralController::obtenerEstadosController() as $estado) { ?>
                            <option value="<?= $estado['id'] ?>"><?= $estado['nombre'] ?></option>
                        <?php }  ?>
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>Ciudad:</label>
                    <select class="form-control select_ciudad select2 filtro" id="ciudad_filtro">
                        <option value="" selected>Todas las ciudades</option>
                        <?php foreach (GeneralController::obtenerCiudadesController() as $ciudad) { ?>
                            <option value="<?= $ciudad['id'] ?>"><?= $ciudad['nombre'] ?></option>
                        <?php }  ?>
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group text-center">
                    <label>Estatus:</label>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-check">
                                <input class="form-check-input" checked type="radio" name="estatus" value="Todos">
                                <label class="form-check-label">Todos</label>
                            </div>

                        </div>
                        <div class="col-md-4">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="estatus" value="Activas">
                                <label class="form-check-label">Activos</label>
                            </div>

                        </div>
                        <div class="col-md-4">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="estatus" value="Inactivas">
                                <label class="form-check-label">Inactivos</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-1 mt-2 text-center">
                <button type="submit" class="btn btn-icono btn-buscar"></button>
            </div>
            <div class="col-md-1 mt-2 text-center">
                <button type="button" class="btn btn-icono btn-basura" id="limpiar_filtros"></button>
            </div>
        </div>
    </div>
</form>
<style>
    /* Ocultar el checkbox estándar */
    .image-checkbox input[type="checkbox"] {
        display: none;
    }

    /* Estilo de la tarjeta */
    .image-checkbox .custom-carousel-item {
        border: 2px solid transparent;
        cursor: pointer;
        transition: 0.3s;
    }

    /* Efecto cuando el checkbox está seleccionado */
    .image-checkbox input[type="checkbox"]:checked+.custom-carousel-item {
        border: 2px solid #007bff;
        /* Cambia el borde para indicar que está seleccionado */
        opacity: 0.8;
        /* Añade un efecto de opacidad */
    }

    /* Efecto hover */
    .image-checkbox .custom-carousel-item:hover {
        border: 2px solid #007bff;
        /* Cambia el borde cuando se pasa el cursor */
    }

    .tanamo {
        width: 150px !important;
        height: 150px !important;
    }
</style>
<h6 class="subtitulo mt-3">Tabla de cafeterías</h6>
<div class="caja">
    <div class="table-responsive">
        <table class="table w-100" id="tabla_cafeterias">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Botones</th>
                    <th>Estado</th>
                    <th>Imagen</th>
                    <th>Nombre</th>
                    <th>Horario de atención</th>
                    <th>Dirección</th>
                    <th>Teléfono</th>
                    <th>Correo electrónico</th>
                    <th>Ciudad</th>
                    <th>Entidad federativa</th>
                    <th>País</th>
                    <th>Usuario de alta</th>
                    <th>fecha de alta</th>
                </tr>
            </thead>
            <tbody>

            </tbody>
        </table>
    </div>
</div>
<br>

<div class="modal fade" id="modal_agregar_servicios" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalLabel">Seleccionando servicios</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form onsubmit="return false;" id="form_servicios">
                    <input type="hidden" id="id_cafeteria">
                    <div class="caja">
                        <div class="row">
                            <div class="col-md" id="servicios">
                                <label class="image-checkbox">
                                    <input type="checkbox" class="chbx_servicios" />
                                    <div class="custom-carousel-item">
                                        <div class="card card-cover overflow-hidden text-bg-dark rounded-4 shadow-lg" style="background-image: url('<?= $url ?>views/assets/img/servicios/123_imagen_servicio_4.webp');">
                                            <div class="d-flex flex-column p-3 pb-1 text-white titulo-oscuro text-center tanamo">
                                                <h5 class="mt-4 display-8 lh-1 fw-bold">Comida rápida</h5>
                                            </div>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-primary">Aceptar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>