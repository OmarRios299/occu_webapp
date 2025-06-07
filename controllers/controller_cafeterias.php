<?php 
class CafeteriasController{
    
    /* OBTENER CAFETERíAS */
    
    static public function obtenerCafeteriasController($datos){

        if($_SESSION['nivel']!='Administrador'){
            $datos['usuario'] = $_SESSION['id'];
        }
        $url = TemplateController::obtenerUrlController();
        $data = [];
        $i=0;
        foreach (CafeteriasModel::obtenerCafeteriasModel($datos) as $cafeteria){
            $checked = ($cafeteria['estado'] == 0) ? "checked" : "";
                $estado = '<div class="form-check form-switch">
                            <input type="checkbox"
                                class="form-check-input cambioEstado"
                                id="witch'.$cafeteria['id'].'"
                                tabla="cafeterias"
                                idRegistro="'.$cafeteria['id'].'"
                                '.$checked.'
                            <label class="custom-control-label" for="switch'.$cafeteria['id'].'"></label>
                        </div>';
            $botones = ' <a type="button" class="btn btn-icono btn-editar" href="'.$url. 'cafeterias/' . $cafeteria['id'].'/editar"></a>
                        <button class="btn btn-icono btn-eliminar eliminarRegistro" tabla="cafeterias" idRegistro="'.$cafeteria['id'].'"></button>
                        <button class="btn btn-icono btn-servicios agregar_servicios" idRegistro="'.$cafeteria['id'].'"></button>
                         <a type="button" class="btn btn-icono btn-menu" href="'.$url. 'propietarios_menu/' . $cafeteria['id'].'"></a>
                         <a type="button" class="btn btn-icono btn-qr" href="'.$url. 'propietarios_menu_qr/' . $cafeteria['id'].'"></a>';
            $data[]=[
                ++$i,
                    $botones,
                    $estado,    
                    '<img width="60px;" src="'.$cafeteria['imagen'].'" alt="">',
                    $cafeteria['nombre'],
                    $cafeteria['horario_apertura'] .' - '. $cafeteria['horario_cierre'],
                    $cafeteria['direccion'],
                    $cafeteria['telefono'],
                    $cafeteria['correo_electronico'],
                    $cafeteria['ciudad'],
                    $cafeteria['entidad_federativa'],
                    $cafeteria['pais'],
                    $cafeteria['usuario_alta'],
                    $cafeteria['fecha_alta'],
            ];
        };

        return json_encode(['data' => $data]);
    }
    
    /* OBTENER CAFETERíAS */

    
    /* OBTENER DATOS DE CAFETERIA */
    
    static public function obtenerDatosCafeteriaController($cafeteria){
        return CafeteriasModel::ObtenerDatosCafeteriaModel($cafeteria);
    }
    
    /* OBTENER DATOS DE CAFETERIA */
    
    
    /* OBTENER CIUDADES DE CADA PAIS */
    
    static public function obtenerCiudadesPaisController($pais){
        return CafeteriasModel::obtenerCiudadesPaisModel($pais);
    }
    
    /* OBTENER CIUDADES DE CADA PAIS */
    
    
    /* REGISTRAR CAFETERIA */
    
    static public function registrarCafeteriaController($datos){
        //var_dump($datos['horarios']);
        //validamos el correo electrónico y nombre
        if ($datos['correo']) {
            $validacion_email = ($datos['id']) && ($datos['correo']) ? 
            GeneralModel::validarCampoEditarModel($datos['correo'],"correo_electronico","cafeterias",$datos['id'])
            : GeneralModel::validarCampoModel($datos['correo'],"correo_electronico","cafeterias");
            if($validacion_email) return "error_validacion_email";
        }else{

        }

        $validacion_nombre = ($datos['id']) ? 
        GeneralModel::validarCampoEditarModel($datos['nombre'],"nombre","cafeterias",$datos['id'])
        : GeneralModel::validarCampoModel($datos['nombre'],"nombre","cafeterias");

        //en caso que el correo ya se encuentre registrado por otra cuenta retornamos el error y terminamos la ejecución
        
        if($validacion_nombre) return "error_validacion_nombre";

        if($datos['id']){
            
            CafeteriasModel::editarCafeteriaModel($datos);

            // Primero, eliminar todos los horarios existentes para esta cafetería
            CafeteriasModel::eliminarHorariosCafeteriaModel($datos['id']);

                // Manejar la actualización de los horarios en formato JSON
            if (isset($datos['horarios']) && is_array($datos['horarios'])) {

                // Insertar nuevamente los horarios detallados en la tabla `horarios_cafeteria`
                foreach ($datos['horarios'] as $dia => $horario) {
                    CafeteriasModel::insertarHorarioCafeteriaModel($datos['id'], $dia, $horario);
                }
            }

        }else{

            $datos['id_alta'] = $_SESSION['id'];
            $datos['fecha_alta'] = date("Y-m-d H:i:s");
            
            $datos['id'] = CafeteriasModel::insertarCafeteriaModel($datos);
            
            // Si los horarios están en formato JSON, insertar en la tabla de horarios
            if (isset($datos['horarios']) && is_array($datos['horarios'])) {
                // Insertar horarios detallados en la tabla `horarios_cafeteria`
                foreach ($datos['horarios'] as $dia => $horario) {
                    CafeteriasModel::insertarHorarioCafeteriaModel($datos['id'], $dia, $horario);
                }
            }
            

        }

        $cafeteria = CafeteriasModel::obtenerCafeteriaModel($datos['id']);

        if($datos['imagen_subir']){

            if($cafeteria['imagen']!=""&&file_exists("../../".$cafeteria['imagen'])&&$cafeteria['imagen']!="views/assets/img/cafeteria_default.png") 
                unlink("../../".$cafeteria['imagen']);
            $nombre_imagen = "imagen_cafeteria_".$datos['id'];
            $datos['imagen'] = GeneralController::subirImagen($datos['imagen_subir'],"cafeterias",$nombre_imagen);
            CafeteriasModel::editarImagenCafeteriaModel($datos);

        }
 
        return json_encode([
            "success"=>true,
            "id_cafeteria"=>$datos['id']
        ]); 
    }
    
