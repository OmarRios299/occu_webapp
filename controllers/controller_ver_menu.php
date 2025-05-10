<?php
class VerMenuController
{

    /* BUSCAR CAFETERÍA */

    static public function buscarCafeteriaController($cafeteria)
    {
        return VerMenuModel::buscarCafeteriaModel($cafeteria);
    }

    /* BUSCAR CAFETERÍA */


    /* OBTENER MENU POR PROPIETARIO */

    static public function obtenerMenuPropietarioController($cafeteria)
    {
        $url = TemplateController::obtenerUrlController();
        $categorias = '<li><a href="todos" class="menu-link active">Todos</a></li>';

        $categoriasLista = VerMenuModel::obtenerCategoriasModel($cafeteria);
        $subcategorias = '';

        foreach ($categoriasLista as $categoria) {
            $subcategorias_data = '';

            // Obtener subcategorías de la categoría actual
            $subcategoriasLista = VerMenuModel::obtenerSubcategoriasModel($cafeteria);

            foreach ($subcategoriasLista as $subcategoria) {
                if ($subcategoria['id_categoria'] == $categoria['id']) { // Asegurar que pertenece a la categoría actual

                    $productos_data = '';

                    // Obtener productos de la subcategoría
                    if ($subcategoria['extra'] == "No") {
                        $productos = VerMenuModel::obtenerProductosModel($subcategoria['id'], $cafeteria);

                        foreach ($productos as $producto) {
                            if ($producto['estado'] == 1) { // Solo productos activos
                                $productos_data .= '<div class="product-item">
                                                    <img src="' . $url . $producto['imagen'] . '" alt="" class="product-image">
                                                    <div class="product-info">
                                                        <span class="product-name">' . $producto['nombre'] . '</span>
                                                        <span class="product-price"></span>
                                                    </div>
                                                </div>';
                            }
                        }
                    } else {
                        $productos = VerMenuModel::obtenerProductosExtraModel($subcategoria['id'], $cafeteria, true);

                        foreach ($productos as $producto) {
                            if ($producto['estado'] == 1) { // Solo productos activos
                                $productos_data .= '<div class="product-item">
                                                    <img src="' . $url . $producto['imagen'] . '" alt="" class="product-image">
                                                    <div class="product-info">
                                                        <span class="product-name">' . $producto['nombre'] . '</span>
                                                        <span class="product-price"></span>
                                                    </div>
                                                </div>';
                            }
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


    /* OBTENER MENU POR PROPIETARIO */
}
