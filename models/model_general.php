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
	
	static public function obtenerCiudadesPorPaisModel($datos){
		$estado ='';
		$pais = '';
		if ($datos['estado']!='') {
			$estado = 'AND ciudades.id_entidad_federativa = :estado';
		}
		if ($datos['pais']!='') {
			$pais = 'AND entidades_federativas.id_pais = :pais';
		}
		$stmt = Conexion::conectar()->prepare("SELECT
		ciudades.*,
		paises.nombre AS pais
		FROM
			ciudades
		INNER JOIN entidades_federativas ON ciudades.id_entidad_federativa = entidades_federativas.id
		INNER JOIN paises ON entidades_federativas.id_pais = paises.id
		WHERE
			ciudades.estado = 0
			$estado
			$pais");

		if ($datos['estado']!='') {
			$stmt -> bindParam(":estado", $datos['estado'], PDO::PARAM_INT);
		}
		if ($datos['pais']!='') {
			$stmt -> bindParam(":pais", $datos['pais'], PDO::PARAM_INT);
		}
		$stmt -> execute();
	
		return $stmt -> fetchAll();
	
		$stmt = null;
	
	}
	
	/* OBTENER CIUDADES */

	
	/* OBTENER ESTADOS (ENTIDADES FEDERATIVAS) */
	
	static public function obtenerEstadosControllerModel($datos){
	
		$filtro = ($datos['pais']) ? "AND id_pais=:pais" : "";

		$stmt = Conexion::conectar()->prepare("SELECT * FROM entidades_federativas WHERE estado = 0 $filtro");
	
		if($datos['pais'])
		$stmt->bindParam(':pais', $datos['pais'],PDO::PARAM_INT);
	
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
	
	
	/* OBTENER NIVELES DE USUARIO */
	
	static public function obtenerNivelesUsuarioModel(){
	
		$stmt = Conexion::conectar()->prepare("SELECT * FROM admin_niveles_usuario WHERE estado!=2 ");
	
		//$stmt->bindParam(':', ,PDO::PARAM_STR);
	
		$stmt -> execute();
	
		return $stmt -> fetchAll();
	
		$stmt = null;
	
	}
	
	/* OBTENER NIVELES DE USUARIO */


	/* BUSCAR MODULO SISTEMA */
	
	static public function buscarModuloSistemaModel($ruta_modulo,$nivel){
		$stmt = Conexion::conectar()->prepare("SELECT
		IF(
			admin_niveles_usuario_modulos.id IS NOT NULL,
			1,
			0
		) AS permiso_modulo
		FROM
			admin_niveles_usuario_modulos
		INNER JOIN permisos_modulos ON admin_niveles_usuario_modulos.id_modulo = permisos_modulos.id
		INNER JOIN admin_niveles_usuario ON admin_niveles_usuario_modulos.id_nivel = admin_niveles_usuario.id
		WHERE
			permisos_modulos.ruta = :ruta AND admin_niveles_usuario.nombre = :nivel
		");
	
		$stmt->bindParam(':ruta',$ruta_modulo,PDO::PARAM_STR);
		$stmt->bindParam(':nivel',$nivel,PDO::PARAM_STR);
	
		$stmt -> execute();
	
		return $stmt -> fetch();
	
		$stmt = null;
	
	}
	
	/* BUSCAR MODULO SISTEMA */

	
	/* OBTENER MODULOS DE SISTEMA */
	
	static public function obtenerModulosNivelModel(){
	
		$stmt = Conexion::conectar()->prepare("SELECT
		admin_niveles_usuario_modulos.*,
		permisos_modulos.ruta,
		permisos_modulos.nombre as modulo,
    	permisos_areas.nombre AS area,
		permisos_modulos.id_area
		FROM
			admin_niveles_usuario_modulos
		INNER JOIN permisos_modulos ON admin_niveles_usuario_modulos.id_modulo = permisos_modulos.id
		AND permisos_modulos.estado=0
		INNER JOIN admin_niveles_usuario ON admin_niveles_usuario_modulos.id_nivel = admin_niveles_usuario.id
		INNER JOIN permisos_areas ON permisos_modulos.id_area = permisos_areas.id
		WHERE admin_niveles_usuario.nombre = :nivel");
	
		$stmt->bindParam(':nivel', $_SESSION['nivel'],PDO::PARAM_STR);
	
		$stmt -> execute();
	
		return $stmt -> fetchAll();
	
		$stmt = null;
	
	}
	
	/* OBTENER MODULOS DE SISTEMA */

	/* BUSCAR SUBCATEGORIAS */

	static public function obtenerSubcategoriasModel(){

		$stmt = Conexion::conectar()->prepare("SELECT menu_subcategorias.* FROM menu_subcategorias WHERE menu_subcategorias.estado=0");
		
		$stmt -> execute();
	
		return $stmt -> fetchAll();
	
		$stmt = null;
	
	}
	
	/* BUSCAR SUBCATEGORIAS */


	/* BUSCAR CATEGORIAS */

	static public function obtenerCategoriasModel(){

		$stmt = Conexion::conectar()->prepare("SELECT menu_categorias.* FROM menu_categorias WHERE menu_categorias.estado=0");
		
		$stmt -> execute();
	
		return $stmt -> fetchAll();
	
		$stmt = null;
	
	}
	
	/* BUSCAR CATEGORIAS */


	/* BUSCAR SUBCATEGORIAS */

	static public function obtenerSubcategoriasFiltroModel($categoria){
		$subcategoria = '';
		if ($categoria!='') {
			$subcategoria = "AND menu_subcategorias.id_categoria='$categoria'";
		}

		$stmt = Conexion::conectar()->prepare("SELECT menu_subcategorias.* 
		FROM menu_subcategorias 
		WHERE menu_subcategorias.estado=0 
		$subcategoria");
		
		$stmt -> execute();
	
		return $stmt -> fetchAll();
	
		$stmt = null;
	
	}
	
	/* BUSCAR SUBCATEGORIAS */


	/* VERIFICAR CAFETERIA */

	static public function verificarCafeteriaModel($id_cafeteria,$propietario){

		$stmt = Conexion::conectar()->prepare("SELECT * FROM cafeterias WHERE id=:id AND id_usuario=:propietario");
	
		$stmt->bindParam(':id', $id_cafeteria,PDO::PARAM_INT);
		$stmt->bindParam(':propietario', $propietario,PDO::PARAM_INT);
	
		$stmt -> execute();
	
		return $stmt -> fetch();
	
		$stmt = null;
	
	}
	
	/* VERIFICAR CAFETERIA */
}