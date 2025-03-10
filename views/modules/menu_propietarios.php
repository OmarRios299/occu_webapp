<?php
include "menu_propietarios/modal_tamano_bebidas.php";
if ($action[0] == 'menu_propietarios' && !isset($action[1])) {
  $menu = MenuPropietariosController::obtenerMenuPropietarioController(true);
?>
  <div class="menu-cafeterias ">
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
  // $menu = MenuPropietariosController::obtenerMenuController('');
?>



<?php
}

?> 