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
<div class="caja  filtro_busqueda" style="display: none;">
    <div class="row">
        <div class="col-md-3">
            <div class="form-group">
                <label>Entidad federativa:</label>
                <select class="form-control select2" id="entidad_filtro" required>
                    <option value="" selected>Todos</option>
                    <?php foreach (GeneralController::obtenerEstadosController() as $estado) { ?>
                        <option value="<?= $estado['id'] ?>"><?= $estado['nombre'] ?></option>
                    <?php }  ?>
                </select>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label>Ciudad:</label>
                <select class="form-control" id="ciudad_filtro" required>
                    <option value="" selected>Todos</option>
                    <?php foreach (GeneralController::obtenerCiudadesController() as $ciudad) { ?>
                        <option value="<?= $ciudad['id'] ?>"><?= $ciudad['nombre'] ?></option>
                    <?php }  ?>
                </select>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group text-center">
                <label>Estatus:</label>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="estatus" value="Todos">
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
        <div class="col-md-3 mt-2 text-center">
            <button type="button" class="btn btn-icono btn-buscar"></button>
        </div>
    </div>
</div>
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
        <table class="table w-100 dataTable">
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
                <?php
                $i = 0;
                foreach (CafeteriasController::obtenerCafeteriasController() as $cafeteria) {
                    $checked = ($cafeteria['estado'] == 0) ? "checked" : ""; ?>
                    <tr>
                        <td><?= ++$i; ?></td>
                        <td>
                            <a type="button" class="btn btn-icono btn-editar" href="<?= $url . 'cafeterias/' . $cafeteria['id'] . '/editar' ?>"></a>
                            <button class="btn btn-icono btn-eliminar eliminarRegistro" tabla="cafeterias" idRegistro="<?= $cafeteria['id']; ?>"></button>
                            <button class="btn btn-icono btn-servicios agregar_servicios" idRegistro="<?= $cafeteria['id']; ?>"></button>
                        </td>
                        <td>
                            <div class="form-check form-switch">
                                <input type="checkbox"
                                    class="form-check-input cambioEstado"
                                    id="switch<?= $cafeteria['id']; ?>"
                                    tabla="cafeterias"
                                    idRegistro="<?= $cafeteria['id']; ?>"
                                    <?= $checked; ?>>
                                <label class="custom-control-label" for="switch<?= $cafeteria['id']; ?>"></label>
                            </div>
                        </td>
                        <td><img width="60px;" src="<?= $cafeteria['imagen'] ?>" alt=""></td>
                        <td><?= $cafeteria['nombre'] ?></td>
                        <td><?= $cafeteria['horario_apertura'] . ' - ' . $cafeteria['horario_cierre'] ?></td>
                        <td><?= $cafeteria['direccion'] ?></td>
                        <td><?= $cafeteria['telefono'] ?></td>
                        <td><?= $cafeteria['correo_electronico'] ?></td>
                        <td><?= $cafeteria['ciudad'] ?></td>
                        <td><?= $cafeteria['entidad_federativa'] ?></td>
                        <td><?= $cafeteria['pais'] ?></td>
                        <td><?= $cafeteria['usuario_alta'] ?></td>
                        <td><?= $cafeteria['fecha_alta'] ?></td>
                    </tr>
                <?php } ?>
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