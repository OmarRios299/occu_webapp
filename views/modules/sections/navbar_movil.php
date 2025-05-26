<!-- NAV DESKTOP (lg+) -->
<div class="navbar fixed-top bg-plantilla desktop-nav d-none d-lg-flex justify-content-between align-items-center px-3" style="height: 60px;">
  <div class="d-flex align-items-center">
    <a href="<?= $url ?>cafeterias_lista" class="nav-btn">
      <i class="fas fa-coffee"></i><span>Cafeterías</span>
    </a>
    <a href="<?= $url ?>cafeterias_mapa" class="nav-btn">
      <i class="fas fa-map-marker-alt"></i><span>Ubicaciones</span>
    </a>
    <a href="<?= $url ?>perfil" class="nav-btn">
      <i class="fas fa-user"></i><span>Mi perfil</span>
    </a>
  </div>
  <div class="user-group">
    <i class="fas fa-user"></i>
    <span>Hola, <?= $_SESSION['nombre_completo'] ?></span>
    <a href="<?= $url ?>salir" class="btn-sesion">Cerrar sesión</a>
  </div>
</div>

<!-- NAV MÓVIL TOP (logo + hide on scroll) -->
<div id="mobileLogoNav" class="mobile-logo-nav d-flex d-lg-none hide-on-scroll">
  <a href="<?= $url ?>cafeterias_lista">
    <img src="<?= $url ?>views/assets/img/logo_1.png" alt="Logo">
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
    <a href="<?= $url ?>perfil" class="nav-item">
      <i class="fas fa-user d-block"></i>
      <small>Mi perfil</small>
    </a>
  </div>
</nav>
