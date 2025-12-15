<?php

class TemplateController{

	/* LLAMADA A LA PLANTILLA */
	
	static public function template(){

		include "views/template.php";

	}
	
	/* End of LLAMADA A LA PLANTILLA */

	/* URL DEL PROYECTO */
	
	static public function obtenerUrlController(){

		// return "http://localhost/OCCU/occu_webApp/";
		return getenv("APP_URL");

	}
	
	/* End of URL DEL PROYECTO */
	
	static public function obtenerKeyGoogle(){
		return getenv('GOOGLE_API');
	}
	
	static public function obtenerGoogleClientId(){
		require_once __DIR__ . '/../config/env.php';
		return obtenerGoogleClientId();
	}
	
	
}