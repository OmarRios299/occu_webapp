<?php
$menu = VerMenuController::obtenerMenuPropietarioController($action[1]);
if ($menu['subcategorias']=="") {
    include '404-menu.php';
}else{

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
<?php } ?>