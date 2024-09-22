<?php 

class CafeteriasServiciosController{

/* OBTENER CATEGORIAS  */

static public function obtenerServiciosController(){
    $url = TemplateController::obtenerUrlController();
    $data = [];
    $i =0;
    foreach(CafeteriasServiciosModel::obtenerServiciosModel() as $servicio){
        $checked = ($servicio['estado']==0) ? "checked" : "";
        $imagen='<img src="'.$url .''. $servicio['imagen'].'" style="width:80px;">';
        $botones = '<button class="btn btn-icono btn-eliminar eliminarRegistro" tabla="servicios" idRegistro="'.$servicio['id'].'"></button>    
                    <button class="btn btn-icono btn-editar btn_editar_servicio" imagen="'.$url .''. $servicio['imagen'].'" idRegistro="'.$servicio['id'].'" nombre="'.$servicio['nombre'].'"></button>';
        $estado = '<div class="form-check form-switch">
                <input type="checkbox"
                class="form-check-input cambioEstado"
                id="switch'.$servicio['id'].'"
                tabla="servicios"
                idRegistro="'.$servicio['id'].'"
                '.$checked.'>
                <label class="custom-control-label" for="switch'.$servicio['id'].'"></label>
            </div>';
        $data[]=[
            ++$i,
            $botones,
            $estado,
            $imagen,
            $servicio['nombre'],
            $servicio['usuario_alta'],
            $servicio['fecha_alta']
        ];
    }

    return json_encode(['data' => $data]);
}

/* OBTENER CATEGORIAS  */


/* AGREGAR CATEGORIAS */

static public function agregarServiciosController($datos){
    $datos['id_alta'] = $_SESSION['id'];
    $datos['fecha_alta'] = date("Y-m-d H:i:s");

    $validacion_nombre = ($datos['id']) ? 
    GeneralModel::validarCampoEditarModel($datos['nombre'],"nombre","servicios",$datos['id'])
    : GeneralModel::validarCampoModel($datos['nombre'],"nombre","servicios");

    //en caso que el correo ya se encuentre registrado por otra cuenta retornamos el error y terminamos la ejecución
    if($validacion_nombre) return "error_validacion_nombre";

    if (!$datos['id']) {

        $datos['id'] = CafeteriasServiciosModel::agregarServicioModel($datos);

    } else {
        CafeteriasServiciosModel::editarServicioModel($datos);
    }
    
    $servicio = CafeteriasServiciosModel::buscarServicioModel($datos['id']);
    
    if($datos['imagen_subir']){

        if($servicio['imagen']!=""&&file_exists("../../".$servicio['imagen'])&&$servicio['imagen']!="views/assets/img/cafeteria_default.png") 
            unlink("../../".$servicio['imagen']);
        $nombre_imagen = "imagen_servicio_".$datos['id'];
        $datos['imagen'] = GeneralController::subirImagen($datos['imagen_subir'],"servicios",$nombre_imagen);
        CafeteriasServiciosModel::editarImagenModel($datos);

    }

    return "success"; 
}

/* AGREGAR CATEGORIAS */


}
