<?php

$cafeteria = GeneralController::verificarCafeteriaContoller($action[1], $_SESSION['id']);
if ($cafeteria) {
    $id_cafeteria = $action[1];
?>

<input type="hidden" id="id_cafeteria_pedidos" value="<?= $id_cafeteria ?>">

<div class="container-fluid py-3">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1"><i class="fas fa-clipboard-list me-2"></i>Pedidos</h4>
            <p class="text-muted mb-0"><?= $cafeteria['nombre'] ?></p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-secondary" id="btn_actualizar_pedidos">
                <i class="fas fa-sync-alt me-1"></i> Actualizar
            </button>
            <div class="form-check form-switch d-flex align-items-center ms-3">
                <input class="form-check-input" type="checkbox" id="auto_refresh" checked>
                <label class="form-check-label ms-2" for="auto_refresh">Auto-actualizar</label>
            </div>
        </div>
    </div>

    <!-- Contador de pedidos por estado -->
    <!-- <div class="row mb-4">
        <div class="col-6 col-md-3 mb-2">
            <div class="card bg-primary bg-opacity-10">
                <div class="card-body py-2 text-center">
                    <h3 class="mb-0 text-white" id="count_pendientes">0</h3>
                    <small class="text-muted">Pendientes</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3 mb-2">
            <div class="card bg-warning bg-opacity-10">
                <div class="card-body py-2 text-center">
                    <h3 class="mb-0 text-white" id="count_preparando">0</h3>
                    <small class="text-muted">En preparación</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3 mb-2">
            <div class="card bg-info bg-opacity-10">
                <div class="card-body py-2 text-center">
                    <h3 class="mb-0 text-white" id="count_entregados">0</h3>
                    <small class="text-muted">Entregados hoy</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3 mb-2">
            <div class="card bg-danger bg-opacity-10">
                <div class="card-body py-2 text-center">
                    <h3 class="mb-0 text-white" id="count_rechazados">0</h3>
                    <small class="text-muted">Rechazados hoy</small>
                </div>
            </div>
        </div>
    </div> -->

    <!-- Tabs para filtrar por estado -->
    <ul class="nav nav-tabs mb-3" id="pedidosTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="tab-pendientes" data-bs-toggle="tab" data-bs-target="#pendientes" type="button" role="tab">
                <i class="fas fa-clock me-1 text-warning"></i> Pendientes
                <span class="badge bg-warning text-dark ms-1" id="badge_pendientes">0</span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="tab-preparando" data-bs-toggle="tab" data-bs-target="#preparando" type="button" role="tab">
                <i class="fas fa-fire me-1 text-primary"></i> En preparación
                <span class="badge bg-primary ms-1" id="badge_preparando">0</span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="tab-entregados" data-bs-toggle="tab" data-bs-target="#entregados" type="button" role="tab">
                <i class="fas fa-check-circle me-1 text-success"></i> Entregados
                <span class="badge bg-success ms-1" id="count_entregados">0</span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="tab-rechazados" data-bs-toggle="tab" data-bs-target="#rechazados" type="button" role="tab">
                <i class="fas fa-times-circle me-1 text-danger"></i> Rechazados
                <span class="badge bg-danger ms-1" id="count_rechazados">0</span>
            </button>
        </li>
    </ul>

    <!-- Contenido de los tabs -->
    <div class="tab-content" id="pedidosTabsContent">
        <!-- Pendientes -->
        <div class="tab-pane fade show active" id="pendientes" role="tabpanel">
            <div class="row" id="lista_pendientes">
                <div class="col-12 text-center py-5">
                    <div class="spinner-border text-warning" role="status"></div>
                    <p class="mt-2 text-muted">Cargando pedidos...</p>
                </div>
            </div>
        </div>

        <!-- En preparación -->
        <div class="tab-pane fade" id="preparando" role="tabpanel">
            <div class="row" id="lista_preparando">
                <div class="col-12 text-center py-5">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="mt-2 text-muted">Cargando pedidos...</p>
                </div>
            </div>
        </div>

        <!-- Entregados -->
        <div class="tab-pane fade" id="entregados" role="tabpanel">
            <div class="row" id="lista_entregados">
                <div class="col-12 text-center py-5">
                    <div class="spinner-border text-success" role="status"></div>
                    <p class="mt-2 text-muted">Cargando pedidos...</p>
                </div>
            </div>
        </div>

        <!-- Rechazados -->
        <div class="tab-pane fade" id="rechazados" role="tabpanel">
            <div class="row" id="lista_rechazados">
                <div class="col-12 text-center py-5">
                    <div class="spinner-border text-danger" role="status"></div>
                    <p class="mt-2 text-muted">Cargando pedidos...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para ver detalle del pedido -->
<div class="modal fade" id="modal_detalle_pedido" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-receipt me-2"></i>Pedido #<span id="detalle_numero_pedido"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="detalle_pedido_contenido">
                <!-- Se llena dinámicamente -->
            </div>
            <div class="modal-footer" id="detalle_pedido_acciones">
                <!-- Se llena dinámicamente según el estado -->
            </div>
        </div>
    </div>
</div>

<!-- Modal para rechazar pedido -->
<div class="modal fade" id="modal_rechazar_pedido" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="fas fa-times-circle me-2"></i>Rechazar pedido</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="rechazar_id_pedido">
                <div class="mb-3">
                    <label class="form-label">Motivo del rechazo <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="motivo_rechazo" rows="3" 
                        placeholder="Ej: Producto no disponible, falta de ingredientes, etc."></textarea>
                </div>
                <div class="alert alert-warning mb-0">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Esta acción notificará al cliente que su pedido fue rechazado.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="btn_confirmar_rechazo">
                    <i class="fas fa-times me-1"></i> Confirmar rechazo
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.pedido-card {
    transition: all 0.3s ease;
    border-left: 4px solid transparent;
}
.pedido-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}
.pedido-card.estado-1 { border-left-color: #ffc107; }
.pedido-card.estado-2 { border-left-color: #0d6efd; }
.pedido-card.estado-3 { border-left-color: #dc3545; }
.pedido-card.estado-4 { border-left-color: #198754; }
.pedido-card.estado-5 { border-left-color: #6c757d; }

.pedido-nuevo {
    animation: pulse-warning 2s infinite;
}
@keyframes pulse-warning {
    0%, 100% { box-shadow: 0 0 0 0 rgba(255, 193, 7, 0.4); }
    50% { box-shadow: 0 0 0 10px rgba(255, 193, 7, 0); }
}

.producto-item {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 10px;
    margin-bottom: 8px;
}
.ingrediente-tag {
    display: inline-block;
    background: #e9ecef;
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 12px;
    margin: 2px;
}
.ingrediente-extra {
    background: #fff3cd;
    color: #856404;
}
.tiempo-pedido {
    font-size: 12px;
}
.tiempo-pedido.urgente {
    color: #dc3545;
    font-weight: bold;
}
</style>

<?php
} else {
    include '404.php';
}
?>

