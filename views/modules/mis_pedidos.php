<style>
/* ========== MIS PEDIDOS - ESTILOS MODERNOS ========== */

/* Hero Header */
.pedidos-hero {
    position: relative;
    background: linear-gradient(135deg, var(--principal) 0%, var(--cuarto) 100%);
    border-radius: 0 0 40px 40px;
    padding: 2rem 1.5rem 3rem;
    margin-bottom: -2rem;
    margin-top: 0;
    overflow: hidden;
}

.pedidos-hero::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -20%;
    width: 400px;
    height: 400px;
    background: rgba(255,255,255,0.08);
    border-radius: 50%;
    animation: float 6s ease-in-out infinite;
}

.pedidos-hero::after {
    content: '';
    position: absolute;
    bottom: -30%;
    left: -10%;
    width: 300px;
    height: 300px;
    background: rgba(255,255,255,0.05);
    border-radius: 50%;
    animation: float 8s ease-in-out infinite reverse;
}

@keyframes float {
    0%, 100% { transform: translateY(0) rotate(0deg); }
    50% { transform: translateY(-20px) rotate(5deg); }
}

.pedidos-hero-content {
    position: relative;
    z-index: 1;
}

.pedidos-hero h1 {
    color: #fff;
    font-size: 2.2rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
    text-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.pedidos-hero p {
    color: rgba(255,255,255,0.9);
    font-size: 1rem;
    margin: 0;
}

/* Contenedor principal */
.pedidos-main-content {
    position: relative;
    z-index: 2;
    padding-top: 1rem;
    padding-bottom: 2rem;
}

/* Card de pedido */
.pedido-card {
    background: #fff;
    border-radius: 20px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.08);
    overflow: hidden;
    transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    border: none;
    cursor: pointer;
    margin-bottom: 1.5rem;
}

.pedido-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 50px rgba(0,0,0,0.12);
}

.pedido-card-header {
    padding: 1.5rem;
    border-bottom: 1px solid rgba(0,0,0,0.05);
    display: flex;
    align-items: center;
    gap: 1rem;
}

.pedido-card-imagen {
    width: 80px;
    height: 80px;
    border-radius: 12px;
    object-fit: cover;
    flex-shrink: 0;
}

.pedido-card-info {
    flex: 1;
    min-width: 0;
}

.pedido-card-info h5 {
    font-size: 1.1rem;
    font-weight: 700;
    color: #333;
    margin: 0 0 0.25rem 0;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.pedido-card-info p {
    font-size: 0.875rem;
    color: #666;
    margin: 0;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.pedido-card-body {
    padding: 1.5rem;
}

.pedido-card-footer {
    padding: 1rem 1.5rem;
    background: rgba(0,0,0,0.02);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.pedido-estado {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    border-radius: 50px;
    font-size: 0.875rem;
    font-weight: 600;
}

.pedido-estado.pendiente {
    background: #FFF3CD;
    color: #856404;
}

.pedido-estado.preparando {
    background: #D1ECF1;
    color: #0C5460;
}

.pedido-estado.entregado {
    background: #D4EDDA;
    color: #155724;
}

.pedido-estado.rechazado {
    background: #F8D7DA;
    color: #721C24;
}

.pedido-estado.terminado {
    background: #D1ECF1;
    color: #0C5460;
}

.pedido-monto {
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--principal);
}

.pedido-fecha {
    font-size: 0.875rem;
    color: #999;
}

/* Mensaje vacío */
.pedidos-vacio {
    text-align: center;
    padding: 4rem 2rem;
    color: #999;
}

.pedidos-vacio i {
    font-size: 4rem;
    margin-bottom: 1rem;
    opacity: 0.5;
}

.pedidos-vacio h4 {
    font-size: 1.5rem;
    margin-bottom: 0.5rem;
    color: #666;
}

.pedidos-vacio p {
    font-size: 1rem;
    color: #999;
}

/* Sección de pedido actual */
.pedido-actual-section {
    margin-bottom: 3rem;
}

.pedido-actual-section h3 {
    font-size: 1.5rem;
    font-weight: 700;
    color: #333;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.pedido-actual-section h3 i {
    color: var(--principal);
}

/* Sección de pedidos anteriores */
.pedidos-anteriores-section h3 {
    font-size: 1.5rem;
    font-weight: 700;
    color: #333;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.pedidos-anteriores-section h3 i {
    color: var(--principal);
}

/* Paginación */
.pedidos-paginacion {
    margin-top: 2rem;
}

.pedidos-paginacion .pagination {
    margin-bottom: 1rem;
}

.pedidos-paginacion .page-link {
    color: var(--principal);
    border-color: rgba(0,0,0,0.1);
    padding: 0.5rem 1rem;
}

.pedidos-paginacion .page-link:hover {
    background-color: var(--principal);
    color: #fff;
    border-color: var(--principal);
}

.pedidos-paginacion .page-item.active .page-link {
    background-color: var(--principal);
    border-color: var(--principal);
    color: #fff;
}

.pedidos-paginacion .page-item.disabled .page-link {
    color: #ccc;
    cursor: not-allowed;
    background-color: #f8f9fa;
}
</style>

<!-- Hero Header -->
<div class="pedidos-hero">
    <div class="pedidos-hero-content">
        <h1><i class="fas fa-receipt me-2"></i>Mis Pedidos</h1>
        <p>Consulta tu historial de pedidos y el estado de tus órdenes</p>
    </div>
</div>

<!-- Contenido Principal -->
<div class="pedidos-main-content container mt-5">
    
    <!-- Pedido Actual -->
    <div class="pedido-actual-section" id="pedido-actual-section" style="display: none;">
        <h3><i class="fas fa-clock"></i> Pedido en Curso</h3>
        <div id="pedido-actual-container"></div>
    </div>

    <!-- Pedidos Anteriores -->
    <div class="pedidos-anteriores-section">
        <h3><i class="fas fa-history"></i> Pedidos Anteriores</h3>
        <div id="pedidos-anteriores-container">
            <div class="pedidos-vacio">
                <i class="fas fa-inbox"></i>
                <h4>No hay pedidos anteriores</h4>
                <p>Aún no has realizado ningún pedido</p>
            </div>
        </div>
    </div>

</div>

<!-- Offcanvas Detalle de Pedido -->
<div class="offcanvas offcanvas-end offcanvas-unified slide-from-right" tabindex="-1" id="offcanvasDetallePedido" aria-labelledby="offcanvasDetallePedidoLabel">
    <div class="offcanvas-header offcanvas-unified-header">
        <div class="offcanvas-unified-header-content">
            <h5 class="offcanvas-unified-title" id="offcanvasDetallePedidoLabel">
                <i class="bi bi-receipt"></i>
                <span>Detalle del Pedido</span>
            </h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Cerrar"></button>
        </div>
    </div>
    <div class="offcanvas-body offcanvas-unified-body">
        <div class="h-100 d-flex flex-column overflow-hidden">
            <!-- Contenido scrollable -->
            <div class="offcanvas-unified-scrollable">
                <div class="offcanvas-unified-content">
                    <div id="detalle-pedido-content">
                        <!-- El contenido se carga dinámicamente aquí -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


