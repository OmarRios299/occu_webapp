<div class="titulo-boton">
    <h1 class="titulo-modulo">Categorías</h1>
    <button type="button" class="btn con-icono btn-agregar" id="btn_agregar_categoria">Agregar categoría</button>
</div>

<h6 class="subtitulo mt-3">Tabla de categorías</h6>
<div class="caja">
    <div class="table-responsive">
        <table class="table w-100" id="tabla_categorias">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Botones</th>
                    <th>Estado</th>
                    <th>Imagen</th>
                    <th>Nombre</th>
                    <th>Productos</th>
                    <th>Usuario de alta</th>
                    <th>Fecha de alta</th>
                </tr>
            </thead>
            <tbody>

            </tbody>
        </table>
    </div>
</div>
<div class="modal fade" id="modal_editar_categorias" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form onsubmit="return false;" class="form_agregar_categoria">
            <div class="modal-header">
                <input type="hidden" id="id_categorias">
                <h5 class="modal-title" id="modalLabel"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                
                    <input type="hidden" class="input_categorias" id="id_categoria">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Nombre:</label>
                                <input type="text" class="form-control input_categorias validarCampo" tabla='cafeterias_menu_categorias' columna='nombre' mensaje='Esta categoría ya se encuentra registrada' id="nombre_categoria">
                                <div class="invalid-feedback" style="display: none;"></div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Agrega una imagen:</label>
                            </div>
                            <div class="input-group">
                                <input type="file" class="form-control input_categorias imagenPrevisualizar validarImagen" id="imagen_categoria" lang="esp">
                            </div>
                        </div>
                        <div class="col-md-4">
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