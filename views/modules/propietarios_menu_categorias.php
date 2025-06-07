<div class="caja">
    <h2 class="mb-3"><b>Selecciona tus categorías</b></h2>
    <p>Selecciona o agrega las categorías con las que cuenta tu menú.</p>

    <div class="mb-3">
        <div class="row">
            <div class="col-6 mt-4">
                <h5><b>Categorías</b></h5>
            </div>
            <div class="col-6  d-flex justify-content-end">
                <button type="button" class="btn btn-icono btn-mas" id="btn_agregar_subcategoria_extra"></button>
            </div>
        </div>
    </div>
    <hr>
    <div class="row p-3" id="div_categorias">

    </div>
    <div class="row">
        <div class="col-md-12 text-center">
            <a href="<?= $url ?>propietarios_menu_productos" class="btn btn-outline-primary">Seleccionar productos > ></a>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_agregar_subcategorias_extra" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form onsubmit="return false;" class="form_agregar_subcategoria_extra">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLabel"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" class="input_subcategorias" id="id_subcategoria">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Nombre:</label>
                                <input type="text" class="form-control input_subcategorias" tabla='propietarios_menu_subcategorias_extra' columna='nombre' mensaje='Esta subcategoría ya se encuentra registrada' id="nombre_subcategoria">
                                <div class="invalid-feedback" style="display: none;"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Categoría:</label>
                                <select class="form-control input_subcategorias" id="select_categoria">
                                    <option value="" disabled>Selecciona una opción</option>
                                    <?php foreach (GeneralController::obtenerCategoriasController() as $item) { ?>
                                        <option value="<?= $item['id'] ?>"><?= $item['nombre'] ?></option>
                                    <?php } ?>
                                </select>
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