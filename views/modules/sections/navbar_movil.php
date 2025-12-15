<!-- NAV DESKTOP (lg+) -->
<nav class="navbar fixed-top bg-plantilla desktop-nav d-none d-lg-flex justify-content-between align-items-center">
  <div class="d-flex align-items-center">
    <button href="<?= $url ?>" class="nav-btn">
      <span class="ms-1"><img src="<?= $url ?>views/assets/img/logo_blanco.png" alt="Logo" style="width: 70px;"></span>
    </button>
    <a href="<?= $url ?>cafeterias_lista" class="nav-btn">
      <i class="fas fa-coffee"></i><span class="ms-1">Cafeterías</span>
    </a>
    <a href="<?= $url ?>cafeterias_mapa" class="nav-btn">
      <i class="fas fa-map-marker-alt"></i><span class="ms-1">Ubicaciones</span>
    </a>
    <!-- <a href="<?= $url ?>perfil" class="nav-btn">
      <i class="fas fa-user"></i><span class="ms-1">Mi perfil</span>
    </a> -->
  </div>

  <div class="btn-group align-items-center" role="group">

    <!-- BOTÓN DE USUARIO -->
    <button class="nav-btn letras-blancas mb-0 dropdown-toggle d-flex align-items-center"
      data-bs-toggle="dropdown" aria-expanded="false" style="border: none;">
      <i class="fas fa-user me-2"></i>
      <span><?= $_SESSION['nombre_completo']; ?></span>
    </button>

    <!-- MENÚ DE USUARIO -->
    <ul class="dropdown-menu dropdown-menu-end shadow">

      <li>
        <a class="dropdown-item d-flex align-items-center text-danger fw-semibold"
          href="<?= $url ?>mis_pedidos">
          <i class="fas fa-list me-2"></i> Mis pedidos
        </a>
      </li>
      <li>
        <hr>
      </li>
      <li>
        <a class="dropdown-item d-flex align-items-center text-danger fw-semibold"
          href="<?= $url ?>salir">
          <i class="fas fa-sign-out-alt me-2"></i> Cerrar sesión
        </a>
      </li>
    </ul>

    <!-- BOTÓN DE CARRITO -->
    <a id="ver-carrito" class="ms-3 me-3 position-relative text-white nav-btn" style="cursor:pointer;">
      <i class="fas fa-shopping-cart fs-4"></i>

      <!-- SI QUIERES UN CONTADOR DE PRODUCTOS -->
      <span id=""
        class="contador-carrito position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
        style="font-size: 0.7rem; display:none;">
      </span>
    </a>

  </div>


</nav>

<!-- NAV MÓVIL TOP (logo + hide on scroll) -->
<div id="mobileLogoNav"
  class="mobile-logo-nav d-flex justify-content-between align-items-center d-lg-none hide-on-scroll">

  <!-- Columna izquierda vacía para balancear -->
  <div style="width:40px;"></div>

  <!-- Logo centrado -->
  <a href="<?= $url ?>cafeterias_lista" class="text-center flex-grow-1">
    <img src="<?= $url ?>views/assets/img/logo_blanco.png" alt="Logo">
  </a>

  <!-- Carrito a la derecha -->
  <a id="ver-carrito" class="me-3 position-relative text-white nav-btn" style="cursor:pointer;">
    <i class="fas fa-shopping-cart fs-4"></i>
    <span
      class="contador-carrito position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
      style="font-size: 0.7rem; display:none;">
    </span>
  </a>

</div>



<!-- NAV MÓVIL BOTTOM -->
<nav class="mobile-bottom-nav d-lg-none bg-plantilla text-white">
  <div class="bottom-nav-inner d-flex">
    <a href="<?= $url ?>cafeterias_lista" class="nav-item">
      <i class="fas fa-home d-block"></i>
      <small>Inicio</small>
    </a>
    <a href="<?= $url ?>cafeterias_mapa" class="nav-item">
      <i class="fas fa-map-marker-alt d-block"></i>
      <small>Ubicaciones</small>
    </a>
    <div class="dropup nav-item">
      <a data-bs-toggle="dropdown" aria-expanded="false">
        <i class="fas fa-user d-block text-white"></i>
        <small class="text-white">Cuenta</small>
      </a>
      <ul class="dropdown-menu dropdown-menu-end shadow">
        <li>
          <a class="dropdown-item d-flex align-items-center text-danger fw-semibold"
            href="<?= $url ?>salir">
            <i class="fas fa-sign-out-alt me-2"></i> Cerrar sesión
          </a>
        </li>
        <li>
          <hr>
        </li>
        <li>
          <a class="dropdown-item d-flex align-items-center text-danger fw-semibold"
            href="<?= $url ?>mis_pedidos">
            <i class="fas fa-list me-2"></i> Mis pedidos
          </a>
        </li>
      </ul>
    </div>
  </div>
</nav>