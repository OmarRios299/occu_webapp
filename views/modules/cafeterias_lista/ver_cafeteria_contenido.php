<?php
// Este archivo contiene los offcanvas completos y estilos unificados para mostrar información de cafetería
// Se puede incluir en cualquier módulo que necesite mostrar detalles de cafetería
// Los IDs tienen sufijo _mapa para evitar conflictos
?>

<style>
/* ============================================
   ESTILOS UNIFICADOS PARA OFFCANVAS DE CAFETERÍA
   ============================================ */

/* ========== ESTILOS BASE (fuera del contenedor offcanvas) ========== */
/* Cards principales */
.modern-card {
    background: #fff;
    border-radius: 20px;
    box-shadow: 0 5px 25px rgba(0,0,0,0.08);
    padding: 2rem;
    margin-bottom: 2rem;
    transition: all 0.3s ease;
}

.modern-card:hover {
    box-shadow: 0 10px 40px rgba(0,0,0,0.12);
    transform: translateY(-2px);
}

/* Card de información */
.info-card {
    padding: 2rem;
}

.info-card-header {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 2px solid var(--bg-body);
}

.info-card-header i {
    font-size: 1.5rem;
    color: var(--principal);
    background: var(--plantilla-claro);
    padding: 0.8rem;
    border-radius: 12px;
}

.info-card-header h2 {
    margin: 0;
    font-size: 1.3rem;
    font-weight: 700;
    color: var(--cuarto);
}

.info-item {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    padding: 0.9rem 0;
    border-bottom: 1px solid rgba(0,0,0,0.05);
    transition: all 0.3s ease;
}

.info-item:hover {
    background: var(--plantilla-claro);
    margin: 0 -1rem;
    padding-left: 1rem;
    padding-right: 1rem;
    border-radius: 10px;
}

.info-item:last-of-type {
    border-bottom: none;
}

.info-icon {
    flex-shrink: 0;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, var(--plantilla-claro) 0%, #fff 100%);
    border-radius: 10px;
    color: var(--principal);
    font-size: 1.1rem;
}

.info-content {
    flex: 1;
}

.info-label {
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: var(--color-gris);
    margin-bottom: 0.2rem;
    font-weight: 600;
}

.info-value {
    font-size: 0.95rem;
    color: var(--cuarto);
    font-weight: 500;
    margin: 0;
}

.copy-feedback {
    display: none;
    color: var(--color-verde);
    font-size: 0.75rem;
    margin-top: 0.25rem;
}

/* Botones de acción */
.action-buttons {
    display: flex;
    gap: 0.75rem;
    margin-top: 1.5rem;
    flex-wrap: wrap;
}

.btn-action {
    flex: 1;
    min-width: 140px;
    padding: 0.9rem 1.2rem;
    border-radius: 12px;
    font-weight: 600;
    font-size: 0.9rem;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    transition: all 0.3s ease;
    text-decoration: none;
}

.btn-location {
    background: linear-gradient(135deg, var(--color-verde) 0%, #02a36a 100%);
    color: #fff;
    border: none;
}

.btn-location:hover {
    background: linear-gradient(135deg, #02a36a 0%, var(--color-verde) 100%);
    color: #fff;
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(3,205,130,0.35);
}

.btn-menu-link {
    background: transparent;
    color: var(--principal);
    border: 2px solid var(--principal);
}

.btn-menu-link:hover {
    background: var(--principal);
    color: #fff;
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(241,108,91,0.35);
}

/* Sección de Servicios */
.services-section {
    margin-top: 2.5rem;
}

.section-header {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 1.5rem;
}

.section-header i {
    font-size: 1.5rem;
    color: var(--principal);
    background: var(--plantilla-claro);
    padding: 0.7rem;
    border-radius: 12px;
}

.section-header h3 {
    margin: 0;
    font-size: 1.4rem;
    font-weight: 700;
    color: var(--cuarto);
}

/* Contenedor de iconos de servicios */
.servicios-iconos-container {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
    margin-top: 1rem;
}

.servicio-icono-item {
    flex: 0 0 auto;
}

.servicio-icono-wrapper {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}

.servicio-icono-img {
    width: 98px;
    height: 98px;
    object-fit: contain;
}

.servicio-icono-nombre {
    font-size: 0.85rem;
    font-weight: 600;
    color: var(--cuarto);
    text-align: center;
    line-height: 1.2;
}



/* Sección de Reseñas */
.reviews-section {
    margin-top: 3rem;
    padding: 2rem;
    background: #fff;
    border-radius: 24px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.06);
}

.reviews-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
    margin-bottom: 1.5rem;
    padding-bottom: 1.5rem;
    border-bottom: 2px solid var(--bg-body);
}

