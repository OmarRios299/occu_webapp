<?php

require_once "conexion.php";

class GeneralModel extends Conexion{

	/* CAMBIO DE ESTADO EN UN ELEMENTO SWITCH */
	
	static public function cambioEstadoSwitchModel($estado,$id,$tabla){

		$stmt = Conexion::conectar()->prepare("UPDATE $tabla SET estado = :estado WHERE id = :id");

        $stmt -> bindParam(":estado", $estado, PDO::PARAM_INT);
        $stmt -> bindParam(":id", $id, PDO::PARAM_INT);

        if($stmt->execute()){

            return 'success';

        }else{

            return 'error';
        }   

	}
	
	/* End of CAMBIO DE ESTADO EN UN ELEMENTO SWITCH */

	/* VALIDAR CAMPO */
	
	static public function validarCampoModel($valor,$columna,$tabla){
// var_dump($tabla);
		$stmt = Conexion::conectar()->prepare("SELECT $columna FROM $tabla WHERE $columna = :$columna AND estado <> 2");

		$stmt -> bindParam(":".$columna, $valor, PDO::PARAM_STR);

		$stmt -> execute();

		return $stmt -> fetch();

		$stmt -> close();

		$stmt = null;

	}
	
	/* End of VALIDAR CAMPO */

	/* VALIDAR UN CAMPO EDITAR */
	
	static public function validarCampoEditarModel($valor,$columna,$tabla,$id){

		$stmt = Conexion::conectar()->prepare("SELECT $columna FROM $tabla WHERE $columna = :$columna AND estado <> 2 AND id <> :id");

		$stmt -> bindParam(":".$columna, $valor, PDO::PARAM_STR);
		$stmt -> bindParam(":id", $id, PDO::PARAM_INT);

		$stmt -> execute();

		return $stmt -> fetch();

		$stmt -> close();

		$stmt = null;

	}
	
	/* End of VALIDAR UN CAMPO EDITAR */
	
	/* ELIMINAR REGISTRO */
	
	static public function eliminarRegistroModel($id,$tabla){

		$stmt = Conexion::conectar()->prepare("UPDATE $tabla SET estado = 2 WHERE id = :id");

        $stmt -> bindParam(":id", $id, PDO::PARAM_INT);

        if($stmt->execute()){

            return 'success';

        }else{

            return 'error';
        }   

	}
	
	/* End of ELIMINAR REGISTRO */

	/* VALIDAR CAMPO SECUNDARIO */
	
