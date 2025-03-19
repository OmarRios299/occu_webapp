<?php 
class VerMenuController{
    /* OBTENER MENU POR PROPIETARIO */

    static public function obtenerMenuPropietarioController($productosActivos, $cafeterias=false){
        $id_propietario = $_SESSION['id'];

        $url = TemplateController::obtenerUrlController();
        $categorias = '<li><a href="todos"class="menu-link active">Todos</a></li>';

        foreach (MenuPropietariosModel::obtenerCategoriasPropietarioModelModel($id_propietario) as $categoria) {
            $categorias .= '<li><a href="' . $categoria['id'] . '" class="menu-link">' . $categoria['nombre'] . '</a></li>';
        }

        $subcategorias = '';

        foreach (MenuPropietariosModel::obtenerSubcategoriasPropietarioModel($id_propietario, 'activas') as $subcategoria) {

            $productos_data = '';
                        
            $productos = MenuPropietariosModel::obtenerProductosModel($subcategoria['id'],$id_propietario, $productosActivos,$cafeterias);

            foreach ($productos as $producto) {
                $checked = '';
                if ($producto['estado']==1) {
                    $checked = 'checked';
                }
                    $productos_data .= '<div class="product-item">
                                        <img src="' . $url . $producto['imagen'] . '" alt="" class="product-image">
                                        <div class="product-info">
                                            <span class="product-name">' . $producto['nombre'] . '</span>
                                            <span class="product-price"></span>
                                        </div>
                                        
                                    </div>';
                
            }
            
            $subcategorias .= '<div class="' . $subcategoria['id_categoria'] . ' category active">
                                    <h2>' . $subcategoria['nombre'] . '</h2>
                                    ' . $productos_data . '
                                    <hr>
                                </div>';
        }
        return array(
            'subcategorias' => $subcategorias,
            'categorias' => $categorias
        );
    }
    
    /* OBTENER MENU POR PROPIETARIO */

}

?>