<?php
$menu = MenuPropietariosController::obtenerMenuController();
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
<?php include "menu_propietarios/modal_tamano_bebidas.php"; ?>