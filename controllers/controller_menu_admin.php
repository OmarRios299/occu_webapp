<?php
class AdminMenuController{


    /* OBTENER CATEGORIAS MENU */

    static public function obtenerMenuController(){
        $url = TemplateController::obtenerUrlController();
        $categorias = '<li><a href="todos"class="menu-link active">Todos</a></li>';
        foreach (AdminMenuModel::obtenerCategoriasModel() as $categoria) {
            $categorias .= '<li><a href="' . $categoria['id'] . '" class="menu-link">' . $categoria['nombre'] . '</a></li>';
        }

        $subcategorias = '';

        foreach (AdminMenuModel::obtenerSubcategoriasModel() as $subcategoria) {

            $productos = '';

            foreach (AdminMenuModel::obtenerProductosModel() as $producto) {
                if ($producto['id_subcategoria'] == $subcategoria['id']) {
                    $productos .= '<div class="product-item">
                                        <img src="' . $url . $producto['imagen'] . '" alt="Americano" class="product-image">
                                        <div class="product-info">
                                            <span class="product-name">' . $producto['nombre'] . '</span>
                                            <span class="product-price"></span>
                                        </div>
                                    </div>';
                }
            }

            $subcategorias .= '<div class="' . $subcategoria['id_categoria'] . ' category active">
                                    <h2>' . $subcategoria['nombre'] . '</h2>
                                    ' . $productos . '
                                    <hr>
                              </div>';
        }
        return array(
            'subcategorias' => $subcategorias,
            'categorias' => $categorias
        );
    }

    /* OBTENER CATEGORIAS MENU */
}
