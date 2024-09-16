<style>
    .header {
        background-color: #FFD3B6;
        /* Color oscuro para el encabezado */
        color: black;
        padding: 20px;
        text-align: center;
    }

    .profile-image {
        border-radius: 8px;
        width: 100%;
        height: auto;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        margin-bottom: 20px;
    }

    .info-section {
        margin-top: 20px;
    }

    .btn-custom {
        background-color: #5a9;
        /* Color personalizado para los botones */
        color: white;
        border-radius: 5px;
    }

    .btn-custom:hover {
        background-color: #468;
        /* Color de hover */
    }

    .star-rating {
        color: #ffc107;
    }

    /* Mantén una relación de aspecto fija para las imágenes del carrusel */
    .carousel-item {
        height: 400px;
        /* Ajusta la altura según tus necesidades */
    }

    .carousel-item img {
        object-fit: cover;
        /* Cubre el contenedor sin distorsionar la imagen */
        height: 100%;
        /* Asegura que la imagen ocupe todo el contenedor */
        width: 100%;
    }

    /* Asegurar que las imágenes llenen el contenedor verticalmente */
    .carousel-item img {
        width: auto;
        height: 100%;
        object-fit: cover;
        /* Asegura que la imagen cubra todo el contenedor sin perder proporción */
        display: block;
        margin: auto;
    }

    /* css para los servicios contenedor */

    #iconCarousel .carousel-item {
        max-height: 150px;
        /* Limita la altura del carrusel */
        overflow-x: hidden;
        /* Oculta el contenido que desborda horizontalmente */
    }

    .icon-item {
        padding-top: 10px;
        flex: 0 0 auto;
        /* Asegura que los íconos no se reduzcan más allá de su tamaño mínimo */
    }

    #iconCarousel img {
        width: auto;
        height: 80px;
    }

    /* CSS para el modal del carousel de imagenes */
    /* Ajustar el modal para que tenga un tamaño consistente */
    #carouselModal .modal-dialog {
        max-width: 80vw;
        /* Ajusta el ancho máximo del modal según tu preferencia */
        max-height: 80vh;
        /* Ajusta la altura máxima del modal */
        width: 100%;
        height: 100%;
        margin: auto;
    }

    #carouselModal .modal-content {
        width: 100%;
        height: 100%;
    }

    #carouselModal .modal-body {
        padding: 0;
        height: 100%;
        /* Mantén el contenido del modal lleno */
    }

    /* Asegurar que las imágenes llenen el contenedor sin cambios de tamaño */
    .carousel-item-modal img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        /* Ajusta la imagen para que se mantenga contenida y no se recorte */
    }
</style>
<!-- Encabezado -->
<div class="header">
    <h1 id="titulo_cafeteria_ver">Nombre de la Cafetería</h1>
    <p id="descripcion">Una breve descripción de la cafetería y su ambiente acogedor.</p>
