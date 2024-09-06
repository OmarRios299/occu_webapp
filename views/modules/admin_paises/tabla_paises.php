<div class="titulo-boton">
    <h1 class="titulo-modulo">Países</h1>
    <button class="btn btn-agregar con-icono" id="agregar_pais">Agregar país</button>
</div>
<h6 class="subtitulo">Tabla de países</h6>
<div class="caja">
    <div class="table-responsive">
        <table class="table w-100 dataTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nombre</th>
                    <th>Botones</th>
                    <th>Estado</th>
                    <th>Usuario de alta</th>
                    <th>fecha de alta</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                    $i=0;
                    foreach (AdminPaisesController::obtenerPaiseController() as $pais){ 
                        $checked = ($pais['estado'] == 0) ? "checked" : ""; ?>
                    <tr>
                        <td><?=++$i?></td>
                        <td><?=$pais['nombre']?></td>
                        <td>
                            <a type="button" class="btn btn-icono btn-ver" href="<?=$url.'admin_paises/'.$pais['id']?>"></a>
                            <button type="button" class="btn btn-icono btn-editar editar_pais" idRegistro='<?=$pais['id']?>' nombre='<?=$pais['nombre']?>'></button>
                            <button type="button" class="btn btn-icono btn-eliminar eliminarRegistro" tabla='paises' idRegistro='<?=$pais['id']?>'></button>
                        </td>
                        <td>
                            <div class="form-check form-switch">
                                <input type="checkbox"
                                    class="form-check-input cambioEstado"
                                    id="switch<?= $pais['id']; ?>"
                                    tabla="paises"
                                    idRegistro="<?= $pais['id']; ?>"
                                    <?= $checked; ?>>
                                <label class="form-check-label" for="switch<?= $pais['id']; ?>"></label>
                            </div>
                        </td>
                        <td><?=$pais['usuario_alta']?></td>
                        <td><?=$pais['fecha_alta']?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="modal_agregar_pais" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalLabel">Agregando país</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form onsubmit="return false;" id="form_agregar_pais">
                <div class="modal-body">
                    
                        <input type="hidden" class="input_pais" id="id_pais">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Nombre:</label>
                                    <input type="text" class="form-control validarCampo input_pais" id="nombre_pais" columna='nombre' tabla='paises' mensaje='Este país ya esta registrado'>
                                    <div class="invalid-feedback" style="display: none;"></div>
                                </div>
                            </div>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary" data-bs-dismiss="modal">Aceptar</button>
                </div>
            </form>
        </div>
    </div>
</div>
