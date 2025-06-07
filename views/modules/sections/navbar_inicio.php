<!-- NAVBAR DESKTOP (pantallas ≥ lg) -->
<div class="navbar fixed-top bg-plantilla desktop-nav d-none d-lg-flex justify-content-between align-items-center px-3" style="height: 60px;">
  <div class="d-flex align-items-center">
    <a href="<?= $url ?>inicio" class="nav-btn">
      <span class="ms-1"><img src="<?= $url ?>views/assets/img/logo_blanco.png" alt="Logo" style="width: 70px;"></span>
    </a>
    <a href="<?= $url ?>registrarme" class="nav-btn">
      <i class="fas fa-user"></i><span class="ms-1">Registrarme</span>
    </a>
    <a href="<?= $url ?>cafeterias_mapa" class="nav-btn">
      <i class="fas fa-map-marker-alt"></i><span class="ms-1">Ubicaciones</span>
    </a>
    <a href="<?= $url ?>cafeterias_lista" class="nav-btn">
      <i class="fas fa-coffee"></i><span class="ms-1">Cafeterías</span>
    </a>
  </div>
  <div class="user-group text-white">
    <a class="btn btn-iniciar-sesion" href="<?= $url ?>login">Iniciar sesión</a>
  </div>
</div>

<!-- NAVBAR MÓVIL (logo) -->
<div id="mobileLogoNav" class="mobile-logo-nav d-flex d-lg-none hide-on-scroll mt-0">
  <a href="<?= $url ?>inicio">
    <img src="<?= $url ?>views/assets/img/logo_blanco.png" alt="Logo">
  </a>
</div>

<!-- MENÚ INFERIOR PARA MÓVILES -->


<nav class="mobile-bottom-nav d-lg-none bg-plantilla text-white">
    <div class="bottom-nav-inner d-flex">
        <a href="<?= $url ?>inicio" class="nav-item text-center">
            <i class="fas fa-home d-block"></i>
            <small>Inicio</small>
        </a>
        <a href="<?= $url ?>cafeterias_lista" class="nav-item text-center">
            <i class="fas fa-coffee d-block"></i>
            <small>Cafeterías</small>
        </a>
        <a href="<?= $url ?>login" class="nav-item text-center">
            <i class="fas fa-user d-block"></i>
            <small>Ingresar</small>
        </a>
    </div>
</nav>