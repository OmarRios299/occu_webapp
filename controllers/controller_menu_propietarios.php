<?php 
class MenuPropietariosController{
    
    /* OBTENER CATEGORIAS */
    
    static public function obtenerCategoriasController(){
        $id_cafeteria = 28;

        $data = '';
        foreach (MenuPropietariosModel::obtenerCategoriasModel() as $categoria){
            $data .='
            <div class="col-md-4">
                <h6 class=""><b>'.$categoria['nombre'].'</b></h6>
                <div class="row mb-3">
            ';
            
            // verificar si la cafeteria ya cuenta con registros de subcategorias para la informacion que se mostrará
            $subcategorias = MenuPropietariosModel::obtenerSubcategoriasModel($categoria['id'],$id_cafeteria);

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
            $datos['id_usuario'] = $_SESSION['id'];
            $cafeterias = MenuPropietariosModel::buscarCafeteriasUsuarioModel($datos['id_usuario']);
            $datos['id_cafeteria'] = $cafeterias[0]['id'];
            MenuPropietariosModel::agregarSubcategoriaModel($datos);
        }
    }
    
    /* INSERTAR REGISTRO DE SUBCATEGORIAS */
    

    /* OBTENER CATEGORIAS MENU */

    static public function obtenerMenuController(){
        $url = TemplateController::obtenerUrlController();
        $categorias = '<li><a href="todos"class="menu-link active">Todos</a></li>';

        // todo: ver si hacer una funcion general
        foreach (AdminMenuModel::obtenerCategoriasModel() as $categoria) {
            $categorias .= '<li><a href="' . $categoria['id'] . '" class="menu-link">' . $categoria['nombre'] . '</a></li>';
        }

        
        

        $subcategorias = '';

        // todo: ver si hacer una funcion general
        foreach (AdminMenuModel::obtenerSubcategoriasModel() as $subcategoria) {

            $productos = '';
            $hidden = '';
            $hidden = ($subcategoria['id_categoria']==1 ||
                        $subcategoria['id_categoria']==2||
                        $subcategoria['id_categoria']==3||
                        $subcategoria['id_categoria']==4) 
                        ? '' : 'hidden';
            
            // todo: ver si hacer una funcion general
            foreach (AdminMenuModel::obtenerProductosModel() as $producto) {
                if ($producto['id_subcategoria'] == $subcategoria['id']) {
                    $productos .= '<div class="product-item">
                                        <img src="' . $url . $producto['imagen'] . '" alt="Americano" class="product-image">
                                        <div class="product-info">
                                            <span class="product-name">' . $producto['nombre'] . '</span>
                                            <span class="product-price"></span>
                                        </div>
                                        <div class="form-check form-switch">
                                            <div class="row align-items-center">
                                                <div class="col">
                                                    <button type="button" class="btn btn-icono btn-categorias btn_editar_tamanos" '.$hidden.'></button>
                                                    <button type="button" class="btn btn-icono btn-comentario" '.$hidden.'></button>
                                                    <input class="form-check-input check_subcategoria mt-3" type="checkbox" id="" estado="" idRegistro="">
                                                </div>
                                            </div>
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