.reviews-title {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.reviews-title i {
    font-size: 1.5rem;
    color: var(--color-amarillo);
    background: var(--color-claro-amarillo);
    padding: 0.7rem;
    border-radius: 12px;
}

.reviews-title h3 {
    margin: 0;
    font-size: 1.4rem;
    font-weight: 700;
    color: var(--cuarto);
}

.btn-add-review {
    background: linear-gradient(135deg, var(--tercero) 0%, var(--color-azul-marino) 100%);
    color: #fff;
    border: none;
    padding: 0.8rem 1.5rem;
    border-radius: 12px;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.3s ease;
}

.btn-add-review:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(62,81,160,0.35);
}

/* Área de comentario */
.comment-area {
    background: var(--bg-body);
    border-radius: 16px;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
    display: none;
}

.comment-area.show {
    display: block;
    animation: slideDown 0.3s ease;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.comment-area textarea {
    border: 2px solid transparent;
    border-radius: 12px;
    padding: 1rem;
    resize: none;
    transition: all 0.3s ease;
}

.comment-area textarea:focus {
    border-color: var(--principal);
    box-shadow: 0 0 0 4px rgba(241,108,91,0.1);
}

.comment-actions {
    display: flex;
    justify-content: flex-end;
    gap: 0.75rem;
    margin-top: 1rem;
}

/* Lista de comentarios */
.comments-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.comments-list li {
    padding: 1.5rem;
    background: var(--bg-body);
    border-radius: 16px;
    margin-bottom: 1rem;
    transition: all 0.3s ease;
}

.comments-list li:hover {
    box-shadow: 0 5px 15px rgba(0,0,0,0.08);
}

/* ========== GALERÍA PROFESIONAL - ESTILO GOOGLE MAPS/AIRBNB ========== */
/* Estilos base de galería - deben estar fuera del contenedor para funcionar */
.gallery-grid {
    display: grid;
    gap: 8px;
    border-radius: 16px;
    overflow: hidden;
    cursor: pointer;
    position: relative;
}

.gallery-item {
    position: relative;
    overflow: hidden;
    background: #f0f0f0;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.gallery-item::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0);
    transition: background 0.3s ease;
    z-index: 1;
    pointer-events: none;
}

.gallery-item:hover::before {
    background: rgba(0, 0, 0, 0.1);
}

.gallery-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
    transition: transform 0.5s cubic-bezier(0.4, 0, 0.2, 1);
}

.gallery-item:hover img {
    transform: scale(1.05);
}

.gallery-counter {
    position: absolute;
    top: 16px;
    left: 16px;
    background: rgba(0, 0, 0, 0.75);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    color: white;
    padding: 8px 16px;
    border-radius: 24px;
    font-size: 14px;
    font-weight: 600;
    z-index: 10;
    display: flex;
    align-items: center;
    gap: 8px;
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.3);
}

.gallery-counter i {
    font-size: 13px;
}

.gallery-view-all {
    position: absolute;
    bottom: 16px;
    right: 16px;
    background: white;
    color: #222;
    border: none;
    padding: 10px 20px;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    z-index: 10;
    display: flex;
    align-items: center;
    gap: 8px;
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.15);
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}

.gallery-view-all:hover {
    background: #f7f7f7;
    transform: translateY(-2px);
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.2);
}

.gallery-view-all i {
    font-size: 13px;
}

.gallery-item.has-more {
    position: relative;
}

.gallery-item.has-more::after {
    content: attr(data-remaining) ' más';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.7);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    font-weight: 700;
    z-index: 2;
    transition: all 0.3s ease;
}

.gallery-item.has-more:hover::after {
    background: rgba(0, 0, 0, 0.8);
}

