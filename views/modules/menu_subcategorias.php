<div class="titulo-boton">
    <h1 class="titulo-modulo">Subcategorías</h1>
    <button type="button" class="btn con-icono btn-agregar" id="btn_agregar_subcategoria">Agregar categoría</button>
</div>

<h6 class="subtitulo mt-3">Tabla de categorías</h6>
<div class="caja">
    <div class="table-responsive">
        <table class="table w-100" id="tabla_subcategorias">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Botones</th>
                    <th>Estado</th>
                    <th>Nombre</th>
                    <th>Categoría</th>
                    <th>Productos</th>
                </tr>
            </thead>
            <tbody>

            </tbody>
        </table>
    </div>
</div>
<div class="modal fade" id="modal_editar_subcategorias" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form onsubmit="return false;" class="form_agregar_subcategoria">
            <div class="modal-header">
                <input type="hidden" id="id_subcategoria">
                <h5 class="modal-title" id="modalLabel"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                    <input type="hidden" class="input_subcategorias" id="id_subcategoria">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Nombre:</label>
                                <input type="text" class="form-control input_subcategorias validarCampo" tabla='menu_subcategorias' columna='nombre' mensaje='Esta subcategoría ya se encuentra registrada' id="nombre_subcategoria">
                                <div class="invalid-feedback" style="display: none;"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Subcategoría:</label>
                                <select class="form-control input_subcategorias" id="select_categoria">
                                    <option value="" disabled>Selecciona una opción</option>
                                    <?php foreach(GeneralController::obtenerCategoriasController() as $item){ ?>
                                        <option value="<?=$item['id']?>"><?= $item['nombre'] ?></option>
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