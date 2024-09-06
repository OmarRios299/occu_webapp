<div class="titulo-boton">
    <h1 class="titulo-modulo">Países</h1>
    <button class="btn btn-agregar con-icono" id="agregar_pais">Agregar país</button>
</div>
<h6 class="subtitulo">Tabla de países</h6>
<div class="caja">
    <div class="table-responsive">
        <table class="table w-100">
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
                        <td><button type="button" class="btn btn-icono btn-ubicacion" id="btn_delimitar_ciudad"></button>
                            <button type="button" class="btn btn-icono btn-ver"></button>
                            <button type="button" class="btn btn-icono btn-editar"></button>
                            <button type="button" class="btn btn-icono btn-eliminar"></button>
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
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalLabel">Selecciona un ubicación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
            
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="submit" class="btn btn-primary" id="btn_guardar_ubicacion" data-bs-dismiss="modal">Aceptar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_delimitar_ciudad" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalLabel">Selecciona un ubicación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
            <div id="map_delimitar_pais" style="height: 500px; width: 100%;"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="submit" class="btn btn-primary" id="btn_guardar_ubicacion" data-bs-dismiss="modal">Aceptar</button>
            </div>
        </div>
    </div>
</div>