<?php 

require_once "conexion.php";

class CafeteriasModel extends Conexion {
    
    /* OBTENER CAFETERíAS */
    
    static public function obtenerCafeteriasModel($datos){
        $entidad ='';
        $ciudad='';
        $estatus = 'WHERE cafeterias.estado != 2';
        $usuario = '';
        if ($datos['entidad']!='') {
            $entidad = 'AND ciudades.id_entidad_federativa = :entidad';
        }
        if ($datos['ciudad']!='') {
            $ciudad = 'AND cafeterias.id_ciudad = :ciudad';
        }
        if ($datos['estatus']=='Activas') {
            $estatus = 'WHERE cafeterias.estado = 0';
        }else if($datos['estatus']=='Inactivas'){
            $estatus = 'WHERE cafeterias.estado = 1';
        }
        if (isset($datos['usuario']) && $datos['usuario']!='') {
            $usuario = 'AND cafeterias.id_usuario = :usuario';
        }

        $stmt = Conexion::conectar()->prepare("SELECT
        cafeterias.*,
        CONCAT(
            admin_usuarios.nombre,
            ' ',
            admin_usuarios.apellido
        ) AS usuario_alta,
        ciudades.nombre AS ciudad,
        entidades_federativas.nombre AS entidad_federativa,
        paises.nombre AS pais
        FROM
            cafeterias
        INNER JOIN admin_usuarios ON admin_usuarios.id = cafeterias.id_usuario
        INNER JOIN ciudades ON cafeterias.id_ciudad = ciudades.id
        INNER JOIN entidades_federativas ON ciudades.id_entidad_federativa = entidades_federativas.id
        INNER JOIN paises ON paises.id = entidades_federativas.id_pais
        $estatus
        $ciudad
        $entidad
        $usuario");
    
        
        if ($datos['entidad']!='') {
            $stmt->bindParam(':entidad',$datos['entidad'] ,PDO::PARAM_INT);
        }
        if ($datos['ciudad']!='') {
            $stmt->bindParam(':ciudad', $datos['ciudad'],PDO::PARAM_INT);
        }
        if (isset($datos['usuario']) && $datos['usuario']!='') {
            $stmt->bindParam(':usuario', $datos['usuario'],PDO::PARAM_INT);
        }
    
        $stmt -> execute();
    
        return $stmt -> fetchAll();
    
        $stmt = null;
    
    }
    
    /* OBTENER CAFETERíAS */

    
    /* OBTENER DATOS DE CAFETERíA */
    
    static public function ObtenerDatosCafeteriaModel($cafeteria){
    
        $stmt = Conexion::conectar()->prepare("SELECT
        cafeterias.*,
        CONCAT(
            admin_usuarios.nombre,
            ' ',
            admin_usuarios.apellido
        ) AS usuario_alta,
        ciudades.nombre AS ciudad,
        entidades_federativas.nombre AS entidad_federativa,
        paises.nombre AS pais
        FROM
            cafeterias
        INNER JOIN admin_usuarios ON admin_usuarios.id = cafeterias.id_usuario
        INNER JOIN ciudades ON admin_usuarios.id_ciudad = ciudades.id
        INNER JOIN entidades_federativas ON ciudades.id_entidad_federativa = entidades_federativas.id
        INNER JOIN paises ON paises.id = entidades_federativas.id_pais
        WHERE cafeterias.estado !=2 AND cafeterias.id = :id");
    
        $stmt->bindParam(':id', $cafeteria ,PDO::PARAM_INT);
    
        $stmt -> execute();
    
        return $stmt -> fetch();
    
        $stmt = null;
    
    }
    
    /* OBTENER DATOS DE CAFETERíA */

    
    /* OBTENER CIUDADES POR PAISES */
    
    static public function obtenerCiudadesPaisModel($pais){
    
        $stmt = Conexion::conectar()->prepare("SELECT
        ciudades.*,
        paises.nombre AS pais
        FROM
            ciudades
        INNER JOIN entidades_federativas ON ciudades.id_entidad_federativa = entidades_federativas.id
        INNER JOIN paises ON entidades_federativas.id_pais = paises.id
        WHERE paises.id = :id");
    
        $stmt->bindParam(':id', $pais,PDO::PARAM_INT);
    
        $stmt -> execute();
    
        return $stmt -> fetchAll();
    
        $stmt = null;
    
    }
    
    /* OBTENER CIUDADES POR PAISES */
    
    
    /* INSERTAR CAFETERIA */
    
    static public function insertarCafeteriaModel($datos) {
        $conexion = Conexion::conectar();
        $stmt = $conexion->prepare("INSERT INTO cafeterias(
            nombre,
            imagen,
            id_ciudad,
            direccion,
            telefono,
            correo_electronico,
            latitud,
            longitud,
            horario_apertura,
            horario_cierre,
            horario_diferente,
            id_usuario,
            id_alta,
            fecha_alta
        )
        VALUES(
            :nombre,
            'views/assets/img/cafeteria_default.png',
            :id_ciudad,
            :direccion,
            :telefono,
            :correo_electronico,
            :latitud,
            :longitud,
            :horario_apertura,
            :horario_cierre,
            :horario_diferente,
            :id_usuario,
            :id_alta,
            :fecha_alta
        )");
    
        $stmt->bindParam(':nombre', $datos['nombre'], PDO::PARAM_STR);
        $stmt->bindParam(':id_ciudad', $datos['ciudad'], PDO::PARAM_INT);
        $stmt->bindParam(':direccion', $datos['direccion'], PDO::PARAM_STR);
        $stmt->bindParam(':telefono', $datos['telefono'], PDO::PARAM_STR);
        $stmt->bindParam(':correo_electronico', $datos['correo'], PDO::PARAM_STR);
        $stmt->bindParam(':latitud', $datos['latitud'], PDO::PARAM_STR);
        $stmt->bindParam(':longitud', $datos['longitud'], PDO::PARAM_STR);
        $stmt->bindParam(':horario_apertura', $datos['horario_apertura'], PDO::PARAM_STR);
        $stmt->bindParam(':horario_cierre', $datos['horario_cierre'], PDO::PARAM_STR);
        $stmt->bindParam(':horario_diferente', $datos['horario_diferente'], PDO::PARAM_STR);
        $stmt->bindParam(':id_usuario', $datos['id_alta'], PDO::PARAM_INT);
        $stmt->bindParam(':id_alta', $datos['id_alta'], PDO::PARAM_INT);
        $stmt->bindParam(':fecha_alta', $datos['fecha_alta'], PDO::PARAM_STR);
    
        if ($stmt->execute()) {
            return $conexion->lastInsertId();
        } else {
            return "error";
        }
        $stmt = null;
    }
    
    static public function insertarHorarioCafeteriaModel($id_cafeteria, $dia, $horario) {
        $conexion = Conexion::conectar();
    
        $stmt = $conexion->prepare("INSERT INTO cafeteria_horarios(
            id_cafeteria,
            dia,
            hora_apertura,
            hora_cierre,
            cerrado
        )
        VALUES(
            :id_cafeteria,
            :dia,
            :hora_apertura,
            :hora_cierre,
            :cerrado
        )");
    
        $stmt->bindParam(':id_cafeteria', $id_cafeteria, PDO::PARAM_INT);
        $stmt->bindParam(':dia', $dia, PDO::PARAM_STR);
        $stmt->bindParam(':hora_apertura', $horario['apertura'], PDO::PARAM_STR);
        $stmt->bindParam(':hora_cierre', $horario['cierre'], PDO::PARAM_STR);
        $stmt->bindParam(':cerrado', $horario['cerrado'], PDO::PARAM_STR);
    
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
        $stmt = null;
    }
    
    
    /* INSERTAR CAFETERIA */
    
    
    /* EDITAR CAFETERIA */
    
    static public function editarCafeteriaModel($datos){
    
        $stmt = Conexion::conectar()->prepare("UPDATE cafeterias SET
        nombre = :nombre,
        id_ciudad = :id_ciudad,
        direccion = :direccion,
        telefono = :telefono,
        correo_electronico = :correo_electronico,
        latitud = :latitud,
        longitud = :longitud,
        horario_apertura = :horario_apertura,
        horario_cierre = :horario_cierre,
        horario_diferente=:horario_diferente
        WHERE id = :id");
    
        $stmt->bindParam(':nombre', $datos['nombre'], PDO::PARAM_STR);
        $stmt->bindParam(':id_ciudad', $datos['ciudad'], PDO::PARAM_INT);
        $stmt->bindParam(':direccion', $datos['direccion'], PDO::PARAM_STR);
        $stmt->bindParam(':telefono', $datos['telefono'], PDO::PARAM_STR);
        $stmt->bindParam(':correo_electronico', $datos['correo'], PDO::PARAM_STR);
        $stmt->bindParam(':latitud', $datos['latitud'], PDO::PARAM_STR);
        $stmt->bindParam(':longitud', $datos['longitud'], PDO::PARAM_STR);
        $stmt->bindParam(':horario_apertura', $datos['horario_apertura'], PDO::PARAM_STR);
        $stmt->bindParam(':horario_cierre', $datos['horario_cierre'], PDO::PARAM_STR);
        $stmt->bindParam(':horario_diferente', $datos['horario_diferente'], PDO::PARAM_STR);
        $stmt->bindParam(':id', $datos['id'], PDO::PARAM_INT);
    
        if($stmt->execute()){
            return 'success';
        }else{
            return 'error';
        }
    
        $stmt = null;
    
    }
    
    /* EDITAR CAFETERIA */

    
    /* ELIMINAR ANTIGUO HORARIO */
    
    static public function eliminarHorariosCafeteriaModel($id_cafeteria) {
        $conexion = Conexion::conectar();
        $stmt = $conexion->prepare("DELETE FROM cafeteria_horarios WHERE id_cafeteria = :id_cafeteria");
        $stmt->bindParam(':id_cafeteria', $id_cafeteria, PDO::PARAM_INT);
    
        if ($stmt->execute()) {
            return 'success';
        } else {
            return 'error';
        }
        $stmt = null;
    }
    
    
    /* ELIMINAR ANTIGUO HORARIO */
    
    

    /* BUSCAR CAFETERIA */
    
    static public function obtenerCafeteriaModel($id_cafeteria){
    
        $stmt = Conexion::conectar()->prepare("SELECT * FROM cafeterias WHERE id=:id");
    
        $stmt->bindParam(':id', $id_cafeteria,PDO::PARAM_STR);
    
        $stmt -> execute();
    
        return $stmt -> fetch();
    
        $stmt = null;
    
    }
    
    /* BUSCAR CAFETERIA */
    
    /* EDITAR IMAGEN */
    
    static public function editarImagenCafeteriaModel($datos){
    
        $stmt = Conexion::conectar()->prepare("UPDATE cafeterias SET imagen=:imagen WHERE id=:id");
    
        $stmt->bindParam(":imagen", $datos['imagen'], PDO::PARAM_STR);
        $stmt->bindParam(":id", $datos['id'], PDO::PARAM_INT);
    
        if($stmt->execute()){
            return 'success';
        }else{
            return 'error';
        }
    
        $stmt = null;
    
    }
    
    /* EDITAR IMAGEN */

    
    /* BUSCAR IMAGENS DE CAFETERIAS */
    
    static public function obtenerImagenesCafeteriaModel($cafeteria){
    
        $stmt = Conexion::conectar()->prepare("SELECT * FROM cafeterias_imagenes 
        WHERE id_cafeteria=:id
        AND estado !=2
        ");
    
        $stmt->bindParam(':id', $cafeteria,PDO::PARAM_INT);
    
        $stmt -> execute();
    
        return $stmt -> fetchAll();
    
        $stmt = null;
    
    }
    
    /* BUSCAR IMAGENS DE CAFETERIAS */
    
    
    /* AGREGAR NUEVAS IMAENES */
    
    static public function agregarImagenesModel($datos){
    
        $stmt = Conexion::conectar()->prepare("INSERT INTO cafeterias_imagenes (id_cafeteria, imagen) VALUES (:id_cafeteria, :imagen)");
    
        $stmt->bindParam(':id_cafeteria', $datos['id'], PDO::PARAM_INT);
        $stmt->bindParam(':imagen', $datos['imagen'], PDO::PARAM_STR);
    
        if($stmt->execute()){
            return 'success';
        }else{
            return 'error';
        }
        $stmt = null;
    }
    
    /* AGREGAR NUEVAS IMAENES */

    
    /* OBTENER SERVICIOS */
    
    static public function obtenerServiciosControllerModel($cafeteria){
    
        $stmt = Conexion::conectar()->prepare("SELECT 
        s.id AS id,
        s.nombre,
        s.imagen,
        CASE 
            WHEN cs.id_servicio IS NOT NULL THEN 1  -- Si el servicio está registrado, devuelve 1
            ELSE 0  -- Si no está registrado, devuelve 0
        END AS servicio_registrado
        FROM 
            servicios s
        LEFT JOIN 
            (SELECT DISTINCT id_servicio 
            FROM cafeterias_servicios 
            WHERE id_cafeteria = :id AND estado = 0) cs  -- Aseguramos que solo tomamos los servicios registrados y activos
        ON 
            s.id = cs.id_servicio 
        WHERE 
            s.estado = 0  -- Mostramos solo los servicios activos
        ");

        $stmt->bindParam(':id', $cafeteria, PDO::PARAM_INT);

        $stmt -> execute();
    
        return $stmt -> fetchAll();
    
        $stmt = null;
    
    }
    
    /* OBTENER SERVICIOS */
    

    
    /* INSERTAR REGISTRO DE SERVICIOS */
    
    static public function registrarServiciosModel($cafeteria, $servicio){
    
        $stmt = Conexion::conectar()->prepare("INSERT INTO cafeterias_servicios (id_servicio, id_cafeteria) VALUES (:servicio, :cafeteria)");
    
        $stmt->bindParam(':cafeteria', $cafeteria, PDO::PARAM_INT);
        $stmt->bindParam(':servicio', $servicio, PDO::PARAM_INT);
    
        if($stmt->execute()){
            return 'success';
        }else{
            return 'error';
        }
        $stmt = null;
    }
    
    /* INSERTAR REGISTRO DE SERVICIOS */

    
    /* ELIMINAR REGISTROS ANTIGUOS */
    
    static public function eliminarServiciosAntiguosModel($cafeteria){
    
        $stmt = Conexion::conectar()->prepare("UPDATE cafeterias_servicios SET estado=2 WHERE id_cafeteria = :id");
    
        $stmt->bindParam(":id", $cafeteria, PDO::PARAM_INT);
    
        if($stmt->execute()){
            return 'success';
        }else{
            return 'error';
        }
    
        $stmt = null;
    
    }
    
    /* ELIMINAR REGISTROS ANTIGUOS */
    
    
    /* OBTENER HORARIO DIFERENTE */
    
    static public function buscarHorarioDiferenteModel($cafeteria, $dia){
    
        $stmt = Conexion::conectar()->prepare("SELECT * FROM cafeteria_horarios WHERE id_cafeteria = :cafeteria AND dia=:dia");
    
        $stmt->bindParam(':cafeteria', $cafeteria,PDO::PARAM_INT);
        $stmt->bindParam(':dia', $dia,PDO::PARAM_STR);
    
        $stmt -> execute();
    
        return $stmt -> fetch();
    
        $stmt = null;
    
    }
    
    /* OBTENER HORARIO DIFERENTE */
    

}