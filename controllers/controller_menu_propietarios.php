<?php 
class MenuPropietariosController{
    
    /* OBTENER CATEGORIAS */
    
    static public function obtenerCategoriasController(){
        $id_propietario = $_SESSION['id'];

        $data = '';
        foreach (MenuPropietariosModel::obtenerCategoriasModel() as $categoria){
            $data .='
            <div class="col-md-4">
                <h6 class=""><b>'.$categoria['nombre'].'</b></h6>
                <div class="row mb-3">
            ';
            
            // verificar si la cafeteria ya cuenta con registros de subcategorias para la informacion que se mostrará
            $subcategorias = MenuPropietariosModel::obtenerSubcategoriasModel($categoria['id'],$id_propietario);

            foreach($subcategorias as $subcategoria){
                $checked = '';
                if ($subcategoria['estado']==1) {
                    $checked = 'checked';
                }
                $data.='
                <div class="form-check form-switch">
                    <div class="row align-items-center">
                        <div class="col-10">
                            <label class="form-check-label" for="'.$subcategoria['id'].'">'.$subcategoria['nombre'].'</label>
                        </div>
                        <div class="col-2">
                            <input class="form-check-input check_subcategoria" type="checkbox" id="'.$subcategoria['id'].'" estado="'.$subcategoria['estado'].'" idRegistro="'.$subcategoria['id_registro'].'" '.$checked.'>
                        </div>
                    </div>
                </div>
                ';
            }
            $data.='</div></div>';
        }

        return json_encode($data);
    }
    
    /* OBTENER CATEGORIAS */

    
    /* INSERTAR REGISTRO DE SUBCATEGORIAS */
    
    static public function agregarSubcategoriaController($datos){
        if ($datos['id_registro']!='No') {
            $datos['estado'] = ($datos['estado']==1) ? 0 : 1;
            MenuPropietariosModel::cambiarEstadoSubcategoriaModel($datos);
        }else{
            //$cafeterias = MenuPropietariosModel::buscarCafeteriasUsuarioModel($datos['id_usuario']);
            $datos['id_propietario'] = $_SESSION['id'];
            MenuPropietariosModel::agregarSubcategoriaModel($datos);
        }
    }
    
    /* INSERTAR REGISTRO DE SUBCATEGORIAS */


    /* INSERTAR REGISTRO DE PRODUCTOS */

    static public function agregarProductoController($datos){
        if ($datos['id_registro']!='No') {
            $datos['estado'] = ($datos['estado']==1) ? 0 : 1;
            MenuPropietariosModel::cambiarEstadoProductoModel($datos);
        }else{
           // $datos['id_usuario'] = $_SESSION['id'];
            //$cafeterias = MenuPropietariosModel::buscarCafeteriasUsuarioModel($datos['id_usuario']);
            $datos['id_propietario'] = $_SESSION['id'];
            MenuPropietariosModel::agregarProductoModel($datos);
        }
    }
    
    /* INSERTAR REGISTRO DE PRODUCTOS */

    
    /* INSERTAR REGISTRO DE PRODUCTOS */

    static public function activarTamanoController($datos){
        $datos['id_propietario'] = $_SESSION['id'];

        if ($datos['accion']=='desactivar') {
            MenuPropietariosModel::desactivarProductoModel($datos);
        }else{
            MenuPropietariosModel::actualizarProductoModel($datos);
        }
    }
    
    /* INSERTAR REGISTRO DE PRODUCTOS */


    /* OBTENER PRODUCTOS MENU */

    static public function obtenerMenuController(){
        $id_propietario = $_SESSION['id'];
        
        $url = TemplateController::obtenerUrlController();
        $categorias = '<li><a href="todos"class="menu-link active">Todos</a></li>';

        // todo: ver si hacer una funcion general
        foreach (MenuPropietariosModel::obtenerCategoriasPropietarioModelModel($id_propietario) as $categoria) {
            $categorias .= '<li><a href="' . $categoria['id'] . '" class="menu-link">' . $categoria['nombre'] . '</a></li>';
        }

        $subcategorias = '';

        // todo: ver si hacer una funcion general
        foreach (MenuPropietariosModel::obtenerSubcategoriasPropietarioModel($id_propietario, 'activas') as $subcategoria) {

            $productos_data = '';
            $hidden = '';
            $hidden = ($subcategoria['id_categoria']==1 ||
                        $subcategoria['id_categoria']==2||
                        $subcategoria['id_categoria']==3||
                        $subcategoria['id_categoria']==4) 
                        ? '' : 'hidden';
                        
            $productos = MenuPropietariosModel::obtenerProductosModel($subcategoria['id'],$id_propietario);

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
                                        <div class="form-check form-switch">
                                            <div class="row align-items-center">
                                                <div class="col">
                                                    <button type="button" class="btn btn-icono btn-categorias btn_editar_tamanos" idProducto="'.$producto['id'].'" '.$hidden.'></button>
                                                    <button type="button" class="btn btn-icono btn-comentario" '.$hidden.'></button>
                                                    <input class="form-check-input check_productos mt-3" type="checkbox" id="'.$producto['id'].'" estado="'.$producto['estado'].'" idRegistro="'.$producto['id_registro'].'" '.$checked.'>
                                                </div>
                                            </div>
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

    /* OBTENER PROCDUTOS MENU */

        
    /* BUSCAR REGISTRO DE PRODUCTOS */

    static public function buscarProductoController($datos){
        $datos['id_propietario'] = $_SESSION['id'];
        return json_encode(MenuPropietariosModel::buscarProductoModel($datos));
    }
    
    /* BUSCAR REGISTRO DE PRODUCTOS */

    /* OBTENER MENU POR PROPIETARIO */

    static public function obtenerMenuPropietarioController(){
        $id_propietario = $_SESSION['id'];
        
        $url = TemplateController::obtenerUrlController();
        $categorias = '<li><a href="todos"class="menu-link active">Todos</a></li>';

        // todo: ver si hacer una funcion general
        foreach (MenuPropietariosModel::obtenerCategoriasPropietarioModelModel($id_propietario) as $categoria) {
            $categorias .= '<li><a href="' . $categoria['id'] . '" class="menu-link">' . $categoria['nombre'] . '</a></li>';
        }

        $subcategorias = '';

        // todo: ver si hacer una funcion general
        foreach (MenuPropietariosModel::obtenerSubcategoriasPropietarioModel($id_propietario, 'activas') as $subcategoria) {

            $productos_data = '';
            $hidden = '';
            $hidden = ($subcategoria['id_categoria']==1 ||
                        $subcategoria['id_categoria']==2||
                        $subcategoria['id_categoria']==3||
                        $subcategoria['id_categoria']==4) 
                        ? '' : 'hidden';
                        
            $productos = MenuPropietariosModel::obtenerProductosModel($subcategoria['id'],$id_propietario, 'activos');

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
                                        <div class="form-check form-switch">
                                            <div class="row align-items-center">
                                                <div class="col">
                                                    <button type="button" class="btn btn-icono btn-categorias btn_editar_tamanos" idProducto="'.$producto['id'].'" '.$hidden.'></button>
                                                    <button type="button" class="btn btn-icono btn-comentario" '.$hidden.'></button>
                                                    <input class="form-check-input check_productos mt-3" type="checkbox" id="'.$producto['id'].'" estado="'.$producto['estado'].'" idRegistro="'.$producto['id_registro'].'" '.$checked.'>
                                                </div>
                                            </div>
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