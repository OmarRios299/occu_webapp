<?php

require_once __DIR__ . '/../models/model_alertas.php';
require_once __DIR__ . '/../controllers/controller_alertas.php';

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

		// [NUEVO] Registrar sesión en usuarios_sesiones
		LoginModel::registrarSesionModel($respuesta['id'], $token_sesion, 'normal');

		// [NUEVO] Obtener alertas aplicables para el usuario
		$alertas = AlertaController::obtenerAlertasUsuarioController($respuesta['id'], $respuesta['nivel'], null);

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
			'redirect' => $ruta,
			'alertas'  => $alertas
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

	/* INGRESO CON GOOGLE */
	
	static public function ingresoGoogleController($id_token)
	{
		require_once __DIR__ . '/../config/env.php';
		loadEnv(__DIR__ . '/../.env');
		
		$client_id = obtenerGoogleClientId();
		
		if (!$client_id) {
			return json_encode([
				'success' => false,
				'mensaje' => 'Configuración de Google no encontrada'
			]);
		}

		// Validar el token con Google
		$url = 'https://oauth2.googleapis.com/tokeninfo?id_token=' . urlencode($id_token);
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
		$response = curl_exec($ch);
		$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);

		if ($http_code !== 200) {
			return json_encode([
				'success' => false,
				'mensaje' => 'token_invalido'
			]);
		}

		$token_data = json_decode($response, true);

		if (!$token_data || !isset($token_data['sub']) || $token_data['aud'] !== $client_id) {
			return json_encode([
				'success' => false,
				'mensaje' => 'token_invalido'
			]);
		}

		// Extraer datos del token
		$sub = $token_data['sub'];
		$email = isset($token_data['email']) ? $token_data['email'] : '';
		$name = isset($token_data['name']) ? $token_data['name'] : '';
		$picture = isset($token_data['picture']) ? $token_data['picture'] : '';

		// Buscar usuario por proveedor e id_usuario_proveedor
		$respuesta = LoginModel::buscarUsuarioPorProveedorModel('google', $sub);

		// Si no existe, crear nuevo usuario
		if (!$respuesta) {
			// Separar nombre y apellido si existe
			$nombre = '';
			$apellido = '';
			if ($name) {
				$name_parts = explode(' ', $name, 2);
				$nombre = $name_parts[0];
				$apellido = isset($name_parts[1]) ? $name_parts[1] : '';
			}

			// Preparar datos para crear usuario
			$datos_usuario = [
				'nombre' => $nombre ?: 'Usuario',
				'apellido' => $apellido ?: 'Google',
				'id_ciudad' => 0, // Por defecto sin ciudad
				'correo_electronico' => $email ?: '',
				'nivel' => 'Cliente', // Nivel por defecto
				'imagen' => $picture ?: 'views/assets/img/usuario_default.png',
				'id_usuario_proveedor' => $sub,
				'id_alta' => 0,
				'fecha_alta' => date("Y-m-d H:i:s")
			];

			$nuevo_usuario_id = LoginModel::crearUsuarioGoogleModel($datos_usuario);

			if ($nuevo_usuario_id === "error" || !is_numeric($nuevo_usuario_id)) {
				return json_encode([
					'success' => false,
					'mensaje' => 'Error al crear usuario'
				]);
			}

			// Obtener el usuario recién creado por ID
			$respuesta = LoginModel::obtenerUsuarioPorIdModel($nuevo_usuario_id);
			
			// Si aún no se encuentra, intentar buscar por proveedor como fallback
			if (!$respuesta) {
				$respuesta = LoginModel::buscarUsuarioPorProveedorModel('google', $sub);
			}
		}

		// Verificar que el usuario existe
		if (!$respuesta) {
			// Log para depuración (remover en producción)
			error_log("Error: No se pudo obtener usuario después de crear. Sub: " . $sub);
			return json_encode([
				'success' => false,
				'mensaje' => 'Error al obtener usuario'
			]);
		}

		// Verificar que el usuario no esté desactivado
		if ($respuesta['estado'] != 0) {
			return json_encode([
				'success' => false,
				'mensaje' => 'desactivado'
			]);
		}

		// Verificar si el usuario necesita completar información (verificado = "No")
		if ($respuesta['verificado'] === 'No') {
			return json_encode([
				'success' => false,
				'mensaje' => 'completar_info',
				'usuario_id' => $respuesta['id']
			]);
		}

		// Crear sesión igual que el login tradicional
		session_start();
		$_SESSION['iniciarSesion']   = "ok";
		$_SESSION['id']              = $respuesta['id'];
		$_SESSION['nivel']           = $respuesta['nivel'];
		$_SESSION['nombre_completo'] = $respuesta['nombre_usuario'];
		$_SESSION['imagen_usuario']  = $respuesta['imagen'];
		$_SESSION['ciudad']          = $respuesta['id_ciudad'];
		$_SESSION['pais']            = isset($respuesta['id_pais']) && $respuesta['id_pais'] ? $respuesta['id_pais'] : null;

		$token_sesion = uniqid();
		date_default_timezone_set("America/Tijuana");
		setcookie('token_session', $token_sesion, time() + 8 * 3600, '/');
		LoginModel::actualizarTokenSession($respuesta['id'], $token_sesion);

		// [NUEVO] Registrar sesión en usuarios_sesiones
		LoginModel::registrarSesionModel($respuesta['id'], $token_sesion, 'google');

		// [NUEVO] Obtener alertas aplicables para el usuario
		$alertas = AlertaController::obtenerAlertasUsuarioController($respuesta['id'], $respuesta['nivel'], null);

		// Preparar ruta de redirección
		if ($respuesta['nivel'] == "Cliente") {
			$ruta = "cafeterias_lista";
		} else if ($respuesta['nivel'] == "Propietario") {
			$ruta = "cafeterias";
		} else {
			$ruta = "dashboard";
		}

		return json_encode([
			'success'  => true,
			'redirect' => $ruta,
			'alertas'  => $alertas
		]);
	}

	/* End of INGRESO CON GOOGLE */

	/* COMPLETAR INFORMACION USUARIO GOOGLE */
	
	static public function completarInfoUsuarioGoogleController($datosController)
	{
		if (empty($datosController['id_usuario']) || 
			empty($datosController['nivel'])) {
			return json_encode([
				'success' => false,
				'mensaje' => 'El tipo de usuario es obligatorio'
			]);
		}

		// Ciudad por defecto si no se proporciona
		$id_ciudad = isset($datosController['id_ciudad']) && $datosController['id_ciudad'] > 0 
			? $datosController['id_ciudad'] 
			: 0;

		$resultado = LoginModel::completarInfoUsuarioGoogleModel(
			$datosController['id_usuario'],
			$id_ciudad,
			$datosController['nivel']
		);

		if ($resultado === 'success') {
			// Obtener el usuario actualizado
			$respuesta = LoginModel::obtenerUsuarioPorIdModel($datosController['id_usuario']);

			if ($respuesta) {
				// Crear sesión
				session_start();
				$_SESSION['iniciarSesion']   = "ok";
				$_SESSION['id']              = $respuesta['id'];
				$_SESSION['nivel']           = $respuesta['nivel'];
				$_SESSION['nombre_completo'] = $respuesta['nombre_usuario'];
				$_SESSION['imagen_usuario']  = $respuesta['imagen'];
				$_SESSION['ciudad']          = $respuesta['id_ciudad'];
				$_SESSION['pais']            = isset($respuesta['id_pais']) && $respuesta['id_pais'] ? $respuesta['id_pais'] : null;

				$token_sesion = uniqid();
				date_default_timezone_set("America/Tijuana");
				setcookie('token_session', $token_sesion, time() + 8 * 3600, '/');
				LoginModel::actualizarTokenSession($respuesta['id'], $token_sesion);

				// [NUEVO] Registrar sesión en usuarios_sesiones
				LoginModel::registrarSesionModel($respuesta['id'], $token_sesion, 'google');

				// [NUEVO] Obtener alertas aplicables para el usuario
				$alertas = AlertaController::obtenerAlertasUsuarioController($respuesta['id'], $respuesta['nivel'], null);

				// Preparar ruta de redirección
				if ($respuesta['nivel'] == "Cliente") {
					$ruta = "cafeterias_lista";
				} else if ($respuesta['nivel'] == "Propietario") {
					$ruta = "cafeterias";
				} else {
					$ruta = "dashboard";
				}

				return json_encode([
					'success'  => true,
					'redirect' => $ruta,
					'alertas'  => $alertas
				]);
			}
		}

		return json_encode([
			'success' => false,
			'mensaje' => 'Error al completar la información'
		]);
	}

	/* End of COMPLETAR INFORMACION USUARIO GOOGLE */
}
