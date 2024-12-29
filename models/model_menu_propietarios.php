<?php 

require_once "conexion.php";

class MenuPropietariosModel extends Conexion {

    
    /* OBTENER CATEGORIAS */
    
    static public function obtenerCategoriasModel(){
    
        $stmt = Conexion::conectar()->prepare("SELECT
        menu_categorias.*
        FROM
            menu_categorias
        WHERE
        menu_categorias.estado = 0");
    
        $stmt -> execute();
    
        return $stmt -> fetchAll();
    
        $stmt = null;
    
    }
    
    /* OBTENER CATEGORIAS */

    
    /* OBTENER SUBCATEGORIAS POR CATEGORIA */
    
    static public function obtenerSubcategoriasModel($categoria, $id_cafeteria){
    
        $stmt = Conexion::conectar()->prepare("SELECT
        menu_subcategorias.id,
        menu_subcategorias.nombre,
        COALESCE(cafeterias_menu_subcategorias.estado, 'No') AS estado,
        COALESCE(cafeterias_menu_subcategorias.id, 'No') AS id_registro
        FROM
            menu_subcategorias
        LEFT JOIN 
            cafeterias_menu_subcategorias ON cafeterias_menu_subcategorias.id_subcategoria = menu_subcategorias.id
        WHERE
            menu_subcategorias.estado = 0
            AND menu_subcategorias.id_categoria = :categoria;
            AND cafeterias_menu_subcategorias.id_cafeteria = :id_cafeteria
        ");
    
        $stmt->bindParam(':categoria', $categoria,PDO::PARAM_INT);
        $stmt->bindParam(':id_cafeteria', $id_cafeteria,PDO::PARAM_INT);
    
        $stmt -> execute();
    
        return $stmt -> fetchAll();
    
        $stmt = null;
    
    }
    
    /* OBTENER SUBCATEGORIAS POR CATEGORIA */


    /* OBTENER PRODUCTOS */

    static public function obtenerProductosModel(){

        $stmt = Conexion::conectar()->prepare("SELECT
        menu_productos.id,
        menu_productos.nombre,
        COALESCE(cafeterias_menu_productos.estado, 'No') AS estado,
        COALESCE(cafeterias_menu_productos.id, 'No') AS id_registro
        FROM
            menu_productos
        LEFT JOIN 
            cafeterias_menu_productos ON cafeterias_menu_productos.id_producto = menu_productos.id
        WHERE
            menu_productos.estado = 0
        AND cafeterias_menu_productos.id_cafeteria = :id_cafeteria
        ");
    
        $stmt -> execute();
    
        return $stmt -> fetchAll();
    
        $stmt = null;
    
    }
    
    /* OBTENER PRODUCTOS */
    
    
    /* INSERTAR SUBCATEGORIA A CAFETERIA */
    
    static public function agregarSubcategoriaModel($datos){
    
        $stmt = Conexion::conectar()->prepare("INSERT INTO cafeterias_menu_subcategorias (id_subcategoria, id_cafeteria, estado) VALUES (:id_subcategoria, :id_cafeteria, 1)");
    
        $stmt->bindParam(':id_subcategoria', $datos['id_subcategoria'], PDO::PARAM_INT);
        $stmt->bindParam(':id_cafeteria', $datos['id_cafeteria'], PDO::PARAM_INT);
    
        if($stmt->execute()){
            return 'success';
        }else{
            return 'error';
        }
        $stmt = null;
    }
    
    /* INSERTAR SUBCATEGORIA A CAFETERIA */

    
    /* CAMBIAR ESTADO SUBCATEGORIA */
    
    static public function cambiarEstadoSubcategoriaModel($datos){
        
        $stmt = Conexion::conectar()->prepare("UPDATE cafeterias_menu_subcategorias SET estado = :estado WHERE id = :id");
    
        $stmt->bindParam(":id", $datos['id_registro'], PDO::PARAM_INT);
        $stmt->bindParam(":estado", $datos['estado'], PDO::PARAM_INT);
    
        if($stmt->execute()){
            return 'success';
        }else{
            return 'error';
        }
    
        $stmt = null;
    
    }
    
    /* CAMBIAR ESTADO SUBCATEGORIA */
    
    
    /* BUSCAR CAFETERIAS POR USUARIO */
    
    static public function buscarCafeteriasUsuarioModel($id_usuario){
    
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
        WHERE cafeterias.estado=0
        AND cafeterias.id_usuario = :usuario");
    
        $stmt->bindParam(':usuario', $id_usuario,PDO::PARAM_INT);
    
        $stmt -> execute();
    
        return $stmt -> fetchAll();
    
        $stmt = null;
    
    }
    
    /* BUSCAR CAFETERIAS POR USUARIO */
    
}