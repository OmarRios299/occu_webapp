<?php 
class MenuPropietariosController{

    
    /* CONTAR CAFETERIAS DE PROPIETARIO */
    
    static public function cafeteriasPropietarioController(){
        return MenuPropietariosModel::cafeteriasPropietarioModel($_SESSION['id']);
    }
    
    /* CONTAR CAFETERIAS DE PROPIETARIO */
    
    
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
        if ($datos['cafeteria']!=='false') {
            $cafeteria = GeneralController::verificarCafeteriaContoller($datos['cafeteria'], $_SESSION['id']);
            if (!$cafeteria) {
                return 'error';
            }
        }        
        if ($datos['id_registro']!='No') {
            $datos['estado'] = ($datos['estado']==1) ? 0 : 1;
            MenuPropietariosModel::cambiarEstadoProductoModel($datos,$datos['cafeteria']);
        }else{
           // $datos['id_usuario'] = $_SESSION['id'];
            //$cafeterias = MenuPropietariosModel::buscarCafeteriasUsuarioModel($datos['id_usuario']);
            $datos['id_propietario'] = $_SESSION['id'];
            MenuPropietariosModel::agregarProductoModel($datos);
        }
    }
    
    /* INSERTAR REGISTRO DE PRODUCTOS */

    
    /* ACTIVA TAMANO DE PRODUCTOS */

    static public function activarTamanoController($datos){
        var_dump($datos);
        $datos['id_propietario'] = $_SESSION['id'];
        if ($datos['cafeteria']!=='false') {
            $cafeteria = GeneralController::verificarCafeteriaContoller($datos['cafeteria'], $_SESSION['id']);
            if (!$cafeteria) {
                return 'error';
            }
        }
        
        if ($datos['accion']=='desactivar') {
            MenuPropietariosModel::desactivarProductoModel($datos, $datos['cafeteria']);
        }else{
            MenuPropietariosModel::actualizarProductoModel($datos, $datos['cafeteria']);
        }
    }
    
    /* ACTIVA TAMANO DE PRODUCTOS */


        
    /* BUSCAR REGISTRO DE PRODUCTOS */

    static public function buscarProductoController($datos){
        $datos['id_propietario'] = $_SESSION['id'];
        if ($datos['cafeteria']!=='false') {
            $cafeteria = GeneralController::verificarCafeteriaContoller($datos['cafeteria'], $_SESSION['id']);
            if (!$cafeteria) {
                return json_encode(['error' => 'Cafetería no válida']);
            }
        }
        
        return json_encode(MenuPropietariosModel::buscarProductoModel($datos, $datos['cafeteria']));
    }
    
    /* BUSCAR REGISTRO DE PRODUCTOS */


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
            $hidden = '';
            $hidden = ($subcategoria['id_categoria']==1 ||
                        $subcategoria['id_categoria']==2||
                        $subcategoria['id_categoria']==3||
                        $subcategoria['id_categoria']==4) 
                        ? '' : 'hidden';
                        
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
                                        <div class="form-check form-switch">
                                            <div class="row align-items-center">
                                                <div class="col">
                                                    <button type="button" class="btn btn-icono btn-categorias btn_editar_tamanos" idProducto="'.$producto['id'].'" '.$hidden.'></button>
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

    
    /* ACTUALIZAR MENU */
    
    static public function actualizarMenuController($datos){
        $id_propietario = $_SESSION['id'];

        if ($datos['actualizar']=='precios') {
            foreach(MenuPropietariosModel::buscarCafeteriasUsuarioModel($id_propietario) as $cafeterias){
                foreach(MenuPropietariosModel::buscarMenuPropietarioModel($id_propietario) as $item){
                    MenuPropietariosModel::actualizarPrecioSucursaleModel($item, $cafeterias['id']);
                } 
            }
        }else {
            MenuPropietariosModel::eliminarMenuSucursalModel($id_propietario);
            foreach(MenuPropietariosModel::buscarCafeteriasUsuarioModel($id_propietario) as $cafeterias){
                foreach(MenuPropietariosModel::buscarMenuPropietarioModel($id_propietario) as $item){
                    MenuPropietariosModel::actualizarMenuSucursalModel($item, $cafeterias['id']);
                } 
            }
            
        }

    }
    
    /* ACTUALIZAR MENU */
    
        
}