<?php
$hidden = 'style="display:none"';
if (isset($action[1])) {
    $hidden = '';
}
?>

<style>
/* ========== VER CAFETERÍA - ESTILOS MODERNOS ========== */

/* Hero Header */
.cafeteria-hero {
    position: relative;
    background: linear-gradient(135deg, var(--principal) 0%, var(--cuarto) 100%);
    border-radius: 0 0 40px 40px;
    padding: 2rem 1.5rem 3rem;
    margin-bottom: -2rem;
    margin-top: -1rem;
    overflow: hidden;
}

.cafeteria-hero::before {
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

.cafeteria-hero::after {
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

.cafeteria-hero-content {
    position: relative;
    z-index: 1;
}

.cafeteria-hero h1 {
    color: #fff;
    font-size: 2.2rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
    text-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.cafeteria-hero p {
    color: rgba(255,255,255,0.9);
    font-size: 1rem;
    margin: 0;
}

/* Breadcrumb moderno */
.breadcrumb-modern {
    background: rgba(255,255,255,0.15);
    backdrop-filter: blur(10px);
    border-radius: 50px;
    padding: 0.6rem 1.2rem !important;
    display: inline-flex;
    margin-bottom: 1rem;
}

.breadcrumb-modern .breadcrumb-item a {
    color: rgba(255,255,255,0.8);
    text-decoration: none;
    transition: all 0.3s ease;
}

.breadcrumb-modern .breadcrumb-item a:hover {
    color: #fff;
}

.breadcrumb-modern .breadcrumb-item.active {
    color: #fff;
    font-weight: 500;
}

.breadcrumb-modern .breadcrumb-item + .breadcrumb-item::before {
    color: rgba(255,255,255,0.5);
}

/* Cards principales */
.cafeteria-main-content {
    position: relative;
    z-index: 2;
    padding-top: 1rem;
}

.modern-card {
    background: #fff;
    border-radius: 20px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.08);
    overflow: hidden;
    transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    border: none;
    height: 100%;
}

.modern-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 50px rgba(0,0,0,0.12);
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

/* Personalización GLightbox */
.glightbox-clean .gslide-media {
    box-shadow: 0 8px 40px rgba(0, 0, 0, 0.4);
}

.glightbox-clean .gslide-image img {
    border-radius: 8px;
}

.glightbox-clean .gbtn {
    background: rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.1);
    transition: all 0.2s ease;
}

.glightbox-clean .gbtn:hover {
    background: rgba(255, 255, 255, 0.3);
    transform: scale(1.1);
}

.glightbox-clean .gslide-description {
    background: rgba(0, 0, 0, 0.8);
    backdrop-filter: blur(10px);
    padding: 12px 20px;
    border-radius: 8px 8px 0 0;
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

/* Cards de servicios en Splide */
.services-card {
    background: #fff;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 5px 20px rgba(0,0,0,0.06);
    transition: all 0.3s ease;
    border: 1px solid rgba(0,0,0,0.04);
}

.services-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 35px rgba(0,0,0,0.1);
}

.services-card img {
    width: 100%;
    height: 160px;
    object-fit: cover;
}

.services-card .card-body {
    padding: 1.2rem;
}

.services-card .card-title {
    font-size: 1rem;
    font-weight: 700;
    color: var(--cuarto);
    margin-bottom: 0.5rem;
}

.services-card .card-text {
    font-size: 0.85rem;
    color: var(--color-gris);
}

/* Splide personalizado - Sin botones de navegación */
#splide .splide__arrow {
    display: none !important;
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

.rating-summary {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.rating-big {
    font-size: 2.5rem;
    font-weight: 800;
    color: var(--cuarto);
    line-height: 1;
}

.rating-details {
    display: flex;
    flex-direction: column;
}

.stars-display {
    color: var(--color-amarillo);
    font-size: 1.2rem;
    letter-spacing: 2px;
}

.rating-count {
    font-size: 0.85rem;
    color: var(--color-gris);
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

.comment-item {
    display: flex;
    gap: 1rem;
    padding: 1.5rem 0;
    border-bottom: 1px solid rgba(0,0,0,0.05);
    transition: all 0.3s ease;
}

.comment-item:hover {
    background: var(--plantilla-claro);
    margin: 0 -1.5rem;
    padding-left: 1.5rem;
    padding-right: 1.5rem;
}

.comment-item:last-child {
    border-bottom: none;
}

.comment-avatar {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--principal) 0%, var(--secundario) 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-weight: 700;
    font-size: 1.2rem;
    flex-shrink: 0;
}

.comment-content {
    flex: 1;
}

.comment-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.5rem;
}

.comment-author {
    font-weight: 700;
    color: var(--cuarto);
}

.comment-date {
    font-size: 0.8rem;
    color: var(--color-gris);
}

.comment-text {
    color: var(--color-texto);
    line-height: 1.6;
    margin: 0;
}

/* Paginación moderna */
.pagination-modern {
    display: flex;
    justify-content: center;
    gap: 0.5rem;
    margin-top: 1.5rem;
}

.pagination-modern button {
    width: 40px;
    height: 40px;
    border: none;
    background: var(--bg-body);
    border-radius: 10px;
    color: var(--color-texto);
    font-weight: 600;
    transition: all 0.3s ease;
}

.pagination-modern button:hover,
.pagination-modern button.active {
    background: var(--principal);
    color: #fff;
}


/* Responsive */
@media (max-width: 768px) {
    .cafeteria-hero {
        padding: 1.5rem 1rem 2.5rem;
        border-radius: 0 0 30px 30px;
    }
    
    .cafeteria-hero h1 {
        font-size: 1.6rem;
    }
    
    /* Galería responsive */
    .gallery-grid {
        height: 300px;
        border-radius: 12px;
        gap: 4px;
    }
    
    /* En móvil, siempre mostrar layout simple */
    .gallery-grid.layout-3,
    .gallery-grid.layout-4,
    .gallery-grid.layout-5plus {
        grid-template-columns: 1fr 1fr;
        grid-template-rows: 1fr 1fr;
    }
    
    .gallery-grid.layout-3 .gallery-item:first-child,
    .gallery-grid.layout-4 .gallery-item:first-child,
    .gallery-grid.layout-5plus .gallery-item:first-child {
        grid-row: auto;
    }
    
    .gallery-counter {
        font-size: 12px;
        padding: 6px 12px;
        top: 12px;
        left: 12px;
    }
    
    .gallery-view-all {
        font-size: 13px;
        padding: 8px 16px;
        bottom: 12px;
        right: 12px;
    }
    
    .info-card {
        padding: 1.5rem;
    }
    
    .action-buttons {
        flex-direction: column;
    }
    
    .btn-action {
        min-width: auto;
    }
    
    .reviews-header {
        flex-direction: column;
        text-align: center;
    }
    
    .rating-summary {
        justify-content: center;
    }
}
</style>

<input type="hidden" id="id_cafeteria" idCafeteria="<?= $action[1] ?>">

<!-- Hero Header -->
<div class="cafeteria-hero" <?= $hidden ?>>
    <div class="container cafeteria-hero-content">
        <!-- Breadcrumb moderno -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-modern mb-3">
                <li class="breadcrumb-item">
                    <a href="<?= $url . 'cafeterias_lista' ?>">
                        <i class="fas fa-store me-1"></i> Cafeterías
                    </a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Detalles</li>
            </ol>
        </nav>
        
        <!-- Título dinámico -->
        <h1 id="titulo_cafeteria_ver"></h1>
        <p id="descripcion" class="mb-0"></p>
    </div>
</div>

<!-- Contenido Principal -->
<div class="container cafeteria-main-content" <?= $hidden ?>>
    <div class="row g-4">
        <!-- Galería de Imágenes Profesional -->
        <div class="col-lg-6">
            <div class="modern-card" style="padding: 0; overflow: visible;">
                <div id="galleryContainer">
                    <!-- La galería se genera aquí -->
                </div>
            </div>
        </div>

        <!-- Información General -->
        <div class="col-lg-6">
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
                        <p class="info-value" id="info1"></p>
                    </div>
                </div>

                <div class="info-item">
                    <div class="info-icon">
                        <i class="fas fa-phone-alt"></i>
                    </div>
                    <div class="info-content">
                        <div class="info-label">Teléfono</div>
                        <p class="info-value" id="info2"></p>
                        <span id="copy-feedback-tel" class="copy-feedback">
                            <i class="fas fa-check-circle"></i> ¡Copiado!
                        </span>
                    </div>
                </div>

                <div class="info-item">
                    <div class="info-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="info-content">
                        <div class="info-label">Horario de Atención</div>
                        <p class="info-value" id="info3"></p>
                        <span id="copy-feedback-email" class="copy-feedback">
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
                        <p class="info-value" id="info4"></p>
                    </div>
                </div>

                <div class="action-buttons">
                    <button class="btn-action btn-location ir_googlemaps">
                        <i class="fas fa-location-arrow"></i>
                        Ir a Ubicación
                    </button>
                    <a class="btn-action btn-menu-link" href="<?=$url?>cafeterias_menu/<?= $action[1] ?>">
                        <i class="fas fa-utensils"></i>
                        Ver Menú
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Sección de Servicios -->
    <div class="services-section">
        <div class="section-header">
            <i class="fas fa-star"></i>
            <h3>Servicios</h3>
        </div>
        
        <div id="splide" class="splide">
            <div class="splide__track">
                <ul class="splide__list" id="carousel_servicios">
                    <!-- Los servicios se cargan dinámicamente -->
                </ul>
            </div>
        </div>
    </div>

    <!-- Sección de Reseñas -->
    <div class="reviews-section comentarios">
        <div class="reviews-header">
            <div class="reviews-title">
                <i class="fas fa-comments"></i>
                <h3>Reseñas de Clientes</h3>
            </div>
            
            <!-- <div class="rating-summary coment-ocultar">
                <div class="rating-big">4.5</div>
                <div class="rating-details">
                    <div class="stars-display">★★★★☆</div>
                    <div class="rating-count"><span id="total_coment"></span> reseñas</div>
                </div>
            </div> -->
            
            <button type="button" class="btn-add-review" id="btn_agregar_comentario">
                <i class="fas fa-plus"></i>
                Agregar Reseña
            </button>
        </div>

        <!-- Área para agregar comentario -->
        <div class="comment-area comentario-area" id="comment-form-area">
            <div class="form-group">
                <label class="mb-2 fw-bold">
                    <i class="fas fa-pen me-2"></i>Tu opinión es importante
                </label>
                <textarea class="form-control" cols="30" rows="4" id="agregar_comentario" 
                    placeholder="Comparte tu experiencia en esta cafetería..."></textarea>
            </div>
            <div class="comment-actions">
                <button type="button" class="btn btn-light" onclick="toggleCommentArea()">
                    Cancelar
                </button>
                <button type="button" class="btn btn-success" id="btn_aceptar_comentario">
                    <i class="fas fa-paper-plane me-1"></i>
                    Publicar
                </button>
            </div>
        </div>

        <!-- Lista de comentarios -->
        <div class="coment-ocultar">
            <ul id="comentariosLista" class="comments-list lista-comentarios">
                <!-- Los comentarios se cargan dinámicamente -->
            </ul>

            <div id="paginacionComentarios" class="pagination-modern">
                <!-- Los controles de paginación se generan dinámicamente -->
            </div>
        </div>
    </div>
</div>

<script>
// ========== GALERÍA PROFESIONAL ESTILO GOOGLE MAPS/AIRBNB ==========

let galleryLightbox = null;

/**
 * Inicializa la galería con el layout apropiado según cantidad de imágenes
 * @param {Array} images - Array de URLs de imágenes
 */
function initGallery(images) {
    const container = document.getElementById('galleryContainer');
    
    if (!images || images.length === 0) {
        container.innerHTML = `
            <div class="gallery-empty">
                <i class="fas fa-image"></i>
                <p>No hay imágenes disponibles</p>
            </div>
        `;
        return;
    }
    
    // Determinar layout según cantidad de imágenes
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
        visibleImages = 5; // Mostrar solo 5 en el grid
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
    
    // Agregar imágenes visibles
    for (let i = 0; i < Math.min(visibleImages, images.length); i++) {
        const isLast = i === visibleImages - 1 && images.length > visibleImages;
        const hasMoreClass = isLast ? 'has-more' : '';
        const remainingCount = isLast ? `+${images.length - visibleImages}` : '';
        
        gridHTML += `
            <div class="gallery-item ${hasMoreClass}" 
                 ${isLast ? `data-remaining="${remainingCount}"` : ''} 
                 onclick="openGallery(${i})">
                <img src="${images[i]}" alt="Imagen ${i + 1}" loading="${i === 0 ? 'eager' : 'lazy'}">
            </div>
        `;
    }
    
    // Agregar botón "Ver todas" solo si hay más de 4 imágenes
    if (images.length > 4) {
        gridHTML += `
            <button class="gallery-view-all" onclick="openGallery(0)">
                <i class="fas fa-th"></i>
                <span>Ver todas las fotos</span>
            </button>
        `;
    }
    
    gridHTML += `</div>`;
    
    container.innerHTML = gridHTML;
    
    // Inicializar GLightbox
    initGLightbox(images);
}

/**
 * Inicializa GLightbox con todas las imágenes
 * @param {Array} images - Array de URLs de imágenes
 */
function initGLightbox(images) {
    // Destruir instancia anterior
    if (galleryLightbox) {
        try {
            galleryLightbox.destroy();
        } catch(e) {
            console.log('Limpiando instancia anterior');
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
        galleryLightbox = GLightbox({
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
 * Abre la galería en un índice específico
 * @param {number} index - Índice de la imagen a mostrar
 */
function openGallery(index = 0) {
    if (galleryLightbox) {
        galleryLightbox.openAt(index);
    }
}

// ========== COMENTARIOS ==========
function toggleCommentArea() {
    const area = document.getElementById('comment-form-area');
    area.classList.toggle('show');
}

// Activar toggle con el botón de agregar reseña
document.addEventListener('DOMContentLoaded', function() {
    const btnAgregar = document.getElementById('btn_agregar_comentario');
    if (btnAgregar) {
        btnAgregar.addEventListener('click', function() {
            toggleCommentArea();
        });
    }
});
</script>
