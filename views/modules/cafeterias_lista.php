<style>
    .btn-custom {
        background-color: #FF6B6B;
        /* Color personalizado de los botones */
        color: white;
    }

    .btn-custom:hover {
        background-color: #FF3B3B;
        /* Color hover */
    }

    .card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .card-img-top {
        height: 200px;
        /* Ajusta el tamaño de las imágenes */
        object-fit: cover;
    }

    .status-open {
        color: green;
    }

    .favorite-icon {
        color: red;
        font-size: 1.2rem;
    }
</style>
<div class="container mt-4">
    <!-- Encabezado -->
    <div class="text-center mb-4">
        <h2>¡Bienvenido!</h2>
        <p>Comienza a explorar y encuentra cafés cerca de ti.</p>
    </div>

    <!-- Barra de botones -->
    <div class="d-flex justify-content-center mb-4">
        <button class="btn btn-custom me-2">
            <i class="bi bi-search"></i> buscar
        </button>
        <button class="btn btn-custom me-2">
            <i class="bi bi-funnel"></i> filtros
        </button>
        <button class="btn btn-custom">
            <i class="bi bi-map"></i> mapa
        </button>
    </div>

    <!-- Listado de cafeterías -->
    <div class="row">
        <!-- Tarjeta de ejemplo -->
        <div class="col-12 col-md-6 col-lg-4 mb-4">
            <div class="card">
                <img src="cafe1.jpg" class="card-img-top" alt="Cafetería 1">
                <div class="card-body">
                    <div class="card-header">
                        <h5 class="card-title">Fraternos Coffee Bar</h5>
                        <i class="bi bi-heart favorite-icon"></i>
                    </div>
                    <p class="card-text">Dirección</p>
                    <div class="d-flex justify-content-between">
                        <span class="status-open">abierto</span>
                        <span>1.2km</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>