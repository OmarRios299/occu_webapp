<?php 
class MenuIngredientesCategoriasController{


static public function obtenerCategoriasController(){
    $url = TemplateController::obtenerUrlController();
    $data = [];
    $i =0;
    foreach(MenuIngredientesCategoriasModel::obtenerCategoriasModel() as $categoria){
        $checked = ($categoria['estado']==0) ? "checked" : "";
        $botones = '<button class="btn btn-icono btn-eliminar eliminarRegistro" tabla="menu_ingredientes_categorias" idRegistro="'.$categoria['id'].'"></button>    
                    <button class="btn btn-icono btn-editar btn_editar_ing_categoria" idRegistro="'.$categoria['id'].'" nombre="'.$categoria['nombre'].'"></button>';
        $estado = '<div class="form-check form-switch">
                <input type="checkbox"
                class="form-check-input cambioEstado"
                id="switch'.$categoria['id'].'"
                tabla="menu_ingredientes_categorias"
                idRegistro="'.$categoria['id'].'"
                '.$checked.'>
                <label class="custom-control-label" for="switch'.$categoria['id'].'"></label>
            </div>';
        $data[]=[
            ++$i,
            $botones,
            $estado,
            $categoria['nombre'],
        ];
    }

    return json_encode(['data' => $data]);
}


static public function agregarCategoriaController($datos){

    $validacion_nombre = ($datos['id']) ? 
    GeneralModel::validarCampoEditarModel($datos['nombre'],"nombre","menu_categorias",$datos['id'])
    : GeneralModel::validarCampoModel($datos['nombre'],"nombre","menu_categorias");

    //en caso que el correo ya se encuentre registrado por otra cuenta retornamos el error y terminamos la ejecución
    if($validacion_nombre) return "error_validacion_nombre";

    if (!$datos['id']) {

        $datos['id'] = MenuIngredientesCategoriasModel::agregarCategoriaModel($datos);

    } else {
        MenuIngredientesCategoriasModel::editarCategoriaModel($datos);
    }
    
    $categoria = MenuIngredientesCategoriasModel::buscarCategoriaModel($datos['id']);
    

    return "success"; 
}



}