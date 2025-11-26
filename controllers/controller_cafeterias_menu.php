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


    static public function agregarCarritoController($datos)
    {
        session_start();
        date_default_timezone_set("America/Tijuana");

        if (!isset($_SESSION['id']) || empty($_SESSION['id'])) {
            return json_encode([
                "error" => true,
                "message" => "Debes iniciar sesión para agregar productos al carrito."
            ]);
        }

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
