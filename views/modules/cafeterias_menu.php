<?php
$menu = CafeteriasMenuController::obtenerMenuPropietarioController($action[1]);
if ($menu['subcategorias'] == "") {
  include '404-menu.php';
} else {

?>

  <style>
    .product-item {
      cursor: pointer;
    }
  </style>
  <input type="hidden" id="id_cafeteria" value="<?= $action[1] ?>">
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
<?php } ?>

<!-- Offcanvas Detalle de Producto -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasProducto" aria-labelledby="offcanvasProductoLabel">
  <div class="offcanvas-header">
    <h5 class="offcanvas-title" id="offcanvasProductoLabel">Iced latte</h5>
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Cerrar"></button>
  </div>
  <div class="offcanvas-body p-0">
    <div id="" class="h-100 d-flex flex-column overflow-hidden">

      <!-- Todo el contenido scrollable -->
      <div class="flex-grow-1 overflow-auto">

        <!-- Imagen -->
        <div class="producto-imagen position-relative">
          <img src="<?= $url ?>views/assets/img/cafeteria_default.png" id="prod_imagen" alt="Producto" class="w-100 object-fit-cover" style="max-height: 120px;">
        </div>

        <!-- Info general -->
        <div class="p-3">
          <div class="mb-2"><small class="text-muted">🔥 100%</small></div>
          <h5 id="nombre">Iced latte</h5>
          <p class="text-muted" id="prod_descripcion">Bebida a base de leche y cafe con hielo</p>
          <!-- <h4 class="fw-bold">MX$64.00</h4> -->
        </div>

        <!-- Opciones -->
        <div class="px-3" id="options">


        </div>
      </div>

      <!-- Footer fijo -->
      <div class="border-top px-3 py-2 bg-white d-flex justify-content-between align-items-center">
        <div class="input-group" style="width: 150px;">
          <button class="btn btn-outline-secondary" type="button" id="btn_restar">-</button>
          <input type="text" class="form-control text-center" value="1" readonly>
          <button class="btn btn-outline-secondary" type="button" id="btn_sumar">+</button>

        </div>
        <button class="btn btn-warning text-white rounded-pill px-4 fw-bold" id="btn_agregar">Agregar · MX$0</button>
      </div>

    </div>
  </div>
</div>