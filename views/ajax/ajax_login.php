<?php

require_once "../../controllers/controller_login.php";
require_once "../../models/model_login.php";

class Login{

	public $datosLogin;
	public $controllerLogin;

	public function funcionLogin(){

		$datosLogin = $this->datosLogin;
		$controllerLogin = $this->controllerLogin;

		$respuesta = LoginController::$controllerLogin($datosLogin);

		echo $respuesta;

	}

}

/*===============================
=            INGRESO            =
===============================*/

if(isset($_POST['usuarioIngreso'])){

	$datosLogin = array(
		"usuario"=>trim($_POST['usuarioIngreso']),
		"contrasena"=>$_POST['contrasenaIngreso']
	);

	$a = new Login();
	$a -> datosLogin = $datosLogin;
	$a -> controllerLogin = "ingresoController";
	$a -> funcionLogin();

}

/*=====  End of INGRESO  ======*/

/*===============================
=       INGRESO CON GOOGLE      =
===============================*/

if(isset($_POST['googleLogin']) && isset($_POST['googleToken'])){

	$googleToken = $_POST['googleToken'];
	
	$respuesta = LoginController::ingresoGoogleController($googleToken);
	
	echo $respuesta;

}

/*=====  End of INGRESO CON GOOGLE  ======*/

/*===============================
=  COMPLETAR INFO USUARIO GOOGLE  =
===============================*/

if(isset($_POST['completarInfoGoogle'])){

	$datos = array(
		"id_usuario" => $_POST['id_usuario'],
		"id_ciudad" => $_POST['id_ciudad'],
		"nivel" => $_POST['nivel']
	);
	
	$respuesta = LoginController::completarInfoUsuarioGoogleController($datos);
	
	echo $respuesta;

}

/*=====  End of COMPLETAR INFO USUARIO GOOGLE  ======*/

