<input type="hidden" id="cafeteria" value="<?=isset($action[1])? $action[1] : '';?>">
<div class="modal fade" id="modal_bebidas" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form onsubmit="return false;" class="">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLabel">Tamaños genéricos de bebidas</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="container">
                        <p>Registra las medidas que manejas en tus bebidas.</p>
                        <div class="row">
                            <input type="hidden" id="id_producto">
                            <input type="hidden" id="campo_tabla">
                            <!-- Opción 4oz -->
                            <div class="col-md-6 d-flex align-items-center mb-3 opcion_vaso" id="tamano_1">
                                <div class="me-2">
                                    <img src="<?= $url ?>/views/assets/css/img/vasos/vaso-mediano.png" alt="Vaso 4oz" style="height: 30px; width: 28px;">
                                </div>
                                <input type="text" class="form-control me-2" style="width: 60px;" value="4 oz" readonly>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" class="form-control precio_vaso" placeholder="0.00">
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input switch_vasos" id_tamano="1" type="checkbox">
                                </div>
                            </div>
                            <!-- Opción 6oz -->
                            <div class="col-md-6 d-flex align-items-center mb-3 opcion_vaso"  id="tamano_2">
                                <div class="me-2">
                                    <img src="<?= $url ?>/views/assets/css/img/vasos/vaso-mediano.png" alt="Vaso 4oz" style="height: 40px; width: 35px;">
                                </div>
                                <input type="text" class="form-control me-2" style="width: 60px;" value="6 oz" readonly>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" class="form-control precio_vaso" placeholder="0.00">
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input switch_vasos" id_tamano="2" type="checkbox">
                                </div>
                            </div>
                            <!-- Opción 12oz -->
                            <div class="col-md-6 d-flex align-items-center mb-3 opcion_vaso" id="tamano_3">
                                <div class="me-2">
                                    <img src="<?= $url ?>/views/assets/css/img/vasos/vaso-mediano.png" alt="Vaso 12oz" style="height: 45px; width: 36px;">
                                </div>
                                <input type="text" class="form-control me-2" style="width: 60px;" value="12 oz" readonly>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" class="form-control precio_vaso" placeholder="0.00">
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input switch_vasos" id_tamano="3" type="checkbox">
                                </div>
                            </div>
                            <!-- Opción 16oz -->
                            <div class="col-md-6 d-flex align-items-center mb-3 opcion_vaso" id="tamano_4">
                                <div class="me-2">
                                    <img src="<?= $url ?>/views/assets/css/img/vasos/vaso-mediano.png" alt="Vaso 16oz" style="height: 55px; width: 40px;">
                                </div>
                                <input type="text" class="form-control me-2" style="width: 60px;" value="16 oz" readonly>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" class="form-control precio_vaso" placeholder="0.00">
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input switch_vasos" id_tamano="4" type="checkbox">
                                </div>
                            </div>
                            <!-- Opción 20oz -->
                            <div class="col-md-6 d-flex align-items-center mb-3 opcion_vaso" id="tamano_5">
                                <div class="me-2">
                                    <img src="<?= $url ?>/views/assets/css/img/vasos/vaso-mediano.png" alt="Vaso 20oz" style="height: 65px; width: 45px;">
                                </div>
                                <input type="text" class="form-control me-2" style="width: 60px;" value="20 oz" readonly>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" class="form-control precio_vaso" placeholder="0.00">
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input switch_vasos" id_tamano="5" type="checkbox">
                                </div>
                            </div>
                            <!-- Opción 24oz -->
                            <div class="col-md-6 d-flex align-items-center mb-3 opcion_vaso" id="tamano_6">
                                <div class="me-2">
                                    <img src="<?= $url ?>/views/assets/css/img/vasos/vaso-xgrande.png" alt="Vaso 24oz" style="height: 70px; width: 50px;">
                                </div>
                                <input type="text" class="form-control me-2" style="width: 60px;" value="24 oz" readonly>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" class="form-control precio_vaso" placeholder="0.00">
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input switch_vasos" id_tamano="6" type="checkbox">
                                </div>
                            </div>
                            <!-- Botón Continuar -->
                            <!-- <div class="d-grid">
                <button type="button" class="btn btn-secondary">CONTINUAR</button>
              </div> -->
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_ingredientes" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form onsubmit="return false;" class="">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLabel">Eligiendo ingredientes</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="container">
                        <p>Elige los ingredientes que quieres mostrar en este producto.</p>
                        <input type="hidden" id="id_producto_ingre">
                        <div class="row" id="cont_ingre">
                            
                            
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </form>
        </div>
    </div>
</div>