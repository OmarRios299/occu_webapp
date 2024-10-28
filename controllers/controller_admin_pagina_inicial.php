<?php 

class AdminPaginaInicialController{
    
    /* OBTENER CAROUSEL CONTROLLER */
    
    static public function obtenerCarouseController(){
        return AdminPaginaInicialModel::obtenerCarouselModel();
    }
    
    /* OBTENER CAROUSEL CONTROLLER */

    
    /* OBTENER CARDS */
    
    static public function obtenerCardsController(){
        return AdminPaginaInicialModel::obtenerCardsModel();
    }
    
    /* OBTENER CARDS */
    
    
    /* REGISTRAR IMAGEN */
    
    static public function registrarImagenController($datos){
         if($datos['id']){
            AdminPaginaInicialModel::actualizarRegistroImagenModel($datos);
        }else{
            $datos['id'] = AdminPaginaInicialModel::insertarRegistroImagenModel($datos);
        }

        $usuario = AdminPaginaInicialModel::buscarImagenModel($datos['id']);

        if($datos['imagen_subir']){

            if($usuario['imagen']!=""&&file_exists("../../".$usuario['imagen'])&&$usuario['imagen']!="views/assets/img/usuario_default.png") 
                unlink("../../".$usuario['imagen']);
            $nombre_imagen = "imagen";
            $datos['imagen'] = GeneralController::subirImagen($datos['imagen_subir'],"pagina_inicio",$nombre_imagen);
            AdminPaginaInicialModel::editarImagenModel($datos);

        }
    }
    
    /* REGISTRAR IMAGEN */
    
    
}