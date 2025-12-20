<input type="hidden" id="cafeteria" value="<?= isset($action[1]) ? $action[1] : ''; ?>">
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
                            <div class="col-md-6 d-flex align-items-center mb-3 opcion_vaso" id="tamano_2">
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
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Aceptar</button>

                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_ingredientes" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <form onsubmit="return false;" class="">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLabel">Eligiendo ingredientes</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="container">
                        <div class="row">
                            <div class="col-9">
                                <p>Elige los ingredientes que quieres mostrar en este producto.</p>
                            </div>
                            <div class="col-2 text-end">
                                <button type="button" class="btn btn-icono btn-mas ms-5" data-bs-toggle="modal" data-bs-target="#addIngredienteExtraModal"></button>
                            </div>
                        </div>

                        <input type="hidden" id="id_producto_ingre">
                        <div class="row" id="cont_ingre">


                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <div class="d-grid">
                        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Aceptar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="addIngredienteExtraModal" tabindex="-1" aria-labelledby="addIngredienteExtraModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addIngredienteExtraModalLabel">Agregando ingrediente</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <form onsubmit="return false;" class="form_add_ing_extra">
        <div class="modal-body">
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label>Nombre:</label>
                <input type="text" class="form-control input_ingre" tabla='menu_ingredientes' columna='nombre' mensaje='Este ingrediente ya se encuentra registrado' id="input_nombre_ingre">
                <div class="invalid-feedback" style="display: none;"></div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>Categoría:</label>
                <select class="form-control input_ingre" id="select_ing_categoria">
                  <option value="" disabled selected>Selecciona una opción</option>
                  <?php foreach (PropietariosMenuController::obtenerCategoriasingredientesController() as $categoria) { ?>
                    <option value="<?= $categoria['id'] ?>"><?= $categoria['nombre'] ?></option>
                  <?php } ?>
                </select>
              </div>
            </div>
          </div>

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="btnCerrarModalIngExtra">Cerrar</button>
          <button type="submit" class="btn btn-primary" id="btnAceptarModalIngExtra">Aceptar</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal para establecer precio de alimentos -->
<div class="modal fade" id="modal_precio_alimento" tabindex="-1" aria-labelledby="modalPrecioAlimentoLabel" aria-hidden="true">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalPrecioAlimentoLabel">Establecer Precio</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <form onsubmit="return false;" class="form_precio_alimento">
        <div class="modal-body">
          <input type="hidden" id="id_producto_precio">
          <div class="row">
            <div class="col-md-12 mb-3">
              <p><strong id="nombre_producto_precio"></strong></p>
            </div>
            <div class="col-md-12">
              <div class="form-group">
                <label>Precio:</label>
                <div class="input-group">
                  <span class="input-group-text">$</span>
                  <input type="number" class="form-control" id="precio_alimento" placeholder="0.00" step="0.01" min="0">
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
          <button type="submit" class="btn btn-primary">Guardar</button>
        </div>
      </form>
    </div>
  </div>
</div>