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
    
    /* Sobrescribir estilos globales de list-group que causan problemas */
    #offcanvasProducto .list-group {
      max-width: 100% !important;
      margin-inline: 0 !important;
      width: 100% !important;
    }
    
    #offcanvasProducto .list-group-item {
      min-width: 0;
      max-width: 100%;
    }
    
    #offcanvasProducto .list-group-item > span:first-child {
      flex: 1 1 0;
      min-width: 0;
      max-width: 100%;
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: nowrap;
      padding-right: 0.5rem;
    }
    
    #offcanvasProducto .list-group-item > span:first-child .badge {
      flex-shrink: 0;
      white-space: nowrap;
    }
    
    #offcanvasProducto .list-group-item > .d-flex.align-items-center {
      flex-shrink: 0;
      min-width: fit-content;
    }
    
    @media (min-width: 992px) {
      #offcanvasProducto {
        width: 420px;
        max-width: 90vw;
        height: 100vh;
        box-shadow: -4px 0 24px rgba(0, 0, 0, 0.15);
      }
      
      #offcanvasProducto .offcanvas-body {
        padding: 0;
        display: flex;
        flex-direction: column;
        height: calc(100vh - 60px);
        overflow: hidden;
      }
      
      #offcanvasProducto .offcanvas-header {
        padding: 1rem;
        border-bottom: 1px solid rgba(0, 0, 0, 0.1);
        flex-shrink: 0;
      }
      
      #offcanvasProducto .flex-grow-1.overflow-auto {
        min-height: 0;
        overflow-y: auto;
      }
      
      #offcanvasProducto #options {
        padding: 0 1rem 1rem 1rem;
      }
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
    <div class="h-100 d-flex flex-column overflow-hidden">

      <!-- Todo el contenido scrollable -->
      <div class="flex-grow-1 overflow-auto" style="min-height: 0;">

        <!-- Imagen -->
        <div class="producto-imagen position-relative">
          <img src="<?= $url ?>views/assets/img/cafeteria_default.png" id="prod_imagen" alt="Producto" class="w-100 object-fit-cover" style="max-height: 200px; width: 100%;">
        </div>

        <!-- Info general -->
        <div class="p-3">
          <div class="mb-2"><small class="text-muted">🔥 100%</small></div>
          <h5 id="nombre" class="mb-2">Iced latte</h5>
          <p class="text-muted mb-0" id="prod_descripcion">Bebida a base de leche y cafe con hielo</p>
        </div>

        <!-- Opciones -->
        <div class="px-3 pb-3" id="options">
          <!-- Las opciones se cargan dinámicamente aquí -->
        </div>
      </div>

      <!-- Footer fijo -->
      <div class="border-top px-3 py-3 bg-white d-flex justify-content-between align-items-center" style="flex-shrink: 0;">
        <div class="input-group" style="width: 120px;">
          <button class="btn btn-outline-secondary" type="button" id="btn_restar">-</button>
          <input type="text" class="form-control text-center" value="1" readonly style="border-left: 0; border-right: 0;">
          <button class="btn btn-outline-secondary" type="button" id="btn_sumar">+</button>
        </div>
          <button class="btn btn-warning text-white rounded-pill px-4 fw-bold" 
          id="btn_agregar_producto_carrito" style="flex-shrink: 0;" 
          <?php if (isset($_SESSION['nivel']) && 
          $_SESSION['nivel'] == "Propietario" || 
          $_SESSION['nivel'] == "Barista" || 
          $_SESSION['nivel'] == "Administrador") { echo 'disabled'; } ?>
          >Agregar · MX$0</button>
        
      </div>

    </div>
  </div>
</div>