<nav class="navbar navbar-expand-lg fixed-top navbar-dark bg-plantilla" aria-label="Main navigation">
  <div class="container-fluid">
    <a class="navbar-brand nav-btn" href="<?= $url ?>"><img src="<?= $url ?>views/assets/img/logo_blanco.png" alt="Logo" style="width: 70px;"></a>
    <button class="navbar-toggler p-0 border-0" type="button" id="navbarSideCollapse" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="navbar-collapse offcanvas-collapse" id="navbarsExampleDefault">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <!-- <?php if ($_SESSION['nivel'] == 'Administrador') {
        ?>
          <li class="nav-item">
            <a class="nav-btn nav-item me-2" aria-current="page" href="<?= $url . 'dashboard'; ?>">
              <span style="color:white !important">Dashboard</span>
            </a>
          </li>
        <?php
        } ?> -->
        <?php foreach (GeneralController::obtenerModulosNivelController() as $area): ?>
          <?php if ($area['id'] == 0): ?>
            <?php foreach ($area['modulos'] as $modulo):
              $nombre = ($modulo['nombre'] == 'Cafeterías') ? 'Mis cafeterías' : $modulo['nombre'];
            ?>
              <li class="nav-item">
                <a class="nav-btn nav-item me-2" href="<?= $url . $modulo['ruta'] ?>">
                  <span style="color:white !important"><?= $nombre; ?></span>
                </a>
              </li>
            <?php endforeach; ?>
          <?php else: ?>
            <li class="nav-item dropdown">
              <a
                class="nav-btn nav-item dropdown-toggle me-2"
                id="area<?= $area['id']; ?>"
                data-bs-toggle="dropdown"
                aria-expanded="false">
                <span style="color:white !important"><?= $area['nombre']; ?></span>
              </a>
              <ul class="dropdown-menu">
                <?php foreach ($area['modulos'] as $modulo): ?>
                  <li>
                    <a
                      class="nav-btn dropdown-item nav-item"
                      href="<?= $url . $modulo['ruta']; ?>">
                      <?= $modulo['nombre']; ?>
                    </a>
                  </li>
                <?php endforeach; ?>
              </ul>
            </li>
          <?php endif; ?>
        <?php endforeach; ?>
      </ul>

      <div class="d-flex align-items-center">
        <p class="letras-blancas mb-0">
          <i class="fas fa-user me-1"></i>
          Hola, <?= $_SESSION['nombre_completo']; ?>
        </p>
        <a class="btn-sesion ms-3" href="<?= $url ?>salir">
          Cerrar sesión
        </a>
      </div>
    </div>
  </div>
</nav>