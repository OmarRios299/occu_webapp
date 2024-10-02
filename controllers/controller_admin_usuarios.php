<?php 
class AdminUsuariosController{
    
    /* OBTENER USUARIOS */
    
    static public function obtenerUsuariosController($datos){
        $url = TemplateController::obtenerUrlController();
        $i=0;
        $data=[];
        foreach (AdminUsuariosModel::obtenerUsuariosModel($datos) as $usuario) {
            // Determinar si el checkbox debe estar marcado
            $checked = ($usuario['estado'] == 0) ? "checked" : "";
            $botones = '<a href="' . $url . 'admin_usuarios/' . $usuario['id'] . '/editar" class="btn btn-icono btn-editar"></a>
                        <a href="' . $url . 'admin_usuarios/' . $usuario['id'] . '/ver" class="btn btn-icono btn-ver"></a>
                        <button class="btn btn-icono btn-eliminar eliminarRegistro" tabla="admin_usuarios" idRegistro="' . $usuario['id'] . '"></button>';
            $estado = '<div class="form-check form-switch">
                        <input type="checkbox" class="form-check-input cambioEstado" id="switch' . $usuario['id'] . '" tabla="admin_usuarios" idRegistro="' . $usuario['id'] . '" ' . $checked . '>
                        <label class="form-check-label" for="switch' . $usuario['id'] . '"></label>
                        </div>';
            $data[] = [
                ++$i,
                $botones,
                $estado,
                '<img width="60px" src="' . $usuario['imagen'] . '" alt="Imagen de usuario">',
                $usuario['nombre_usuario'],
                $usuario['correo_electronico'],
                $usuario['telefono'],
                $usuario['ciudad'],
                $usuario['entidad_federativa'],
                $usuario['pais'],
                $usuario['nivel'],
                $usuario['fecha_alta']
            ];
        }
        return json_encode(['data'=>$data]);
    }
    
    /* OBTENER USUARIOS */
    
    
    /* INSERTAR USUARIO */
    
    static public function inserarUsuarioController($datos){
        return AdminUsuariosModel::insertarUsuariosModel($datos);
    }
    
    /* INSERTAR USUARIO */
    
    
    /* OBTENER INFORMACION DE USUARIO */
    
    static public function obtenerInfoUsuarioController($id){
        return AdminUsuariosModel::obtenerInfoUsuarioModel($id);
    }
    
    /* OBTENER INFORMACION DE USUARIO */

        /**
     * REGISTRAR USUARIO
     * Validar un usuario y registrarlo en la bd 
    */
    
    static public function registrarUsuarioController($datos){

        date_default_timezone_set("America/Tijuana");

        //validamos el correo electrónico
        $validacion_email = ($datos['id']) ? 
        GeneralModel::validarCampoEditarModel($datos['correo_electronico'],"correo_electronico","admin_usuarios",$datos['id'])
        : GeneralModel::validarCampoModel($datos['correo_electronico'],"correo_electronico","admin_usuarios");

        //en caso que el correo ya se encuentre registrado por otra cuenta retornamos el error y terminamos la ejecución
        if($validacion_email) return "error_validacion_email";

        //si se envio una contraseña la editamos
        if($datos['contrasena']) $datos['contrasena'] = crypt($datos['contrasena'],'$2a$07$asxx54ahjppf45sd87a5a4dDDGsystemdev$');
        
        //verificamos si enviamos un id, para identificar si se trata de un alta o una edición
        if($datos['id']){
            
            //registramos los cambios en la bd
            AdminUsuariosModel::editarUsuarioModel($datos);

        }else{

            //damos de alta el registro en la bd
            $datos['id_alta'] = $_SESSION['id'];
            $datos['fecha_alta']= date("Y-m-d H:i:s");
            $datos['id'] = AdminUsuariosModel::insertarUsuarioModel($datos);

        }

        // buscamos al usuario para obtener el nombre del archivo de la imagen actual
        $usuario = AdminUsuariosModel::buscarUsuarioModel($datos['id']);

        if($datos['imagen_subir']){

            //verificamos si la imagen existe para eliminarla
            if($usuario['imagen']!=""&&file_exists("../../".$usuario['imagen'])&&$usuario['imagen']!="views/assets/img/usuario_default.png") 
                unlink("../../".$usuario['imagen']);
            //almacenar imagen en el servidor
            $nombre_imagen = "imagen_usuario_".$datos['id'];
            $datos['imagen'] = GeneralController::subirImagen($datos['imagen_subir'],"admin_usuarios",$nombre_imagen);
            //editamos la imagen del usuario
            AdminUsuariosModel::editarImagenUsuarioModel($datos);

        }
 
        return "success"; 
    } 
    
    /* REGISTRAR USUARIO */
    
} 