    /* REGISTRAR CAFETERIA */
    
    
    /* CARGAR TABLA DE IAMGENS */
    
    static public function cargarTablaImagenesController($id){
        $url = TemplateController::obtenerUrlController();
        $i=0;
        $data=[];
        foreach (CafeteriasModel::obtenerImagenesCafeteriaModel($id) as $imagen){
            $checked = ($imagen['estado']==0) ? "checked" : "";
            $botones = '<button class="btn btn-icono btn-eliminar eliminarRegistro" tabla="cafeterias_imagenes" idRegistro="'.$imagen['id'].'"></button>';
            $estado = '<div class="form-check form-switch">
                            <input type="checkbox"
                            class="form-check-input cambioEstado"
                            id="switch'.$imagen['id'].'"
                            tabla="cafeterias_imagenes"
                            idRegistro="'.$imagen['id'].'"
                            '.$checked.'>
                            <label class="custom-control-label" for="switch'.$imagen['id'].'"></label>
                        </div>';
            $img = '<img src="'.$url.''.$imagen['imagen'].'" alt="" style="width: 100px; heigth: auto;">';
            $data[]=[
                ++$i,
                $botones,
                $estado,
                $img,
                $imagen['descripcion']
            ];
        }
        
     return json_encode(['data' => $data, 'i'=>$i]);

    }
    
    /* CARGAR TABLA DE IAMGENS */
    
    
    /* SUBIR IMAGEN DE CAFETERIA */
    
    static public function subirImagenCafeteriaController($datos){
        $i = is_numeric($datos['contador']) +1;
        $nombre_imagen = "cafeteria_".$datos['id']."_".$i;
        $datos['imagen'] = GeneralController::subirImagen($datos['imagen_subir'],"cafeterias_imagenes",$nombre_imagen);
        CafeteriasModel::agregarImagenesModel($datos);

        return 'success';
    }
    
    /* SUBIR IMAGEN DE CAFETERIA */

    
    /* OBTENER SERVICIOS */
    
    static public function obtenerServicioController($cafeteria){
        $url = TemplateController::obtenerUrlController();
        $data='';
        foreach (CafeteriasModel::obtenerServiciosControllerModel($cafeteria) as $servicio){
            $checked = ($servicio['servicio_registrado'] == 1) ? 'checked' : '';
            $data .='
                <label class="image-checkbox">
                    <input type="checkbox" class="chbx_servicios" idServicio="'.$servicio['id'].'" '.$checked.'/>
                    <div class="custom-carousel-item">
                        <div class="card card-cover overflow-hidden text-bg-dark rounded-4 shadow-lg" style="background-image: url('.$url.$servicio['imagen'].');">
                            <div class="d-flex flex-column p-3 pb-1 text-white titulo-oscuro text-center tanamo">
                                <h5 class="mt-4 display-8 lh-1 fw-bold">'.$servicio['nombre'].'</h5>
                            </div>
                        </div>
                    </div>
                </label>';
        }
        return json_encode($data);
    }
    
    /* OBTENER SERVICIOS */

    
    /* REGISTRAR SERVICIOS */
    
    static public function registrarServiciosController($datos){

        CafeteriasModel::eliminarServiciosAntiguosModel($datos['id_cafeteria']);

        foreach ($datos['servicios'] as $servicio){
            CafeteriasModel::registrarServiciosModel($datos['id_cafeteria'], $servicio['id']);
        }
        return 'success';
    }
    
    /* REGISTRAR SERVICIOS */
    
    
    /* BUSCAR HORARIO DIFERENTE */
    
    static public function buscarHorarioDiferenteController($cafeteria, $dia){
        return CafeteriasModel::buscarHorarioDiferenteModel($cafeteria, $dia);
    }
    
    /* BUSCAR HORARIO DIFERENTE */
    
    
} ?>