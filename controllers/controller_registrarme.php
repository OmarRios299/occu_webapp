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
        RegistrarmeModel::insertarUsuarioModel($datos);
        //Mailchimp::enviarCorreoRegistroController();

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

}
?>