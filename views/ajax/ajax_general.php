<?php

require_once "../../controllers/controller_general.php";
require_once "../../models/model_general.php";

class General{

	public $datos;
	public $controller;

	public function funcionGeneral(){
		$datos = $this->datos;
		$controller = $this->controller;
		$respuesta = GeneralController::$controller($datos);
		echo $respuesta;
	}

}

session_start();

if(isset($_SESSION['iniciarSesion']) && $_SESSION['iniciarSesion'] == 'ok'){
	
	if(isset($_POST['estadoSwitch'])){
		
		/* CAMBIO DE ESTADO EN UN ELEMENTO SWITCH */
		$datos = array(
			"estado"=>$_POST['estadoSwitch'],
			"tabla"=>$_POST['tablaSwitch'],
			"id"=>$_POST['idSwitch']
		);
		$funcion = "cambioEstadoSwitchController";

	}else if(isset($_POST['valorCampo'])){
		
		/* VALIDAR CAMPO */
		$datos = array(
			"valor"=>$_POST['valorCampo'],
			"columna"=>$_POST['columnaCampo'],
			"tabla"=>$_POST['tablaCampo']
		);
		$funcion = "validarCampoController";
		
	}else if(isset($_POST['idEliminar'])){

		/* ELIMINAR REGISTRO */
		$datos = array(
			"id"=>$_POST['idEliminar'],
			"tabla"=>$_POST['tablaEliminar']
		);
		$funcion = "eliminarRegistroController";

	}else if(isset($_POST['valorCampoPrimario'])){
		
		/* VALIDAR CAMPO SECUNDARIO */
		$datos = array(
			"valorPrimario"=>$_POST['valorCampoPrimario'],
			"columnaPrimario"=>$_POST['columnaCampoPrimario'],
			"valorSecundario"=>$_POST['valorCampoSecundario'],
			"columnaSecundario"=>$_POST['columnaCampoSecundario'],
			"tabla"=>$_POST['tablaCampo']
		);
		$funcion = "validarCampoSecundarioController";

	}else if(isset($_POST['valorCampoEditar'])){
	
		/* VALIDAR CAMPO EDITAR */
		$datos = array(
			"valor"=>$_POST['valorCampoEditar'],
			"columna"=>$_POST['columnaCampoEditar'],
			"id"=>$_POST['idRegistro'],
			"tabla"=>$_POST['tablaCampoEditar']
		);
		$funcion = "validarCampoEditarController";

	}else if(isset($_POST['valorCampoPrimarioEditar'])){
	
		/* VALIDAR CAMPO SECUNDARIO EDITAR */
		$datos = array(
			"valorPrimario"=>$_POST['valorCampoPrimarioEditar'],
			"columnaPrimario"=>$_POST['columnaCampoPrimarioEditar'],
			"valorSecundario"=>$_POST['valorCampoSecundarioEditar'],
			"columnaSecundario"=>$_POST['columnaCampoSecundarioEditar'],
			"tabla"=>$_POST['tablaCampoEditar'],
			"id"=>$_POST['idRegistro']
		);
		$funcion = "validarCampoSecundarioEditarController";

	}else if(isset($_POST['cargar_estados'])){
	
		/* CARGAR ENTIDADES FEDERATIVAS */
		$datos = array(
			"pais"=>$_GET['id_pais'],
		);
		$funcion = "obtenerEstadosController";

	}else if(isset($_GET['cargar_ciudades'])){
	
		/* CARGAR ENTIDADES FEDERATIVAS */
		$datos =$_GET['id_pais'];
		$funcion = "obtenerCiudadesPorPaisController";

	}else{
		$datos = false;
	}
	
	if($datos){
		$a = new General();
		$a -> datos = $datos;
		$a -> controller = $funcion;
		$a -> funcionGeneral();
	}

}else{
	echo "session_expired";
}



	
	


	
	

	



