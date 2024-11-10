<?php   
class RegistrarmeController{
   
   static public function registrarUsuarioController($datos){

       date_default_timezone_set("America/Tijuana");

       //validamos el correo electrónico
       $validacion_email = GeneralModel::validarCampoModel($datos['correo'],"correo_electronico","admin_usuarios");

       //en caso que el correo ya se encuentre registrado por otra cuenta retornamos el error y terminamos la ejecución
       if($validacion_email) return "error_validacion_email";

       //si se envio una contraseña la editamos
       if($datos['contrasena']) $datos['contrasena'] = crypt($datos['contrasena'],'$2a$07$asxx54ahjppf45sd87a5a4dDDGsystemdev$');
       
        //damos de alta el registro en la bd
        $datos['id_alta'] = 0;
        $datos['fecha_alta']= date("Y-m-d H:i:s");

        $datos['pin'] = rand(100000, 999999); // Generar un PIN de 6 dígitos
        RegistrarmeModel::insertarUsuarioModel($datos);
        MailController::enviarCorreoRegistro($datos);

       return "success"; 
   }

   	/* VALIDAR UN CAMPO */

	static public function validarCampoController($datosController)
	{

		$valor = $datosController['valor'];
		$columna = $datosController['columna'];
		$tabla = $datosController['tabla'];

		$respuesta = GeneralModel::validarCampoModel($valor, $columna, $tabla);

		if ($respuesta) {
			echo 0;
		} else {
			echo 1;
		}
	}

	/* End of VALIDAR UN CAMPO */

    
	/* INGRESO */
	
	static public function validarUsuarioController($datosController){

		
		$encriptar = crypt($datosController['contrasena'],'$2a$07$asxx54ahjppf45sd87a5a4dDDGsystemdev$');
		
		$respuesta = LoginModel::ingresoModel($datosController["usuario"]);
		
		if($respuesta){
			//usuario correcto
			
			if(strtoupper($respuesta['correo_electronico']) == strtoupper($datosController['usuario']) && $respuesta['contrasena'] == $encriptar){
				//usuario y contraseña validos
				
				if ($respuesta['pin']==$datosController['pin']) {

                    RegistrarmeModel::codigoVerificacionModel($respuesta);
                    if($respuesta['estado'] == 0){
                        //usuario activo
                        
                        //variables de sesión
                        session_start();
                        $_SESSION['iniciarSesion'] = "ok";
                        $_SESSION['id'] = $respuesta['id'];
                        $_SESSION['nivel'] = $respuesta['nivel'];
                        $_SESSION['nombre_completo'] = $respuesta['nombre'];
                        $_SESSION['imagen_usuario'] = $respuesta['imagen'];
                        $_SESSION['ciudad'] = $respuesta['id_ciudad'];
                        $_SESSION['pais'] = $respuesta['id_pais'];
                        
                        $token_sesion = uniqid();
                        date_default_timezone_set("America/Tijuana");
                        setcookie('token_session', $token_sesion,time() + (3600 * 8),'/');
                        
                        LoginModel::actualizarTokenSession($respuesta['id'],$token_sesion);
                        
                        echo 'dashboard';
    
                    }else{
                        //usuario desactivado
    
                        echo 'desactivado';
                    }
    
                }else{
                    echo 'verificacion';
                }
			}else{
				//usuario o contraseña invalidos

				echo 'invalido';
			}

		}else{
			//usuario incorrecto

			echo 'invalido';
		}

	}
	
	/* End of INGRESO */	

    
    /* REENVIAR CODIGO */
    
    static public function reenviarCodigoController($datos){
        $respuesta = LoginModel::ingresoModel($datos["correo"]);
        if (!$respuesta) {
            return 'invalido';
        }else if ($respuesta['verificado']=='Si') {
            return 'verificado';
        }else{
            $datos['pin'] = rand(100000, 999999);
            RegistrarmeModel::reenviarcodigoModel($datos);
            MailController::enviarCorreoRegistro($datos);
        }
        return 'success';
        
    }
    
    /* REENVIAR CODIGO */
    
    
}
?>