.gallery-empty {
    border-radius: 16px;
    background: linear-gradient(135deg, #f5f5f5 0%, #e8e8e8 100%);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: #999;
}

.gallery-empty i {
    font-size: 4rem;
    margin-bottom: 1rem;
    opacity: 0.3;
}

.gallery-empty p {
    margin: 0;
    font-size: 1.1rem;
    font-weight: 500;
}

/* Layouts de galería */
.gallery-grid.layout-1 {
    grid-template-columns: 1fr;
}

.gallery-grid.layout-2 {
    grid-template-columns: 1fr 1fr;
}

.gallery-grid.layout-3 {
    grid-template-columns: 2fr 1fr;
    grid-template-rows: 1fr 1fr;
}

.gallery-grid.layout-3 .gallery-item:first-child {
    grid-row: 1 / 3;
}

.gallery-grid.layout-4 {
    grid-template-columns: 2fr 1fr;
    grid-template-rows: 1fr 1fr;
}

.gallery-grid.layout-4 .gallery-item:first-child {
    grid-row: 1 / 3;
}

.gallery-grid.layout-5plus {
    grid-template-columns: 2fr 1fr 1fr;
    grid-template-rows: 1fr 1fr;
}

.gallery-grid.layout-5plus .gallery-item:first-child {
    grid-row: 1 / 3;
}

/* ========== GALERÍA PROFESIONAL - ESTILO GOOGLE MAPS/AIRBNB ========== */
.gallery-grid {
    display: grid;
    gap: 8px;
    height: 450px;
    border-radius: 16px;
    overflow: hidden;
    cursor: pointer;
    position: relative;
}

/* Layout 1 imagen */
.gallery-grid.layout-1 {
    grid-template-columns: 1fr;
}

/* Layout 2 imágenes */
.gallery-grid.layout-2 {
    grid-template-columns: 1fr 1fr;
}

/* Layout 3 imágenes */
.gallery-grid.layout-3 {
    grid-template-columns: 2fr 1fr;
    grid-template-rows: 1fr 1fr;
}

.gallery-grid.layout-3 .gallery-item:first-child {
    grid-row: 1 / 3;
}

/* Layout 4 imágenes */
.gallery-grid.layout-4 {
    grid-template-columns: 2fr 1fr;
    grid-template-rows: 1fr 1fr;
}

.gallery-grid.layout-4 .gallery-item:first-child {
    grid-row: 1 / 3;
}

/* Layout 5+ imágenes */
.gallery-grid.layout-5plus {
    grid-template-columns: 2fr 1fr 1fr;
    grid-template-rows: 1fr 1fr;
}

.gallery-grid.layout-5plus .gallery-item:first-child {
    grid-row: 1 / 3;
}

/* Items de galería */
.gallery-item {
    position: relative;
    overflow: hidden;
    background: #f0f0f0;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.gallery-item::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0);
    transition: background 0.3s ease;
    z-index: 1;
    pointer-events: none;
}

.gallery-item:hover::before {
    background: rgba(0, 0, 0, 0.1);
}

.gallery-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
    transition: transform 0.5s cubic-bezier(0.4, 0, 0.2, 1);
}

.gallery-item:hover img {
    transform: scale(1.05);
}

/* Contador de imágenes */
.gallery-counter {
    position: absolute;
    top: 16px;
    left: 16px;
    background: rgba(0, 0, 0, 0.75);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    color: white;
    padding: 8px 16px;
    border-radius: 24px;
    font-size: 14px;
    font-weight: 600;
    z-index: 10;
    display: flex;
    align-items: center;
    gap: 8px;
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.3);
}

.gallery-counter i {
    font-size: 13px;
}

/* Botón ver todas */
.gallery-view-all {
    position: absolute;
    bottom: 16px;
    right: 16px;
    background: white;
    color: #222;
    border: none;
    padding: 10px 20px;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    z-index: 10;
    display: flex;
    align-items: center;
    gap: 8px;
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.15);
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}

.gallery-view-all:hover {
    background: #f7f7f7;
    transform: translateY(-2px);
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.2);
}

.gallery-view-all i {
    font-size: 13px;
}

/* Overlay de más imágenes */
.gallery-item.has-more {
    position: relative;
}

.gallery-item.has-more::after {
    content: attr(data-remaining) ' más';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.7);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    font-weight: 700;
    z-index: 2;
    transition: all 0.3s ease;
}

.gallery-item.has-more:hover::after {
    background: rgba(0, 0, 0, 0.8);
}

/* Estado vacío */
.gallery-empty {
    height: 450px;
    border-radius: 16px;
    background: linear-gradient(135deg, #f5f5f5 0%, #e8e8e8 100%);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: #999;
}

.gallery-empty i {
    font-size: 4rem;
    margin-bottom: 1rem;
    opacity: 0.3;
}

.gallery-empty p {
    margin: 0;
    font-size: 1.1rem;
    font-weight: 500;
}

/* Scrollbar personalizado para mejor visualización */
.cafeteria-content-offcanvas::-webkit-scrollbar,
.offcanvas-body::-webkit-scrollbar {
    width: 8px;
}

.cafeteria-content-offcanvas::-webkit-scrollbar-track,
.offcanvas-body::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

.cafeteria-content-offcanvas::-webkit-scrollbar-thumb,
.offcanvas-body::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 10px;
}

.cafeteria-content-offcanvas::-webkit-scrollbar-thumb:hover,
.offcanvas-body::-webkit-scrollbar-thumb:hover {
    background: #555;
}

