<style>
    /* Estilos para el módulo de imágenes mejorado */
    .form-section {
        background: #fff;
        border-radius: 20px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        padding: 2rem;
        margin-bottom: 2rem;
        transition: all 0.3s ease;
    }

    .form-section:hover {
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
    }

    .section-header {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 1rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid var(--bg-body);
    }

    .section-header-icon {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, var(--principal) 0%, #e65a4a 100%);
        color: white;
        font-size: 1.5rem;
    }

    .section-header-content h3 {
        margin: 0;
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--cuarto);
    }

    .section-header-content p {
        margin: 0.25rem 0 0 0;
        font-size: 0.9rem;
        color: #666;
    }

    /* Área de drag & drop */
    .drop-zone {
        border: 3px dashed #ddd;
        border-radius: 20px;
        padding: 1rem 1rem;
        text-align: center;
        background: #f8f9fa;
        transition: all 0.3s ease;
        cursor: pointer;
        position: relative;
    }

    .drop-zone:hover,
    .drop-zone.drag-over {
        border-color: var(--principal);
        background: #fff;
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    .drop-zone-content {
        pointer-events: none;
    }

    .drop-zone-icon {
        font-size: 4rem;
        color: var(--principal);
        margin-bottom: 1rem;
    }

    .drop-zone-text {
        font-size: 1.1rem;
        font-weight: 600;
        color: var(--cuarto);
        margin-bottom: 0.5rem;
    }

    .drop-zone-hint {
        font-size: 0.9rem;
        color: #999;
        margin-bottom: 1rem;
    }

    .drop-zone-buttons {
        margin-top: 1.5rem;
        pointer-events: all;
    }

    /* Grid de preview de imágenes */
    .images-preview-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        gap: 1.5rem;
        margin-top: 1rem;
    }

    .image-preview-card {
        position: relative;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        background: #fff;
        transition: all 0.3s ease;
    }

    .image-preview-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    }

    .image-preview-card img {
        width: 100%;
        height: 150px;
        object-fit: cover;
        display: block;
    }

    .image-preview-actions {
        position: absolute;
        top: 0.5rem;
        right: 0.5rem;
        display: flex;
        gap: 0.5rem;
    }

    .image-preview-remove {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: rgba(220, 53, 69, 0.9);
        border: none;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease;
        font-size: 1.2rem;
    }

    .image-preview-remove:hover {
        background: rgba(220, 53, 69, 1);
        transform: scale(1.1);
    }

    .image-preview-info {
        padding: 1rem;
        background: #fff;
    }

    .image-preview-name {
        font-size: 0.85rem;
        color: #666;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        margin-bottom: 0.5rem;
    }

    .image-preview-progress {
        width: 100%;
        height: 6px;
        background: #e9ecef;
        border-radius: 3px;
        overflow: hidden;
        margin-top: 0.5rem;
    }

    .image-preview-progress-bar {
        height: 100%;
        background: linear-gradient(90deg, var(--principal), #e65a4a);
        width: 0%;
        transition: width 0.3s ease;
    }

    .image-preview-status {
        font-size: 0.75rem;
        font-weight: 600;
        margin-top: 0.5rem;
    }

    .image-preview-status.uploading {
        color: #ffc107;
    }

    .image-preview-status.success {
        color: #28a745;
    }

    .image-preview-status.error {
        color: #dc3545;
    }

    /* Botón de subir */
    .btn-upload-all {
        margin-top: 2rem;
        padding: 1rem 2rem;
        font-size: 1.1rem;
        font-weight: 600;
        border-radius: 12px;
    }

    /* Grid de imágenes existentes */
    .images-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
        gap: 1.5rem;
        margin-top: 2rem;
    }

    .image-card {
        background: #fff;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
    }

    .image-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    }

    .image-card img {
        width: 100%;
        height: auto;
        max-height: 220px;
        object-fit: contain;
        display: block;
    }

    .image-card-body {
        padding: 1rem;
    }

    .image-card-actions {
        display: flex;
        gap: 0.5rem;
        align-items: center;
        justify-content: space-between;
    }

    /* Paginación */
    .images-pagination {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 0.5rem;
        margin-top: 2rem;
        padding: 1rem;
        flex-wrap: wrap;
    }

    .images-pagination-info {
        color: #666;
        font-size: 0.9rem;
        margin: 0 1rem;
    }

    .images-pagination-btn {
        padding: 0.5rem 1rem;
        border: 1px solid #ddd;
        background: #fff;
        color: #333;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.3s ease;
        font-size: 0.9rem;
    }

    .images-pagination-btn:hover:not(:disabled) {
        background: var(--principal);
        color: white;
        border-color: var(--principal);
    }

    .images-pagination-btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .images-pagination-btn.active {
        background: var(--principal);
        color: white;
        border-color: var(--principal);
    }

    /* Responsive */
    @media (max-width: 768px) {

        .images-preview-grid,
        .images-grid {
            grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
            gap: 1rem;
        }

        .form-section {
            padding: 1.5rem;
        }

        .drop-zone {
            padding: 2rem 1rem;
        }

        .section-header {
            flex-direction: column;
            text-align: center;
        }
    }
