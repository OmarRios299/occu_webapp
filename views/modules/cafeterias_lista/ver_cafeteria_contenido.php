<?php
// Este archivo contiene solo el contenido de ver_cafeteria.php sin el hero header
// Se usa para mostrar en el offcanvas del mapa
// Los IDs tienen sufijo _mapa para evitar conflictos
?>

<style>
/* Estilos adaptados para offcanvas - mismos estilos que ver_cafeteria.php pero más compactos */
.cafeteria-content-offcanvas {
    padding: 0;
    max-width: 100%;
    overflow-x: hidden;
    height: auto;
    min-height: 100%;
    position: relative;
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

/* Asegurar que todo el contenido se adapte al ancho del offcanvas */
.cafeteria-content-offcanvas * {
    max-width: 100%;
    box-sizing: border-box;
}

.cafeteria-content-offcanvas img {
    max-width: 100%;
    height: auto;
}

.cafeteria-content-offcanvas .splide__list {
    display: flex;
}

.cafeteria-content-offcanvas .splide__slide {
    min-width: 0;
    flex-shrink: 0;
}
</style>

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
            <a class="btn-action btn-menu-link" href="#" id="link_menu_mapa" target="_blank" rel="noopener noreferrer">
                <i class="fas fa-utensils"></i>
                Ver Menú
            </a>
        </div>
    </div>

    <!-- Descripción -->
    <!-- <div class="modern-card" style="margin-bottom: 1.5rem;">
        <div class="info-card-header">
            <i class="fas fa-align-left"></i>
            <h2>Descripción</h2>
        </div>
        <p id="descripcion_mapa" style="color: #666; line-height: 1.6;"></p>
    </div> -->

    <!-- Sección de Servicios -->
    <div class="services-section">
        <div class="section-header">
            <i class="fas fa-star"></i>
            <h3>Servicios</h3>
        </div>
        
        <div id="splide_mapa" class="splide">
            <div class="splide__track">
                <ul class="splide__list" id="carousel_servicios_mapa">
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

<script>
// Función para toggle del área de comentarios en el mapa (ya no se usa, se maneja con eventos delegados)

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
        if (images.length > 4) {
            gridHTML += `
                <button class="gallery-view-all" onclick="openGalleryMapa(0)">
                    <i class="fas fa-th"></i>
                    <span>Ver todas las fotos</span>
                </button>
            `;
        }
        
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

