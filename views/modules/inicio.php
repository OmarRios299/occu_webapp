<style>
    /* GLOBAL STYLES */
    body {
      padding-top: 3rem;
      padding-bottom: 3rem;
      color: rgb(var(--bs-tertiary-color-rgb));
    }

    /* CUSTOMIZE THE CAROUSEL */
    .carousel {
      margin-bottom: 4rem;
    }

    .carousel-caption {
      position: absolute;
      top: 75%;
      left: 50%;
      transform: translate(-50%, -50%);
      z-index: 10;
      width: 100%;
      max-width: 700px;
      padding: 0 1rem;
      text-align: center;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
    }

    .carousel-item {
      height: 100vh;
      min-height: 300px;
    }

    .carousel-item img {
      object-fit: cover;
      width: 100%;
      height: 100%;
    }

    @media (min-width: 768px) {
      .carousel-caption {
        bottom: auto;
      }
    }

    @media (min-width: 40em) {
      .carousel-caption p {
        margin-bottom: 1.25rem;
        font-size: 1.25rem;
        line-height: 1.4;
      }

      .featurette-heading {
        font-size: 50px;
      }
    }

    @media (min-width: 62em) {
      .featurette-heading {
        margin-top: 7rem;
      }
    }

    .fondo{
        background-color: rgba(0, 0, 0, 0.6);
    /* Fondo oscuro semi-transparente */
    z-index: 1;
    }
</style>
<div id="myCarousel" class="carousel slide mb-1" data-bs-ride="carousel">
    <div class="carousel-indicators">
        <button type="button" data-bs-target="#myCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
        <button type="button" data-bs-target="#myCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
        <button type="button" data-bs-target="#myCarousel" data-bs-slide-to="2" aria-label="Slide 3"></button>
    </div>
    <div class="carousel-inner">
        <div class="carousel-item active">
            <img src="<?=$url?>views/assets/img/imagen1.jpg" class="d-block w-100" alt="Slide 1">
            <div class="container">
                <div class="carousel-caption">
                    <h1>Una frase de ejemplo.</h1>
                    <p>Si deseas encontrar el mejor lugar para tomar un rico café.</p>
                    <p><a class="btn btn-lg btn-primary" href="#">Registrate</a></p>
                </div>
            </div>
        </div>
        <div class="carousel-item">
            <img src="<?=$url?>views/assets/img/imagen1.jpg" class="d-block w-100" alt="Slide 2">
            <div class="container">
                <div class="carousel-caption">
                    <h1>Una frase de ejemplo.</h1>
                    <p>Si deseas encontrar el mejor lugar para tomar un rico café.</p>
                    <p><a class="btn btn-lg btn-primary" href="#">Registrate</a></p>
                </div>
            </div>
        </div>
        <div class="carousel-item">
            <img src="<?=$url?>views/assets/img/imagen1.jpg" class="d-block w-100" alt="Slide 3">
            <div class="container">
                <div class="carousel-caption">
                    <h1>Una frase de ejemplo.</h1>
                    <p>Si deseas encontrar el mejor lugar para tomar un rico café.</p>
                    <p><a class="btn btn-lg btn-primary" href="#">Registrate</a></p>
                </div>
            </div>
        </div>
    </div>
    <button class="carousel-control-prev" type="button" data-bs-target="#myCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Previous</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#myCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Next</span>
    </button>
</div>

<div class="row row-cols-1 row-cols-lg-3 align-items-stretch g-4 py-5">
      <div class="col">
        <div class="card card-cover h-100 overflow-hidden text-bg-dark rounded-4 shadow-lg" style="background-image: url('<?=$url?>views/assets/img/cafe.jpg');">
          <div class="d-flex flex-column h-100 p-5 pb-3 text-white text-shadow-1 fondo">
            <h3 class="pt-5 mt-5 mb-4 display-6 lh-1 fw-bold">Encuentra las mejores cafeterías de la ciudad</h3>
            <ul class="d-flex list-unstyled mt-auto">
              <li class="me-auto">
                <img src="https://github.com/twbs.png" alt="Bootstrap" width="32" height="32" class="rounded-circle border border-white">
              </li>
              <li class="d-flex align-items-center me-3">
                <svg class="bi me-2" width="1em" height="1em"><use xlink:href="#geo-fill"/></svg>
                <small>Café</small>
              </li>
              <li class="d-flex align-items-center">
                <svg class="bi me-2" width="1em" height="1em"><use xlink:href="#calendar3"/></svg>
                <small>1</small>
              </li>
            </ul>
          </div>
        </div>
      </div>

      <div class="col">
        <div class="card card-cover h-100 overflow-hidden text-bg-dark rounded-4 shadow-lg" style="background-image: url('<?=$url?>views/assets/img/espresso.jpeg');">
          <div class="d-flex flex-column h-100 p-5 pb-3 text-white text-shadow-1 fondo">
            <h3 class="pt-5 mt-5 mb-4 display-6 lh-1 fw-bold">Preparados con los mejores ingredientes</h3>
            <ul class="d-flex list-unstyled mt-auto">
              <li class="me-auto">
                <img src="https://github.com/twbs.png" alt="Bootstrap" width="32" height="32" class="rounded-circle border border-white">
              </li>
              <li class="d-flex align-items-center me-3">
                <svg class="bi me-2" width="1em" height="1em"><use xlink:href="#geo-fill"/></svg>
                <small>Espresso</small>
              </li>
              <li class="d-flex align-items-center">
                <svg class="bi me-2" width="1em" height="1em"><use xlink:href="#calendar3"/></svg>
                <small>4</small>
              </li>
            </ul>
          </div>
        </div>
      </div>

      <div class="col">
        <div class="card card-cover h-100 overflow-hidden text-bg-dark rounded-4 shadow-lg" style="background-image: url('<?=$url?>views/assets/img/latte.jpg');">
          <div class="d-flex flex-column h-100 p-5 pb-3 text-shadow-1 fondo">
            <h3 class="pt-5 mt-5 mb-4 display-6 lh-1 fw-bold">Elaborados por baristas calificados</h3>
            <ul class="d-flex list-unstyled mt-auto">
              <li class="me-auto">
                <img src="https://github.com/twbs.png" alt="Bootstrap" width="32" height="32" class="rounded-circle border border-white">
              </li>
              <li class="d-flex align-items-center me-3">
                <svg class="bi me-2" width="1em" height="1em"><use xlink:href="#geo-fill"/></svg>
                <small>Latte</small>
              </li>
              <li class="d-flex align-items-center">
                <svg class="bi me-2" width="1em" height="1em"><use xlink:href="#calendar3"/></svg>
                <small>5</small>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>