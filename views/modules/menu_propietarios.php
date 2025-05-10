<?php
include "menu_propietarios/modal_tamano_bebidas.php";
if ($action[0] == 'menu_propietarios' && !isset($action[1])) {
  $menu = MenuPropietariosController::obtenerMenuPropietarioController(true,false,true,true);
  $hidden = (MenuPropietariosController::cafeteriasPropietarioController() > 1) ? '' : 'display:none';

  if ($menu['subcategorias'] == "") {
    include "404-menu.php";
  } else {

?>
    <div class="text-end">
      <!-- <div class="text-end" style="<?= $hidden ?>"> -->
      <div class="btn-group align-items-center">
        <button type="button" class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
          🔄 Actualizar menú
        </button>
        <ul class="dropdown-menu">
          <li>
            <a class="dropdown-item" href="#" id="actualizarMenu" actualizar="menu">
              ✅ En todas las sucursales
            </a>
          </li>
          <li>
            <a class="dropdown-item" href="#" id="actualizarPrecios" actualizar="precios">
              💲 Solo precios
            </a>
          </li>
        </ul>

        <!-- Botón de ayuda -->
        <button type="button" class="btn btn-secondary ms-1" data-bs-toggle="modal" data-bs-target="#infoModal">
          ?
        </button>
      </div>
      <button type="button" class="btn btn-icono btn-mas ms-5" data-bs-toggle="modal" data-bs-target="#addProductoExtraModal"></button>

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

  }
} else if ($action[0] == 'menu_propietarios' && isset($action[1])) {
  $cafeteria = GeneralController::verificarCafeteriaContoller($action[1], $_SESSION['id']);
  if ($cafeteria) {
    $menu = MenuPropietariosController::obtenerMenuPropietarioController(false, $cafeteria['id'],true,true);
    if ($menu['subcategorias'] == "") {
      include "404-menu.php";
    } else {
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
    }
  } else {
    include '404.php';
  }
}

?>

<!-- Modal de ayuda -->
<div class="modal fade" id="infoModal" tabindex="-1" aria-labelledby="infoModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="infoModalLabel">¿Qué hacen estas opciones?</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <div class="row text-start">
          <div class="col-md-12">
            <p>Este menú no es visible para los clientes hasta que lo actualices.</p>
          </div>
          <div class="col-md-12">
            <p><strong>✅ En todas las sucursales:</strong> Actualiza el menú completo (Productos activos y precios) en todas tus sucursales.</p>
          </div>
          <div class="col-md-12">
            <p><strong>💲 Solo precios:</strong> Solo actualiza los precios, sin cambiar los productos que están inactivos.</p>

          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Entendido</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal de producto extra -->
<div class="modal fade" id="addProductoExtraModal" tabindex="-1" aria-labelledby="addProductoExtraModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addProductoExtraModalLabel">Agregando producto</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <form onsubmit="return false;" class="form_add_producto_extra">
        <div class="modal-body">
          <div class="row">
            <div class="col-md-3">
              <div class="form-group">
                <label>Nombre:</label>
                <input type="text" class="form-control input_productos" tabla='menu_productos' columna='nombre' mensaje='Este producto ya se encuentra registrada' id="nombre_producto">
                <div class="invalid-feedback" style="display: none;"></div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label>Categoría:</label>
                <select class="form-control input_productos" id="select_subcategoria">
                  <option value="" disabled selected>Selecciona una opción</option>
                  <?php foreach (MenuPropietariosController::obtenerSubcategoriasExtraController() as $subcategoria) { ?>
                    <option value="<?= $subcategoria['id'] ?>" campo="id_subcategoria_extra"><?= $subcategoria['categoria'] . ' - ' . $subcategoria['nombre'] ?></option>
                  <?php } ?>
                  <?php foreach (MenuProductosController::obtenerSubcategoriasController() as $subcategoria) { ?>
                    <option value="<?= $subcategoria['id'] ?>"  campo="id_subcategoria"><?= $subcategoria['categoria'] . ' - ' . $subcategoria['nombre'] ?></option>
                  <?php } ?>
                </select>
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label>Agrega una imagen:</label>
              </div>
              <div class="input-group">
                <input type="file" class="form-control input_productos imagenPrevisualizar validarImagen" id="imagen_producto" lang="esp">
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <img src="<?= $url ?>/views/assets/img/cafeteria_default.png" class="imagen_editar" style="width:110px;" id="imagen_previsualizar">
              </div>
            </div>
          </div>

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
          <button type="submit" class="btn btn-primary">Aceptar</button>
        </div>
      </form>
    </div>
  </div>
</div>