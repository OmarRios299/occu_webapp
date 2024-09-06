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

