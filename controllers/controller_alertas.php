<?php

require_once __DIR__ . '/../models/model_alertas.php';
require_once __DIR__ . '/../models/model_general.php';

class AlertaController
{

	/* OBTENER ALERTAS PARA UN USUARIO */
	
	static public function obtenerAlertasUsuarioController($modulo = null){
		
		$id_modulo = GeneralModel::obtenerIdModuloPorRutaModel($modulo);

		// Obtener alertas aplicables
		$alertas = AlertaModel::obtenerAlertasAplicablesModel($_SESSION['id'], $_SESSION['nivel'], $id_modulo);
		
		// Procesar alertas para el frontend
		$alertas_procesadas = [];
		foreach ($alertas as $alerta) {
			$alertas_procesadas[] = [
				'id' => $alerta['id'],
				'codigo' => $alerta['codigo'],
				'titulo' => $alerta['titulo'],
				'mensaje' => $alerta['mensaje'],
				'tipo' => $alerta['tipo'],
				'icono' => $alerta['icono'],
				'plantilla' => $alerta['plantilla'] ?: 'default',
				'html_personalizado' => $alerta['html_personalizado'],
				'botones' => $alerta['botones'] ? json_decode($alerta['botones'], true) : null,
				'estilo_personalizado' => $alerta['estilo_personalizado'],
				'prioridad' => (int)$alerta['prioridad'],
				'veces_ignorar' => isset($alerta['veces_ignorar']) ? (int)$alerta['veces_ignorar'] : 0
			];
		}
		
		return $alertas_procesadas;
	}
	
	/* End of OBTENER ALERTAS PARA UN USUARIO */

	/* MARCAR ALERTA COMO VISTA */
	
	static public function marcarAlertaVistaController($id_usuario, $id_alerta){
		
		$resultado = AlertaModel::marcarAlertaVistaModel($id_usuario, $id_alerta);
		
		if ($resultado === 'success') {
			return json_encode([
				'success' => true,
				'mensaje' => 'Alerta marcada como vista'
			]);
		} else {
			return json_encode([
				'success' => false,
				'mensaje' => 'Error al marcar alerta'
			]);
		}
	}
	
	/* End of MARCAR ALERTA COMO VISTA */

	/* OBTENER CONTADOR DE IGNORAR */
	
	static public function obtenerContadorIgnorarController($id_usuario, $id_alerta){
		
		$contador = AlertaModel::obtenerContadorIgnorarModel($id_usuario, $id_alerta);
		
		return json_encode([
			'success' => true,
			'contador' => $contador
		]);
	}
	
	/* End of OBTENER CONTADOR DE IGNORAR */
	
	
	/* INCREMENTAR CONTADOR DE IGNORAR */
	
	static public function incrementarContadorIgnorarController($id_usuario, $id_alerta){
		
		$resultado = AlertaModel::incrementarContadorIgnorarModel($id_usuario, $id_alerta);
		
		if ($resultado === 'success') {
			// Obtener el nuevo contador
			$nuevo_contador = AlertaModel::obtenerContadorIgnorarModel($id_usuario, $id_alerta);
			
			return json_encode([
				'success' => true,
				'contador' => $nuevo_contador,
				'mensaje' => 'Contador incrementado'
			]);
		} else {
			return json_encode([
				'success' => false,
				'mensaje' => 'Error al incrementar contador'
			]);
		}
	}
	
	/* End of INCREMENTAR CONTADOR DE IGNORAR */

	/* VERIFICAR SI ES PRIMERA VEZ */
	
	static public function esPrimeraVezController($id_usuario){
		
		return AlertaModel::esPrimeraVezModel($id_usuario);
	}
	
	/* End of VERIFICAR SI ES PRIMERA VEZ */

}

