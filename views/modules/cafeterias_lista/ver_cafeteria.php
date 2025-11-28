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

/* ========== GALERÍA DE IMÁGENES MODERNA ========== */
.gallery-container {
    padding: 1rem;
}

/* Imagen Principal */
.main-image-wrapper {
    position: relative;
    border-radius: 16px;
    overflow: hidden;
    margin-bottom: 1rem;
    background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
    aspect-ratio: 16/10;
}

.main-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    cursor: zoom-in;
    transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
    opacity: 0;
    animation: fadeInImage 0.5s ease forwards;
}

@keyframes fadeInImage {
    from { opacity: 0; transform: scale(1.05); }
    to { opacity: 1; transform: scale(1); }
}

.main-image:hover {
    transform: scale(1.03);
}

/* Overlay con controles */
.image-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(
        to bottom,
        rgba(0,0,0,0.3) 0%,
        transparent 30%,
        transparent 70%,
        rgba(0,0,0,0.5) 100%
    );
    opacity: 0;
    transition: opacity 0.3s ease;
    pointer-events: none;
}

.main-image-wrapper:hover .image-overlay {
    opacity: 1;
}

/* Contador de imágenes */
.image-counter {
    position: absolute;
    top: 1rem;
    left: 1rem;
    background: rgba(0,0,0,0.6);
    backdrop-filter: blur(10px);
    color: #fff;
    padding: 0.5rem 1rem;
    border-radius: 30px;
    font-size: 0.85rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    z-index: 2;
}

.image-counter i {
    color: var(--principal);
}

/* Botón ampliar */
.btn-expand {
    position: absolute;
    top: 1rem;
    right: 1rem;
    width: 45px;
    height: 45px;
    background: rgba(255,255,255,0.95);
    border: none;
    border-radius: 12px;
    color: var(--cuarto);
    font-size: 1.1rem;
    cursor: pointer;
    z-index: 2;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
}

.btn-expand:hover {
    background: var(--principal);
    color: #fff;
    transform: scale(1.1);
}

/* Navegación de la imagen principal */
.nav-btn-gallery {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    width: 50px;
    height: 50px;
    background: rgba(255,255,255,0.95);
    border: none;
    border-radius: 50%;
    color: var(--cuarto);
    font-size: 1.2rem;
    cursor: pointer;
    z-index: 2;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
    box-shadow: 0 4px 20px rgba(0,0,0,0.15);
    opacity: 0;
}

.main-image-wrapper:hover .nav-btn-gallery {
    opacity: 1;
}

.nav-btn-gallery:hover {
    background: var(--principal);
    color: #fff;
    transform: translateY(-50%) scale(1.1);
}

.nav-btn-gallery.prev {
    left: 1rem;
}

.nav-btn-gallery.next {
    right: 1rem;
}

/* Contenedor de miniaturas */
.thumbnails-wrapper {
    position: relative;
    padding: 0 2.5rem;
}

.thumbnails-container {
    display: flex;
    gap: 0.75rem;
    overflow-x: auto;
    scroll-behavior: smooth;
    padding: 0.5rem 0;
    scrollbar-width: none;
    -ms-overflow-style: none;
}

.thumbnails-container::-webkit-scrollbar {
    display: none;
}

/* Miniaturas individuales */
.thumbnail-item {
    flex: 0 0 auto;
    width: 80px;
    height: 60px;
    border-radius: 10px;
    overflow: hidden;
    cursor: pointer;
    position: relative;
    transition: all 0.3s ease;
    border: 3px solid transparent;
}

.thumbnail-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.thumbnail-item:hover {
    transform: translateY(-3px);
}

.thumbnail-item:hover img {
    transform: scale(1.1);
}

.thumbnail-item.active {
    border-color: var(--principal);
    box-shadow: 0 5px 20px rgba(241,108,91,0.4);
}

.thumbnail-item::after {
    content: '';
    position: absolute;
    inset: 0;
    background: rgba(0,0,0,0.3);
    opacity: 1;
    transition: opacity 0.3s ease;
}

.thumbnail-item:hover::after,
.thumbnail-item.active::after {
    opacity: 0;
}

