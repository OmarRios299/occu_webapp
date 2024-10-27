<?php
$hidden = 'style="display:none"';
if (isset($action[1])) {
    $hidden = '';
}
?>
<!-- Encabezado -->
<div class="titulo-cafeteria">
    <h1 id="titulo_cafeteria_ver"></h1>
    <p id="descripcion"></p>
</div>
<div class="container my-4" <?= $hidden ?>>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb breadcrumb-chevron p-3 bg-body-tertiary rounded-3">
            <li class="breadcrumb-item">
                <a class="link-body-emphasis" href="<?= $url . 'cafeterias_lista' ?>">
                    <i class="fas fa-home" style="color: black;"></i>
                    <span class="visually-hidden">Home</span>
                </a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">Información de cafetería</li>
        </ol>
    </nav>
</div>
<input type="hidden" id="id_cafeteria" idCafeteria="<?= $action[1] ?>">


<!-- Contenido principal -->
<div class="container">
    <div class="row d-flex align-items-stretch">
        <!-- Imagen destacada de la cafetería -->
        <div class="carousel-imgs col-md-6 d-flex mt-3">
            <div class="caja flex-fill">
                <div id="carouselExampleInterval" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-inner carousel_imagenes" id="">
                        <!-- <div class="carousel-item active" data-bs-interval="100000">
                            <img src="<?= $url ?>/views/assets/img/cafeterias_imagenes/.jpg" class="d-block w-100 img-fluid" alt="...">
                        </div>
                        <div class="carousel-item" data-bs-interval="2000">
                            <img src="<?= $url ?>/views/assets/img/cafeterias_imagenes/.jpg" class="d-block w-100 img-fluid" alt="...">
                        </div> -->
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleInterval" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Previous</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleInterval" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Next</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Información de la cafetería -->
        <div class="col-md-6 d-flex mt-3">
            <div class="caja flex-fill d-flex justify-content-center ">
                <div class="mt-3">
                    <h2>Información General</h2>
                    <p id="info1"><strong>Dirección:</strong></p>
                    <p id="info2"><strong>Teléfono:</strong></p>
                    <p id="copy-feedback-tel" style="display:none; color:green;">¡Teléfono copiado!</p>
                    <p id="info3"><strong>Horario:</strong></p>
                    <p id="copy-feedback-email" style="display:none; color:green;">¡Correo copiado!</p>
                    <p id="info4"><strong>Servicios:</strong></p>
                    <button class="btn btn-custom con-icono btn-ubicacion mt-3 ir_googlemaps">Ir a ubicación</button>
                    <button class="btn btn-outline-secondary mt-3">Ver menú</button>
                </div>
            </div>
        </div>
        <div class="col-md-12 mt-3">
            <h3 class="">Sevicios</h3>
            <div class="servicios">
                <div class="position-relative">
                    <button class="prev" onclick="scrollCarousel(-1)">&#10094;</button>
                    <div class="custom-carousel" id="carousel_servicios">
                        <!-- <div class="custom-carousel-item">
                            <div class="card card-cover h-100 overflow-hidden text-bg-dark rounded-4 shadow-lg" style="background-image: url('<?= $url ?>/views/assets/img/unsplash-photo-1.jpg');">
                                <div class="d-flex flex-column h-100 p-5 pb-3 text-white text-shadow-1">
                                    <h3 class="pt-5 mt-5 mb-4 display-6 lh-1 fw-bold">Título 1</h3>
                                </div>
                            </div>
                        </div> -->
                    </div>
                    <button class="next" onclick="scrollCarousel(1)">&#10095;</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Sección de Reseñas -->
    <div class="row mt-5 comentarios">
        <div class="col-md-10">
            <h3>Reseñas de Clientes</h3>
            <div class="d-flex align-items-center coment-ocultar">
                <div class="star-rating coment-ocultar">
                    ★★★★☆
                </div>
                <p class="ml-3 mt-3 coment-ocultar">4.5 de 5 estrellas (<span id="total_coment"></span> reseñas)</p>
            </div>
        </div>
        <div class="col-md-2 mt-3">
            <div class='text-end'>
                <button type="button" class="btn btn-icono btn-comentario" id="btn_agregar_comentario"></button>
            </div>
        </div>
        <div class="col-md-11 mb-3 comentario-area" style="display: none;">
            <div class="form-group">
                <label>Comentario:</label>
                <textarea class="form-control" cols="30" rows="5" id="agregar_comentario" placeholder="Escribe tu comentario"></textarea>
            </div>
        </div>
        <div class="col-md-1 mt-5 comentario-area" style="display: none;">
            <button type="button" class="btn btn-icono btn-mas" id="btn_aceptar_comentario"></button>
        </div>
        <div class="col-md-12 coment-ocultar">
            <div class="comentarios-container mt-1">
                <ul id="comentariosLista" class="lista-comentarios">
                    <!-- Los comentarios se cargarán aquí dinámicamente -->
                </ul>

                <div id="paginacionComentarios" class="paginacion">
                    <!-- Los controles de paginación se generarán aquí dinámicamente -->
                </div>
            </div>
        </div>


    </div>
</div>

<div class="modal fade" id="carouselModal" tabindex="-1" aria-labelledby="carouselModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="carouselModalLabel">Ver Imágenes</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="carouselExampleIntervalModal" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-inner carousel_imageness" id="">
                        <div class="carousel-item carousel-item-modal active" data-bs-interval="100000">
                            <img src="<?= $url ?>/views/assets/img/cafeterias_imagenes/1_imagen2.jpg" class="d-block w-100 img-fluid" alt="...">
                        </div>
                        <div class="carousel-item carousel-item-modal" data-bs-interval="2000">
                            <img src="<?= $url ?>/views/assets/img/cafeterias_imagenes/1_imagen1.jpg" class="d-block w-100 img-fluid" alt="...">
                        </div>
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleIntervalModal" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Previous</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleIntervalModal" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Next</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>