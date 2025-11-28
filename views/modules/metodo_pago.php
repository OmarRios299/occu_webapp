<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            
            <div class="card shadow-sm">
                <div class="card-header bg-warning text-white">
                    <h5 class="mb-0"><i class="fas fa-credit-card me-2"></i>Método de pago</h5>
                </div>
                <div class="card-body">
                    
                    <!-- Resumen del pedido -->
                    <div class="mb-4">
                        <h6 class="text-muted mb-3">Resumen de tu pedido</h6>
                        <div id="resumen_pedido" class="border rounded p-3 bg-light">
                            <div class="text-center py-3">
                                <div class="spinner-border spinner-border-sm text-warning" role="status">
                                    <span class="visually-hidden">Cargando...</span>
                                </div>
                                <p class="mb-0 mt-2 text-muted">Cargando resumen...</p>
                            </div>
                        </div>
                    </div>

                    <!-- Selección de método de pago -->
                    <h6 class="text-muted mb-3">Selecciona tu método de pago</h6>
                    
                    <div class="d-flex flex-column gap-3 mb-4">
                        <!-- Pago con tarjeta -->
                        <label class="metodo-pago-option border rounded p-3 cursor-pointer" for="pago_tarjeta">
                            <div class="form-check d-flex align-items-center gap-3 m-0">
                                <input class="form-check-input" type="radio" name="metodo_pago" id="pago_tarjeta" value="tarjeta">
                                <div class="d-flex align-items-center gap-3 flex-grow-1">
                                    <div class="icon-container bg-primary bg-opacity-10 rounded-circle p-3">
                                        <i class="fas fa-credit-card text-primary fa-lg"></i>
                                    </div>
                                    <div>
                                        <span class="fw-bold d-block">Pago con tarjeta</span>
                                        <small class="text-muted">Débito o crédito</small>
                                    </div>
                                </div>
                            </div>
                        </label>

                        <!-- Pago en efectivo -->
                        <label class="metodo-pago-option border rounded p-3 cursor-pointer" for="pago_efectivo">
                            <div class="form-check d-flex align-items-center gap-3 m-0">
                                <input class="form-check-input" type="radio" name="metodo_pago" id="pago_efectivo" value="efectivo">
                                <div class="d-flex align-items-center gap-3 flex-grow-1">
                                    <div class="icon-container bg-success bg-opacity-10 rounded-circle p-3">
                                        <i class="fas fa-money-bill-wave text-success fa-lg"></i>
                                    </div>
                                    <div>
                                        <span class="fw-bold d-block">Pago en efectivo</span>
                                        <small class="text-muted">Pagar en sucursal</small>
                                    </div>
                                </div>
                            </div>
                        </label>
                    </div>

                    <!-- Total a pagar -->
                    <div class="border-top pt-3 mb-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fs-5 fw-bold">Total a pagar:</span>
                            <span class="fs-4 fw-bold text-warning" id="total_pagar">$0.00</span>
                        </div>
                    </div>

                    <!-- Botones de acción -->
                    <div class="d-flex gap-2">
                        <a href="<?= $url ?>cafeterias_lista" class="btn btn-outline-secondary flex-grow-1">
                            <i class="fas fa-arrow-left me-2"></i>Cancelar
                        </a>
                        <button type="button" class="btn btn-warning text-white flex-grow-1" id="btn_confirmar_pago" disabled>
                            <i class="fas fa-check me-2"></i>Aceptar
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<style>
.metodo-pago-option {
    cursor: pointer;
    transition: all 0.2s ease;
}
.metodo-pago-option:hover {
    border-color: #ffc107 !important;
    background-color: #fffbf0;
}
.metodo-pago-option:has(input:checked) {
    border-color: #ffc107 !important;
    background-color: #fff8e1;
    box-shadow: 0 0 0 2px rgba(255, 193, 7, 0.25);
}
.icon-container {
    width: 50px;
    height: 50px;
    display: flex;
    align-items: center;
    justify-content: center;
}
.cursor-pointer {
    cursor: pointer;
}
</style>