.cafeteria-content-offcanvas .modern-card {
    margin-bottom: 1.5rem;
    width: 100%;
    box-sizing: border-box;
}

.cafeteria-content-offcanvas .gallery-container {
    border-radius: 12px;
    overflow: hidden;
}

.cafeteria-content-offcanvas .info-card {
    padding: 1.5rem;
}

.cafeteria-content-offcanvas .info-item {
    padding: 0.75rem 0;
}

.cafeteria-content-offcanvas .services-section,
.cafeteria-content-offcanvas .reviews-section {
    margin-top: 2rem;
}

.cafeteria-content-offcanvas .section-header {
    margin-bottom: 1rem;
}

.cafeteria-content-offcanvas .reviews-header {
    margin-bottom: 1.5rem;
}

/* Galería adaptada para offcanvas */
.cafeteria-content-offcanvas .gallery-grid {
    height: auto;
    min-height: 250px;
    max-height: 400px;
    border-radius: 12px;
    gap: 4px;
}

.cafeteria-content-offcanvas .gallery-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

/* En offcanvas, siempre usar layout simple para mejor visualización */
.cafeteria-content-offcanvas .gallery-grid.layout-3,
.cafeteria-content-offcanvas .gallery-grid.layout-4,
.cafeteria-content-offcanvas .gallery-grid.layout-5plus {
    grid-template-columns: 1fr 1fr;
    grid-template-rows: 1fr 1fr;
    max-height: 300px;
}

.cafeteria-content-offcanvas .gallery-grid.layout-3 .gallery-item:first-child,
.cafeteria-content-offcanvas .gallery-grid.layout-4 .gallery-item:first-child,
.cafeteria-content-offcanvas .gallery-grid.layout-5plus .gallery-item:first-child {
    grid-row: auto;
}

.cafeteria-content-offcanvas .gallery-empty {
    height: 250px;
    min-height: 250px;
}

/* Responsive para offcanvas */
@media (max-width: 768px) {
    .cafeteria-content-offcanvas .gallery-grid {
        height: auto;
        min-height: 200px;
        max-height: 250px;
    }
    
    .cafeteria-content-offcanvas .info-card {
        padding: 1rem;
    }
    
    .cafeteria-content-offcanvas .action-buttons {
        flex-direction: column;
    }
    
    .cafeteria-content-offcanvas .btn-action {
        min-width: auto;
        width: 100%;
    }
    
    .cafeteria-content-offcanvas .reviews-header {
        flex-direction: column;
        text-align: center;
    }
    
    .cafeteria-content-offcanvas .services-section .splide {
        margin: 0 -0.5rem;
    }
}



</style>