/* Botones de scroll para miniaturas */
.thumb-scroll-btn {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    width: 32px;
    height: 32px;
    background: #fff;
    border: 2px solid var(--bg-body);
    border-radius: 50%;
    color: var(--cuarto);
    font-size: 0.9rem;
    cursor: pointer;
    z-index: 2;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.thumb-scroll-btn:hover {
    background: var(--principal);
    border-color: var(--principal);
    color: #fff;
}

.thumb-scroll-btn.left {
    left: 0;
}

.thumb-scroll-btn.right {
    right: 0;
}

/* Indicador de más imágenes */
.more-images-indicator {
    position: absolute;
    bottom: 1rem;
    left: 50%;
    transform: translateX(-50%);
    background: rgba(255,255,255,0.95);
    padding: 0.5rem 1.2rem;
    border-radius: 30px;
    font-size: 0.8rem;
    font-weight: 600;
    color: var(--cuarto);
    display: flex;
    align-items: center;
    gap: 0.5rem;
    box-shadow: 0 4px 15px rgba(0,0,0,0.15);
    cursor: pointer;
    transition: all 0.3s ease;
    z-index: 2;
}

.more-images-indicator:hover {
    background: var(--principal);
    color: #fff;
}

/* Estado vacío */
.gallery-empty {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 300px;
    color: var(--color-gris);
    text-align: center;
}

.gallery-empty i {
    font-size: 4rem;
    margin-bottom: 1rem;
    opacity: 0.3;
}

.gallery-empty p {
    margin: 0;
    font-size: 1rem;
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

/* ========== MODAL DE GALERÍA ========== */
#carouselModal .modal-content {
    border-radius: 20px;
    overflow: hidden;
    border: none;
    background: #0a0a0a;
}

#carouselModal .modal-header {
    background: transparent;
    border: none;
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    z-index: 10;
    padding: 1.5rem;
}

#carouselModal .modal-title {
    color: #fff;
    font-weight: 700;
    text-shadow: 0 2px 10px rgba(0,0,0,0.5);
}

#carouselModal .btn-close {
    filter: brightness(0) invert(1);
    opacity: 0.8;
    transition: all 0.3s ease;
    background-color: rgba(255,255,255,0.1);
    border-radius: 50%;
    padding: 0.8rem;
}

#carouselModal .btn-close:hover {
    opacity: 1;
    transform: rotate(90deg);
}

#carouselModal .modal-body {
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 70vh;
}

#carouselModal .carousel {
    width: 100%;
}

#carouselModal .carousel-item {
    transition: transform 0.5s ease, opacity 0.5s ease;
}

#carouselModal .carousel-item img {
    max-height: 80vh;
    width: auto;
    max-width: 100%;
    object-fit: contain;
    margin: 0 auto;
    display: block;
}

#carouselModal .carousel-control-prev,
#carouselModal .carousel-control-next {
    width: 60px;
    height: 60px;
    background: rgba(255,255,255,0.1);
    border-radius: 50%;
    top: 50%;
    transform: translateY(-50%);
    opacity: 0.7;
    transition: all 0.3s ease;
}

#carouselModal .carousel-control-prev:hover,
#carouselModal .carousel-control-next:hover {
    background: var(--principal);
    opacity: 1;
}

#carouselModal .carousel-control-prev {
    left: 20px;
}

