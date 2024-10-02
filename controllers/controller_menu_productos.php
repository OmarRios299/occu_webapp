<?php 
class MenuProductosController{


/* OBTENER PRODUCTOS  */

static public function obtenerProductosController($datos){
    $url = TemplateController::obtenerUrlController();
    $data = [];
    $i =0;
    foreach(MenuProductosModel::obtenerProductosModel($datos) as $producto){
        $checked = ($producto['estado']==0) ? "checked" : "";
        $imagen='<img src="'.$url .''. $producto['imagen'].'" style="width:80px;">';
        $botones = '<button class="btn btn-icono btn-eliminar eliminarRegistro" tabla="cafeterias_menu_productos" idRegistro="'.$producto['id'].'"></button>    
                    <button class="btn btn-icono btn-editar btn_editar_producto" imagen="'.$url .''. $producto['imagen'].'" idRegistro="'.$producto['id'].'" nombre="'.$producto['nombre'].'" categoria="'.$producto['id_categoria'].'"></button>';
        $estado = '<div class="form-check form-switch">
                <input type="checkbox"
                class="form-check-input cambioEstado"
                id="switch'.$producto['id'].'"
                tabla="cafeterias_menu_productos"
                idRegistro="'.$producto['id'].'"
                '.$checked.'>
                <label class="custom-control-label" for="switch'.$producto['id'].'"></label>
            </div>';
        $data[]=[
            ++$i,
            $botones,
            $estado,
            $imagen,
            $producto['nombre'],
            $producto['categoria'],
            $producto['usuario_alta'],
            $producto['fecha_alta']
        ];
    }

    return json_encode(['data' => $data]);
}

/* OBTENER PRODUCTOS  */


/* OBTENER CATEGORIAS */

static public function obtenerCategoriasController(){
    return MenuProductosModel::obtenerCategoriasModel();
}

/* OBTENER CATEGORIAS */


/* AGREGAR PRODUCTOS */

static public function agregarProductosController($datos){
    $datos['id_alta'] = $_SESSION['id'];
    $datos['fecha_alta'] = date("Y-m-d H:i:s");

    $validacion_nombre = ($datos['id']) ? 
    GeneralModel::validarCampoEditarModel($datos['nombre'],"nombre","cafeterias_menu_productos",$datos['id'])
    : GeneralModel::validarCampoModel($datos['nombre'],"nombre","cafeterias_menu_productos");

    //en caso que el correo ya se encuentre registrado por otra cuenta retornamos el error y terminamos la ejecución
    if($validacion_nombre) return "error_validacion_nombre";

    if (!$datos['id']) {

        $datos['id'] = MenuProductosModel::agregarProductoModel($datos);

    } else {
        MenuProductosModel::editarProductoModel($datos);
    }
    
    $producto = MenuProductosModel::buscarProductoModel($datos['id']);
    
    if($datos['imagen_subir']){

        if($producto['imagen']!=""&&file_exists("../../".$producto['imagen'])&&$producto['imagen']!="views/assets/img/cafeteria_default.png") 
            unlink("../../".$producto['imagen']);
        $nombre_imagen = "imagen_producto_".$datos['id'];
        $datos['imagen'] = GeneralController::subirImagen($datos['imagen_subir'],"menu_productos",$nombre_imagen);
        MenuProductosModel::editarImagenModel($datos);

    }

    return "success"; 
}

/* AGREGAR PRODUCTOS */


}