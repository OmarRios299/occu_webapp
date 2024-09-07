<style>
    .header {
        background-color: #343a40;
        /* Color oscuro para el encabezado */
        color: white;
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
</style>
<!-- Encabezado -->
<div class="header">
    <h1 id="titulo_cafeteria_ver">Nombre de la Cafetería</h1>
    <p id="descripcion">Una breve descripción de la cafetería y su ambiente acogedor.</p>
</div>
<div class="container my-5">
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
<div class="container mt-4">
    <div class="row d-flex align-items-stretch">
        <!-- Imagen destacada de la cafetería -->
        <div class="col-md-6 d-flex">
            <div class="caja flex-fill">
                <div id="carouselExampleInterval" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-inner">
                        <div class="carousel-item active" data-bs-interval="100000">
                            <img src="<?= $url ?>/views/assets/img/cafeterias_imagenes/1_imagen1.jpg" class="d-block w-100 img-fluid" alt="...">
                        </div>
                        <div class="carousel-item" data-bs-interval="2000">
                            <img src="<?= $url ?>/views/assets/img/cafeterias_imagenes/1_imagen2.jpg" class="d-block w-100 img-fluid" alt="...">
                        </div>
                        <div class="carousel-item">
                            <img src="<?= $url ?>/views/assets/img/cafeterias_imagenes/1_imagen3.jpg" class="d-block w-100 img-fluid" alt="...">
                        </div>
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
        <div class="col-md-6 d-flex">
            <div class="caja flex-fill d-flex justify-content-center ">
                <div class="mt-3">
                <h2>Información General</h2>
                <p><strong>Dirección:</strong> Calle Principal 123, Ciudad</p>
                <p><strong>Teléfono:</strong> (123) 456-7890</p>
                <p><strong>Horario:</strong> Lunes - Viernes: 7:00 AM - 8:00 PM</p>
                <p><strong>Servicios:</strong> WiFi, Pet-Friendly, Asientos al Aire Libre</p>
                <button class="btn btn-custom mt-3">Reservar una Mesa</button>
                <button class="btn btn-outline-secondary mt-3">Ordenar en Línea</button>
                </div>
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