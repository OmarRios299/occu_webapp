<?php
// Vista para Propietarios - Lista simple de sus cafeterías
?>
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="titulo-modulo">Mis Cafeterías</h1>
        <a class="btn btn-agregar con-icono" href="<?= $url . 'cafeterias/agregar' ?>">Agregar Cafetería</a>
    </div>

    <!-- Contenedor para Listado de Cafeterías -->
    <div class="cafeterias-grid-propietario" id="div_cafeterias_propietario">
        <!-- Aquí se cargarán las cafeterías dinámicamente -->
        <div class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Cargando...</span>
            </div>
        </div>
    </div>
</div>

<!-- Offcanvas para mostrar información y acciones de cafetería -->
<div class="offcanvas offcanvas-end offcanvas-unified slide-from-right width-medium" tabindex="-1" id="offcanvasCafeteriaPropietario" aria-labelledby="offcanvasCafeteriaPropietarioLabel">
    <div class="offcanvas-header offcanvas-unified-header">
        <div class="offcanvas-unified-header-content">
            <h5 class="offcanvas-unified-title" id="offcanvasCafeteriaPropietarioLabel">
                <i class="bi bi-cup-hot-fill"></i>
                <span id="titulo_cafeteria_propietario">Información de Cafetería</span>
            </h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Cerrar"></button>
        </div>
    </div>
    <div class="offcanvas-body offcanvas-unified-body">
        <div class="h-100 d-flex flex-column overflow-hidden">
            <div class="offcanvas-unified-scrollable">
                <div class="offcanvas-unified-content">
                <input type="hidden" id="id_cafeteria_propietario">

                <!-- Información de la cafetería -->
                <div id="info_cafeteria_propietario">
                    <!-- Se carga dinámicamente -->
                </div>

                <!-- Botones de acción -->
                <div class="mt-4 pt-4 border-top">
                    <h6 class="mb-3">Acciones</h6>
                    <div class="row">
                        <div class="col-6 ">
                            <a class="btn btn-editar con-icono btn-action-propietario" id="btn-editar-cafeteria" href="#">
                                Editar Cafetería
                            </a>
                        </div>
                        <div class="col-6">
                            <button class="btn btn-servicios con-icono btn-action-propietario" id="btn-servicios-cafeteria">
                                Gestionar Servicios
                            </button>
                        </div>
                       <div class="col-6 mt-2">
                            <a class="btn btn-menu con-icono btn-action-propietario" id="btn-menu-cafeteria" href="#">
                                Gestionar Menú
                            </a>
                       </div>
                        <div class="col-6 mt-2">
                            <a class="btn btn-pedidos con-icono btn-action-propietario" id="btn-pedidos-cafeteria" href="#">
                                Ver Pedidos
                            </a>
                        </div>
                        <div class="col-6 mt-2">
                            <a class="btn btn-qr con-icono btn-action-propietario" id="btn-qr-cafeteria" href="#">
                                Ver Código QR
                            </a>
                        </div>
                        <div class="col-6 mt-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input cambioEstado" type="checkbox" id="switch-estado-cafeteria" tabla="cafeterias" idRegistro="">
                                <label class="form-check-label" for="switch-estado-cafeteria">
                                    Cafetería Activa
                                </label>
                            </div>
                        </div>
                        <div class="col-12 mt-3 d-flex justify-content-center">
                            <button class="btn btn-eliminar con-icono btn-action-propietario eliminarRegistro" id="btn-eliminar-cafeteria" tabla="cafeterias">
                                Eliminar Cafetería
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para servicios (reutilizar el existente) -->
<div class="modal fade" id="modal_agregar_servicios" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalLabel">Seleccionando servicios</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form onsubmit="return false;" id="form_servicios">
                    <input type="hidden" id="id_cafeteria">
                    <div class="caja">
                        <div class="row">
                            <div class="col-md" id="servicios">
                                <!-- Los servicios se cargan dinámicamente -->
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
</div>

<style>
    .cafeterias-grid-propietario {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 1.5rem;
        margin-top: 2rem;
    }

    .cafeteria-card-propietario {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        transition: all 0.3s ease;
        cursor: pointer;
        border: 2px solid transparent;
    }

    .cafeteria-card-propietario:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        border-color: var(--principal);
    }

    .cafeteria-card-propietario .card-image {
        width: 100%;
        height: 200px;
        overflow: hidden;
        position: relative;
    }

    .cafeteria-card-propietario .card-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }

    .cafeteria-card-propietario:hover .card-image img {
        transform: scale(1.1);
    }

    .cafeteria-card-propietario .card-status {
        position: absolute;
        top: 10px;
        right: 10px;
        padding: 0.4rem 0.8rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        backdrop-filter: blur(10px);
    }

    .cafeteria-card-propietario .card-status.status-active {
        background: rgba(40, 167, 69, 0.9);
        color: white;
    }

    .cafeteria-card-propietario .card-status.status-inactive {
        background: rgba(220, 53, 69, 0.9);
        color: white;
    }

    .cafeteria-card-propietario .card-body {
        padding: 1.5rem;
    }

    .cafeteria-card-propietario .card-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--cuarto);
        margin-bottom: 0.5rem;
    }

    .cafeteria-card-propietario .card-info {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: #666;
        font-size: 0.9rem;
        margin-bottom: 0.5rem;
    }

    .cafeteria-card-propietario .card-info i {
        color: var(--principal);
        width: 18px;
    }

    .cafeteria-card-propietario .card-horario {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: #666;
        font-size: 0.85rem;
        margin-top: 0.5rem;
        padding-top: 0.5rem;
        border-top: 1px solid #eee;
    }

    .btn-action-propietario {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0.75rem 1rem;
        font-weight: 500;
        transition: all 0.3s ease;
        position: relative;
        text-align: center;
        font-size: 0.95rem;
    }

    /* Asegurar que los iconos de los botones con clase con-icono se muestren correctamente */
    .btn-action-propietario.con-icono::before {
        display: inline-block;
        width: 1.2rem;
        height: 1.2rem;
        margin-right: 0.5rem;
        background-size: contain;
        background-repeat: no-repeat;
        background-position: center;
        vertical-align: middle;
    }

    .btn-action-propietario:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    #info_cafeteria_propietario .info-label {
        font-size: 0.75rem;
        text-transform: uppercase;
        color: #999;
        font-weight: 600;
        margin-bottom: 0.25rem;
    }

    #info_cafeteria_propietario .info-value {
        font-size: 1rem;
        color: var(--cuarto);
        font-weight: 500;
    }

    @media (max-width: 768px) {
        .cafeterias-grid-propietario {
            grid-template-columns: 1fr;
        }
    }
</style>