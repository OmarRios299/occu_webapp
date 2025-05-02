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

    
    /* CONTAR CAFETERIAS POR PROPIETARIO */
    
    static public function cafeteriasPropietarioModel($id_propietario){
        $stmt = Conexion::conectar()->prepare("SELECT COUNT(id) AS sucursales FROM cafeterias 
        WHERE estado !=2
        AND id_usuario=:usuario");
        $stmt->bindParam(':usuario', $id_propietario, PDO::PARAM_INT);
        
        $stmt->execute();
        
        // Obtener solo el número de sucursales
        $result = $stmt->fetch();
        
        // Retornar solo el valor de "sucursales"
        return $result['sucursales'];
        
        $stmt = null;
    }    
    
    /* CONTAR CAFETERIAS POR PROPIETARIO */
    

    
    /* OBTENER CATEGORIAS POR PROPIETARIO */
    
    static public function obtenerCategoriasPropietarioModel($id_propietario){
    
        $stmt = Conexion::conectar()->prepare("SELECT DISTINCT
            menu_categorias.*
        FROM
            menu_categorias
        INNER JOIN menu_subcategorias ON menu_subcategorias.id_categoria = menu_categorias.id
        INNER JOIN cafeterias_menu_subcategorias ON cafeterias_menu_subcategorias.id_subcategoria = menu_subcategorias.id
        WHERE
            cafeterias_menu_subcategorias.id_propietario = :propietario AND cafeterias_menu_subcategorias.estado = 1");
    
        $stmt->bindParam(':propietario', $id_propietario,PDO::PARAM_INT);
    
        $stmt -> execute();
    
        return $stmt -> fetchAll();
    
        $stmt = null;
    
    }
    
    /* OBTENER CATEGORIAS POR PROPIETARIO */


    /* OBTENER SUBCATEGORIAS */

    static public function obtenerSubcategoriasPropietarioModel($id_propietario, $estado=false){
        $filtro='';
        if ($estado) {
            $filtro = ' AND cafeterias_menu_subcategorias.estado=1';
        }
        $stmt = Conexion::conectar()->prepare("SELECT 
        menu_subcategorias.*
        FROM
            menu_subcategorias
        INNER JOIN 
            cafeterias_menu_subcategorias 
            ON cafeterias_menu_subcategorias.id_subcategoria = menu_subcategorias.id
            AND cafeterias_menu_subcategorias.id_propietario = :propietario
        WHERE
            menu_subcategorias.estado = 0 $filtro");

        $stmt->bindParam(':propietario', $id_propietario,PDO::PARAM_INT);
    
        $stmt -> execute();
    
        return $stmt -> fetchAll();
    
        $stmt = null;
    
    }
    
    /* OBTENER SUBCATEGORIAS */

    
    /* OBTENER SUBCATEGORIAS POR CATEGORIA */
    
    static public function obtenerSubcategoriasModel($categoria, $id_propietario){
    
        $stmt = Conexion::conectar()->prepare("SELECT 
        menu_subcategorias.id,
        menu_subcategorias.nombre,
        COALESCE(cafeterias_menu_subcategorias.estado, 'No') AS estado,
        COALESCE(cafeterias_menu_subcategorias.id, 'No') AS id_registro
        FROM
            menu_subcategorias
        LEFT JOIN 
            cafeterias_menu_subcategorias 
            ON cafeterias_menu_subcategorias.id_subcategoria = menu_subcategorias.id
            AND cafeterias_menu_subcategorias.id_propietario = :id_propietario -- Se mueve aquí
        WHERE
            menu_subcategorias.estado = 0
            AND menu_subcategorias.id_categoria = :categoria;

        ");
    
        $stmt->bindParam(':categoria', $categoria,PDO::PARAM_INT);
        $stmt->bindParam(':id_propietario', $id_propietario,PDO::PARAM_INT);
    
        $stmt -> execute();
    
        return $stmt -> fetchAll();
    
        $stmt = null;
    
    }
    
    /* OBTENER SUBCATEGORIAS POR CATEGORIA */


    /* OBTENER PRODUCTOS */

    static public function obtenerProductosModel($subcategoria, $id_propietario, $estado=false, $cafeteria=false){

        $tabla='cafeterias_menu_productos';
        $campo ="id_propietario";
        $id = $id_propietario;
        $condicion='';
        if ($cafeteria) {
            $tabla = 'cafeterias_menu_sucursales';
            $campo ="id_cafeteria";
            $id = $cafeteria;
            $condicion=' AND '.$tabla.'.estado !=2';
        }

        $filtro=' AND '.$tabla.'.estado !=2';
        if ($estado) {
            $filtro = ' AND '.$tabla.'.estado=1';
            $condicion=' AND '.$tabla.'.estado =1';
        }

        $stmt = Conexion::conectar()->prepare("SELECT
        menu_productos.id,
        menu_productos.nombre,
        COALESCE($tabla.estado, 'No') AS estado,
        COALESCE($tabla.id, 'No') AS id_registro,
        menu_productos.imagen
        FROM
            menu_productos
        LEFT JOIN $tabla ON $tabla.id_producto = menu_productos.id
            AND $tabla.$campo = :id
            AND id_tamano = 0
            $filtro
        WHERE menu_productos.estado = 0
        AND menu_productos.id_subcategoria = :subcategoria
        $condicion
        ");

        $stmt->bindParam(':subcategoria', $subcategoria,PDO::PARAM_INT);
        $stmt->bindParam(':id', $id,PDO::PARAM_INT);

        $stmt -> execute();
    
        return $stmt -> fetchAll();
    
        $stmt = null;
    
    }
    
    /* OBTENER PRODUCTOS */
    
    
    /* INSERTAR SUBCATEGORIA A CAFETERIA */
    
    static public function agregarSubcategoriaModel($datos){
    
        $stmt = Conexion::conectar()->prepare("INSERT INTO cafeterias_menu_subcategorias (id_subcategoria, id_propietario, estado) VALUES (:id_subcategoria, :id_propietario, 1)");
    
        $stmt->bindParam(':id_subcategoria', $datos['id_subcategoria'], PDO::PARAM_INT);
        $stmt->bindParam(':id_propietario', $datos['id_propietario'], PDO::PARAM_INT);
    
        if($stmt->execute()){
            return 'success';
        }else{
            return 'error';
        }
        $stmt = null;
    }
    
    /* INSERTAR SUBCATEGORIA A CAFETERIA */

    /* INSERTAR SUBCATEGORIA A CAFETERIA */

    static public function  agregarProductoModel($datos){

        $stmt = Conexion::conectar()->prepare("INSERT INTO cafeterias_menu_productos (id_producto, id_propietario, estado) VALUES (:id_producto, :id_propietario, 1)");
    
        $stmt->bindParam(':id_producto', $datos['id_producto'], PDO::PARAM_INT);
        $stmt->bindParam(':id_propietario', $datos['id_propietario'], PDO::PARAM_INT);
    
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

    
    /* CAMBIAR ESTADO PRODUCTOS */

    static public function cambiarEstadoProductoModel($datos, $cafeteria=false){

        $tabla='cafeterias_menu_productos';
        if ($cafeteria!=='false') {
            $tabla = 'cafeterias_menu_sucursales';
        }
    
        $stmt = Conexion::conectar()->prepare("UPDATE $tabla SET estado = :estado WHERE id = :id");
    
        $stmt->bindParam(":id", $datos['id_registro'], PDO::PARAM_INT);
        $stmt->bindParam(":estado", $datos['estado'], PDO::PARAM_INT);
    
        if($stmt->execute()){
            return 'success';
        }else{
            return 'error';
        }
    
        $stmt = null;
    
    }
    
    /* CAMBIAR ESTADO PRODUCTOS */

    
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

    
    /* BUSCAR TAMANOS POR BEBIDA */
    
    static public function buscarTamanoBebidaController(){
        $stmt = Conexion::conectar()->prepare("SELECT
        COALESCE(cafeterias_menu_productos.estado, 'No') AS estado,
        COALESCE(cafeterias_menu_productos.id, 'No') AS id_registro,
        FROM cafeterias_menu_productos
            
        WHERE cafeterias_menu_productos.id_tamano = 0
        AND cafeterias_menu_productos.id_propietario = :id_propietario
        ");

        $stmt->bindParam(':subcategoria', $subcategoria,PDO::PARAM_INT);
        $stmt->bindParam(':id_propietario', $id_propietario,PDO::PARAM_INT);

        $stmt -> execute();
    
        return $stmt -> fetchAll();
    
        $stmt = null;
    }
    
    /* BUSCAR TAMANOS POR BEBIDA */


    /* DESACTIVAR PRODUCTOS */

    static public function desactivarProductoModel($datos, $cafeteria=false){

        $tabla='cafeterias_menu_productos';
        $campo ="id_propietario";
        $id = $datos['id_propietario'];
        if ($cafeteria!=='false') {
            $tabla = 'cafeterias_menu_sucursales';
            $campo ="id_cafeteria";
            $id = $cafeteria;
        }

        $stmt = Conexion::conectar()->prepare("UPDATE $tabla SET estado = 0 
        WHERE id_producto = :id_producto 
        AND id_tamano = :id_tamano 
        AND $campo = :id");
    
        $stmt->bindParam(":id_producto", $datos['id_producto'], PDO::PARAM_INT);
        $stmt->bindParam(":id_tamano", $datos['id_tamano'], PDO::PARAM_INT);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
    
        if($stmt->execute()){
            return 'success';
        }else{
            return 'error';
        }
    
        $stmt = null;
    
    }
    
    /* DESACTIVAR PRODUCTOS */

    
    /* ACTUALIZAR/AGREGAR PRODUCTOS */

    static public function actualizarProductoModel($datos, $cafeteria) {
        $tabla='cafeterias_menu_productos';
        $campo ="id_propietario";
        $id = $datos['id_propietario'];
        if ($cafeteria!=='false') {
            $tabla = 'cafeterias_menu_sucursales';
            $campo ="id_cafeteria";
            $id = $cafeteria;
        }

        $conexion = Conexion::conectar();
        $stmt = $conexion->prepare("UPDATE $tabla 
            SET estado = 1, precio = :precio
            WHERE id_producto = :id_producto 
            AND id_tamano = :id_tamano 
            AND $campo = :id");
    
        $stmt->bindParam(":id_producto", $datos['id_producto'], PDO::PARAM_INT);
        $stmt->bindParam(":id_tamano", $datos['id_tamano'], PDO::PARAM_INT);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->bindParam(":precio", $datos['precio'], PDO::PARAM_STR);
        
        $stmt->execute();
    
        // Si no se actualizó ningún registro, insertar uno nuevo
        if ($stmt->rowCount() == 0) {
            $stmt = $conexion->prepare("INSERT INTO cafeterias_menu_productos 
                (id_producto, id_tamano, id_propietario, precio, estado) 
                VALUES (:id_producto, :id_tamano, :id_propietario, :precio, 1)");
    
            $stmt->bindParam(":id_producto", $datos['id_producto'], PDO::PARAM_INT);
            $stmt->bindParam(":id_tamano", $datos['id_tamano'], PDO::PARAM_INT);
            $stmt->bindParam(":id_propietario", $datos['id_propietario'], PDO::PARAM_INT);
            $stmt->bindParam(":precio", $datos['precio'], PDO::PARAM_STR);
    
            if ($stmt->execute()) {
                return 'success';
            } else {
                return 'error_insert';
            }
        }
    
        return 'success';
    }
    
    
    /* ACTUALIZAR/AGREGAR PRODUCTOS */
    
    
    /* BUSCAR PRODUCTO */
    
    static public function buscarProductoModel($datos, $cafeteria=false){
        $tabla='cafeterias_menu_productos';
        $campo ="id_propietario";
        $id = $datos['id_propietario'];
        if ($cafeteria!=='false') {
            $tabla = 'cafeterias_menu_sucursales';
            $campo ="id_cafeteria";
            $id = $cafeteria;
        }
    
        $stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla              
        WHERE id_producto = :id_producto 
        AND $campo = :id
        AND id_tamano != 0");
    
        $stmt->bindParam(":id_producto", $datos['id_producto'], PDO::PARAM_INT);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
    
        $stmt -> execute();
    
        return $stmt -> fetchAll();
    
        $stmt = null;
    
    }
    
    /* BUSCAR PRODUCTO */
    
    
    /* ELIMINAR REGISTROS DE MENU SUCURSALES */
    
    static public function eliminarMenuSucursalModel($id_propietario){
    
        $stmt = Conexion::conectar()->prepare("DELETE cafeterias_menu_sucursales 
        FROM cafeterias_menu_sucursales
        INNER JOIN cafeterias ON cafeterias.id = cafeterias_menu_sucursales.id_cafeteria
        WHERE cafeterias.id_usuario = :usuario;
        ");

        $stmt->bindParam(":usuario", $id_propietario, PDO::PARAM_INT);
    
        if($stmt->execute()){
            return 'success';
        }else{
            return 'error';
        }
    
        $stmt = null;
    
    }
    
    /* ELIMINAR REGISTROS DE MENU SUCURSALES */

    
    /* BUSCAR MENU PROPIETARIO */
    
    static public function buscarMenuPropietarioModel($id_propietario){
    
        $stmt = Conexion::conectar()->prepare("SELECT * FROM cafeterias_menu_productos WHERE id_propietario=:propietario");
    
        $stmt->bindParam(':propietario', $id_propietario,PDO::PARAM_INT);
    
        $stmt -> execute();
    
        return $stmt -> fetchAll();
    
        $stmt = null;
    
    }
    
    /* BUSCAR MENU PROPIETARIO */

    
    /* INSERTAR ACTUALIZACION DE MENU POR SUCURSAL */
    
    static public function actualizarMenuSucursalModel($datos,$cafeteria){
    
        $stmt = Conexion::conectar()->prepare("INSERT INTO cafeterias_menu_sucursales (id_producto, id_tamano, id_cafeteria, precio, estado) 
        VALUES (:id_producto, :id_tamano, :id_cafeteria, :precio, :estado)");
    
        $stmt->bindParam(':id_producto', $datos['id_producto'], PDO::PARAM_INT);
        $stmt->bindParam(':id_tamano', $datos['id_tamano'], PDO::PARAM_INT);
        $stmt->bindParam(':id_cafeteria', $cafeteria, PDO::PARAM_INT);
        $stmt->bindParam(':precio', $datos['precio'], PDO::PARAM_STR);
        $stmt->bindParam(':estado', $datos['estado'], PDO::PARAM_INT);
    
        if($stmt->execute()){
            return 'success';
        }else{
            return 'error';
        }
        $stmt = null;
    }
    
    /* INSERTAR ACTUALIZACION DE MENU POR SUCURSAL */
    

    
    /* ACTUALIZAR PRECIOS DE MENU POR SUCURSAL */
    
    static public function actualizarPrecioSucursaleModel($datos, $cafeteria){
    
        $stmt = Conexion::conectar()->prepare("UPDATE cafeterias_menu_sucursales SET precio = :precio 
        WHERE id_producto = :id_producto
        AND id_tamano = :id_tamano
        AND id_cafeteria = :id_cafeteria");
    
        $stmt->bindParam(':id_producto', $datos['id_producto'], PDO::PARAM_INT);
        $stmt->bindParam(':id_tamano', $datos['id_tamano'], PDO::PARAM_INT);
        $stmt->bindParam(':id_cafeteria', $cafeteria, PDO::PARAM_INT);
        $stmt->bindParam(':precio', $datos['precio'], PDO::PARAM_STR);
    
        if($stmt->execute()){
            return 'success';
        }else{
            return 'error';
        }
    
        $stmt = null;
    
    }
    
    /* ACTUALIZAR PRECIOS DE MENU POR SUCURSAL */
    
    
    
}