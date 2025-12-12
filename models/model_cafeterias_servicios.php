<?php 

require_once __DIR__ . '/../config/conexion.php';

class CafeteriasServiciosModel extends Conexion {
    /* OBTENER CATEGORIAS */

    static public function obtenerServiciosModel()
    {

        $stmt = Conexion::conectar()->prepare("SELECT
        servicios.*,
        CONCAT(
            admin_usuarios.nombre,
            ' ',
            admin_usuarios.apellido
        ) AS usuario_alta
        FROM
            servicios
        INNER JOIN admin_usuarios ON servicios.id_alta = admin_usuarios.id
        WHERE servicios.estado !=2
        GROUP BY
            servicios.id
        ORDER BY
            servicios.id;
        ");

        //$stmt->bindParam(':', ,PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->fetchAll();

        $stmt = null;
    }

    /* OBTENER CATEGORIAS */



    /* AGREGAR CATEGORIAS */

    static public function agregarServicioModel($datos)
    {

        $conexion = Conexion::conectar();
        $stmt = $conexion->prepare("INSERT INTO servicios(nombre, imagen, id_alta, fecha_alta) 
        VALUES (:nombre, 'views/assets/img/cafeteria_default.png', :id_alta, :fecha_alta)");

        $stmt->bindParam(':nombre', $datos['nombre'], PDO::PARAM_STR);
        $stmt->bindParam(':id_alta', $datos['id_alta'], PDO::PARAM_INT);
        $stmt->bindParam(':fecha_alta', $datos['fecha_alta'], PDO::PARAM_STR);
        
        if($stmt->execute()){
            return $conexion-> lastInsertId();
        }else{
            return "error";
        }
        $stmt = null;
        
    }

    /* AGREGAR CATEGORIAS */
    
    
    /* EDITAR IMAGEN */
    
    static public function editarImagenModel($datos){
    
        $stmt = Conexion::conectar()->prepare("UPDATE servicios SET imagen=:imagen WHERE id = :id");
    
        $stmt->bindParam(":id", $datos['id'], PDO::PARAM_INT);
        $stmt->bindParam(":imagen", $datos['imagen'], PDO::PARAM_STR);
    
        if($stmt->execute()){
            return 'success';
        }else{
            return 'error';
        }
    
        $stmt = null;
    
    }
    
    /* EDITAR IMAGEN */
    
    
    /* BUSCAR CATEGORIA */
    
    static public function buscarServicioModel($id){
    
        $stmt = Conexion::conectar()->prepare("SELECT servicios.* FROM servicios WHERE servicios.id=:id");
    
        $stmt->bindParam(':id', $id,PDO::PARAM_INT);
    
        $stmt -> execute();
    
        return $stmt -> fetch();
    
        $stmt = null;
    
    }
    
    /* BUSCAR CATEGORIA */
    
    
    /* EDITAR CATEGORIA */
    
    static public function editarServicioModel($datos){
    
        $stmt = Conexion::conectar()->prepare("UPDATE servicios SET nombre=:nombre WHERE id = :id");
    
        $stmt->bindParam(":id", $datos['id'], PDO::PARAM_INT);
        $stmt->bindParam(":nombre", $datos['nombre'], PDO::PARAM_STR);
    
        if($stmt->execute()){
            return 'success';
        }else{
            return 'error';
        }
    
        $stmt = null;
    
    }
    
    /* EDITAR CATEGORIA */
    
}