<?php

require_once __DIR__ . '/../config/conexion.php';

class AlertaModel extends Conexion{

	/* OBTENER ALERTAS APLICABLES PARA UN USUARIO */
	
	static public function obtenerAlertasAplicablesModel($id_usuario, $nivel_usuario, $id_modulo = null){
		
		// Construir consulta base
		$sql = "SELECT 
			sa.*
			FROM sistema_alertas sa
			WHERE sa.activa = 1 
			AND sa.estado = 0
			AND (sa.fecha_inicio IS NULL OR sa.fecha_inicio <= NOW())
			AND (sa.fecha_fin IS NULL OR sa.fecha_fin >= NOW())";
		
		$params = [];
		
		// Filtro por módulo
		if ($id_modulo !== null) {
			// Cuando se busca por módulo específico, SOLO devolver alertas de ese módulo
			// NO incluir alertas generales (id_modulo IS NULL) para evitar duplicados
			$sql .= " AND sa.id_modulo = :id_modulo";
			$params[':id_modulo'] = $id_modulo;
		} else {
			// Cuando se busca sin módulo (login), SOLO devolver alertas generales
			$sql .= " AND sa.id_modulo IS NULL";
		}
		
		$sql .= " ORDER BY sa.prioridad DESC, sa.fecha_alta ASC";
		
		$stmt = Conexion::conectar()->prepare($sql);
		
		// Bind de parámetros
		foreach ($params as $key => $value) {
			$stmt->bindValue($key, $value, PDO::PARAM_INT);
		}
		
		$stmt->execute();
		
		$alertas = $stmt->fetchAll(PDO::FETCH_ASSOC);
		
		// Filtrar por rol y por vistas previas
		$alertas_filtradas = [];
		foreach ($alertas as $alerta) {
			// Verificar criterios de rol
			$cumple_rol = true;
			if ($alerta['criterios_rol']) {
				$roles_permitidos = json_decode($alerta['criterios_rol'], true);
				if (is_array($roles_permitidos) && !in_array($nivel_usuario, $roles_permitidos)) {
					$cumple_rol = false;
				}
			}
			
			// Si cumple el rol y debe mostrarse, agregar
			if ($cumple_rol && self::debeMostrarAlerta($alerta, $id_usuario)) {
				$alertas_filtradas[] = $alerta;
			}
		}
		
		$stmt = null;
		
		return $alertas_filtradas;
	}
	
	/* End of OBTENER ALERTAS APLICABLES PARA UN USUARIO */

	/* VERIFICAR SI DEBE MOSTRAR UNA ALERTA */
	
	static private function debeMostrarAlerta($alerta, $id_usuario){
		
		// Verificar si ya fue vista
		$vista = self::obtenerVistaAlertaModel($id_usuario, $alerta['id']);
		
		// Verificar contador de ignorar
		$veces_ignorar = isset($alerta['veces_ignorar']) ? (int)$alerta['veces_ignorar'] : 0;
		if ($veces_ignorar > 0) {
			// Si tiene contador de ignorar, verificar si ya se alcanzó el límite
			$veces_ignorada = $vista ? (int)($vista['veces_ignorada'] ?? 0) : 0;
			// Si aún no se alcanza el límite, NO mostrar (se ignorará en el frontend)
			// Pero debemos devolverla para que el frontend pueda procesarla
			// El frontend se encargará de ignorarla si corresponde
			// IMPORTANTE: Si ya se alcanzó el límite (veces_ignorada >= veces_ignorar), continuar con la lógica normal
			if ($veces_ignorada < $veces_ignorar) {
				// Aún no se alcanza el límite, pero devolverla para que el frontend la procese
				// El frontend se encargará de incrementar el contador e ignorarla
				return true;
			}
			// Si ya se alcanzó el límite, continuar con la lógica normal de tipo_mostrar
		}
		
		if ($alerta['tipo_mostrar'] == 'una_vez') {
			// Solo mostrar si nunca se ha visto (o si se vio pero solo se ignoró, no se vio realmente)
			if (!$vista) {
				return true;
			}
			// Si existe registro pero solo tiene veces_ignorada y veces_vista = 0, aún debe mostrarse
			$veces_vista = $vista ? (int)($vista['veces_vista'] ?? 0) : 0;
			return $veces_vista == 0;
		}
		
		if ($alerta['tipo_mostrar'] == 'n_veces') {
			// Mostrar si no ha alcanzado el límite
			$veces_vista = $vista ? (int)($vista['veces_vista'] ?? 0) : 0;
			return $veces_vista < (int)$alerta['veces_mostrar'];
		}
		
		if ($alerta['tipo_mostrar'] == 'mientras_activa') {
			// Siempre mostrar si está activa (ya se filtró arriba)
			return true;
		}
		
		return false;
	}
	
