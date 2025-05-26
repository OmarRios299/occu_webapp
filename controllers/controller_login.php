<?php

class LoginController
{

	/* INGRESO */

	static public function ingresoController($datosController)
	{
		// Encriptar
		$encriptar = crypt(
			$datosController['contrasena'],
			'$2a$07$asxx54ahjppf45sd87a5a4dDDGsystemdev$'
		);

		// Traer usuario
		$respuesta = LoginModel::ingresoModel($datosController['usuario']);

		// 1) Usuario no existe o contraseña incorrecta
		if (
			!$respuesta ||
			strtoupper($respuesta['correo_electronico']) !== strtoupper($datosController['usuario']) ||
			$respuesta['contrasena'] !== $encriptar
		) {
			return json_encode([
				'success' => false,
				'mensaje' => 'invalido'
			]);
		}

		// 2) Usuario desactivado
		if ($respuesta['estado'] != 0) {
			return json_encode([
				'success' => false,
				'mensaje' => 'desactivado'
			]);
		}

		// 3) No verificado
		if ($respuesta['verificado'] !== 'Si') {
			return json_encode([
				'success' => false,
				'mensaje' => 'verificacion'
			]);
		}

		// 4) OK: crear sesión y token
		session_start();
		$_SESSION['iniciarSesion']   = "ok";
		$_SESSION['id']              = $respuesta['id'];
		$_SESSION['nivel']           = $respuesta['nivel'];
		$_SESSION['nombre_completo'] = $respuesta['nombre_usuario'];
		$_SESSION['imagen_usuario']  = $respuesta['imagen'];
		$_SESSION['ciudad']          = $respuesta['id_ciudad'];
		$_SESSION['pais']            = $respuesta['id_pais'];

		$token_sesion = uniqid();
		date_default_timezone_set("America/Tijuana");
		setcookie('token_session', $token_sesion, time() + 8 * 3600, '/');
		LoginModel::actualizarTokenSession($respuesta['id'], $token_sesion);

		// Preparar ruta de redirección
		if ($respuesta['nivel'] == "Cliente") {
			$ruta = "cafeterias_lista";
		} else if ($respuesta['nivel'] == "Propietario") {
			$ruta = "cafeterias";
		}else{
			$ruta = "dashboard";
		}

		return json_encode([
			'success'  => true,
			'redirect' => $ruta
		]);
	}

	/* End of INGRESO */

	/* INGRESO TOKEN */

	static public function ingresoTokenController($token_sesion)
	{

		$respuesta = LoginModel::ingresoTokenModel($token_sesion);

		if ($respuesta) {
			$_SESSION['iniciarSesion'] = "ok";
			$_SESSION['id'] = $respuesta['id'];
			$_SESSION['nivel'] = $respuesta['nivel'];
			$_SESSION['nombre_completo'] = $respuesta['nombre_usuario'];
			$_SESSION['imagen_usuario'] = $respuesta['imagen'];
			$_SESSION['pais'] = $respuesta['pais'];
		}
	}

	/* INGRESO TOKEN */
}
