<?php 
class MenuSubcategoriasController{


/* OBTENER CATEGORIAS  */

static public function obtenerSubcategoriasController(){
    $url = TemplateController::obtenerUrlController();
    $data = [];
    $i =0;
    foreach(MenuSubcategoriasModel::obtenerSubcategoriasModel() as $subcategoria){
        $checked = ($subcategoria['estado']==0) ? "checked" : "";
        $botones = '<button class="btn btn-icono btn-eliminar eliminarRegistro" tabla="menu_subcategorias" idRegistro="'.$subcategoria['id'].'"></button>    
                    <button class="btn btn-icono btn-editar btn_editar_subcategoria" idRegistro="'.$subcategoria['id'].'" nombre="'.$subcategoria['nombre'].'" categoria='.$subcategoria['id_categoria'].'></button>';
        $estado = '<div class="form-check form-switch">
                <input type="checkbox"
                class="form-check-input cambioEstado"
                id="switch'.$subcategoria['id'].'"
                tabla="menu_subcategorias"
                idRegistro="'.$subcategoria['id'].'"
                '.$checked.'>
                <label class="custom-control-label" for="switch'.$subcategoria['id'].'"></label>
            </div>';
        $data[]=[
            ++$i,
            $botones,
            $estado,
            $subcategoria['nombre'],
            $subcategoria['categoria'],
            $subcategoria['total_productos'],
        ];
    }

    return json_encode(['data' => $data]);
}

/* OBTENER CATEGORIAS  */


/* AGREGAR CATEGORIAS */

static public function agregarSubcategoriaController($datos){

    $validacion_nombre = ($datos['id']) ? 
    GeneralModel::validarCampoEditarModel($datos['nombre'],"nombre","menu_subcategorias",$datos['id'])
    : GeneralModel::validarCampoModel($datos['nombre'],"nombre","menu_subcategorias");

    //en caso que el correo ya se encuentre registrado por otra cuenta retornamos el error y terminamos la ejecución
    if($validacion_nombre) return "error_validacion_nombre";

    if (!$datos['id']) {

        $datos['id'] = MenuSubcategoriasModel::agregarSubcategoriaModel($datos);

    } else {
        MenuSubcategoriasModel::editarSubcategoriaModel($datos);
    }
    
    $subcategoria = MenuSubcategoriasModel::buscarSubcategoriaModel($datos['id']);
    

    return "success"; 
}

/* AGREGAR CATEGORIAS */


}