<!-- Offcanvas para mostrar información de cafetería (Desktop - desde la derecha) -->
<div class="offcanvas offcanvas-end offcanvas-unified slide-from-right width-medium filtros-offcanvas-desktop" tabindex="-1" id="offcanvasCafeteria" aria-labelledby="offcanvasCafeteriaLabel">
    <div class="offcanvas-header offcanvas-unified-header">
        <div class="offcanvas-unified-header-content">
            <h5 id="offcanvasCafeteriaLabel" class="offcanvas-unified-title">
                <i class="bi bi-cup-hot-fill"></i>
                <span id="titulo_cafeteria_ver_mapa">Información de Cafetería</span>
            </h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Cerrar"></button>
            
        </div>
    </div>
    <div class="offcanvas-body offcanvas-unified-body">
        <div class="h-100 d-flex flex-column overflow-hidden">
            <!-- Contenido scrollable -->
            <div class="offcanvas-unified-scrollable">
                <input type="hidden" id="id_cafeteria_mapa">
                <div id="contenido_cafeteria_mapa" class="offcanvas-unified-content">
                    <div class="cafeteria-content-offcanvas">
                        <!-- Galería de Imágenes -->
                        <div class="modern-card" style="padding: 0; overflow: visible; margin-bottom: 1.5rem;">
                            <div id="galleryContainer_mapa">
                                <!-- La galería se genera aquí -->
                            </div>
                        </div>

                        <!-- Información General -->
                        <div class="modern-card info-card">
                            <div class="info-card-header">
                                <i class="fas fa-info-circle"></i>
                                <h2>Información General</h2>
                            </div>
                            
                            <div class="info-item">
                                <div class="info-icon">
                                    <i class="fas fa-map-marker-alt"></i>
                                </div>
                                <div class="info-content">
                                    <div class="info-label">Dirección</div>
                                    <p class="info-value" id="info1_mapa"></p>
                                </div>
                            </div>

                            <div class="info-item">
                                <div class="info-icon">
                                    <i class="fas fa-phone-alt"></i>
                                </div>
                                <div class="info-content">
                                    <div class="info-label">Teléfono</div>
                                    <p class="info-value" id="info2_mapa"></p>
                                    <span id="copy-feedback-tel_mapa" class="copy-feedback">
                                        <i class="fas fa-check-circle"></i> ¡Copiado!
                                    </span>
                                </div>
                            </div>

                            <div class="info-item">
                                <div class="info-icon">
                                    <i class="fas fa-envelope"></i>
                                </div>
                                <div class="info-content">
                                    <div class="info-label">Correo Electrónico</div>
                                    <p class="info-value" id="info3_mapa"></p>
                                    <span id="copy-feedback-email_mapa" class="copy-feedback">
                                        <i class="fas fa-check-circle"></i> ¡Copiado!
                                    </span>
                                </div>
                            </div>

                            <div class="info-item">
                                <div class="info-icon">
                                    <i class="fas fa-concierge-bell"></i>
                                </div>
                                <div class="info-content">
                                    <div class="info-label">Servicios Disponibles</div>
                                    <p class="info-value" id="info4_mapa"></p>
                                </div>
                            </div>

                            <div class="action-buttons">
                                <button class="btn-action btn-location ir_googlemaps_mapa">
                                    <i class="fas fa-location-arrow"></i>
                                    Ir a Ubicación
                                </button>
                                <button class="btn-action btn-menu-link" type="button" id="btn_ver_menu_mapa">
                                    <i class="fas fa-utensils"></i>
                                    Ver Menú
                                </button>
                            </div>
                        </div>

                        <!-- Sección de Servicios -->
                        <div class="services-section">
                            <div class="section-header">
                                <i class="fas fa-star"></i>
                                <h3>Servicios</h3>
                            </div>
                            
                            <div class="servicios-iconos-container" id="servicios_iconos_mapa">
                                <!-- Los servicios se cargan dinámicamente -->
                            </div>
                        </div>

                        <!-- Sección de Reseñas -->
                        <div class="reviews-section comentarios">
                            <div class="reviews-header">
                                <div class="reviews-title">
                                    <i class="fas fa-comments"></i>
                                    <h3>Reseñas de Clientes</h3>
                                </div>
                                
                                <button type="button" class="btn-add-review" id="btn_agregar_comentario_mapa">
                                    <i class="fas fa-plus"></i>
                                    Agregar Reseña
                                </button>
                            </div>

                            <!-- Área para agregar comentario -->
                            <div class="comment-area comentario-area" id="comment-form-area_mapa" style="display: none;">
                                <div class="form-group">
                                    <label class="mb-2 fw-bold">
                                        <i class="fas fa-pen me-2"></i>Tu opinión es importante
                                    </label>
                                    <textarea class="form-control" cols="30" rows="4" id="agregar_comentario_mapa" 
                                        placeholder="Comparte tu experiencia en esta cafetería..."></textarea>
                                </div>
                                <div class="comment-actions">
                                    <button type="button" class="btn btn-light btn-cancelar-comentario-mapa">
                                        Cancelar
                                    </button>
                                    <button type="button" class="btn btn-success" id="btn_aceptar_comentario_mapa">
                                        <i class="fas fa-paper-plane me-1"></i>
                                        Publicar
                                    </button>
                                </div>
                            </div>

                            <!-- Lista de comentarios -->
                            <div class="coment-ocultar">
                                <!-- Mensaje cuando no hay reseñas -->
                                <div id="mensaje-sin-resenas_mapa" class="text-center" style="display: none;">
                                    <i class="fas fa-comment-slash fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">Sé el primero en dejar una reseña</h5>
                                    <p class="text-muted" style="font-size: 14px;">Comparte tu experiencia y ayuda a otros usuarios.</p>
                                </div>
                                
                                <ul id="comentariosLista_mapa" class="comments-list lista-comentarios">
                                    <!-- Los comentarios se cargan dinámicamente -->
                                </ul>

                                <div id="paginacionComentarios_mapa" class="pagination-modern">
                                    <!-- Los controles de paginación se generan dinámicamente -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Offcanvas para mostrar información de cafetería (Mobile - desde abajo) -->
