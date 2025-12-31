<?php
class CafeteriasMenuController
{

    static public function buscarCafeteriaController($cafeteria)
    {
        return CafeteriasMenuModel::buscarCafeteriaModel($cafeteria);
    }


    static public function obtenerMenuPropietarioController($cafeteria)
    {
        $url = TemplateController::obtenerUrlController();
        $categorias = '<li><a href="todos" class="menu-link active">Todos</a></li>';

        $categoriasLista = CafeteriasMenuModel::obtenerCategoriasModel($cafeteria);
        $subcategorias = '';

        foreach ($categoriasLista as $categoria) {
            $subcategorias_data = '';

            // Obtener subcategorías de la categoría actual
            $subcategoriasLista = CafeteriasMenuModel::obtenerSubcategoriasModel($cafeteria);

            foreach ($subcategoriasLista as $subcategoria) {
                if ($subcategoria['id_categoria'] == $categoria['id']) { // Asegurar que pertenece a la categoría actual

                    $productos_data = '';

                    // Obtener productos de la subcategoría
                    $productos = CafeteriasMenuModel::obtenerProductosModel($subcategoria['id'], $cafeteria);

                    foreach ($productos as $producto) {
                        if ($producto['estado'] == 1) { // Solo productos activos
                            $productos_data .= '<div class="product-item addProducto" id-producto="' . $producto['id'] . '">
                                                    <img src="' . $url . $producto['imagen'] . '" alt="" class="product-image">
                                                    <div class="product-info">
                                                        <span class="product-name">' . $producto['nombre'] . '</span>
                                                        <span class="product-price"></span>
                                                    </div>
                                                </div>';
                        }
                    }

                    // Agregar subcategoría solo si tiene productos
                    if (!empty($productos_data)) {
                        $subcategorias_data .= '<div class="' . $subcategoria['id_categoria'] . ' category active">
                                                    <h2>' . $subcategoria['nombre'] . '</h2>
                                                    ' . $productos_data . '
                                                    <hr>
                                                </div>';
                    }
                }
            }

            // Agregar categoría solo si tiene al menos una subcategoría con productos
            if (!empty($subcategorias_data)) {
                $categorias .= '<li><a href="' . $categoria['id'] . '" class="menu-link">' . $categoria['nombre'] . '</a></li>';
                $subcategorias .= $subcategorias_data;
            }
        }

        return array(
            'subcategorias' => $subcategorias,
            'categorias' => $categorias
        );
    }


    static public function obtenerProductoController($datos)
    {
        return json_encode([
            'producto' => CafeteriasMenuModel::buscarProductoModel($datos),
            'ingredientes' => CafeteriasMenuModel::buscarCategoriasIngredientesModel($datos),
            'tamanos' => CafeteriasMenuModel::buscarTamanosModel($datos)
        ]);
    }

    static public function obtenerMenuOffcanvasController($id_cafeteria)
    {
        // Usar la misma función que usa cafeterias_menu.php
        $menu = self::obtenerMenuPropietarioController($id_cafeteria);
        
        if ($menu['subcategorias'] == "") {
            return json_encode([
                'success' => false,
                'message' => 'Esta cafetería aún no tiene menú disponible.'
            ]);
        }
        
        // Verificar si el usuario tiene un carrito activo en esta cafetería
        $carrito_info = null;
        session_start();
        if (isset($_SESSION['id']) && !empty($_SESSION['id'])) {
            require_once __DIR__ . '/../models/model_carrito.php';
            $carrito_info = CarritoModel::obtenerResumenCarritoCafeteriaModel($_SESSION['id'], $id_cafeteria);
        }
        
        // Generar el HTML del menú en el mismo formato que cafeterias_menu.php
        $html = '<div class="menu-cafeterias">
                    <input type="hidden" id="id_cafeteria_menu_offcanvas" value="' . $id_cafeteria . '">
                    <!-- Menú de Navegación -->
                    <div class="nav-menu">
                        <ul>
                            ' . $menu['categorias'] . '
                        </ul>
                    </div>
                    <div class="product-list">
                        <!-- Secciones de Categorías -->
                        <div class="cafe-menu">
                            ' . $menu['subcategorias'] . '
                        </div>
                    </div>';
        
        // Agregar información del carrito si existe
        if ($carrito_info && $carrito_info['total_productos'] > 0) {
            $html .= '<div class="menu-carrito-resumen" style="position: sticky; bottom: 0; background: var(--principal, #ffc107); color: white; padding: 1rem; margin-top: 1rem; border-radius: 12px 12px 0 0; box-shadow: 0 -2px 10px rgba(0,0,0,0.1); z-index: 10;">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <small style="display: block; opacity: 0.9;">Total en carrito</small>
                                <strong style="font-size: 1.1rem;">' . $carrito_info['total_productos'] . ' producto' . ($carrito_info['total_productos'] > 1 ? 's' : '') . ' · MX$' . number_format($carrito_info['total'], 2) . '</strong>
                            </div>
                            <button type="button" class="btn btn-light btn-sm" onclick="document.getElementById(\'ver-carrito\').click();" style="border-radius: 20px;">
                                <i class="fas fa-shopping-cart"></i> Carrito
                            </button>
                        </div>
                      </div>';
        }
        
        $html .= '</div>';
        
        return json_encode([
            'success' => true,
            'html' => $html,
            'carrito' => $carrito_info
        ]);
    }