</div>
<div class="container my-4">
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
        <div class="col-md-6 d-flex mt-3">
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
                    <p id="info1"><strong>Dirección:</strong> Calle Principal 123, Ciudad</p>
                    <p id="info2"><strong>Teléfono:</strong> (123) 456-7890</p>
                    <p id="info3"><strong>Horario:</strong> Lunes - Viernes: 7:00 AM - 8:00 PM</p>
                    <p id="info4"><strong>Servicios:</strong> WiFi, Pet-Friendly, Asientos al Aire Libre</p>
                    <button class="btn btn-custom con-icono btn-ubicacion mt-3">Ver ubicación</button>
                    <button class="btn btn-outline-secondary mt-3">Ver menú</button>
                </div>
            </div>
        </div>
        <div class="col-md-12 mt-3">
            <h3 class="">Servicios</h3>

            <div class="caja">
                <div class="col-md-12 mt-3">
                    <div class="caja">
                        <!-- Carrusel de Íconos y Descripciones (delgado y ajustable) -->
                        <div id="iconCarousel" class="carousel slide" data-bs-ride="carousel" style="height: 150px;">
                            <div class="carousel-inner">
                                <!-- Elemento Activo del Carrusel -->
                                <div class="carousel-item active">
                                    <div class="flex-wrap d-flex justify-content-center align-items-center" style="height: 150px;">

                                        <div class="icon-item d-flex flex-column align-items-center me-5">
                                            <img src="<?= $url ?>/views/assets/css/img/iconos/icono_wifi.png" alt="Coffee To-Go" class="iconos">
                                            <p class="">Wi-Fi</p>
                                        </div>

                                        <div class="icon-item d-flex flex-column align-items-center me-5">
                                            <img src="<?= $url ?>/views/assets/css/img/iconos/icono_perro.png" alt="Breakfast" class="iconos">
                                            <p class="">Pet-Friendly</p>
                                        </div>
                                        <div class="icon-item d-flex flex-column align-items-center me-5">
                                            <img src="<?= $url ?>/views/assets/css/img/iconos/icono_tarjeta.png" alt="Co-Working Space" class="iconos">
                                            <p class="">Pago con tarjeta</p>
                                        </div>
                                        <div class="icon-item d-flex flex-column align-items-center me-5">
                                            <img src="<?= $url ?>/views/assets/css/img/iconos/icono_carro.png" alt="Private Events" class="iconos">
                                            <p class="">Drive Thru</p>
                                        </div>
                                        <div class="icon-item d-flex flex-column align-items-center me-5">
                                            <img src="<?= $url ?>/views/assets/css/img/iconos/icono_celular.png" alt="Coffee Beans for Sale" class="iconos">
                                            <p class="">Pedido por teléfono</p>
                                        </div>
                                        <div class="icon-item d-flex flex-column align-items-center me-5">
                                            <img src="<?= $url ?>/views/assets/css/img/iconos/icono_pastel.png" alt="Coffee Beans for Sale" class="iconos">
                                            <p class="">Postres</p>
                                        </div>
                                        <div class="icon-item d-flex flex-column align-items-center me-5">
                                            <img src="<?= $url ?>/views/assets/css/img/iconos/icono_desayuno.png" alt="Coffee Beans for Sale" class="iconos">
                                            <p class="">Desayunos</p>
                                        </div>
                                        <div class="icon-item d-flex flex-column align-items-center me-5">
                                            <img src="<?= $url ?>/views/assets/css/img/iconos/icono_sandwich.png" alt="Coffee Beans for Sale" class="iconos">
                                            <p class="">Comidas</p>
                                        </div>
                                        <div class="icon-item d-flex flex-column align-items-center me-5">
                                            <img src="<?= $url ?>/views/assets/css/img/iconos/icono_cafe1.png" alt="Coffee Beans for Sale" class="iconos">
                                            <p class="">Café en grano</p>
                                        </div>
                                        <div class="icon-item d-flex flex-column align-items-center me-5">
                                            <img src="<?= $url ?>/views/assets/css/img/iconos/icono_cafe2.png" alt="Coffee Beans for Sale" class="iconos">
                                            <p class="">Café molido</p>
                                        </div>
                                    </div>
                                </div>
                                <!-- Más elementos del carrusel si es necesario -->
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CSS adicional para el carrusel -->
                <style>

                </style>

            </div>
        </div>
    </div>


    <!-- Sección de Reseñas -->
    <div class="row mt-5">
        <div class="col-12">
            <h3>Reseñas de Clientes</h3>
            <div class="d-flex align-items-center">
                <div class="star-rating">
                    ★★★★☆
                </div>
                <p class="ml-2">4.5 de 5 estrellas (300 reseñas)</p>
            </div>
            <div class="card mt-3">
                <div class="card-body">
                    <h5 class="card-title">Juan Pérez</h5>
                    <p class="card-text">Un lugar increíble con un ambiente acogedor y un excelente café. ¡Muy recomendado!</p>
                </div>
            </div>
            <div class="card mt-3">
                <div class="card-body">
                    <h5 class="card-title">Maria García</h5>
                    <p class="card-text">Me encanta venir aquí para trabajar y disfrutar de su delicioso pastel de zanahoria.</p>
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