<div class="offcanvas offcanvas-end offcanvas-unified slide-from-right" tabindex="-1" id="offcanvasCafeteriaMobile" aria-labelledby="offcanvasCafeteriaMobileLabel">
    <div class="offcanvas-header offcanvas-unified-header">
        <div class="offcanvas-unified-header-content">
            <h5 id="offcanvasCafeteriaMobileLabel" class="offcanvas-unified-title">
                <i class="bi bi-cup-hot-fill"></i>

                <span id="titulo_cafeteria_ver_mapa_mobile">Información de Cafetería</span>
            </h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Cerrar"></button>
        </div>
    </div>
    <div class="offcanvas-body offcanvas-unified-body">
        <div class="h-100 d-flex flex-column overflow-hidden">
            <!-- Contenido scrollable -->
            <div class="offcanvas-unified-scrollable">
                <input type="hidden" id="id_cafeteria_mapa_mobile">
                <div id="contenido_cafeteria_mapa_mobile" class="offcanvas-unified-content">
                    <div class="cafeteria-content-offcanvas">
                        <!-- Galería de Imágenes -->
                        <div class="modern-card" style="padding: 0; overflow: visible; margin-bottom: 1.5rem;">
                            <div id="galleryContainer_mapa">
                                <!-- La galería se genera aquí -->
                            </div>
                        </div>

                        <!-- Información General -->
                        <div class="modern-card info-card">
                            <div class="info-card-header">
                                <i class="fas fa-info-circle"></i>
                                <h2>Información General</h2>
                            </div>
                            
                            <div class="info-item">
                                <div class="info-icon">
                                    <i class="fas fa-map-marker-alt"></i>
                                </div>
                                <div class="info-content">
                                    <div class="info-label">Dirección</div>
                                    <p class="info-value" id="info1_mapa"></p>
                                </div>
                            </div>

                            <div class="info-item">
                                <div class="info-icon">
                                    <i class="fas fa-phone-alt"></i>
                                </div>
                                <div class="info-content">
                                    <div class="info-label">Teléfono</div>
                                    <p class="info-value" id="info2_mapa"></p>
                                    <span id="copy-feedback-tel_mapa" class="copy-feedback">
                                        <i class="fas fa-check-circle"></i> ¡Copiado!
                                    </span>
                                </div>
                            </div>

                            <div class="info-item">
                                <div class="info-icon">
                                    <i class="fas fa-envelope"></i>
                                </div>
                                <div class="info-content">
                                    <div class="info-label">Correo Electrónico</div>
                                    <p class="info-value" id="info3_mapa"></p>
                                    <span id="copy-feedback-email_mapa" class="copy-feedback">
                                        <i class="fas fa-check-circle"></i> ¡Copiado!
                                    </span>
                                </div>
                            </div>

                            <div class="info-item">
                                <div class="info-icon">
                                    <i class="fas fa-concierge-bell"></i>
                                </div>
                                <div class="info-content">
                                    <div class="info-label">Servicios Disponibles</div>
                                    <p class="info-value" id="info4_mapa"></p>
                                </div>
                            </div>

                            <div class="action-buttons">
                                <button class="btn-action btn-location ir_googlemaps_mapa">
                                    <i class="fas fa-location-arrow"></i>
                                    Ir a Ubicación
                                </button>
                                <button class="btn-action btn-menu-link" type="button" id="btn_ver_menu_mapa">
                                    <i class="fas fa-utensils"></i>
                                    Ver Menú
                                </button>
                            </div>
                        </div>

                        <!-- Sección de Servicios -->
                        <div class="services-section">
                            <div class="section-header">
                                <i class="fas fa-star"></i>
                                <h3>Servicios</h3>
                            </div>
                            
                            <div class="servicios-iconos-container" id="servicios_iconos_mapa">
                                <!-- Los servicios se cargan dinámicamente -->
                            </div>
                        </div>

                        <!-- Sección de Reseñas -->
                        <div class="reviews-section comentarios">
                            <div class="reviews-header">
                                <div class="reviews-title">
                                    <i class="fas fa-comments"></i>
                                    <h3>Reseñas de Clientes</h3>
                                </div>
                                
                                <button type="button" class="btn-add-review" id="btn_agregar_comentario_mapa">
                                    <i class="fas fa-plus"></i>
                                    Agregar Reseña
                                </button>
                            </div>

                            <!-- Área para agregar comentario -->
                            <div class="comment-area comentario-area" id="comment-form-area_mapa" style="display: none;">
                                <div class="form-group">
                                    <label class="mb-2 fw-bold">
                                        <i class="fas fa-pen me-2"></i>Tu opinión es importante
                                    </label>
                                    <textarea class="form-control" cols="30" rows="4" id="agregar_comentario_mapa" 
                                        placeholder="Comparte tu experiencia en esta cafetería..."></textarea>
                                </div>
                                <div class="comment-actions">
                                    <button type="button" class="btn btn-light btn-cancelar-comentario-mapa">
                                        Cancelar
                                    </button>
                                    <button type="button" class="btn btn-success" id="btn_aceptar_comentario_mapa">
                                        <i class="fas fa-paper-plane me-1"></i>
                                        Publicar
                                    </button>
                                </div>
                            </div>

                            <!-- Lista de comentarios -->
                            <div class="coment-ocultar">
                                <!-- Mensaje cuando no hay reseñas -->
                                <div id="mensaje-sin-resenas_mapa" class="text-center" style="display: none;">
                                    <i class="fas fa-comment-slash fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">Sé el primero en dejar una reseña</h5>
                                    <p class="text-muted" style="font-size: 14px;">Comparte tu experiencia y ayuda a otros usuarios.</p>
                                </div>
                                
                                <ul id="comentariosLista_mapa" class="comments-list lista-comentarios">
                                    <!-- Los comentarios se cargan dinámicamente -->
                                </ul>

                                <div id="paginacionComentarios_mapa" class="pagination-modern">
                                    <!-- Los controles de paginación se generan dinámicamente -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Variable global para el lightbox del mapa
