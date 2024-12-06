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
            $subcategorias = MenuPropietariosModel::obtenerSubcategoriasModel($categoria['id']);

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
    
    
}