	/* End of VERIFICAR SI DEBE MOSTRAR UNA ALERTA */

	/* OBTENER VISTA DE ALERTA */
	
	static public function obtenerVistaAlertaModel($id_usuario, $id_alerta){
		
		$stmt = Conexion::conectar()->prepare("SELECT * 
			FROM usuarios_alertas_vistas 
			WHERE id_usuario = :id_usuario 
			AND id_alerta = :id_alerta");
		
		$stmt->bindParam(":id_usuario", $id_usuario, PDO::PARAM_INT);
		$stmt->bindParam(":id_alerta", $id_alerta, PDO::PARAM_INT);
		
		$stmt->execute();
		
		$resultado = $stmt->fetch(PDO::FETCH_ASSOC);
		
		$stmt = null;
		
		return $resultado;
	}
	
	/* End of OBTENER VISTA DE ALERTA */

	/* MARCAR ALERTA COMO VISTA */
	
	static public function marcarAlertaVistaModel($id_usuario, $id_alerta){
		
		// Verificar si ya existe registro
		$vista_existente = self::obtenerVistaAlertaModel($id_usuario, $id_alerta);
		
		if ($vista_existente) {
			// Actualizar contador
			$stmt = Conexion::conectar()->prepare("UPDATE usuarios_alertas_vistas 
				SET veces_vista = veces_vista + 1,
				fecha_vista = NOW()
				WHERE id_usuario = :id_usuario 
				AND id_alerta = :id_alerta");
		} else {
			// Crear nuevo registro
			$stmt = Conexion::conectar()->prepare("INSERT INTO usuarios_alertas_vistas 
				(id_usuario, id_alerta, fecha_vista, veces_vista, veces_ignorada) 
				VALUES (:id_usuario, :id_alerta, NOW(), 1, 0)");
		}
		
		$stmt->bindParam(":id_usuario", $id_usuario, PDO::PARAM_INT);
		$stmt->bindParam(":id_alerta", $id_alerta, PDO::PARAM_INT);
		
		if($stmt->execute()){
			return 'success';
		}else{
			return 'error';
		}
		
		$stmt = null;
	}
	
	/* End of MARCAR ALERTA COMO VISTA */

	/* OBTENER CONTADOR DE IGNORAR */
	
	static public function obtenerContadorIgnorarModel($id_usuario, $id_alerta){
		
		$vista = self::obtenerVistaAlertaModel($id_usuario, $id_alerta);
		
		if ($vista && isset($vista['veces_ignorada'])) {
			return (int)$vista['veces_ignorada'];
		}
		
		return 0;
	}
	
	/* End of OBTENER CONTADOR DE IGNORAR */
	
	
	/* INCREMENTAR CONTADOR DE IGNORAR */
	
	static public function incrementarContadorIgnorarModel($id_usuario, $id_alerta){
		
		// Verificar si ya existe registro
		$vista_existente = self::obtenerVistaAlertaModel($id_usuario, $id_alerta);
		
		if ($vista_existente) {
			// Actualizar contador de ignorar
			$stmt = Conexion::conectar()->prepare("UPDATE usuarios_alertas_vistas 
				SET veces_ignorada = veces_ignorada + 1
				WHERE id_usuario = :id_usuario 
				AND id_alerta = :id_alerta");
		} else {
			// Crear nuevo registro solo con el contador de ignorar
			// fecha_vista y veces_vista se establecerán cuando realmente se vea la alerta
			$stmt = Conexion::conectar()->prepare("INSERT INTO usuarios_alertas_vistas 
				(id_usuario, id_alerta, fecha_vista, veces_vista, veces_ignorada) 
				VALUES (:id_usuario, :id_alerta, NOW(), 0, 1)");
		}
		
		$stmt->bindParam(":id_usuario", $id_usuario, PDO::PARAM_INT);
		$stmt->bindParam(":id_alerta", $id_alerta, PDO::PARAM_INT);
		
		if($stmt->execute()){
			return 'success';
		}else{
			return 'error';
		}
		
		$stmt = null;
	}
	
	/* End of INCREMENTAR CONTADOR DE IGNORAR */

	/* VERIFICAR SI ES PRIMERA VEZ QUE INICIA SESIÓN */
	
	static public function esPrimeraVezModel($id_usuario){
		
		$stmt = Conexion::conectar()->prepare("SELECT COUNT(*) as total 
			FROM usuarios_sesiones 
			WHERE id_usuario = :id_usuario");
		
		$stmt->bindParam(":id_usuario", $id_usuario, PDO::PARAM_INT);
		
		$stmt->execute();
		
		$resultado = $stmt->fetch(PDO::FETCH_ASSOC);
		
		$stmt = null;
		
		// Si solo hay 1 sesión (la que acabamos de registrar), es primera vez
		return (int)$resultado['total'] <= 1;
	}
	
	/* End of VERIFICAR SI ES PRIMERA VEZ QUE INICIA SESIÓN */


	/* ========== MÉTODOS DE ADMINISTRACIÓN ========== */

	/* OBTENER TODAS LAS ALERTAS (PARA ADMINISTRACIÓN) */
	
	static public function obtenerTodasAlertasModel($filtros = []){
		
		$sql = "SELECT * FROM sistema_alertas WHERE 1=1";
		$params = [];
		
		// Filtro por estado activa/inactiva
		if (isset($filtros['activa']) && $filtros['activa'] !== '') {
			$sql .= " AND activa = :activa";
			$params[':activa'] = $filtros['activa'];
		}
		
		// Filtro por estado (eliminado/no eliminado)
		if (isset($filtros['estado']) && $filtros['estado'] !== '') {
			$sql .= " AND estado = :estado";
			$params[':estado'] = $filtros['estado'];
		} else {
			// Por defecto, solo mostrar no eliminadas
			$sql .= " AND estado = 0";
		}
		
		$sql .= " ORDER BY fecha_alta DESC";
		
		$stmt = Conexion::conectar()->prepare($sql);
		
		foreach ($params as $key => $value) {
			$stmt->bindValue($key, $value, PDO::PARAM_INT);
		}
		
		$stmt->execute();
		
		$resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
		
		$stmt = null;
		
		return $resultado;
	}
	
	/* End of OBTENER TODAS LAS ALERTAS */
	
	
	/* OBTENER ALERTA POR ID */
	
	static public function obtenerAlertaPorIdModel($id_alerta){
		
		$stmt = Conexion::conectar()->prepare("SELECT * FROM sistema_alertas WHERE id = :id");
		
		$stmt->bindParam(":id", $id_alerta, PDO::PARAM_INT);
		
		$stmt->execute();
		
		$resultado = $stmt->fetch(PDO::FETCH_ASSOC);
		
		$stmt = null;
		
		return $resultado;
	}
	
	/* End of OBTENER ALERTA POR ID */
	
	
	/* INSERTAR ALERTA */
	
	static public function insertarAlertaModel($datos){
		
		$stmt = Conexion::conectar()->prepare("INSERT INTO sistema_alertas 
			(codigo, titulo, mensaje, tipo, icono, tipo_mostrar, veces_mostrar, veces_ignorar,
			fecha_inicio, fecha_fin, activa, id_modulo, criterios_rol, criterios_version, 
			criterios_permisos, prioridad, plantilla, html_personalizado, botones, 
			estilo_personalizado, estado, id_alta, fecha_alta) 
			VALUES 
			(:codigo, :titulo, :mensaje, :tipo, :icono, :tipo_mostrar, :veces_mostrar, :veces_ignorar,
			:fecha_inicio, :fecha_fin, :activa, :id_modulo, :criterios_rol, :criterios_version,
			:criterios_permisos, :prioridad, :plantilla, :html_personalizado, :botones,
			:estilo_personalizado, :estado, :id_alta, NOW())");
		
		$stmt->bindValue(":codigo", $datos['codigo'], PDO::PARAM_STR);
		$stmt->bindValue(":titulo", $datos['titulo'], PDO::PARAM_STR);
		$stmt->bindValue(":mensaje", $datos['mensaje'], PDO::PARAM_STR);
		$stmt->bindValue(":tipo", $datos['tipo'], PDO::PARAM_STR);
		$stmt->bindValue(":icono", $datos['icono'] ?: null, $datos['icono'] ? PDO::PARAM_STR : PDO::PARAM_NULL);
		$stmt->bindValue(":tipo_mostrar", $datos['tipo_mostrar'], PDO::PARAM_STR);
		$stmt->bindValue(":veces_mostrar", $datos['veces_mostrar'], PDO::PARAM_INT);
		$stmt->bindValue(":veces_ignorar", isset($datos['veces_ignorar']) ? (int)$datos['veces_ignorar'] : 0, PDO::PARAM_INT);
		$stmt->bindValue(":fecha_inicio", $datos['fecha_inicio'] ?: null, $datos['fecha_inicio'] ? PDO::PARAM_STR : PDO::PARAM_NULL);
		$stmt->bindValue(":fecha_fin", $datos['fecha_fin'] ?: null, $datos['fecha_fin'] ? PDO::PARAM_STR : PDO::PARAM_NULL);
		$stmt->bindValue(":activa", $datos['activa'], PDO::PARAM_INT);
		$stmt->bindValue(":id_modulo", $datos['id_modulo'] ?: null, $datos['id_modulo'] ? PDO::PARAM_INT : PDO::PARAM_NULL);
		$stmt->bindValue(":criterios_rol", $datos['criterios_rol'] ?: null, $datos['criterios_rol'] ? PDO::PARAM_STR : PDO::PARAM_NULL);
		$stmt->bindValue(":criterios_version", $datos['criterios_version'] ?: null, $datos['criterios_version'] ? PDO::PARAM_STR : PDO::PARAM_NULL);
		$stmt->bindValue(":criterios_permisos", $datos['criterios_permisos'] ?: null, $datos['criterios_permisos'] ? PDO::PARAM_STR : PDO::PARAM_NULL);
		$stmt->bindValue(":prioridad", $datos['prioridad'], PDO::PARAM_INT);
		$stmt->bindValue(":plantilla", $datos['plantilla'], PDO::PARAM_STR);
		$stmt->bindValue(":html_personalizado", $datos['html_personalizado'] ?: null, $datos['html_personalizado'] ? PDO::PARAM_STR : PDO::PARAM_NULL);
		$stmt->bindValue(":botones", $datos['botones'] ?: null, $datos['botones'] ? PDO::PARAM_STR : PDO::PARAM_NULL);
		$stmt->bindValue(":estilo_personalizado", $datos['estilo_personalizado'] ?: null, $datos['estilo_personalizado'] ? PDO::PARAM_STR : PDO::PARAM_NULL);
		$stmt->bindValue(":estado", $datos['estado'], PDO::PARAM_INT);
		$stmt->bindValue(":id_alta", $datos['id_alta'], PDO::PARAM_INT);
		
		if($stmt->execute()){
			return 'success';
		}else{
			return 'error';
		}
		
		$stmt = null;
	}
	
	/* End of INSERTAR ALERTA */
	
	
	/* ACTUALIZAR ALERTA */
	
	static public function actualizarAlertaModel($id_alerta, $datos){
		
		$stmt = Conexion::conectar()->prepare("UPDATE sistema_alertas SET 
			codigo = :codigo,
			titulo = :titulo,
			mensaje = :mensaje,
			tipo = :tipo,
			icono = :icono,
			tipo_mostrar = :tipo_mostrar,
			veces_mostrar = :veces_mostrar,
			veces_ignorar = :veces_ignorar,
			fecha_inicio = :fecha_inicio,
			fecha_fin = :fecha_fin,
			activa = :activa,
			id_modulo = :id_modulo,
			criterios_rol = :criterios_rol,
			criterios_version = :criterios_version,
			criterios_permisos = :criterios_permisos,
			prioridad = :prioridad,
			plantilla = :plantilla,
			html_personalizado = :html_personalizado,
			botones = :botones,
			estilo_personalizado = :estilo_personalizado
			WHERE id = :id");
		
		$stmt->bindValue(":id", $id_alerta, PDO::PARAM_INT);
		$stmt->bindValue(":codigo", $datos['codigo'], PDO::PARAM_STR);
		$stmt->bindValue(":titulo", $datos['titulo'], PDO::PARAM_STR);
		$stmt->bindValue(":mensaje", $datos['mensaje'], PDO::PARAM_STR);
		$stmt->bindValue(":tipo", $datos['tipo'], PDO::PARAM_STR);
		$stmt->bindValue(":icono", $datos['icono'] ?: null, $datos['icono'] ? PDO::PARAM_STR : PDO::PARAM_NULL);
		$stmt->bindValue(":tipo_mostrar", $datos['tipo_mostrar'], PDO::PARAM_STR);
		$stmt->bindValue(":veces_mostrar", $datos['veces_mostrar'], PDO::PARAM_INT);
		$stmt->bindValue(":veces_ignorar", isset($datos['veces_ignorar']) ? (int)$datos['veces_ignorar'] : 0, PDO::PARAM_INT);
		$stmt->bindValue(":fecha_inicio", $datos['fecha_inicio'] ?: null, $datos['fecha_inicio'] ? PDO::PARAM_STR : PDO::PARAM_NULL);
		$stmt->bindValue(":fecha_fin", $datos['fecha_fin'] ?: null, $datos['fecha_fin'] ? PDO::PARAM_STR : PDO::PARAM_NULL);
		$stmt->bindValue(":activa", $datos['activa'], PDO::PARAM_INT);
		$stmt->bindValue(":id_modulo", $datos['id_modulo'] ?: null, $datos['id_modulo'] ? PDO::PARAM_INT : PDO::PARAM_NULL);
		$stmt->bindValue(":criterios_rol", $datos['criterios_rol'] ?: null, $datos['criterios_rol'] ? PDO::PARAM_STR : PDO::PARAM_NULL);
		$stmt->bindValue(":criterios_version", $datos['criterios_version'] ?: null, $datos['criterios_version'] ? PDO::PARAM_STR : PDO::PARAM_NULL);
		$stmt->bindValue(":criterios_permisos", $datos['criterios_permisos'] ?: null, $datos['criterios_permisos'] ? PDO::PARAM_STR : PDO::PARAM_NULL);
		$stmt->bindValue(":prioridad", $datos['prioridad'], PDO::PARAM_INT);
		$stmt->bindValue(":plantilla", $datos['plantilla'], PDO::PARAM_STR);
		$stmt->bindValue(":html_personalizado", $datos['html_personalizado'] ?: null, $datos['html_personalizado'] ? PDO::PARAM_STR : PDO::PARAM_NULL);
		$stmt->bindValue(":botones", $datos['botones'] ?: null, $datos['botones'] ? PDO::PARAM_STR : PDO::PARAM_NULL);
		$stmt->bindValue(":estilo_personalizado", $datos['estilo_personalizado'] ?: null, $datos['estilo_personalizado'] ? PDO::PARAM_STR : PDO::PARAM_NULL);
		
		if($stmt->execute()){
			return 'success';
		}else{
			return 'error';
		}
		
		$stmt = null;
	}
	
	/* End of ACTUALIZAR ALERTA */
	
	
	/* ELIMINAR ALERTA (SOFT DELETE) */
	
	static public function eliminarAlertaModel($id_alerta){
		
		$stmt = Conexion::conectar()->prepare("UPDATE sistema_alertas SET estado = 1 WHERE id = :id");
		
		$stmt->bindParam(":id", $id_alerta, PDO::PARAM_INT);
		
		if($stmt->execute()){
			return 'success';
		}else{
			return 'error';
		}
		
		$stmt = null;
	}
	
	/* End of ELIMINAR ALERTA */
	
	
	/* CAMBIAR ESTADO DE ALERTA */
	
	static public function cambiarEstadoAlertaModel($id_alerta, $campo, $valor){
		
		$stmt = Conexion::conectar()->prepare("UPDATE sistema_alertas SET $campo = :valor WHERE id = :id");
		
		$stmt->bindParam(":id", $id_alerta, PDO::PARAM_INT);
		$stmt->bindParam(":valor", $valor, PDO::PARAM_INT);
		
		if($stmt->execute()){
			return 'success';
		}else{
			return 'error';
		}
		
		$stmt = null;
	}
	
	/* End of CAMBIAR ESTADO DE ALERTA */

	/* ========== FIN MÉTODOS DE ADMINISTRACIÓN ========== */

}