    static public function agregarCarritoController($datos)
    {
        session_start();

        if (!isset($_SESSION['id']) || empty($_SESSION['id'])) {
            return json_encode([
                "error" => 'error_sesion',
                "message" => "Debes iniciar sesión para agregar productos al carrito."
            ]);
        }

        if (isset($_SESSION['nivel']) && 
        $_SESSION['nivel'] == "Propietario" || 
        $_SESSION['nivel'] == "Barista" || 
        $_SESSION['nivel'] == "Administrador") {
            return json_encode([
                "error" => 'error_sesion',
                "message" => "Debes iniciar sesión como cliente para agregar productos al carrito."
            ]);
        }

        // Obtener información de la cafetería para obtener su id_ciudad
        $cafeteria_info = CafeteriasMenuModel::buscarCafeteriaModel($datos['id_cafeteria']);
        
        if (!$cafeteria_info || !isset($cafeteria_info['id_ciudad'])) {
            return json_encode([
                "error" => 'error_cafeteria',
                "message" => "No se pudo obtener la información de la cafetería."
            ]);
        }

        // Obtener la zona horaria de la ciudad de la cafetería
        $zona_horaria = CafeteriasMenuModel::obtenerZonaHorariaCiudadModel($cafeteria_info['id_ciudad']);
        
        // Si no se encuentra zona horaria, usar la por defecto
        if (!$zona_horaria) {
            $zona_horaria = "America/Tijuana";
        }

        // Establecer la zona horaria antes de obtener la fecha
        date_default_timezone_set($zona_horaria);

        $datos['id_usuario'] = $_SESSION['id'];
        $datos['fecha_alta'] = date("Y-m-d H:i:s");

        $carrito = CafeteriasMenuModel::buscarCarritoModel($datos);

        if ($carrito) {

            // verificar si tiene items
            $tieneItems = CafeteriasMenuModel::carritoTieneItemsModel($carrito['id']);

            if ($carrito['id_cafeteria'] != $datos['id_cafeteria']) {

                // Si NO hay items → eliminar siempre sin preguntar
                if (!$tieneItems) {
                    CafeteriasMenuModel::eliminarCarritoCompletoModel($datos['id_usuario'], $carrito['id']);
                    $id_carrito = CafeteriasMenuModel::crearCarritoModel($datos);

                    // Si SÍ hay items → mostrar alerta solo si el usuario no confirmó aún
                } else if ($datos['eliminarOtroCarrito'] == "Si") {
                    CafeteriasMenuModel::eliminarCarritoCompletoModel($datos['id_usuario'], $carrito['id']);
                    $id_carrito = CafeteriasMenuModel::crearCarritoModel($datos);
                } else {
                    return json_encode([
                        "error" => "otroCarrito",
                        "message" => 'Tienes un carrito activo en ' . $carrito['nombre'] . ', ¿Deseas eliminarlo y agregar nuevos productos?',
                    ]);
                }
            } else {
                $id_carrito = $carrito['id'];
            }
        } else {
            $id_carrito = CafeteriasMenuModel::crearCarritoModel($datos);
        }


        $id_carrito = $id_carrito;
        $id_producto = $datos['id_producto'];
        $id_tamano = $datos['id_tamano'];
        $ingredientes = $datos['ingredientes']; // array con id y cantidad

        // 1. Revisar si el item ya existe con las mismas características
        $id_item_existente = CafeteriasMenuModel::buscarItemRepetidoModel(
            $id_carrito,
            $id_producto,
            $id_tamano,
            $ingredientes
        );

        if ($id_item_existente) {

            // ITEM YA EXISTE → SUMAR CANTIDAD
            CafeteriasMenuModel::aumentarCantidadItemModel($id_item_existente);

            return json_encode([
                "success" => true,
                "tipo" => "sumado",
                "id_item" => $id_item_existente
            ]);

        } else {

            // ITEM NUEVO → INSERTAR COMO SIEMPRE
            $id_item = CafeteriasMenuModel::carritoItemsModel($datos, $id_carrito);

            foreach ($ingredientes as $ingrediente) {
                CafeteriasMenuModel::carritoItemsIngredientesModel($datos, $ingrediente, $id_item);
            }

            return json_encode([
                "success" => true,
                "tipo" => "nuevo",
                "id_item" => $id_item
            ]);
        }
    }
}
