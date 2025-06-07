<?php 

require_once "conexion.php";

class PropietariosMenuModel extends Conexion {

    
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
        INNER JOIN propietarios_menu_subcategorias ON propietarios_menu_subcategorias.id_subcategoria = menu_subcategorias.id
        WHERE
            propietarios_menu_subcategorias.id_propietario = :propietario AND propietarios_menu_subcategorias.estado = 1");
    
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
        INNER JOIN propietarios_menu_subcategorias_extra ON propietarios_menu_subcategorias_extra.id_categoria = menu_categorias.id
        WHERE
            propietarios_menu_subcategorias_extra.id_propietario = :propietario AND propietarios_menu_subcategorias_extra.estado = 1");
    
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
            $filtro = ' AND propietarios_menu_subcategorias.estado=1';
        }
        $stmt = Conexion::conectar()->prepare("SELECT 
        menu_subcategorias.*
        FROM
            menu_subcategorias
        INNER JOIN 
            propietarios_menu_subcategorias 
            ON propietarios_menu_subcategorias.id_subcategoria = menu_subcategorias.id
            AND propietarios_menu_subcategorias.id_propietario = :propietario
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
        COALESCE(propietarios_menu_subcategorias.estado, 'No') AS estado,
        COALESCE(propietarios_menu_subcategorias.id, 'No') AS id_registro
        FROM
            menu_subcategorias
        LEFT JOIN 
            propietarios_menu_subcategorias 
            ON propietarios_menu_subcategorias.id_subcategoria = menu_subcategorias.id
            AND propietarios_menu_subcategorias.id_propietario = :id_propietario -- Se mueve aquí
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

        $tabla='propietarios_menu_productos';
        $campo ="id_propietario";
        $id = $id_propietario;
        $condicion='';
        if ($cafeteria) {
            $tabla = 'propietarios_menu_cafeterias';
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
        $stmtValidar = $conexion->prepare("SELECT COUNT(*) FROM propietarios_menu_subcategorias WHERE id_subcategoria = :id_subcategoria AND id_propietario = :id_propietario");
        $stmtValidar->bindParam(':id_subcategoria', $datos['id_subcategoria'], PDO::PARAM_INT);
        $stmtValidar->bindParam(':id_propietario', $datos['id_propietario'], PDO::PARAM_INT);
        $stmtValidar->execute();
    
        if ($stmtValidar->fetchColumn() > 0) {
            // Ya existe, no se inserta
            return 'existe';
        }
    
        // Si no existe, se inserta
        $stmtInsertar = $conexion->prepare("INSERT INTO propietarios_menu_subcategorias (id_subcategoria, id_propietario, estado) VALUES (:id_subcategoria, :id_propietario, 1)");
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
        $stmtValidar = $conexion->prepare("SELECT COUNT(*) FROM propietarios_menu_productos WHERE id_producto = :id_producto AND id_propietario = :id_propietario");
        $stmtValidar->bindParam(':id_producto', $datos['id_producto'], PDO::PARAM_INT);
        $stmtValidar->bindParam(':id_propietario', $datos['id_propietario'], PDO::PARAM_INT);
        $stmtValidar->execute();
    
        if ($stmtValidar->fetchColumn() > 0) {
            // Ya existe, no se inserta
            return 'existe';
        }
    
        // Insertar si no existe
        $stmtInsertar = $conexion->prepare("INSERT INTO propietarios_menu_productos (id_producto, id_propietario, estado) VALUES (:id_producto, :id_propietario, 1)");
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
        
        $stmt = Conexion::conectar()->prepare("UPDATE propietarios_menu_subcategorias SET estado = :estado WHERE id = :id");
    
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

        $tabla='propietarios_menu_productos';
        if ($cafeteria!=='false') {
            $tabla = 'propietarios_menu_cafeterias';
        }

        // if ($datos['campo']=='id_producto_extra') {
        //     $tabla = 'propietarios_menu_productos';
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
        COALESCE(propietarios_menu_productos.estado, 'No') AS estado,
        COALESCE(propietarios_menu_productos.id, 'No') AS id_registro,
        FROM propietarios_menu_productos
            
        WHERE propietarios_menu_productos.id_tamano = 0
        AND propietarios_menu_productos.id_propietario = :id_propietario
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

        $tabla='propietarios_menu_productos';
        $campo ="id_propietario";
        $campoProducto = $datos['campo'];
        $id = $datos['id_propietario'];
        if ($cafeteria!=='false') {
            $tabla = 'propietarios_menu_cafeterias';
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
        $tabla='propietarios_menu_productos';
        $campo ="id_propietario";
        $campoProducto = $datos['campo'];
        $id = $datos['id_propietario'];
        if ($cafeteria!=='false') {
            $tabla = 'propietarios_menu_cafeterias';
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
        $tabla='propietarios_menu_productos';
        $campo ="id_propietario";
        $id = $datos['id_propietario'];
        if ($cafeteria!=='false') {
            $tabla = 'propietarios_menu_cafeterias';
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
    
        $stmt = Conexion::conectar()->prepare("DELETE propietarios_menu_cafeterias 
        FROM propietarios_menu_cafeterias
        INNER JOIN cafeterias ON cafeterias.id = propietarios_menu_cafeterias.id_cafeteria
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
    
        $stmt = Conexion::conectar()->prepare("SELECT * FROM propietarios_menu_productos WHERE id_propietario=:propietario");
    
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
    
        $stmt = Conexion::conectar()->prepare("INSERT INTO propietarios_menu_cafeterias ($campo, id_tamano, id_cafeteria, precio, estado) 
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
    
        $stmt = Conexion::conectar()->prepare("UPDATE propietarios_menu_cafeterias 
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
                    propietarios_menu_subcategorias_extra.*,
                    COALESCE(propietarios_menu_subcategorias_extra.estado, 'No') AS estado,
                    menu_categorias.nombre AS categoria
                FROM propietarios_menu_subcategorias_extra
                INNER JOIN menu_categorias ON menu_categorias.id = propietarios_menu_subcategorias_extra.id_categoria
                WHERE propietarios_menu_subcategorias_extra.id_propietario = :id_propietario";

        if ($categoria !== false) {
            $sql .= " AND propietarios_menu_subcategorias_extra.id_categoria = :categoria";
        }

        if ($subcategoriasExtrasActivas !== false) {
            $sql .= " AND propietarios_menu_subcategorias_extra.estado = 1";
        }else{
            $sql .= " AND propietarios_menu_subcategorias_extra.estado != 2";
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
        $stmt = $conexion->prepare("INSERT INTO propietarios_menu_subcategorias_extra(nombre, id_categoria, id_propietario) 
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

        $stmt = Conexion::conectar()->prepare("UPDATE propietarios_menu_subcategorias_extra SET estado = :estado WHERE id = :id");
    
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
        $stmt = $conexion->prepare("INSERT INTO propietarios_menu_productos_extra(nombre, $campo, imagen, id_propietario) 
        VALUES (:nombre, :id_subcategoria, 'views/assets/img/cafeteria_default.png', :id_propietario)");

        $stmt->bindParam(':nombre', $datos['nombre'], PDO::PARAM_STR);
        $stmt->bindParam(':id_subcategoria', $datos['id_subcategoria'], PDO::PARAM_INT);
        $stmt->bindParam(':id_propietario', $datos['id_propietario'], PDO::PARAM_INT);

        if ($stmt->execute()) {
            $id = $conexion->lastInsertId();

            $stmtInsertar = $conexion->prepare("INSERT INTO propietarios_menu_productos (id_producto_extra, id_propietario, estado) VALUES (:id_producto_extra, :id_propietario, 1)");
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

        $stmt = Conexion::conectar()->prepare("SELECT * FROM propietarios_menu_productos_extra WHERE id=:id_producto");
    
        $stmt->bindParam(':id_producto', $id_producto,PDO::PARAM_INT);
    
        $stmt -> execute();
    
        return $stmt -> fetchAll();
    
        $stmt = null;
    
    }
    
    /* BUSCAR PRODUCTO EXTRA */


    /* EDITAR IMAGEN */

    static public function editarImagenModel($datos)
    {

        $stmt = Conexion::conectar()->prepare("UPDATE propietarios_menu_productos_extra SET imagen=:imagen WHERE id = :id");

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
            propietarios_menu_productos_extra.id,
            propietarios_menu_cafeterias.id_producto_extra,
            propietarios_menu_productos_extra.nombre,
            COALESCE(propietarios_menu_cafeterias.estado, 'No') AS estado,
            COALESCE(propietarios_menu_cafeterias.id, 'No') AS id_registro,
            propietarios_menu_productos_extra.imagen
            FROM
                propietarios_menu_productos_extra
            LEFT JOIN propietarios_menu_cafeterias ON propietarios_menu_cafeterias.id_producto_extra = propietarios_menu_productos_extra.id
                AND propietarios_menu_cafeterias.id_cafeteria = :propietario
                AND id_tamano = 0
            WHERE propietarios_menu_productos_extra.estado = 0
            AND propietarios_menu_productos_extra.$campo = :subcategoria 
            AND propietarios_menu_cafeterias.estado !=2
            ");

        }else{
            $filtro='';
            if ($productosActivos) {
                $filtro = ' AND estado=1';
            }
            $stmt = Conexion::conectar()->prepare("SELECT *,
            propietarios_menu_productos.id AS id_registro,
            propietarios_menu_productos.id_producto_extra
            FROM propietarios_menu_productos_extra 
            INNER JOIN propietarios_menu_productos ON propietarios_menu_productos.id_producto_extra = propietarios_menu_productos_extra.id
                AND propietarios_menu_productos.id_tamano = 0
            WHERE propietarios_menu_productos_extra.$campo=:subcategoria
            AND propietarios_menu_productos.id_propietario=:propietario
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