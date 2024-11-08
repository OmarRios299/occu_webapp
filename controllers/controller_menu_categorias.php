<?php 
class MenuCategoriasController{


/* OBTENER CATEGORIAS  */

static public function obtenerCategoriasController(){
    $url = TemplateController::obtenerUrlController();
    $data = [];
    $i =0;
    foreach(MenuCategoriasModel::obtenerCategoriasModel() as $categoria){
        $checked = ($categoria['estado']==0) ? "checked" : "";
        $imagen='<img src="'.$url .''. $categoria['imagen'].'" style="width:80px;">';
        $botones = '<button class="btn btn-icono btn-eliminar eliminarRegistro" tabla="menu_categorias" idRegistro="'.$categoria['id'].'"></button>    
                    <button class="btn btn-icono btn-editar btn_editar_categoria" imagen="'.$url .''. $categoria['imagen'].'" idRegistro="'.$categoria['id'].'" nombre="'.$categoria['nombre'].'"></button>';
        $estado = '<div class="form-check form-switch">
                <input type="checkbox"
                class="form-check-input cambioEstado"
                id="switch'.$categoria['id'].'"
                tabla="menu_categorias"
                idRegistro="'.$categoria['id'].'"
                '.$checked.'>
                <label class="custom-control-label" for="switch'.$categoria['id'].'"></label>
            </div>';
        $data[]=[
            ++$i,
            $botones,
            $estado,
            $imagen,
            $categoria['nombre'],
            $categoria['total_subcategorias'],
            $categoria['total_productos'],
            $categoria['usuario_alta'],
            $categoria['fecha_alta']
        ];
    }

    return json_encode(['data' => $data]);
}

/* OBTENER CATEGORIAS  */


/* AGREGAR CATEGORIAS */

static public function agregarCategoriasController($datos){
    $datos['id_alta'] = $_SESSION['id'];
    $datos['fecha_alta'] = date("Y-m-d H:i:s");

    $validacion_nombre = ($datos['id']) ? 
    GeneralModel::validarCampoEditarModel($datos['nombre'],"nombre","menu_categorias",$datos['id'])
    : GeneralModel::validarCampoModel($datos['nombre'],"nombre","menu_categorias");

    //en caso que el correo ya se encuentre registrado por otra cuenta retornamos el error y terminamos la ejecución
    if($validacion_nombre) return "error_validacion_nombre";

    if (!$datos['id']) {

        $datos['id'] = MenuCategoriasModel::agregarCategoriaModel($datos);

    } else {
        MenuCategoriasModel::editarCategoriaModel($datos);
    }
    
    $categoria = MenuCategoriasModel::buscarCategoriaModel($datos['id']);
    
    if($datos['imagen_subir']){

        if($categoria['imagen']!=""&&file_exists("../../".$categoria['imagen'])&&$categoria['imagen']!="views/assets/img/cafeteria_default.png") 
            unlink("../../".$categoria['imagen']);
        $nombre_imagen = "imagen_categoria_".$datos['id'];
        $datos['imagen'] = GeneralController::subirImagen($datos['imagen_subir'],"menu_categorias",$nombre_imagen);
        MenuCategoriasModel::editarImagenModel($datos);

    }

    return "success"; 
}

/* AGREGAR CATEGORIAS */


}