#carouselModal .carousel-control-next {
    right: 20px;
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
    .main-image-wrapper {
        aspect-ratio: 4/3;
    }
    
    .nav-btn-gallery {
        width: 40px;
        height: 40px;
        opacity: 1;
    }
    
    .thumbnail-item {
        width: 65px;
        height: 50px;
    }
    
    .image-counter {
        font-size: 0.75rem;
        padding: 0.4rem 0.8rem;
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
        <!-- Galería de Imágenes -->
        <div class="col-lg-6">
            <div class="modern-card gallery-container">
                <!-- Imagen Principal -->
                <div class="main-image-wrapper" id="mainImageWrapper">
                    <!-- Contador -->
                    <div class="image-counter">
                        <i class="fas fa-images"></i>
                        <span id="imageCounter">1 / 0</span>
                    </div>
                    
                    <!-- Botón expandir -->
                    <button class="btn-expand" onclick="openGalleryModal()" title="Ver en pantalla completa">
                        <i class="fas fa-expand"></i>
                    </button>
                    
                    <!-- Overlay -->
                    <div class="image-overlay"></div>
                    
                    <!-- Imagen -->
                    <img src="" alt="Imagen de la cafetería" class="main-image" id="mainGalleryImage">
                    
                    <!-- Navegación -->
                    <button class="nav-btn-gallery prev" onclick="changeMainImage(-1)">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <button class="nav-btn-gallery next" onclick="changeMainImage(1)">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
                
                <!-- Miniaturas -->
                <div class="thumbnails-wrapper">
                    <button class="thumb-scroll-btn left" onclick="scrollThumbnails(-1)">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    
                    <div class="thumbnails-container carousel_imagenes" id="thumbnailsContainer">
                        <!-- Las miniaturas se cargan dinámicamente -->
                    </div>
                    
                    <button class="thumb-scroll-btn right" onclick="scrollThumbnails(1)">
                        <i class="fas fa-chevron-right"></i>
                    </button>
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
            
            <div class="rating-summary coment-ocultar">
                <div class="rating-big">4.5</div>
                <div class="rating-details">
                    <div class="stars-display">★★★★☆</div>
                    <div class="rating-count"><span id="total_coment"></span> reseñas</div>
                </div>
            </div>
            
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

<!-- Modal para ver imágenes en pantalla completa -->
<div class="modal fade" id="carouselModal" tabindex="-1" aria-labelledby="carouselModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="carouselModalLabel">
                    <i class="fas fa-images me-2"></i>Galería
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <div id="carouselExampleIntervalModal" class="carousel slide" data-bs-ride="false">
                    <div class="carousel-inner carousel_imageness">
                        <!-- Las imágenes se cargan dinámicamente desde JavaScript -->
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleIntervalModal" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Anterior</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleIntervalModal" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Siguiente</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// ========== GALERÍA DE IMÁGENES ==========
let galleryImages = [];
let currentImageIndex = 0;

// Inicializar galería con imágenes
function initGallery(images) {
    galleryImages = images;
    if (images.length > 0) {
        currentImageIndex = 0;
        updateMainImage();
        renderThumbnails();
    }
}

// Actualizar imagen principal
function updateMainImage() {
    const mainImg = document.getElementById('mainGalleryImage');
    const counter = document.getElementById('imageCounter');
    
    if (galleryImages.length > 0 && mainImg) {
        // Efecto de transición
        mainImg.style.opacity = '0';
        mainImg.style.transform = 'scale(1.05)';
        
        setTimeout(() => {
            mainImg.src = galleryImages[currentImageIndex];
            mainImg.style.opacity = '1';
            mainImg.style.transform = 'scale(1)';
        }, 200);
        
        // Actualizar contador
        if (counter) {
            counter.textContent = `${currentImageIndex + 1} / ${galleryImages.length}`;
        }
        
        // Actualizar thumbnail activo
        updateActiveThumbnail();
    }
}

// Cambiar imagen principal
function changeMainImage(direction) {
    if (galleryImages.length === 0) return;
    
    currentImageIndex += direction;
    
    if (currentImageIndex >= galleryImages.length) {
        currentImageIndex = 0;
    } else if (currentImageIndex < 0) {
        currentImageIndex = galleryImages.length - 1;
    }
    
    updateMainImage();
    scrollToActiveThumbnail();
}

// Seleccionar imagen desde thumbnail
function selectImage(index) {
    currentImageIndex = index;
    updateMainImage();
}

// Renderizar miniaturas
function renderThumbnails() {
    const container = document.getElementById('thumbnailsContainer');
    if (!container) return;
    
    container.innerHTML = '';
    
    galleryImages.forEach((img, index) => {
        const thumb = document.createElement('div');
        thumb.className = `thumbnail-item ${index === 0 ? 'active' : ''}`;
        thumb.onclick = () => selectImage(index);
        thumb.innerHTML = `<img src="${img}" alt="Miniatura ${index + 1}">`;
        container.appendChild(thumb);
    });
}

// Actualizar thumbnail activo
function updateActiveThumbnail() {
    const thumbnails = document.querySelectorAll('.thumbnail-item');
    thumbnails.forEach((thumb, index) => {
        if (index === currentImageIndex) {
            thumb.classList.add('active');
        } else {
            thumb.classList.remove('active');
        }
    });
}

// Scroll a thumbnail activo
function scrollToActiveThumbnail() {
    const container = document.getElementById('thumbnailsContainer');
    const activeThumb = container?.querySelector('.thumbnail-item.active');
    
    if (activeThumb && container) {
        const containerRect = container.getBoundingClientRect();
        const thumbRect = activeThumb.getBoundingClientRect();
        
        if (thumbRect.left < containerRect.left || thumbRect.right > containerRect.right) {
            activeThumb.scrollIntoView({ behavior: 'smooth', inline: 'center', block: 'nearest' });
        }
    }
}

// Scroll de miniaturas
function scrollThumbnails(direction) {
    const container = document.getElementById('thumbnailsContainer');
    if (container) {
        const scrollAmount = 200;
        container.scrollBy({ left: direction * scrollAmount, behavior: 'smooth' });
    }
}

// Abrir modal de galería
function openGalleryModal() {
    const modal = new bootstrap.Modal(document.getElementById('carouselModal'));
    
    // Actualizar imagen del modal
    const modalContainer = document.querySelector('.carousel_imageness');
    if (modalContainer && galleryImages.length > 0) {
        modalContainer.innerHTML = '';
        galleryImages.forEach((img, index) => {
            const item = document.createElement('div');
            item.className = `carousel-item ${index === currentImageIndex ? 'active' : ''}`;
            item.innerHTML = `<img src="${img}" class="d-block w-100" alt="Imagen ${index + 1}">`;
            modalContainer.appendChild(item);
        });
    }
    
    modal.show();
}

// Navegación con teclado
document.addEventListener('keydown', function(e) {
    const modal = document.getElementById('carouselModal');
    const isModalOpen = modal?.classList.contains('show');
    
    if (!isModalOpen) {
        if (e.key === 'ArrowLeft') {
            changeMainImage(-1);
        } else if (e.key === 'ArrowRight') {
            changeMainImage(1);
        }
    }
});

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