	static public function validarCampoSecundarioModel($valorPrimario,$columnaPrimario,$valorSecundario,$columnaSecundario,$tabla){

		$stmt = Conexion::conectar()->prepare("SELECT 
		$columnaPrimario,
		$columnaSecundario FROM $tabla WHERE $columnaPrimario = :valorPrimario AND $columnaSecundario = :valorSecundario AND estado <> 2");

		$stmt -> bindParam(":valorPrimario",$valorPrimario, PDO::PARAM_STR);
		$stmt -> bindParam(":valorSecundario",$valorSecundario, PDO::PARAM_INT);

		$stmt -> execute();

		return $stmt -> fetch();

		$stmt = null;

	}
	
	/* End of VALIDAR CAMPO SECUNDARIO */

	/* VALIDAR CAMPO SECUNDARIO EDITAR */
	
	static public function validarCampoSecundarioEditarModel($valorPrimario,$columnaPrimario,$valorSecundario,$columnaSecundario,$tabla,$id){

		$stmt = Conexion::conectar()->prepare("SELECT 
		$columnaPrimario,
		$columnaSecundario FROM $tabla WHERE $columnaPrimario = :valorPrimario AND $columnaSecundario = :valorSecundario AND estado <> 2 AND id <> :id");

		$stmt -> bindParam(":valorPrimario", $valorPrimario, PDO::PARAM_STR);
		$stmt -> bindParam(":valorSecundario", $valorSecundario, PDO::PARAM_INT);
		$stmt -> bindParam(":id", $id, PDO::PARAM_INT);

		$stmt -> execute();

		return $stmt -> fetch();

		$stmt = null;

	}
	
	/* End of VALIDAR CAMPO SECUNDARIO EDITAR */

	
	/* OBTENER PAISES */
	
	static public function obtenerPaisesModel(){
	
		$stmt = Conexion::conectar()->prepare("SELECT * FROM paises WHERE estado = 0");		
	
		$stmt -> execute();
	
		return $stmt -> fetchAll();
	
		$stmt = null;
	
	}
	
	/* OBTENER PAISES */

	/* OBTENER PAISES */

	static public function obtenerEstadosModel(){

		$stmt = Conexion::conectar()->prepare("SELECT * FROM entidades_federativas WHERE estado = 0");		
	
		$stmt -> execute();
	
		return $stmt -> fetchAll();
	
		$stmt = null;
	
	}
	
	/* OBTENER PAISES */

	/* OBTENER CIUDADES */
	
	static public function obtenerCiudadesModel(){
	
		$stmt = Conexion::conectar()->prepare("SELECT * FROM ciudades WHERE estado = 0");		
	
		$stmt -> execute();
	
		return $stmt -> fetchAll();
	
		$stmt = null;
	
	}
	
	/* OBTENER CIUDADES */

	/* OBTENER CIUDADES */
	
	static public function obtenerCiudadesPorPaisModel($pais){
	
		$stmt = Conexion::conectar()->prepare("SELECT
		ciudades.*
		FROM
			ciudades
		INNER JOIN entidades_federativas ON ciudades.id_entidad_federativa = entidades_federativas.id
		WHERE
			ciudades.estado = 0 AND entidades_federativas.id_pais=:id");

		$stmt -> bindParam(":id", $pais, PDO::PARAM_INT);
	
		$stmt -> execute();
	
		return $stmt -> fetchAll();
	
		$stmt = null;
	
	}
	
	/* OBTENER CIUDADES */

	
	/* OBTENER ESTADOS (ENTIDADES FEDERATIVAS) */
	
	static public function obtenerEstadosControllerModel($filtro){
	
		$filtro = ($filtro['pais']) ? "AND id_pais=:pais" : "";

		$stmt = Conexion::conectar()->prepare("SELECT * FROM entidades_federativas WHERE estado = 0 $filtro");
	
		if($filtro['pais'])
		$stmt->bindParam(':pais', $filtro['pais'],PDO::PARAM_INT);
	
		$stmt -> execute();
	
		return $stmt -> fetchAll();
	
		$stmt = null;
	
	}
	
	/* OBTENER ESTADOS (ENTIDADES FEDERATIVAS) */
	

	// Obtener los horarios simples de una cafetería

    static public function obtenerHorariosSimplesCafeteriaModel($id_cafeteria) {
        $conexion = Conexion::conectar();
        $stmt = $conexion->prepare("SELECT horario_apertura, horario_cierre FROM cafeterias WHERE id = :id_cafeteria");
        $stmt->bindParam(':id_cafeteria', $id_cafeteria, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Obtener los horarios detallados (JSON) de una cafetería
	
    static public function obtenerHorariosDetalladosCafeteriaModel($id_cafeteria) {
        $conexion = Conexion::conectar();
        $stmt = $conexion->prepare("SELECT dia, hora_apertura, hora_cierre, cerrado FROM cafeteria_horarios WHERE id_cafeteria = :id_cafeteria");
        $stmt->bindParam(':id_cafeteria', $id_cafeteria, PDO::PARAM_INT);
        $stmt->execute();
        
        $horarios = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $horarios[$row['dia']] = [
                'apertura' => $row['hora_apertura'],
                'cierre' => $row['hora_cierre'],
                'cerrado' => $row['cerrado']
            ];
        }

        return $horarios;
    }
	
}