<?php
include "menu_propietarios/modal_tamano_bebidas.php";
if ($action[0] == 'menu_propietarios' && !isset($action[1])) {
  $menu = MenuPropietariosController::obtenerMenuPropietarioController(true);
  $hidden = (MenuPropietariosController::cafeteriasPropietarioController() > 1) ? '' : 'display:none';
?>
  <div class="text-end" style="<?= $hidden ?>">
    <div class="dropdown">
      <a class="btn btn-secondary dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
        Actualizar menú
      </a>

      <ul class="dropdown-menu">
        <li><a class="dropdown-item" href="#" id="actualizarMenu" actualizar="menu">Actualizar menú en todas las sucursales</a></li>
        <li><a class="dropdown-item" href="#" id="actualizarPrecios" actualizar="precios">Actualizar solo precios</a></li>
      </ul>
    </div>
  </div>
  <div class="menu-cafeterias mt-3">
    <!-- Menú de Navegación -->
    <div class="nav-menu">
      <ul>
        <?php echo $menu['categorias'] ?>
      </ul>
    </div>
    <div class="product-list">
      <!-- Secciones de Categorías -->
      <div class="cafe-menu">
        <?php echo $menu['subcategorias'] ?>
      </div>
    </div>
  </div>
  <?php
} else if ($action[0] == 'menu_propietarios' && isset($action[1])) {
  $cafeteria = GeneralController::verificarCafeteriaContoller($action[1], $_SESSION['id']);
  if ($cafeteria) {
    $menu = MenuPropietariosController::obtenerMenuPropietarioController(true, $cafeteria['id']);
  ?>
    <div class="P ">
      <!-- Menú de Navegación -->
      <div class="nav-menu">
        <ul>
          <?php echo $menu['categorias'] ?>
        </ul>
      </div>
      <div class="product-list">
        <!-- Secciones de Categorías -->
        <div class="cafe-menu">
          <?php echo $menu['subcategorias'] ?>
        </div>
      </div>
    </div>
<?php
  } else {
    include '404.php';
  }
}

?>