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
    
    static public function obtenerCategoriasPropietarioModel($id_propietario,$extras){
    
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
    
        $categorias = $stmt -> fetchAll();

        if ($extras) {
            $categorias = array_merge(
                $categorias,
                self::obtenerCategoriasExtraPropietarioModel($id_propietario)
            );
        }
        
    
        return $categorias;
        
        $stmt = null;
    
    }

    static public function obtenerCategoriasExtraPropietarioModel($id_propietario){
    
        $stmt = Conexion::conectar()->prepare("SELECT DISTINCT
            menu_categorias.*
        FROM
            menu_categorias
        INNER JOIN cafeterias_menu_subcategorias_extra ON cafeterias_menu_subcategorias_extra.id_categoria = menu_categorias.id
        WHERE
            cafeterias_menu_subcategorias_extra.id_propietario = :propietario AND cafeterias_menu_subcategorias_extra.estado = 1");
    
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

    static public function obtenerProductosModel($subcategoria, $id_propietario, $estado=false, $cafeteria=false,$extras){

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
    
        $productos = $stmt -> fetchAll();

        if ($extras) {
            $productos = array_merge(
                $productos,
                self::obtenerProductosExtraModel($subcategoria, $id_propietario, "id_subcategoria",false, $cafeteria)
            );
        }
        
    
        return $productos;
        
        $stmt = null;
    
    }
    
    /* OBTENER PRODUCTOS */
    
    
    /* INSERTAR SUBCATEGORIA A CAFETERIA */
    
    static public function agregarSubcategoriaModel($datos) {
        $conexion = Conexion::conectar();
    
        // Primero, validamos si ya existe la subcategoría con ese propietario
        $stmtValidar = $conexion->prepare("SELECT COUNT(*) FROM cafeterias_menu_subcategorias WHERE id_subcategoria = :id_subcategoria AND id_propietario = :id_propietario");
        $stmtValidar->bindParam(':id_subcategoria', $datos['id_subcategoria'], PDO::PARAM_INT);
        $stmtValidar->bindParam(':id_propietario', $datos['id_propietario'], PDO::PARAM_INT);
        $stmtValidar->execute();
    
        if ($stmtValidar->fetchColumn() > 0) {
            // Ya existe, no se inserta
            return 'existe';
        }
    
        // Si no existe, se inserta
        $stmtInsertar = $conexion->prepare("INSERT INTO cafeterias_menu_subcategorias (id_subcategoria, id_propietario, estado) VALUES (:id_subcategoria, :id_propietario, 1)");
        $stmtInsertar->bindParam(':id_subcategoria', $datos['id_subcategoria'], PDO::PARAM_INT);
        $stmtInsertar->bindParam(':id_propietario', $datos['id_propietario'], PDO::PARAM_INT);
    
        if ($stmtInsertar->execute()) {
            return 'success';
        } else {
            return 'error';
        }
    }
    
    
    /* INSERTAR SUBCATEGORIA A CAFETERIA */


    /* INSERTAR PRODUCTO A CAFETERIA */

    static public function agregarProductoModel($datos) {
        $conexion = Conexion::conectar();
    
        // Verificar si ya existe el producto para ese propietario
        $stmtValidar = $conexion->prepare("SELECT COUNT(*) FROM cafeterias_menu_productos WHERE id_producto = :id_producto AND id_propietario = :id_propietario");
        $stmtValidar->bindParam(':id_producto', $datos['id_producto'], PDO::PARAM_INT);
        $stmtValidar->bindParam(':id_propietario', $datos['id_propietario'], PDO::PARAM_INT);
        $stmtValidar->execute();
    
        if ($stmtValidar->fetchColumn() > 0) {
            // Ya existe, no se inserta
            return 'existe';
        }
    
        // Insertar si no existe
        $stmtInsertar = $conexion->prepare("INSERT INTO cafeterias_menu_productos (id_producto, id_propietario, estado) VALUES (:id_producto, :id_propietario, 1)");
        $stmtInsertar->bindParam(':id_producto', $datos['id_producto'], PDO::PARAM_INT);
        $stmtInsertar->bindParam(':id_propietario', $datos['id_propietario'], PDO::PARAM_INT);
    
        if ($stmtInsertar->execute()) {
            return 'success';
        } else {
            return 'error';
        }
    }
    
    
    /* INSERTAR PRODUCTO A CAFETERIA */

    
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

        // if ($datos['campo']=='id_producto_extra') {
        //     $tabla = 'cafeterias_menu_productos';
        // }
    
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
        $campoProducto = $datos['campo'];
        $id = $datos['id_propietario'];
        if ($cafeteria!=='false') {
            $tabla = 'cafeterias_menu_sucursales';
            $campo ="id_cafeteria";
            $id = $cafeteria;
        }

        $stmt = Conexion::conectar()->prepare("UPDATE $tabla SET estado = 0 
        WHERE $campoProducto = :id_producto 
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
        $campoProducto = $datos['campo'];
        $id = $datos['id_propietario'];
        if ($cafeteria!=='false') {
            $tabla = 'cafeterias_menu_sucursales';
            $campo ="id_cafeteria";
            $id = $cafeteria;
        }

        $conexion = Conexion::conectar();
        $stmt = $conexion->prepare("UPDATE $tabla 
            SET estado = 1, precio = :precio
            WHERE $campoProducto = :id_producto 
            AND id_tamano = :id_tamano 
            AND $campo = :id");
    
        $stmt->bindParam(":id_producto", $datos['id_producto'], PDO::PARAM_INT);
        $stmt->bindParam(":id_tamano", $datos['id_tamano'], PDO::PARAM_INT);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->bindParam(":precio", $datos['precio'], PDO::PARAM_STR);
        
        $stmt->execute();
    
        // Si no se actualizó ningún registro, insertar uno nuevo
        if ($stmt->rowCount() == 0) {
            $stmt = $conexion->prepare("INSERT INTO $tabla 
                ($campoProducto, id_tamano, $campo, precio, estado) 
                VALUES (:id_producto, :id_tamano, :id, :precio, 1)");
    
            $stmt->bindParam(":id_producto", $datos['id_producto'], PDO::PARAM_INT);
            $stmt->bindParam(":id_tamano", $datos['id_tamano'], PDO::PARAM_INT);
            $stmt->bindParam(":id", $id, PDO::PARAM_INT);
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
    
    static public function buscarProductoModel($datos, $cafeteria=false,$campoProducto){
        $tabla='cafeterias_menu_productos';
        $campo ="id_propietario";
        $id = $datos['id_propietario'];
        if ($cafeteria!=='false') {
            $tabla = 'cafeterias_menu_sucursales';
            $campo ="id_cafeteria";
            $id = $cafeteria;
        }
    
        $stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla              
        WHERE $campoProducto = :id_producto 
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
    
    static public function actualizarMenuSucursalModel($datos, $cafeteria){

        $campo='id_producto';
        $valor = $datos['id_producto'];
        if ($datos['id_producto']==0) {
            $campo = 'id_producto_extra';
            $valor = $datos['id_producto_extra'];
        }
    
        $stmt = Conexion::conectar()->prepare("INSERT INTO cafeterias_menu_sucursales ($campo, id_tamano, id_cafeteria, precio, estado) 
        VALUES (:id_producto, :id_tamano, :id_cafeteria, :precio, :estado)");
    
        $stmt->bindParam(':id_producto', $valor, PDO::PARAM_INT);
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
    
        $stmt = Conexion::conectar()->prepare("UPDATE cafeterias_menu_sucursales 
        SET precio = :precio 
        WHERE id_producto = :id_producto
        AND id_producto_extra = :id_producto_extra
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




    /* De aqui en adelante se gestiona lo relacionado 
    con las categorias subcategorias "Extra" agregadas por los propietarios */

    
    /* OBTENER SUBCATEGORIAS POR CATEGORIA */

    static public function obtenerSubcategoriasExtraModel($categoria, $id_propietario, $subcategoriasExtrasActivas=false) {
        $sql = "SELECT 
                    cafeterias_menu_subcategorias_extra.*,
                    COALESCE(cafeterias_menu_subcategorias_extra.estado, 'No') AS estado,
                    menu_categorias.nombre AS categoria
                FROM cafeterias_menu_subcategorias_extra
                INNER JOIN menu_categorias ON menu_categorias.id = cafeterias_menu_subcategorias_extra.id_categoria
                WHERE cafeterias_menu_subcategorias_extra.id_propietario = :id_propietario";

        if ($categoria !== false) {
            $sql .= " AND cafeterias_menu_subcategorias_extra.id_categoria = :categoria";
        }

        if ($subcategoriasExtrasActivas !== false) {
            $sql .= " AND cafeterias_menu_subcategorias_extra.estado = 1";
        }else{
            $sql .= " AND cafeterias_menu_subcategorias_extra.estado != 2";
        }

        $stmt = Conexion::conectar()->prepare($sql);
        $stmt->bindParam(':id_propietario', $id_propietario, PDO::PARAM_INT);

        if ($categoria !== false) {
            $stmt->bindParam(':categoria', $categoria, PDO::PARAM_INT);
        }

        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /* OBTENER SUBCATEGORIAS POR CATEGORIA */
    
    
    /* AGREGAR SUBCATEGORIAS */

    static public function registrarSubcategoriaModel($datos)
    {

        $conexion = Conexion::conectar();
        $stmt = $conexion->prepare("INSERT INTO cafeterias_menu_subcategorias_extra(nombre, id_categoria, id_propietario) 
        VALUES (:nombre, :id_categoria, :id_propietario)");

        $stmt->bindParam(':nombre', $datos['nombre'], PDO::PARAM_STR);
        $stmt->bindParam(':id_categoria', $datos['id_categoria'], PDO::PARAM_INT);
        $stmt->bindParam(':id_propietario', $datos['id_propietario'], PDO::PARAM_INT);
        
        if($stmt->execute()){
            return $conexion-> lastInsertId();
        }else{
            return "error";
        }
        $stmt = null;
        
    }

    /* AGREGAR SUBCATEGORIAS */


    /* CAMBIAR ESTADO SUBCATEGORIA */

    static public function cambiarEstadoSubcategoriaExtraModel($datos){

        $stmt = Conexion::conectar()->prepare("UPDATE cafeterias_menu_subcategorias_extra SET estado = :estado WHERE id = :id");
    
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


    /* AGREGAR PRODUCTOS */

    static public function agregarProductoExtraModel($datos)
    {
        $campo = $datos['campo'];
        $conexion = Conexion::conectar();
        $stmt = $conexion->prepare("INSERT INTO cafeterias_menu_productos_extra(nombre, $campo, imagen, id_propietario) 
        VALUES (:nombre, :id_subcategoria, 'views/assets/img/cafeteria_default.png', :id_propietario)");

        $stmt->bindParam(':nombre', $datos['nombre'], PDO::PARAM_STR);
        $stmt->bindParam(':id_subcategoria', $datos['id_subcategoria'], PDO::PARAM_INT);
        $stmt->bindParam(':id_propietario', $datos['id_propietario'], PDO::PARAM_INT);

        if ($stmt->execute()) {
            $id = $conexion->lastInsertId();

            $stmtInsertar = $conexion->prepare("INSERT INTO cafeterias_menu_productos (id_producto_extra, id_propietario, estado) VALUES (:id_producto_extra, :id_propietario, 1)");
            $stmtInsertar->bindParam(':id_producto_extra', $id, PDO::PARAM_INT);
            $stmtInsertar->bindParam(':id_propietario', $datos['id_propietario'], PDO::PARAM_INT);
    
            if ($stmtInsertar->execute()) {
                return $id;
            } else {
                return 'error';
            }

        } else {
            return "error";
        }
        $stmt = null;
    }

    /* AGREGAR PRODUCTOS */


    /* BUSCAR PRODUCTO EXTRA */

    static public function buscarProductoExtraModel($id_producto){

        $stmt = Conexion::conectar()->prepare("SELECT * FROM cafeterias_menu_productos_extra WHERE id=:id_producto");
    
        $stmt->bindParam(':id_producto', $id_producto,PDO::PARAM_INT);
    
        $stmt -> execute();
    
        return $stmt -> fetchAll();
    
        $stmt = null;
    
    }
    
    /* BUSCAR PRODUCTO EXTRA */


    /* EDITAR IMAGEN */

    static public function editarImagenModel($datos)
    {

        $stmt = Conexion::conectar()->prepare("UPDATE cafeterias_menu_productos_extra SET imagen=:imagen WHERE id = :id");

        $stmt->bindParam(":id", $datos['id'], PDO::PARAM_INT);
        $stmt->bindParam(":imagen", $datos['imagen'], PDO::PARAM_STR);

        if ($stmt->execute()) {
            return 'success';
        } else {
            return 'error';
        }

        $stmt = null;
    }

    /* EDITAR IMAGEN */

    
    /* OBTENER PRODUCTOS EXTRA */
    
    static public function obtenerProductosExtraModel($subcategoria, $id_propietario, $campo, $productosActivos, $cafeteria){

        if ($cafeteria) {
            $id_propietario = $cafeteria;
            $stmt = Conexion::conectar()->prepare("SELECT
            cafeterias_menu_productos_extra.id,
            cafeterias_menu_sucursales.id_producto_extra,
            cafeterias_menu_productos_extra.nombre,
            COALESCE(cafeterias_menu_sucursales.estado, 'No') AS estado,
            COALESCE(cafeterias_menu_sucursales.id, 'No') AS id_registro,
            cafeterias_menu_productos_extra.imagen
            FROM
                cafeterias_menu_productos_extra
            LEFT JOIN cafeterias_menu_sucursales ON cafeterias_menu_sucursales.id_producto_extra = cafeterias_menu_productos_extra.id
                AND cafeterias_menu_sucursales.id_cafeteria = :propietario
                AND id_tamano = 0
            WHERE cafeterias_menu_productos_extra.estado = 0
            AND cafeterias_menu_productos_extra.$campo = :subcategoria 
            AND cafeterias_menu_sucursales.estado !=2
            ");

        }else{
            $filtro='';
            if ($productosActivos) {
                $filtro = ' AND estado=1';
            }
            $stmt = Conexion::conectar()->prepare("SELECT *,
            cafeterias_menu_productos.id AS id_registro,
            cafeterias_menu_productos.id_producto_extra
            FROM cafeterias_menu_productos_extra 
            INNER JOIN cafeterias_menu_productos ON cafeterias_menu_productos.id_producto_extra = cafeterias_menu_productos_extra.id
                AND cafeterias_menu_productos.id_tamano = 0
            WHERE cafeterias_menu_productos_extra.$campo=:subcategoria
            AND cafeterias_menu_productos.id_propietario=:propietario
            $filtro");

        }
        
            $stmt->bindParam(':subcategoria',$subcategoria ,PDO::PARAM_INT);
            $stmt->bindParam(':propietario',$id_propietario ,PDO::PARAM_INT);
        
            $stmt -> execute();
        
            return $stmt -> fetchAll();
        
            $stmt = null; 
    
    }
    
    /* OBTENER PRODUCTOS EXTRA */

    
}