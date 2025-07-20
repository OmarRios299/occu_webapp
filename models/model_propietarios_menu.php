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
    
    static public function obtenerCategoriasPropietarioModel($id_propietario){
    
        $stmt = Conexion::conectar()->prepare("SELECT DISTINCT
            menu_categorias.*
        FROM
            menu_categorias
        INNER JOIN menu_subcategorias ON menu_subcategorias.id_categoria = menu_categorias.id
        INNER JOIN propietarios_menu_subcategorias ON propietarios_menu_subcategorias.id_subcategoria = menu_subcategorias.id
        WHERE propietarios_menu_subcategorias.id_propietario = :propietario 
            AND propietarios_menu_subcategorias.estado = 1");
    
        $stmt->bindParam(':propietario', $id_propietario,PDO::PARAM_INT);
    
        $stmt -> execute();
    
        $categorias = $stmt -> fetchAll();
        
        return $categorias;
        
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
        menu_subcategorias.registro_occu,
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

    static public function obtenerProductosModel($subcategoria, $id_propietario, $estado=false, $cafeteria=false, $occu=false){

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

        if ($occu) {
            $condicion = ' AND menu_productos.registro_occu = 1';
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
        $id = $datos['id_propietario'];
        if ($cafeteria!=='false') {
            $tabla = 'propietarios_menu_cafeterias';
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
        $tabla='propietarios_menu_productos';
        $campo ="id_propietario";
        $id = $datos['id_propietario'];
        if ($cafeteria!=='false') {
            $tabla = 'propietarios_menu_cafeterias';
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
            $stmt = $conexion->prepare("INSERT INTO $tabla 
                (id_producto, id_tamano, $campo, precio, estado) 
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
    
    static public function buscarProductoModel($datos, $cafeteria=false){
        $tabla="propietarios_menu_productos";
        $campo ="id_propietario";
        $id = $datos['id_propietario'];
        if ($cafeteria!=='false') {
            $tabla = 'propietarios_menu_cafeterias';
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

        $stmt = Conexion::conectar()->prepare("INSERT INTO propietarios_menu_cafeterias (id_producto, id_tamano, id_cafeteria, precio, estado) 
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
    
        $stmt = Conexion::conectar()->prepare("UPDATE propietarios_menu_cafeterias 
        SET precio = :precio 
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


    
    /* AGREGAR SUBCATEGORIAS */

    static public function registrarSubcategoriaModel($datos)
    {

        $conexion = Conexion::conectar();
        $stmt = $conexion->prepare("INSERT INTO menu_subcategorias(nombre, id_categoria, id_propietario, registro_occu) 
        VALUES (:nombre, :id_categoria, :id_propietario, 2)");

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


    /* AGREGAR PRODUCTOS */

    static public function agregarProductoExtraModel($datos)
    {
        $conexion = Conexion::conectar();
        $stmt = $conexion->prepare("INSERT INTO menu_productos(nombre, id_subcategoria, imagen, id_alta, registro_occu) 
        VALUES (:nombre, :id_subcategoria, 'views/assets/img/cafeteria_default.png', :id_propietario, 2)");

        $stmt->bindParam(':nombre', $datos['nombre'], PDO::PARAM_STR);
        $stmt->bindParam(':id_subcategoria', $datos['id_subcategoria'], PDO::PARAM_INT);
        $stmt->bindParam(':id_propietario', $datos['id_propietario'], PDO::PARAM_INT);

        if ($stmt->execute()) {
            $id = $conexion->lastInsertId();

            $stmtInsertar = $conexion->prepare("INSERT INTO propietarios_menu_productos (id_producto, id_propietario, estado) VALUES (:id_producto, :id_propietario, 1)");
            $stmtInsertar->bindParam(':id_producto', $id, PDO::PARAM_INT);
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

        $stmt = Conexion::conectar()->prepare("SELECT * FROM menu_productos WHERE id=:id_producto");
    
        $stmt->bindParam(':id_producto', $id_producto,PDO::PARAM_INT);
    
        $stmt -> execute();
    
        return $stmt -> fetchAll();
    
        $stmt = null;
    
    }
    
    /* BUSCAR PRODUCTO EXTRA */


    /* EDITAR IMAGEN */

    static public function editarImagenModel($datos)
    {

        $stmt = Conexion::conectar()->prepare("UPDATE menu_productos SET imagen=:imagen WHERE id = :id");

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



    /* ----- FUNCIONAMIENTO DEL MODAL DE SELECCION DE INGREDIENTES ----- 
    ---------------------------------------------------------------------*/


    static public function obtenerCategoriasingredientesModel(){
    
        $stmt = Conexion::conectar()->prepare("SELECT
        cat.*
        FROM
            menu_ingredientes_categorias cat
        WHERE
        cat.estado = 0");
    
        $stmt -> execute();
    
        return $stmt -> fetchAll();
    
        $stmt = null;
    
    }


    static public function obtenerIngredientesModel($categoria, $datos, $id_producto){

        $campo ='id_propietario';
        $registro = $datos['id_propietario'];

        if ($datos['cafeteria']!=='false') {
            $campo ='id_cafeteria';
            $registro = $datos['cafeteria'];
        }
    
        $stmt = Conexion::conectar()->prepare("SELECT 
        menu_ingredientes.id,
        menu_ingredientes.nombre,
        menu_ingredientes.registro_occu,
        COALESCE(prop_ingre.estado, 'No') AS estado,
        COALESCE(prop_ingre.id, 'No') AS id_registro
        FROM
            menu_ingredientes
        LEFT JOIN propietarios_menu_ingredientes prop_ingre ON prop_ingre.id_ingrediente = menu_ingredientes.id
            AND prop_ingre.$campo = :registro
            AND prop_ingre.id_producto = :id_producto
        WHERE
            menu_ingredientes.estado = 0
            AND menu_ingredientes.id_ingrediente_categoria = :categoria;

        ");
    
        $stmt->bindParam(':categoria', $categoria,PDO::PARAM_INT);
        $stmt->bindParam(':registro', $registro,PDO::PARAM_INT);
        $stmt->bindParam(':id_producto', $id_producto,PDO::PARAM_INT);
    
        $stmt -> execute();
    
        return $stmt -> fetchAll();
    
        $stmt = null;
    
    }

    static public function cambiarEstadoIngredienteModel($datos){
        
        $stmt = Conexion::conectar()->prepare("UPDATE propietarios_menu_ingredientes SET estado = :estado WHERE id = :id");
    
        $stmt->bindParam(":id", $datos['id_registro'], PDO::PARAM_INT);
        $stmt->bindParam(":estado", $datos['estado'], PDO::PARAM_INT);
    
        if($stmt->execute()){
            return 'success';
        }else{
            return 'error';
        }
    
        $stmt = null;
    
    }


    static public function agregarIngredienteModel($datos) {

        $campo ='id_propietario';
        $registro = $datos['id_propietario'];

        if ($datos['id_cafeteria']!==false) {
            $campo ='id_cafeteria';
            $registro = $datos['id_cafeteria'];
        }

        $conexion = Conexion::conectar();
    
        // Primero, validamos si ya existe el ingrediente
        $stmtValidar = $conexion->prepare("SELECT COUNT(*) FROM propietarios_menu_ingredientes 
        WHERE id_producto = :id_producto 
        AND id_ingrediente = :id_ingrediente
        AND $campo = :registro");

        $stmtValidar->bindParam(':id_producto', $datos['id_producto'], PDO::PARAM_INT);
        $stmtValidar->bindParam(':id_ingrediente', $datos['id_ingrediente'], PDO::PARAM_INT);
        $stmtValidar->bindParam(':registro', $registro, PDO::PARAM_INT);
        $stmtValidar->execute();
    
        if ($stmtValidar->fetchColumn() > 0) {
            // Ya existe, no se inserta
            return 'existe';
        }
    
        // Si no existe, se inserta
        $stmtInsertar = $conexion->prepare("INSERT INTO propietarios_menu_ingredientes 
        (id_producto, id_ingrediente, $campo, estado) 
        VALUES 
        (:id_producto, :id_ingrediente, :registro, 1)");

        $stmtInsertar->bindParam(':id_producto', $datos['id_producto'], PDO::PARAM_INT);
        $stmtInsertar->bindParam(':id_ingrediente', $datos['id_ingrediente'], PDO::PARAM_INT);
        $stmtInsertar->bindParam(':registro', $registro, PDO::PARAM_INT);
    
        if ($stmtInsertar->execute()) {
            return 'success';
        } else {
            return 'error';
        }
    }
    
    
}