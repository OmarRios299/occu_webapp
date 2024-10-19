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

  .fondo {
    background-color: rgba(0, 0, 0, 0.6);
    /* Fondo oscuro semi-transparente */
    z-index: 1;
  }
</style>
<div id="myCarousel" class="carousel slide mb-1" data-bs-ride="carousel">
  <div class="carousel-inner">
    <?php
    $i = 0;
    foreach (PaginaInicialController::obtenerCarouselController() as $item) {
      ++$i;
    ?>
      <div class="carousel-item <?= ($i == 1) ? 'active' : '' ?>">
        <img src="<?= $url . $item['imagen'] ?>" class="d-block w-100" alt="Slide <?= $i ?>">
        <div class="container">
          <div class="carousel-caption">
            <h1><?= $item['titulo'] ?></h1>
            <p><?= $item['descripcion'] ?></p>
            <p><a class="btn btn-lg btn-primary" href="<?= $item['enlace'] ?>"><?= $item['nombre_enlace'] ?></a></p>
          </div>
        </div>
      </div>
    <?php } ?>
  </div>

  <?php if ($i > 1) { ?>
    <div class="carousel-indicators">
      <?php for ($btn = 0; $btn < $i; $btn++) { ?>
        <button type="button" data-bs-target="#myCarousel" data-bs-slide-to="<?= $btn ?>"
          class="<?= $btn === 0 ? 'active' : '' ?>"
          aria-current="<?= $btn === 0 ? 'true' : 'false' ?>"
          aria-label="Slide <?= $btn + 1 ?>"></button>
      <?php } ?>
    </div>

    <button class="carousel-control-prev" type="button" data-bs-target="#myCarousel" data-bs-slide="prev">
      <span class="carousel-control-prev-icon" aria-hidden="true"></span>
      <span class="visually-hidden">Previous</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#myCarousel" data-bs-slide="next">
      <span class="carousel-control-next-icon" aria-hidden="true"></span>
      <span class="visually-hidden">Next</span>
    </button>
  <?php } ?>

</div>


<div class="row row-cols-1 row-cols-lg-3 align-items-stretch g-4 py-5">
  <?php
  foreach (PaginaInicialController::obtenerCardsController() as $item) {
  ?>
  <div class="col" onclick="window.location.href='<?= $item['enlace'] ?>';">
      <div class="card card-cover h-100 overflow-hidden text-bg-dark rounded-4 shadow-lg" style="background-image: url('<?= $url . $item['imagen'] ?>');">
        <div class="d-flex flex-column h-100 p-5 pb-3 text-white text-shadow-1 fondo">
          <h3 class="pt-5 mt-5 mb-4 display-6 lh-1 fw-bold"><?= $item['descripcion'] ?></h3>
          <ul class="d-flex list-unstyled mt-auto">
            <li class="me-auto">
              <img src="'<?= $url ?>'views/assets/img/logo_1.png" alt="" width="32" height="32" class="rounded-circle border border-white">
            </li>
            <li class="d-flex align-items-center me-3">
              <svg class="bi me-2" width="1em" height="1em">
                <use xlink:href="#geo-fill" />
              </svg>
              <small><?= $item['titulo'] ?></small>
            </li>
            <li class="d-flex align-items-center">
              <svg class="bi me-2" width="1em" height="1em">
                <use xlink:href="#calendar3" />
              </svg>
              <small></small>
            </li>
          </ul>
        </div>
      </div>
    </div>
  <?php } ?>
</div>