let galleryLightboxMapa = null;

// Función para inicializar galería en el mapa (misma lógica que en ver_cafeteria.php)
function initGalleryMapa(images) {
    // Actualizar galería en todos los contenedores (desktop y mobile)
    const containers = document.querySelectorAll('#galleryContainer_mapa');
    
    containers.forEach(function(container) {
        if (!images || images.length === 0) {
            container.innerHTML = `
                <div class="gallery-empty">
                    <i class="fas fa-image"></i>
                    <p>No hay imágenes disponibles</p>
                </div>
            `;
            return;
        }
        
        // Determinar layout según cantidad de imágenes (adaptado para offcanvas)
        let layoutClass = 'layout-1';
        let visibleImages = images.length;
        
        if (images.length === 2) {
            layoutClass = 'layout-2';
        } else if (images.length === 3) {
            layoutClass = 'layout-3';
        } else if (images.length === 4) {
            layoutClass = 'layout-4';
        } else if (images.length >= 5) {
            layoutClass = 'layout-5plus';
            visibleImages = 4; // Mostrar solo 5 en el grid
        }
        
        // Construir HTML del grid
        let gridHTML = `<div class="gallery-grid ${layoutClass}">`;
        
        // Agregar contador solo si hay más de 1 imagen
        if (images.length > 1) {
            gridHTML += `
                <div class="gallery-counter">
                    <i class="fas fa-images"></i>
                    <span>${images.length} fotos</span>
                </div>
            `;
        }
        
        // Agregar imágenes visibles (las imágenes ya vienen con URL completa desde el servidor)
        for (let i = 0; i < Math.min(visibleImages, images.length); i++) {
            const isLast = i === visibleImages - 1 && images.length > visibleImages;
            const hasMoreClass = isLast ? 'has-more' : '';
            const remainingCount = isLast ? `+${images.length - visibleImages}` : '';
            
            gridHTML += `
                <div class="gallery-item ${hasMoreClass}" 
                     ${isLast ? `data-remaining="${remainingCount}"` : ''} 
                     onclick="openGalleryMapa(${i})">
                    <img src="${images[i]}" alt="Imagen ${i + 1}" loading="${i === 0 ? 'eager' : 'lazy'}">
                </div>
            `;
        }
        
        // Agregar botón "Ver todas" solo si hay más de 4 imágenes
        // if (images.length > 4) {
        //     gridHTML += `
        //         <button class="gallery-view-all" onclick="openGalleryMapa(0)">
        //             <i class="fas fa-th"></i>
        //             <span>Ver todas las fotos</span>
        //         </button>
        //     `;
        // }
        
        gridHTML += `</div>`;
        
        container.innerHTML = gridHTML;
    });
    
    // Inicializar GLightbox una sola vez para todas las imágenes
    initGLightboxMapa(images);
}

/**
 * Inicializa GLightbox con todas las imágenes para el mapa
 * @param {Array} images - Array de URLs de imágenes
 */
function initGLightboxMapa(images) {
    // Destruir instancia anterior
    if (galleryLightboxMapa) {
        try {
            galleryLightboxMapa.destroy();
        } catch(e) {
            console.log('Limpiando instancia anterior del mapa');
        }
    }
    
    // Crear elementos GLightbox (ocultos)
    const lightboxElements = images.map((img, index) => ({
        href: img,
        type: 'image',
        alt: `Imagen ${index + 1}`,
        description: `Imagen ${index + 1} de ${images.length}`
    }));
    
    // Inicializar GLightbox si está disponible
    if (typeof GLightbox !== 'undefined') {
        galleryLightboxMapa = GLightbox({
            elements: lightboxElements,
            touchNavigation: true,
            loop: true,
            autoplayVideos: false,
            openEffect: 'zoom',
            closeEffect: 'fade',
            slideEffect: 'slide',
            closeButton: true,
            touchFollowAxis: true,
            keyboardNavigation: true,
            closeOnOutsideClick: true,
            zoomable: true,
            draggable: true,
            dragToleranceX: 40,
            dragToleranceY: 65
        });
    } else {
        console.warn('GLightbox no está disponible. Asegúrate de incluir la librería.');
    }
}

