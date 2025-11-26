<?php
$menu = PropietariosMenuController::obtenerMenuPropietarioController(false,false,true);

if ($menu['subcategorias'] == "") {
    include "404-menu.php";
} else {
?>
    <input type="hidden" id="input_alerta_menu">
    <div class="menu-cafeterias">
        <!-- Menú de Navegación -->
        <div class="nav-menu">
            <ul>
                <?php echo $menu['categorias'] ?>
            </ul>
        </div>
        <div class="product-list">
            <!-- Secciones de Categorías -->
            <div class="cafe-menu">
                <div class="product-item">
                    <div class="alert alert-info">
                        <span class="">Activa los productos que quieres mostrar en tu menú. Para ver más productos activa otras categorías.</span>
                    </div>
                </div>
                <?php echo $menu['subcategorias'] ?>
            </div>
        </div>
    </div>
<?php } ?>