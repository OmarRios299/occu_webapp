<?php 


require_once "conexion.php";

class AdminPaginaInicialModel extends Conexion {
    static public function obtenerCarouselModel(){

        $stmt = Conexion::conectar()->prepare("SELECT *
        FROM pagina_inicial
        WHERE area = 'carousel'
        AND estado != 2 
        ");
    
        $stmt -> execute();
    
        return $stmt -> fetchAll();
    
        $stmt = null;
    
    }
    
    static public function obtenerCardsModel(){
    
        $stmt = Conexion::conectar()->prepare("SELECT *
        FROM pagina_inicial
        WHERE area = 'card'
        AND estado != 2 
        ");
    
        $stmt -> execute();
    
        return $stmt -> fetchAll();
    
        $stmt = null;
    
    }

    static public function buscarImagenModel($id){

        $stmt = Conexion::conectar()->prepare("SELECT *
        FROM pagina_inicial
        WHERE id = :id
        ");

        $stmt->bindParam(":id", $id, PDO::PARAM_INT);

        $stmt -> execute();
    
        return $stmt -> fetch();
    
        $stmt = null;
    
    }
    

    /* INSERTAR IMAGEN */

    static public function insertarRegistroImagenModel($datos) {

        $conexion = Conexion::conectar();
        $stmt = $conexion->prepare("INSERT INTO `pagina_inicial` (
            `area`,
            `titulo`,
            `descripcion`,
            `enlace`,
            `nombre_enlace`
        ) VALUES (
            :area,
            :titulo,
            :descripcion,
            :enlace,
            :nombre_enlace
        )");
    
        $stmt->bindParam(":area", $datos['area'], PDO::PARAM_STR);
        $stmt->bindParam(":titulo", $datos['titulo'], PDO::PARAM_STR);
        $stmt->bindParam(":descripcion", $datos['descripcion'], PDO::PARAM_STR);
        $stmt->bindParam(":enlace", $datos['enlace'], PDO::PARAM_STR);
        $stmt->bindParam(":nombre_enlace", $datos['nombre_enlace'], PDO::PARAM_STR);
    
        if ($stmt->execute()) {
            return $conexion->lastInsertId();
        } else {
            return "error";
        }
    
        $stmt = null;
    }
    

    /* INSERTAR IMAGEN */

    
    /* EDITAR IMAGENES */
    
    static public function actualizarRegistroImagenModel($datos) {

        $conexion = Conexion::conectar();
        $stmt = $conexion->prepare("UPDATE `pagina_inicial` SET 
            `area` = :area,
            `titulo` = :titulo,
            `descripcion` = :descripcion,
            `enlace` = :enlace,
            `nombre_enlace` = :nombre_enlace
            WHERE `id` = :id");
    
        // Binding parameters
        $stmt->bindParam(":area", $datos['area'], PDO::PARAM_STR);
        $stmt->bindParam(":titulo", $datos['titulo'], PDO::PARAM_STR);
        $stmt->bindParam(":descripcion", $datos['descripcion'], PDO::PARAM_STR);
        $stmt->bindParam(":enlace", $datos['enlace'], PDO::PARAM_STR);
        $stmt->bindParam(":nombre_enlace", $datos['nombre_enlace'], PDO::PARAM_STR);
        $stmt->bindParam(":id", $datos['id'], PDO::PARAM_INT);
    
        if ($stmt->execute()) {
            return "success";
        } else {
            return "error";
        }
    
        // Closing the statement
        $stmt = null;
    }

    static public function editarImagenModel($datos){
    
        $stmt = Conexion::conectar()->prepare("UPDATE pagina_inicial SET imagen=:imagen WHERE id=:id");
    
        $stmt->bindParam(":imagen", $datos['imagen'], PDO::PARAM_STR);
        $stmt->bindParam(":id", $datos['id'], PDO::PARAM_INT);
    
        if($stmt->execute()){
            return 'success';
        }else{
            return 'error';
        }
    
        $stmt = null;
    
    }
    
    
    /* EDITAR IMAGENES */
    
}