<div class="titulo-boton">
    <h1 class="titulo-modulo">Productos</h1>
    <div class="row tetxt-end">
        <div class="col-md">
            <button type="button" class="btn con-icono btn-buscar" id="filtro_busqueda">Filtrar</button>
        </div>
        <div class="col-md">
            <button type="button" class="btn con-icono btn-agregar" id="btn_agregar_producto">Agregar</button>
        </div>
    </div>
</div>
<h6 class="subtitulo filtro_busqueda" style="display: none;">Filtro de búsqueda</h6>
<form onsubmit="return false;" id="form_filtro_productos">
    <div class="caja  filtro_busqueda" style="display: none;">
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label>Categoría:</label>
                    <select class="form-control select2 filtro select_categoria" id="categoria_filtro">
                        <option value="" selected>Todas</option>
                        <?php foreach (MenuProductosController::obtenercategoriasController() as $categoria) { ?>
                            <option value="<?= $categoria['id'] ?>"><?= $categoria['nombre'] ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Subategoría:</label>
                    <select class="form-control select2 filtro select_subcategoria" id="subcategoria_filtro">
                        <option value="" selected>Todas</option>
                        <?php foreach (MenuProductosController::obtenerSubcategoriasController() as $subcategoria) { ?>
                            <option value="<?= $subcategoria['id'] ?>"><?= $subcategoria['nombre'] ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <label>Estatus:</label>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-check">
                                <input class="form-check-input" checked type="radio" name="estatus" value="Todos">
                                <label class="form-check-label">Todos</label>
                            </div>

                        </div>
                        <div class="col-md-12">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="estatus" value="Activas">
                                <label class="form-check-label">Activos</label>
                            </div>

                        </div>
                        <div class="col-md-12">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="estatus" value="Inactivas">
                                <label class="form-check-label">Inactivos</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-1 mt-2">
                <button type="submit" class="btn btn-icono btn-buscar"></button>
            </div>
            <div class="col-md-1 mt-2">
                <button type="button" class="btn btn-icono btn-basura" id="limpiar_filtros"></button>
            </div>
        </div>
    </div>
</form>
<h6 class="subtitulo mt-3">Tabla de productos</h6>
<div class="caja">
    <div class="table-responsive">
        <table class="table w-100" id="tabla_productos">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Botones</th>
                    <th>Estado</th>
                    <th>Imagen</th>
                    <th>Nombre</th>
                    <th>Subcategoría</th>
                    <th>Categoría</th>
                    <th>Usuario de alta</th>
                    <th>Fecha de alta</th>
                </tr>
            </thead>
            <tbody>

            </tbody>
        </table>
    </div>
</div>
<div class="modal fade" id="modal_editar_productos" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form onsubmit="return false;" class="form_agregar_producto">
                <div class="modal-header">
                    <input type="hidden" id="id_productos">
                    <h5 class="modal-title" id="modalLabel"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" class="input_productos" id="id_producto">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Nombre:</label>
                                <input type="text" class="form-control input_productos validarCampo" tabla='menu_productos' columna='nombre' mensaje='Este producto ya se encuentra registrada' id="nombre_producto">
                                <div class="invalid-feedback" style="display: none;"></div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Categoría:</label>
                                <select class="form-control input_productos" id="select_subcategoria">
                                    <option value="" disabled selected>Selecciona una opción</option>
                                    <?php foreach (MenuProductosController::obtenerSubcategoriasController() as $subcategoria) { ?>
                                        <option value="<?= $subcategoria['id'] ?>"><?= $subcategoria['categoria'] .' - ' .$subcategoria['nombre'] ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Agrega una imagen:</label>
                            </div>
                            <div class="input-group">
                                <input type="file" class="form-control input_productos imagenPrevisualizar validarImagen" id="imagen_producto" lang="esp">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <img src="<?= $url ?>/views/assets/img/cafeteria_default.png" class="imagen_editar" style="width:110px;" id="imagen_previsualizar">
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

<!-- Modal para seleccionar categorías de ingredientes base -->
<div class="modal fade" id="modal_bases_producto" tabindex="-1" aria-labelledby="modalBasesLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <input type="hidden" id="id_producto_bases">
                <h5 class="modal-title" id="modalBasesLabel">Categorías de Ingredientes Base</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="mb-3"><strong id="nombre_producto_bases"></strong></p>
                <p class="text-muted mb-4">Selecciona las categorías de ingredientes base que aplican para este producto:</p>
                <div class="row" id="contenedor_categorias_bases">
                    <!-- Las categorías se cargarán aquí dinámicamente -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="btn_guardar_bases">Guardar</button>
            </div>
        </div>
    </div>
</div>