<div class="titulo-boton">
    <h1 class="titulo-modulo">Categoría de ingredientes</h1>
    <button type="button" class="btn con-icono btn-agregar" id="btn_agregar_ing_categoria">Agregar categoría</button>
</div>

<h6 class="subtitulo mt-3">Tabla de categorías</h6>
<div class="caja">
    <div class="table-responsive">
        <table class="table w-100" id="tabla_ing_categorias">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Botones</th>
                    <th>Estado</th>
                    <th>Nombre</th>
                </tr>
            </thead>
            <tbody>

            </tbody>
        </table>
    </div>
</div>
<div class="modal fade" id="modal_ing_categorias" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form onsubmit="return false;" class="form_add_ing_categoria">
                <div class="modal-header">
                    <input type="hidden" id="id_categoria">
                    <h5 class="modal-title" id="modalLabel"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" class="input_categorias" id="id_categoria">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Nombre:</label>
                                <input type="text" class="form-control input_categorias validarCampo" tabla='menu_ingredientes_categorias' columna='nombre' mensaje='Esta categoría ya se encuentra registrada' id="nombre_categoria">
                                <div class="invalid-feedback" style="display: none;"></div>
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