</style>

<div class="titulo-boton mt-4">
    <h1 class="titulo-modulo">Cafeterías</h1>
</div>

<div class="my-5">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb breadcrumb-chevron p-3 bg-body-tertiary rounded-3">
            <li class="breadcrumb-item">
                <a class="link-body-emphasis" href="<?= $url . 'cafeterias' ?>">
                    <i class="fas fa-home" style="color: black;"></i>
                    <span class="visually-hidden">Home</span>
                </a>
            </li>
            <li class="breadcrumb-item">
                <a class="link-body-emphasis fw-semibold text-decoration-none" href="<?= $url . 'cafeterias/' . $action[2] . '/editar' ?>">Mi cafetería</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
                Agregando imágenes
            </li>
        </ol>
    </nav>
</div>

<!-- Sección para agregar nuevas imágenes -->
<div class="form-section">
    <div class="section-header">
        <div class="section-header-icon" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%);">
            <i class="bi bi-cloud-upload-fill"></i>
        </div>
        <div class="section-header-content">
            <h3>Agregar Imágenes</h3>
            <p>Arrastra y suelta imágenes o selecciona múltiples archivos</p>
        </div>
    </div>

    <!-- Área de Drag & Drop -->
    <div class="drop-zone" id="dropZone">
        <input type="file"
            class="d-none"
            id="imagen_cafeteria"
            multiple
            accept="image/*">

        <div class="drop-zone-content">
            <div class="drop-zone-icon">
                <i class="bi bi-images"></i>
            </div>
            <div class="drop-zone-text">
                Arrastra y suelta tus imágenes aquí
            </div>
            <div class="drop-zone-hint">
                O haz clic para seleccionar archivos<br>
                Formatos: JPG, PNG, WEBP, GIF, BMP (Se convertirán automáticamente a WebP)
            </div>
            <div class="drop-zone-buttons">
                <button type="button" class="btn btn-primary btn-lg" onclick="document.getElementById('imagen_cafeteria').click();">
                    <i class="bi bi-folder2-open me-2"></i>
                    Seleccionar Imágenes
                </button>
            </div>
        </div>
    </div>

    <!-- Preview de imágenes seleccionadas (antes de subir) -->
    <div id="imagesPreviewContainer" style="display: none;">
        <div class="d-flex justify-content-between align-items-center mt-4 mb-3">
            <h5 class="mb-0">
                <i class="bi bi-image me-2"></i>
                Imágenes Seleccionadas (<span id="imagesCount">0</span>)
            </h5>
            <button type="button" class="btn btn-success btn-upload-all" id="btnUploadAll">
                <i class="bi bi-upload me-2"></i>
                Subir Todas
            </button>
        </div>
        <div class="images-preview-grid" id="imagesPreviewGrid">
            <!-- Las imágenes preview se agregan aquí dinámicamente -->
        </div>
    </div>
</div>

<!-- Sección de imágenes existentes -->
<div class="form-section">
    <div class="section-header">
        <div class="section-header-icon" style="background: linear-gradient(135deg, #6f42c1 0%, #e83e8c 100%);">
            <i class="bi bi-collection-fill"></i>
        </div>
        <div class="section-header-content">
            <h3>Imágenes Existentes</h3>
            <p>Gestiona las imágenes de tu cafetería</p>
        </div>
    </div>

    <input type="hidden" id="id_cafeteria" value="<?= $action[2] ?>">
    <input type="hidden" id="contador_items">

    <div class="images-grid" id="imagesGrid">
        <!-- Las imágenes existentes se cargarán aquí dinámicamente -->
        <div class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Cargando...</span>
            </div>
        </div>
    </div>

    <!-- Controles de paginación -->
    <div class="images-pagination" id="imagesPagination" style="display: none;">
        <!-- Los controles de paginación se agregarán aquí dinámicamente -->
    </div>
</div>
<?php if (isset($action[4]) && $action[4] == "nueva") { ?>
    <div class="col-12 d-flex justify-content-center">
        <div class="col-md-4">
            <a href="<?= $url ?>propietarios_menu_categorias/nueva" class="products-button-preview">Siguiente paso <i class="fas fa-arrow-right"></i> </a>
        </div>
    </div>
<?php } ?>