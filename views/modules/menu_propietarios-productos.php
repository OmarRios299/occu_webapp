<?php
$menu = MenuPropietariosController::obtenerMenuPropietarioController(false);
?>
<input type="hidden" id="input_alerta_menu">
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
