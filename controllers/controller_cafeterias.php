<?php 
class CafeteriasController{
    
    /* OBTENER CAFETERíAS */
    
    static public function obtenerCafeteriasController(){
        return CafeteriasModel::obtenerCafeteriasModel();
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

        //validamos el correo electrónico y nombre
        $validacion_email = ($datos['id']) && ($datos['correo']) ? 
        GeneralModel::validarCampoEditarModel($datos['correo'],"correo_electronico","cafeterias",$datos['id'])
        : GeneralModel::validarCampoModel($datos['correo'],"correo_electronico","cafeterias");

        $validacion_nombre = ($datos['id']) ? 
        GeneralModel::validarCampoEditarModel($datos['nombre'],"nombre","cafeterias",$datos['id'])
        : GeneralModel::validarCampoModel($datos['nombre'],"nombre","cafeterias");

        //en caso que el correo ya se encuentre registrado por otra cuenta retornamos el error y terminamos la ejecución
        if($validacion_email) return "error_validacion_email";
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
 
        return "success"; 
    }
    
    /* REGISTRAR CAFETERIA */
    
    
} ?>