/**
 * Abre la galería en un índice específico para el mapa
 * @param {number} index - Índice de la imagen a mostrar
 */
function openGalleryMapa(index = 0) {
    if (galleryLightboxMapa) {
        galleryLightboxMapa.openAt(index);
    }
}
</script>

<!-- Offcanvas del Menú de Cafetería -->
<div class="offcanvas offcanvas-end offcanvas-unified slide-from-right width-medium" tabindex="-1" id="offcanvasMenuCafeteria" aria-labelledby="offcanvasMenuCafeteriaLabel">
    <div class="offcanvas-header offcanvas-unified-header">
        <div class="offcanvas-unified-header-content">
            <h5 id="offcanvasMenuCafeteriaLabel" class="offcanvas-unified-title">
                <i class="fas fa-utensils"></i>
                <span id="titulo_menu_cafeteria">Menú</span>
            </h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Cerrar"></button>
        </div>
    </div>
    <div class="offcanvas-body offcanvas-unified-body">
        <div class="h-100 d-flex flex-column overflow-hidden">
            <!-- Contenido scrollable -->
            <div class="offcanvas-unified-scrollable">
                <div class="offcanvas-unified-content">
                    <input type="hidden" id="id_cafeteria" value="">
                    <input type="hidden" id="id_cafeteria_menu_offcanvas" value="">
                    <!-- El contenido del menú se cargará aquí dinámicamente -->
                    <div id="contenido_menu_offcanvas">
                        <div class="text-center py-5">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Cargando...</span>
                            </div>
                            <p class="mt-3 text-muted">Cargando menú...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Offcanvas Detalle de Producto -->
<div class="offcanvas offcanvas-end offcanvas-unified slide-from-right" tabindex="-1" id="offcanvasProducto" aria-labelledby="offcanvasProductoLabel">
    <div class="offcanvas-header offcanvas-unified-header">
        <div class="offcanvas-unified-header-content">
            <h5 class="offcanvas-unified-title" id="offcanvasProductoLabel">
                <i class="bi bi-cup-hot"></i>
                <span>Iced latte</span>
            </h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Cerrar"></button>
        </div>
    </div>
    <div class="offcanvas-body offcanvas-unified-body">
        <div class="h-100 d-flex flex-column overflow-hidden">
            <!-- Contenido scrollable -->
            <div class="offcanvas-unified-scrollable">
                <div class="offcanvas-unified-content">
                    <!-- Imagen -->
                    <div class="producto-imagen position-relative">
                        <img src="<?= $url ?>views/assets/img/cafeteria_default.png" id="prod_imagen" alt="Producto" class="w-100 object-fit-cover" style="max-height: 200px; width: 100%;">
                    </div>

                    <!-- Info general -->
                    <div class="p-3">
                        <div class="mb-2"><small class="text-muted">🔥 100%</small></div>
                        <h5 id="nombre" class="mb-2">Iced latte</h5>
                        <p class="text-muted mb-0" id="prod_descripcion">Bebida a base de leche y cafe con hielo</p>
                    </div>

                    <!-- Opciones -->
                    <div class="px-3 pb-3" id="options">
                        <!-- Las opciones se cargan dinámicamente aquí -->
                    </div>
                </div>
            </div>

            <!-- Footer fijo -->
            <div class="offcanvas-unified-footer">
                <div class="input-group" style="width: 120px;">
                    <button class="btn btn-outline-secondary" type="button" id="btn_restar">-</button>
                    <input type="text" class="form-control text-center" value="1" readonly style="border-left: 0; border-right: 0;">
                    <button class="btn btn-outline-secondary" type="button" id="btn_sumar">+</button>
                </div>
                <button class="btn btn-warning text-white rounded-pill px-4 fw-bold" 
                id="btn_agregar_producto_carrito" style="flex-shrink: 0;" 
                <?php if (isset($_SESSION['nivel']) && 
                $_SESSION['nivel'] == "Propietario" || 
                $_SESSION['nivel'] == "Barista" || 
                $_SESSION['nivel'] == "Administrador") { echo 'disabled'; } ?>
                >Agregar · MX$0</button>
            </div>
        </div>
    </div>
</div>
