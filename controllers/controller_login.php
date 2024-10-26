<?php

class LoginController{

	/* INGRESO */
	
	static public function ingresoController($datosController){

		
		$encriptar = crypt($datosController['contrasena'],'$2a$07$asxx54ahjppf45sd87a5a4dDDGsystemdev$');
		
		$respuesta = LoginModel::ingresoModel($datosController["usuario"]);
		
		if($respuesta){
			//usuario correcto
			
			if(strtoupper($respuesta['correo_electronico']) == strtoupper($datosController['usuario']) && $respuesta['contrasena'] == $encriptar){
				//usuario y contraseña validos
				
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
				//usuario o contraseña invalidos

				echo 'invalido';
			}

		}else{
			//usuario incorrecto

			echo 'invalido';
		}

	}
	
	/* End of INGRESO */	

	/* INGRESO TOKEN */
	
	static public function ingresoTokenController($token_sesion){

		$respuesta = LoginModel::ingresoTokenModel($token_sesion);
		
		if($respuesta){
			$_SESSION['iniciarSesion'] = "ok";
			$_SESSION['id'] = $respuesta['id'];
			$_SESSION['nivel'] = $respuesta['nivel'];
			$_SESSION['nombre_completo'] = $respuesta['nombre'].' '.$respuesta['apellido'];
			$_SESSION['imagen_usuario'] = $respuesta['imagen'];
		}
	}
	
	/* INGRESO TOKEN */
}