<div class="titulo-boton">
    <h1 class="titulo-modulo">Editar página inicial</h1>
    <button type="button" class="btn con-icono btn-agregar" id="agregar_imagen_inicio">Agregar</button>
</div>

<h6 class="subtitulo">Imagenes de carousel</h6>
<div class="caja">
    <div class="table-responsive">
        <table class="table w-100">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Botones</th>
                    <th>Estado</th>
                    <th>Imagen</th>
                    <th>Titulo</th>
                    <th>Descripción</th>
                    <th>Dirección</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $i = 0;
                foreach (AdminPaginaInicialController::obtenerCarouseController() as $item) {
                    $checked = ($item['estado'] == 0) ? "checked" : "";
                ?>
                    <tr>
                        <td><?= ++$i; ?></td>
                        <td>
                            <button class="btn btn-icono btn-eliminar eliminarRegistro" tabla="pagina_inicial" idRegistro="<?= $item['id'] ?>"></button>
                            <button type="button" class="btn btn-icono btn-editar editar_imagen" registro='<?= json_encode($item, JSON_HEX_APOS) ?>'></button>
                        </td>
                        <td>
                            <div class="form-check form-switch">
                                <input type="checkbox"
                                    class="form-check-input cambioEstado"
                                    id="switch<?= $item['id']; ?>"
                                    tabla="pagina_inicial"
                                    idRegistro="<?= $item['id']; ?>"
                                    <?= $checked; ?>>
                                <label class="form-check-label" for="switch<?= $item['id']; ?>"></label>
                            </div>
                        </td>
                        <td><img src="<?= $item['imagen'] ?>" style="width:220px;"></td>
                        <td><?= $item['titulo'] ?></td>
                        <td><?= $item['descripcion'] ?></td>
                        <td><?= $item['enlace'] ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<h6 class="subtitulo mt-3">Imagenes de cards</h6>
<div class="caja">
    <div class="table-responsive">
        <table class="table w-100">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Botones</th>
                    <th>Estado</th>
                    <th>Imagen</th>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Dirección</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $i = 0;
                foreach (AdminPaginaInicialController::obtenerCardsController() as $item) {
                    $checked = ($item['estado'] == 0) ? "checked" : "";
                ?>
                    <tr>
                        <td><?= ++$i; ?></td>
                        <td>
                            <button class="btn btn-icono btn-eliminar eliminarRegistro" tabla="pagina_inicial" idRegistro="<?= $item['id'] ?>"></button>
                            <button type="button" class="btn btn-icono btn-editar editar_imagen" registro='<?= json_encode($item, JSON_HEX_APOS) ?>'></button>
                        </td>
                        <td>
                            <div class="form-check form-switch">
                                <input type="checkbox"
                                    class="form-check-input cambioEstado"
                                    id="switch<?= $item['id']; ?>"
                                    tabla="pagina_inicial"
                                    idRegistro="<?= $item['id']; ?>"
                                    <?= $checked; ?>>
                                <label class="form-check-label" for="switch<?= $item['id']; ?>"></label>
                            </div>
                        </td>
                        <td><img src="<?= $item['imagen'] ?>" style="width:220px;"></td>
                        <td><?= $item['titulo'] ?></td>
                        <td><?= $item['descripcion'] ?></td>
                        <td><?= $item['enlace'] ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="modal_agregar_imagen" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form onsubmit="return false;" class="form_agregar_imagen">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLabel"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" class="input_imagen" id="id_imagen">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Titulo:</label>
                                        <input type="text" class="form-control input_imagen" id="titulo_imagen">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Agrega una imagen:</label>
                                    </div>
                                    <div class="input-group">
                                        <input type="file" class="form-control input_imagen imagenPrevisualizar validarImagen" id="imagen_inicio" lang="esp">
                                    </div>
                                </div>
                                <div class="col-md-12 mt-3">
                                    <div class="form-group">
                                        <label>Tipo:</label>
                                        <select class="form-control input_imagen" id="select_tipo_imagen">
                                            <option value="" disabled selected>Selecciona una opción</option>
                                            <option value="carousel">Carousel</option>
                                            <option value="card">Card</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4 text-center">
                            <div class="form-group">
                                <img src="<?= $url ?>/views/assets/img/cafeteria_default.png" style="width:200px;" id="imagen_previsualizar">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Descripción:</label>
                                <textarea class="form-control input_imagen" cols="30" rows="5" id="descripcion_imagen"></textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Dirección:</label>
                                        <input type="text" class="form-control input_imagen" id="enlace_imagen">
                                    </div>
                                </div>
                                <div class="col-md-12 mt-1">
                                    <div class="form-group">
                                        <label>Nombre de botón:</label>
                                        <input type="text" class="form-control input_imagen" id="nombre_boton">
                                    </div>
                                </div>
                            </div>
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