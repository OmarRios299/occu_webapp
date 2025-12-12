<?php 

require_once __DIR__ . '/../config/conexion.php';

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
    
        static public function cafeteriasSucPropietarioModel($id_propietario){
        $stmt = Conexion::conectar()->prepare("SELECT * FROM cafeterias 
        WHERE estado !=2
        AND id_usuario=:usuario");
        $stmt->bindParam(':usuario', $id_propietario, PDO::PARAM_INT);
        
        $stmt->execute();
        
        $result = $stmt->fetchAll();
        
        return $result;
        
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
        menu_subcategorias.*,
        menu_categorias.es_bebida
        FROM
            menu_subcategorias
        INNER JOIN menu_categorias ON menu_categorias.id = menu_subcategorias.id_categoria
        INNER JOIN 
            propietarios_menu_subcategorias 
            ON propietarios_menu_subcategorias.id_subcategoria = menu_subcategorias.id
            AND propietarios_menu_subcategorias.id_propietario = :propietario
        WHERE
            menu_subcategorias.estado = 0 $filtro
            AND (
                menu_subcategorias.registro_occu != 2
                OR (menu_subcategorias.registro_occu = 2 AND menu_subcategorias.id_propietario = :propietario)
            )");

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
            AND propietarios_menu_subcategorias.id_propietario = :id_propietario
        WHERE
            menu_subcategorias.estado = 0
            AND menu_subcategorias.id_categoria = :categoria
            AND (
                menu_subcategorias.registro_occu != 2
                OR (menu_subcategorias.registro_occu = 2 AND menu_subcategorias.id_propietario = :id_propietario)
            )
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

        // Determinar id_cafeteria (0 si es propietario, o el id si es cafetería específica)
        $id_cafeteria = 0;
        if ($cafeteria) {
            $id_cafeteria = $cafeteria;
        }

        // Si es propietario general (id_cafeteria = 0): mostrar TODOS los productos disponibles para activar
        // Si es cafetería específica (id_cafeteria > 0): mostrar solo los productos activados de esa cafetería
        if ($id_cafeteria == 0) {
            // Propietario general: mostrar todos los productos disponibles para activar
            $filtro_join = '';
            $condicion = '';
            
            if ($estado) {
                // Si se pide solo activos, filtrar por estado = 1
                $filtro_join = ' AND propietarios_productos.estado = 1';
                $condicion = ' AND propietarios_productos.estado = 1';
            } else {
                // Mostrar todos, pero excluir los que están desactivados explícitamente (estado = 2)
                $filtro_join = ' AND (propietarios_productos.estado IS NULL OR propietarios_productos.estado != 2)';
            }
            
            // Si occu = true, solo mostrar productos OCCU (registro_occu = 1)
            if ($occu) {
                $condicion = ' AND menu_productos.registro_occu = 1';
            }else{
                $condicion = ' AND propietarios_productos.id_propietario = :id_propietario';
            }
            
            $stmt = Conexion::conectar()->prepare("SELECT
            menu_productos.id,
            menu_productos.nombre,
            COALESCE(propietarios_productos.estado, 'No') AS estado,
            COALESCE(propietarios_productos.id, 'No') AS id_registro,
            menu_productos.imagen
            FROM
                menu_productos
            LEFT JOIN propietarios_productos ON propietarios_productos.id_producto = menu_productos.id
                AND propietarios_productos.id_propietario = :id_propietario
                AND propietarios_productos.id_cafeteria = 0
                $filtro_join
            WHERE menu_productos.estado = 0
            AND menu_productos.id_subcategoria = :subcategoria
            $condicion
            ");
        } else {
            // Cafetería específica: mostrar solo los productos activados de esa cafetería
            $condicion = '';
            if ($occu) {
                $condicion = ' AND menu_productos.registro_occu = 1';
            }
            
            $stmt = Conexion::conectar()->prepare("SELECT
            menu_productos.id,
            menu_productos.nombre,
            COALESCE(propietarios_productos.estado, 'No') AS estado,
            COALESCE(propietarios_productos.id, 'No') AS id_registro,
            menu_productos.imagen
            FROM
                menu_productos
            INNER JOIN propietarios_productos ON propietarios_productos.id_producto = menu_productos.id
                AND propietarios_productos.id_propietario = :id_propietario
                AND propietarios_productos.id_cafeteria = :id_cafeteria
                AND propietarios_productos.estado = 1
            WHERE menu_productos.estado = 0
            AND menu_productos.id_subcategoria = :subcategoria
            $condicion
            ");
        }

        $stmt->bindParam(':subcategoria', $subcategoria,PDO::PARAM_INT);
        $stmt->bindParam(':id_propietario', $id_propietario,PDO::PARAM_INT);
        if ($id_cafeteria > 0) {
            $stmt->bindParam(':id_cafeteria', $id_cafeteria,PDO::PARAM_INT);
        }

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
    
        // Determinar id_cafeteria (0 si es propietario, o el id si es cafetería específica)
        $id_cafeteria = 0;
        if (isset($datos['cafeteria']) && $datos['cafeteria'] !== 'false' && $datos['cafeteria'] !== false) {
            $id_cafeteria = $datos['cafeteria'];
        }
    
        // Verificar si ya existe el producto para ese propietario/cafetería
        $stmtValidar = $conexion->prepare("SELECT COUNT(*) FROM propietarios_productos 
        WHERE id_producto = :id_producto 
        AND id_propietario = :id_propietario 
        AND id_cafeteria = :id_cafeteria");
        $stmtValidar->bindParam(':id_producto', $datos['id_producto'], PDO::PARAM_INT);
        $stmtValidar->bindParam(':id_propietario', $datos['id_propietario'], PDO::PARAM_INT);
        $stmtValidar->bindParam(':id_cafeteria', $id_cafeteria, PDO::PARAM_INT);
        $stmtValidar->execute();
    
        if ($stmtValidar->fetchColumn() > 0) {
            // Ya existe, no se inserta
            return 'existe';
        }
    
        // Insertar si no existe
        $stmtInsertar = $conexion->prepare("INSERT INTO propietarios_productos 
        (id_producto, id_propietario, id_cafeteria, precio_base, estado) 
        VALUES (:id_producto, :id_propietario, :id_cafeteria, '0', 1)");
        $stmtInsertar->bindParam(':id_producto', $datos['id_producto'], PDO::PARAM_INT);
        $stmtInsertar->bindParam(':id_propietario', $datos['id_propietario'], PDO::PARAM_INT);
        $stmtInsertar->bindParam(':id_cafeteria', $id_cafeteria, PDO::PARAM_INT);
    
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
        // Usar la nueva tabla propietarios_productos
        $stmt = Conexion::conectar()->prepare("UPDATE propietarios_productos SET estado = :estado WHERE id = :id");
    
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
        // Determinar id_cafeteria
        $id_cafeteria = 0;
        if ($cafeteria !== 'false' && $cafeteria !== false) {
            $id_cafeteria = $cafeteria;
        }
        
        // Obtener el id_propietario_producto
        $stmt_producto = Conexion::conectar()->prepare("SELECT id FROM propietarios_productos 
        WHERE id_producto = :id_producto 
        AND id_propietario = :id_propietario
        AND id_cafeteria = :id_cafeteria");
    
        $stmt_producto->bindParam(":id_producto", $datos['id_producto'], PDO::PARAM_INT);
        $stmt_producto->bindParam(":id_propietario", $datos['id_propietario'], PDO::PARAM_INT);
        $stmt_producto->bindParam(":id_cafeteria", $id_cafeteria, PDO::PARAM_INT);
        $stmt_producto->execute();
        $producto = $stmt_producto->fetch();
        
        if (!$producto) {
            return 'error';
        }
        
        // Si id_tamano es 0, desactivar el producto, si no, desactivar el tamaño específico
        if ($datos['id_tamano'] == 0) {
            $stmt = Conexion::conectar()->prepare("UPDATE propietarios_productos SET estado = 0 
            WHERE id = :id");
            $stmt->bindParam(":id", $producto['id'], PDO::PARAM_INT);
        } else {
            $stmt = Conexion::conectar()->prepare("UPDATE propietarios_productos_tamanos SET estado = 0 
            WHERE id_propietario_producto = :id_propietario_producto 
            AND id_tamano = :id_tamano");
            $stmt->bindParam(":id_propietario_producto", $producto['id'], PDO::PARAM_INT);
            $stmt->bindParam(":id_tamano", $datos['id_tamano'], PDO::PARAM_INT);
        }
    
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
        // Determinar id_cafeteria
        $id_cafeteria = 0;
        if ($cafeteria !== 'false' && $cafeteria !== false) {
            $id_cafeteria = $cafeteria;
        }

        $conexion = Conexion::conectar();
        
        // Primero obtener o crear el registro en propietarios_productos
        $stmt_producto = $conexion->prepare("SELECT id FROM propietarios_productos 
            WHERE id_producto = :id_producto 
            AND id_propietario = :id_propietario
            AND id_cafeteria = :id_cafeteria");
    
        $stmt_producto->bindParam(":id_producto", $datos['id_producto'], PDO::PARAM_INT);
        $stmt_producto->bindParam(":id_propietario", $datos['id_propietario'], PDO::PARAM_INT);
        $stmt_producto->bindParam(":id_cafeteria", $id_cafeteria, PDO::PARAM_INT);
        $stmt_producto->execute();
        $producto = $stmt_producto->fetch();
        
        $id_propietario_producto = null;
        if ($producto) {
            $id_propietario_producto = $producto['id'];
            // Asegurar que el producto esté activo
            $stmt_act = $conexion->prepare("UPDATE propietarios_productos SET estado = 1 WHERE id = :id");
            $stmt_act->bindParam(":id", $id_propietario_producto, PDO::PARAM_INT);
            $stmt_act->execute();
        } else {
            // Crear el registro del producto si no existe
            $stmt_insert = $conexion->prepare("INSERT INTO propietarios_productos 
                (id_producto, id_propietario, id_cafeteria, precio_base, estado) 
                VALUES (:id_producto, :id_propietario, :id_cafeteria, '0', 1)");
            $stmt_insert->bindParam(":id_producto", $datos['id_producto'], PDO::PARAM_INT);
            $stmt_insert->bindParam(":id_propietario", $datos['id_propietario'], PDO::PARAM_INT);
            $stmt_insert->bindParam(":id_cafeteria", $id_cafeteria, PDO::PARAM_INT);
            $stmt_insert->execute();
            $id_propietario_producto = $conexion->lastInsertId();
        }
        
        // Ahora actualizar o insertar el tamaño en propietarios_productos_tamanos
        $stmt = $conexion->prepare("SELECT id FROM propietarios_productos_tamanos 
            WHERE id_propietario_producto = :id_propietario_producto 
            AND id_tamano = :id_tamano");
    
        $stmt->bindParam(":id_propietario_producto", $id_propietario_producto, PDO::PARAM_INT);
        $stmt->bindParam(":id_tamano", $datos['id_tamano'], PDO::PARAM_INT);
        $stmt->execute();
        $tamano_existe = $stmt->fetch();
        
        if ($tamano_existe) {
            // Actualizar precio y estado
            $stmt = $conexion->prepare("UPDATE propietarios_productos_tamanos 
                SET precio = :precio, estado = 1
                WHERE id = :id");
            $stmt->bindParam(":id", $tamano_existe['id'], PDO::PARAM_INT);
            $stmt->bindParam(":precio", $datos['precio'], PDO::PARAM_STR);
        } else {
            // Insertar nuevo tamaño
            $stmt = $conexion->prepare("INSERT INTO propietarios_productos_tamanos 
                (id_propietario_producto, id_tamano, precio, estado) 
                VALUES (:id_propietario_producto, :id_tamano, :precio, 1)");
            $stmt->bindParam(":id_propietario_producto", $id_propietario_producto, PDO::PARAM_INT);
            $stmt->bindParam(":id_tamano", $datos['id_tamano'], PDO::PARAM_INT);
            $stmt->bindParam(":precio", $datos['precio'], PDO::PARAM_STR);
        }
    
        if ($stmt->execute()) {
            return 'success';
        } else {
            return 'error_insert';
        }
    }
    
    
    /* ACTUALIZAR/AGREGAR PRODUCTOS */
    
    
    /* BUSCAR PRODUCTO */
    
    static public function buscarProductoModel($datos, $cafeteria=false){
        // Determinar id_cafeteria
        $id_cafeteria = 0;
        if ($cafeteria !== 'false' && $cafeteria !== false) {
            $id_cafeteria = $cafeteria;
        }
    
        // Primero obtener el id_propietario_producto
        $stmt_producto = Conexion::conectar()->prepare("SELECT id FROM propietarios_productos              
        WHERE id_producto = :id_producto 
        AND id_propietario = :id_propietario
        AND id_cafeteria = :id_cafeteria");
    
        $stmt_producto->bindParam(":id_producto", $datos['id_producto'], PDO::PARAM_INT);
        $stmt_producto->bindParam(":id_propietario", $datos['id_propietario'], PDO::PARAM_INT);
        $stmt_producto->bindParam(":id_cafeteria", $id_cafeteria, PDO::PARAM_INT);
        $stmt_producto->execute();
        $producto = $stmt_producto->fetch();
    
        if (!$producto) {
            return array();
        }
    
        // Obtener los tamaños desde la nueva tabla
        $stmt = Conexion::conectar()->prepare("SELECT 
        ppt.id,
        ppt.id_tamano,
        ppt.precio,
        ppt.estado,
        mpt.nombre AS nombre_tamano,
        mpt.unidad_medida,
        mpt.medida
        FROM propietarios_productos_tamanos ppt
        INNER JOIN menu_productos_tamanos mpt ON ppt.id_tamano = mpt.id
        WHERE ppt.id_propietario_producto = :id_propietario_producto
        AND ppt.estado = 1");
    
        $stmt->bindParam(":id_propietario_producto", $producto['id'], PDO::PARAM_INT);
        $stmt->execute();
    
        return $stmt->fetchAll();
    
        $stmt = null;
    
    }
    
    /* BUSCAR PRODUCTO */


    /* OBTENER PRECIO DE ALIMENTO */
    
    static public function obtenerPrecioAlimentoModel($datos, $cafeteria=false){
        // Determinar id_cafeteria
        $id_cafeteria = 0;
        if ($cafeteria !== 'false' && $cafeteria !== false) {
            $id_cafeteria = $cafeteria;
        }
    
        $stmt = Conexion::conectar()->prepare("SELECT precio_base FROM propietarios_productos              
        WHERE id_producto = :id_producto 
        AND id_propietario = :id_propietario
        AND id_cafeteria = :id_cafeteria");
    
        $stmt->bindParam(":id_producto", $datos['id_producto'], PDO::PARAM_INT);
        $stmt->bindParam(":id_propietario", $datos['id_propietario'], PDO::PARAM_INT);
        $stmt->bindParam(":id_cafeteria", $id_cafeteria, PDO::PARAM_INT);
    
        $stmt -> execute();
    
        $resultado = $stmt -> fetch();
    
        $stmt = null;
        
        return $resultado ? $resultado['precio_base'] : '';
    
    }
    
    /* OBTENER PRECIO DE ALIMENTO */


    /* GUARDAR PRECIO DE ALIMENTO */
    
    static public function guardarPrecioAlimentoModel($datos, $cafeteria=false){
        // Determinar id_cafeteria
        $id_cafeteria = 0;
        if ($cafeteria !== 'false' && $cafeteria !== false) {
            $id_cafeteria = $cafeteria;
        }
        
        $conexion = Conexion::conectar();
        
        // Buscar el registro en propietarios_productos
        $stmt = $conexion->prepare("SELECT id FROM propietarios_productos              
        WHERE id_producto = :id_producto 
        AND id_propietario = :id_propietario
        AND id_cafeteria = :id_cafeteria");
        
        $stmt->bindParam(":id_producto", $datos['id_producto'], PDO::PARAM_INT);
        $stmt->bindParam(":id_propietario", $datos['id_propietario'], PDO::PARAM_INT);
        $stmt->bindParam(":id_cafeteria", $id_cafeteria, PDO::PARAM_INT);
        $stmt->execute();
        $existe = $stmt->fetch();
        
        if($existe){
            // Actualizar precio_base
            $stmt = $conexion->prepare("UPDATE propietarios_productos SET precio_base = :precio WHERE id = :id");
            $stmt->bindParam(":id", $existe['id'], PDO::PARAM_INT);
            $stmt->bindParam(":precio", $datos['precio'], PDO::PARAM_STR);
        } else {
            // Insertar nuevo registro (esto no debería pasar normalmente, pero por si acaso)
            $stmt = $conexion->prepare("INSERT INTO propietarios_productos (id_producto, id_propietario, id_cafeteria, precio_base, estado) 
            VALUES (:id_producto, :id_propietario, :id_cafeteria, :precio, 1)");
            $stmt->bindParam(":id_producto", $datos['id_producto'], PDO::PARAM_INT);
            $stmt->bindParam(":id_propietario", $datos['id_propietario'], PDO::PARAM_INT);
            $stmt->bindParam(":id_cafeteria", $id_cafeteria, PDO::PARAM_INT);
            $stmt->bindParam(":precio", $datos['precio'], PDO::PARAM_STR);
        }
        
        if($stmt->execute()){
            return 'success';
        } else {
            return 'error';
        }
        
        $stmt = null;
    
    }
    
    /* GUARDAR PRECIO DE ALIMENTO */


    /* VERIFICAR SI PRODUCTO TIENE INGREDIENTES ACTIVOS */
    
    static public function tieneIngredientesActivosModel($id_producto, $id_propietario, $cafeteria=false){
        // Determinar id_cafeteria
        $id_cafeteria = 0;
        if ($cafeteria !== 'false' && $cafeteria !== false) {
            $id_cafeteria = $cafeteria;
        }
        
        // Primero obtener el id_propietario_producto
        $stmt_producto = Conexion::conectar()->prepare("SELECT id FROM propietarios_productos 
        WHERE id_producto = :id_producto 
        AND id_propietario = :id_propietario
        AND id_cafeteria = :id_cafeteria");
        
        $stmt_producto->bindParam(":id_producto", $id_producto, PDO::PARAM_INT);
        $stmt_producto->bindParam(":id_propietario", $id_propietario, PDO::PARAM_INT);
        $stmt_producto->bindParam(":id_cafeteria", $id_cafeteria, PDO::PARAM_INT);
        $stmt_producto->execute();
        $producto = $stmt_producto->fetch();
        
        if (!$producto) {
            return false;
        }
        
        // Verificar si tiene ingredientes activos
        $stmt = Conexion::conectar()->prepare("SELECT COUNT(*) as total FROM propietarios_ingredientes 
        WHERE id_propietario_producto = :id_propietario_producto
        AND estado = 1");
        
        $stmt->bindParam(":id_propietario_producto", $producto['id'], PDO::PARAM_INT);
        $stmt->execute();
        
        $resultado = $stmt->fetch();
        
        $stmt = null;
        
        return ($resultado['total'] > 0);
    }
    
    /* VERIFICAR SI PRODUCTO TIENE INGREDIENTES ACTIVOS */


    /* VERIFICAR SI PRODUCTO TIENE TAMAÑOS ACTIVOS */
    
    static public function tieneTamanosActivosModel($id_producto, $id_propietario, $cafeteria=false){
        // Determinar id_cafeteria
        $id_cafeteria = 0;
        if ($cafeteria !== 'false' && $cafeteria !== false) {
            $id_cafeteria = $cafeteria;
        }
        
        // Primero obtener el id_propietario_producto
        $stmt_producto = Conexion::conectar()->prepare("SELECT id FROM propietarios_productos 
        WHERE id_producto = :id_producto 
        AND id_propietario = :id_propietario
        AND id_cafeteria = :id_cafeteria");
        
        $stmt_producto->bindParam(":id_producto", $id_producto, PDO::PARAM_INT);
        $stmt_producto->bindParam(":id_propietario", $id_propietario, PDO::PARAM_INT);
        $stmt_producto->bindParam(":id_cafeteria", $id_cafeteria, PDO::PARAM_INT);
        $stmt_producto->execute();
        $producto = $stmt_producto->fetch();
        
        if (!$producto) {
            return false;
        }
        
        // Verificar si tiene tamaños activos
        $stmt = Conexion::conectar()->prepare("SELECT COUNT(*) as total FROM propietarios_productos_tamanos 
        WHERE id_propietario_producto = :id_propietario_producto
        AND estado = 1");
        
        $stmt->bindParam(":id_propietario_producto", $producto['id'], PDO::PARAM_INT);
        $stmt->execute();
        
        $resultado = $stmt->fetch();
        
        $stmt = null;
        
        return ($resultado['total'] > 0);
    }
    
    /* VERIFICAR SI PRODUCTO TIENE TAMAÑOS ACTIVOS */


    /* OBTENER NOMBRE DE PRODUCTO */
    
    static public function obtenerNombreProductoModel($id_producto){
        $stmt = Conexion::conectar()->prepare("SELECT nombre FROM menu_productos WHERE id = :id_producto");
        $stmt->bindParam(":id_producto", $id_producto, PDO::PARAM_INT);
        $stmt->execute();
        $resultado = $stmt->fetch();
        $stmt = null;
        return $resultado ? $resultado['nombre'] : '';
    }
    
    /* OBTENER NOMBRE DE PRODUCTO */


    /* BUSCAR PRODUCTO SIMILAR CON INGREDIENTES Y TAMAÑOS */
    
    static public function buscarProductoSimilarModel($id_producto, $id_propietario, $cafeteria=false){
        // Obtener categoría y subcategoría del producto
        $stmt = Conexion::conectar()->prepare("SELECT 
            menu_productos.id_subcategoria,
            menu_subcategorias.id_categoria,
            menu_categorias.es_bebida
        FROM menu_productos
        INNER JOIN menu_subcategorias ON menu_productos.id_subcategoria = menu_subcategorias.id
        INNER JOIN menu_categorias ON menu_subcategorias.id_categoria = menu_categorias.id
        WHERE menu_productos.id = :id_producto");
        
        $stmt->bindParam(":id_producto", $id_producto, PDO::PARAM_INT);
        $stmt->execute();
        $producto_info = $stmt->fetch();
        
        if(!$producto_info){
            return null;
        }
        
        // Determinar id_cafeteria
        $id_cafeteria = 0;
        if ($cafeteria !== 'false' && $cafeteria !== false) {
            $id_cafeteria = $cafeteria;
        }
        
        // Buscar otro producto de la misma categoría y subcategoría que tenga ingredientes o tamaños activos
        $stmt = Conexion::conectar()->prepare("SELECT DISTINCT mp.id
        FROM menu_productos mp
        INNER JOIN menu_subcategorias ms ON mp.id_subcategoria = ms.id
        INNER JOIN propietarios_productos pp ON pp.id_producto = mp.id
            AND pp.id_propietario = :id_propietario
            AND pp.id_cafeteria = :id_cafeteria
            AND pp.estado = 1
        WHERE mp.id != :id_producto
        AND mp.id_subcategoria = :id_subcategoria
        AND ms.id_categoria = :id_categoria
        AND (
            EXISTS (
                SELECT 1 FROM propietarios_productos_tamanos ppt
                WHERE ppt.id_propietario_producto = pp.id
                AND ppt.estado = 1
            )
            OR EXISTS (
                SELECT 1 FROM propietarios_ingredientes pi
                WHERE pi.id_propietario_producto = pp.id
                AND pi.estado = 1
            )
        )
        LIMIT 1");
        
        $stmt->bindParam(":id_producto", $id_producto, PDO::PARAM_INT);
        $stmt->bindParam(":id_subcategoria", $producto_info['id_subcategoria'], PDO::PARAM_INT);
        $stmt->bindParam(":id_categoria", $producto_info['id_categoria'], PDO::PARAM_INT);
        $stmt->bindParam(":id_propietario", $id_propietario, PDO::PARAM_INT);
        $stmt->bindParam(":id_cafeteria", $id_cafeteria, PDO::PARAM_INT);
        $stmt->execute();
        
        $producto_similar = $stmt->fetch();
        
        $stmt = null;
        
        if($producto_similar){
            return array(
                'id_producto_origen' => $producto_similar['id'],
                'es_bebida' => $producto_info['es_bebida']
            );
        }
        
        return null;
    }
    
    /* BUSCAR PRODUCTO SIMILAR CON INGREDIENTES Y TAMAÑOS */


    /* COPIAR INGREDIENTES DE UN PRODUCTO A OTRO */
    
    static public function copiarIngredientesModel($id_producto_origen, $id_producto_destino, $id_propietario, $cafeteria=false){
        $conexion = Conexion::conectar();
        
        // Determinar id_cafeteria
        $id_cafeteria = 0;
        if ($cafeteria !== 'false' && $cafeteria !== false) {
            $id_cafeteria = $cafeteria;
        }
        
        // Obtener id_propietario_producto del producto origen
        $stmt_origen = $conexion->prepare("SELECT id FROM propietarios_productos
        WHERE id_producto = :id_producto_origen
        AND id_propietario = :id_propietario
        AND id_cafeteria = :id_cafeteria");
        
        $stmt_origen->bindParam(":id_producto_origen", $id_producto_origen, PDO::PARAM_INT);
        $stmt_origen->bindParam(":id_propietario", $id_propietario, PDO::PARAM_INT);
        $stmt_origen->bindParam(":id_cafeteria", $id_cafeteria, PDO::PARAM_INT);
        $stmt_origen->execute();
        $producto_origen = $stmt_origen->fetch();
        
        if (!$producto_origen) {
            return 'no_producto_origen';
        }
        
        // Obtener id_propietario_producto del producto destino
        $stmt_destino = $conexion->prepare("SELECT id FROM propietarios_productos
        WHERE id_producto = :id_producto_destino
        AND id_propietario = :id_propietario
        AND id_cafeteria = :id_cafeteria");
        
        $stmt_destino->bindParam(":id_producto_destino", $id_producto_destino, PDO::PARAM_INT);
        $stmt_destino->bindParam(":id_propietario", $id_propietario, PDO::PARAM_INT);
        $stmt_destino->bindParam(":id_cafeteria", $id_cafeteria, PDO::PARAM_INT);
        $stmt_destino->execute();
        $producto_destino = $stmt_destino->fetch();
        
        if (!$producto_destino) {
            return 'no_producto_destino';
        }
        
        // Obtener ingredientes activos del producto origen
        $stmt = $conexion->prepare("SELECT 
            id_ingrediente,
            estado,
            cantidad_gratis,
            precio,
            costo_extra
        FROM propietarios_ingredientes
        WHERE id_propietario_producto = :id_propietario_producto_origen
        AND estado = 1");
        
        $stmt->bindParam(":id_propietario_producto_origen", $producto_origen['id'], PDO::PARAM_INT);
        $stmt->execute();
        $ingredientes = $stmt->fetchAll();
        
        if(empty($ingredientes)){
            return 'no_ingredientes';
        }
        
        try {
            $conexion->beginTransaction();
            
            // Insertar ingredientes en el producto destino
            foreach($ingredientes as $ingrediente){
                // Verificar si ya existe
                $stmt_check = $conexion->prepare("SELECT id FROM propietarios_ingredientes
                WHERE id_propietario_producto = :id_propietario_producto_destino
                AND id_ingrediente = :id_ingrediente");
                
                $stmt_check->bindParam(":id_propietario_producto_destino", $producto_destino['id'], PDO::PARAM_INT);
                $stmt_check->bindParam(":id_ingrediente", $ingrediente['id_ingrediente'], PDO::PARAM_INT);
                $stmt_check->execute();
                $existe = $stmt_check->fetch();
                
                if($existe){
                    // Actualizar
                    $stmt_update = $conexion->prepare("UPDATE propietarios_ingredientes 
                    SET estado = :estado,
                        cantidad_gratis = :cantidad_gratis,
                        precio = :precio,
                        costo_extra = :costo_extra
                    WHERE id = :id_registro");
                    
                    $stmt_update->bindParam(":id_registro", $existe['id'], PDO::PARAM_INT);
                    $stmt_update->bindParam(":estado", $ingrediente['estado'], PDO::PARAM_INT);
                    $stmt_update->bindParam(":cantidad_gratis", $ingrediente['cantidad_gratis'], PDO::PARAM_INT);
                    $stmt_update->bindParam(":precio", $ingrediente['precio'], PDO::PARAM_STR);
                    $stmt_update->bindParam(":costo_extra", $ingrediente['costo_extra'], PDO::PARAM_STR);
                    $stmt_update->execute();
                } else {
                    // Insertar
                    $stmt_insert = $conexion->prepare("INSERT INTO propietarios_ingredientes 
                        (id_propietario_producto, id_ingrediente, estado, cantidad_gratis, precio, costo_extra)
                    VALUES 
                        (:id_propietario_producto_destino, :id_ingrediente, :estado, :cantidad_gratis, :precio, :costo_extra)");
                    
                    $stmt_insert->bindParam(":id_propietario_producto_destino", $producto_destino['id'], PDO::PARAM_INT);
                    $stmt_insert->bindParam(":id_ingrediente", $ingrediente['id_ingrediente'], PDO::PARAM_INT);
                    $stmt_insert->bindParam(":estado", $ingrediente['estado'], PDO::PARAM_INT);
                    $stmt_insert->bindParam(":cantidad_gratis", $ingrediente['cantidad_gratis'], PDO::PARAM_INT);
                    $stmt_insert->bindParam(":precio", $ingrediente['precio'], PDO::PARAM_STR);
                    $stmt_insert->bindParam(":costo_extra", $ingrediente['costo_extra'], PDO::PARAM_STR);
                    $stmt_insert->execute();
                }
            }
            
            $conexion->commit();
            return 'success';
            
        } catch (Exception $e) {
            $conexion->rollBack();
            return 'error';
        }
        
        $stmt = null;
    }
    
    /* COPIAR INGREDIENTES DE UN PRODUCTO A OTRO */


    /* COPIAR TAMAÑOS DE UN PRODUCTO A OTRO */
    
    static public function copiarTamanosModel($id_producto_origen, $id_producto_destino, $id_propietario, $cafeteria=false){
        $conexion = Conexion::conectar();
        
        // Determinar id_cafeteria
        $id_cafeteria = 0;
        if ($cafeteria !== 'false' && $cafeteria !== false) {
            $id_cafeteria = $cafeteria;
        }
        
        // Obtener id_propietario_producto del producto origen
        $stmt_origen = $conexion->prepare("SELECT id FROM propietarios_productos
        WHERE id_producto = :id_producto_origen
        AND id_propietario = :id_propietario
        AND id_cafeteria = :id_cafeteria");
        
        $stmt_origen->bindParam(":id_producto_origen", $id_producto_origen, PDO::PARAM_INT);
        $stmt_origen->bindParam(":id_propietario", $id_propietario, PDO::PARAM_INT);
        $stmt_origen->bindParam(":id_cafeteria", $id_cafeteria, PDO::PARAM_INT);
        $stmt_origen->execute();
        $producto_origen = $stmt_origen->fetch();
        
        if (!$producto_origen) {
            return 'no_producto_origen';
        }
        
        // Obtener id_propietario_producto del producto destino
        $stmt_destino = $conexion->prepare("SELECT id FROM propietarios_productos
        WHERE id_producto = :id_producto_destino
        AND id_propietario = :id_propietario
        AND id_cafeteria = :id_cafeteria");
        
        $stmt_destino->bindParam(":id_producto_destino", $id_producto_destino, PDO::PARAM_INT);
        $stmt_destino->bindParam(":id_propietario", $id_propietario, PDO::PARAM_INT);
        $stmt_destino->bindParam(":id_cafeteria", $id_cafeteria, PDO::PARAM_INT);
        $stmt_destino->execute();
        $producto_destino = $stmt_destino->fetch();
        
        if (!$producto_destino) {
            return 'no_producto_destino';
        }
        
        // Obtener tamaños activos del producto origen
        $stmt = $conexion->prepare("SELECT 
            id_tamano,
            precio,
            estado
        FROM propietarios_productos_tamanos
        WHERE id_propietario_producto = :id_propietario_producto_origen
        AND estado = 1");
        
        $stmt->bindParam(":id_propietario_producto_origen", $producto_origen['id'], PDO::PARAM_INT);
        $stmt->execute();
        $tamanos = $stmt->fetchAll();
        
        if(empty($tamanos)){
            return 'no_tamanos';
        }
        
        try {
            $conexion->beginTransaction();
            
            // Insertar tamaños en el producto destino
            foreach($tamanos as $tamano){
                // Verificar si ya existe
                $stmt_check = $conexion->prepare("SELECT id FROM propietarios_productos_tamanos
                WHERE id_propietario_producto = :id_propietario_producto_destino
                AND id_tamano = :id_tamano");
                
                $stmt_check->bindParam(":id_propietario_producto_destino", $producto_destino['id'], PDO::PARAM_INT);
                $stmt_check->bindParam(":id_tamano", $tamano['id_tamano'], PDO::PARAM_INT);
                $stmt_check->execute();
                $existe = $stmt_check->fetch();
                
                if($existe){
                    // Actualizar
                    $stmt_update = $conexion->prepare("UPDATE propietarios_productos_tamanos 
                    SET precio = :precio,
                        estado = :estado
                    WHERE id = :id_registro");
                    
                    $stmt_update->bindParam(":id_registro", $existe['id'], PDO::PARAM_INT);
                    $stmt_update->bindParam(":precio", $tamano['precio'], PDO::PARAM_STR);
                    $stmt_update->bindParam(":estado", $tamano['estado'], PDO::PARAM_INT);
                    $stmt_update->execute();
                } else {
                    // Insertar
                    $stmt_insert = $conexion->prepare("INSERT INTO propietarios_productos_tamanos 
                        (id_propietario_producto, id_tamano, precio, estado)
                    VALUES 
                        (:id_propietario_producto_destino, :id_tamano, :precio, :estado)");
                    
                    $stmt_insert->bindParam(":id_propietario_producto_destino", $producto_destino['id'], PDO::PARAM_INT);
                    $stmt_insert->bindParam(":id_tamano", $tamano['id_tamano'], PDO::PARAM_INT);
                    $stmt_insert->bindParam(":precio", $tamano['precio'], PDO::PARAM_STR);
                    $stmt_insert->bindParam(":estado", $tamano['estado'], PDO::PARAM_INT);
                    $stmt_insert->execute();
                }
            }
            
            $conexion->commit();
            return 'success';
            
        } catch (Exception $e) {
            $conexion->rollBack();
            return 'error';
        }
        
        $stmt = null;
    }
    
    /* COPIAR TAMAÑOS DE UN PRODUCTO A OTRO */
    
    
    /* ELIMINAR REGISTROS DE MENU SUCURSALES */
    
    static public function eliminarMenuSucursalModel($id_cafeteria){
        $conexion = Conexion::conectar();
        
        // Eliminar tamaños de productos de la cafetería
        $stmt_tamanos = $conexion->prepare("DELETE ppt FROM propietarios_productos_tamanos ppt
        INNER JOIN propietarios_productos pp ON ppt.id_propietario_producto = pp.id
        WHERE pp.id_cafeteria = :id_cafeteria");
        $stmt_tamanos->bindParam(":id_cafeteria", $id_cafeteria, PDO::PARAM_INT);
        $stmt_tamanos->execute();
        
        // Eliminar ingredientes de productos de la cafetería
        $stmt_ingredientes = $conexion->prepare("DELETE pi FROM propietarios_ingredientes pi
        INNER JOIN propietarios_productos pp ON pi.id_propietario_producto = pp.id
        WHERE pp.id_cafeteria = :id_cafeteria");
        $stmt_ingredientes->bindParam(":id_cafeteria", $id_cafeteria, PDO::PARAM_INT);
        $stmt_ingredientes->execute();
        
        // Eliminar productos de la cafetería
        $stmt = $conexion->prepare("DELETE FROM propietarios_productos 
        WHERE id_cafeteria = :id_cafeteria");
    
        $stmt->bindParam(":id_cafeteria", $id_cafeteria, PDO::PARAM_INT);
    
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
        // Obtener productos del propietario (id_cafeteria = 0) y sus tamaños
        // Necesitamos devolver datos compatibles con la estructura antigua para actualizarMenuSucursalModel
        $stmt = Conexion::conectar()->prepare("SELECT 
            pp.id_producto,
            pp.id_propietario,
            pp.precio_base AS precio,
            0 AS id_tamano,
            pp.estado
        FROM propietarios_productos pp
        WHERE pp.id_propietario = :propietario 
        AND pp.id_cafeteria = 0
        AND pp.estado = 1
        
        UNION ALL
        
        SELECT 
            pp.id_producto,
            pp.id_propietario,
            ppt.precio,
            ppt.id_tamano,
            ppt.estado
        FROM propietarios_productos pp
        INNER JOIN propietarios_productos_tamanos ppt ON ppt.id_propietario_producto = pp.id
        WHERE pp.id_propietario = :propietario 
        AND pp.id_cafeteria = 0
        AND ppt.estado = 1");
    
        $stmt->bindParam(':propietario', $id_propietario,PDO::PARAM_INT);
    
        $stmt -> execute();
    
        return $stmt -> fetchAll();
    
        $stmt = null;
    
    }
    
    /* BUSCAR MENU PROPIETARIO */

    
    /* INSERTAR ACTUALIZACION DE MENU POR SUCURSAL */
    
    static public function actualizarMenuSucursalModel($datos, $cafeteria){
        $conexion = Conexion::conectar();
        
        // Primero obtener o crear el registro en propietarios_productos para la cafetería
        $stmt_producto = $conexion->prepare("SELECT id FROM propietarios_productos 
            WHERE id_producto = :id_producto 
            AND id_propietario = :id_propietario
            AND id_cafeteria = :id_cafeteria");
    
        $stmt_producto->bindParam(":id_producto", $datos['id_producto'], PDO::PARAM_INT);
        $stmt_producto->bindParam(":id_propietario", $datos['id_propietario'], PDO::PARAM_INT);
        $stmt_producto->bindParam(":id_cafeteria", $cafeteria, PDO::PARAM_INT);
        $stmt_producto->execute();
        $producto = $stmt_producto->fetch();
        
        $id_propietario_producto = null;
        if ($producto) {
            $id_propietario_producto = $producto['id'];
            // Asegurar que el producto esté activo
            $stmt_act = $conexion->prepare("UPDATE propietarios_productos SET estado = 1 WHERE id = :id");
            $stmt_act->bindParam(":id", $id_propietario_producto, PDO::PARAM_INT);
            $stmt_act->execute();
        } else {
            // Crear el registro del producto si no existe
            $stmt_insert = $conexion->prepare("INSERT INTO propietarios_productos 
                (id_producto, id_propietario, id_cafeteria, precio_base, estado) 
                VALUES (:id_producto, :id_propietario, :id_cafeteria, '0', 1)");
            $stmt_insert->bindParam(":id_producto", $datos['id_producto'], PDO::PARAM_INT);
            $stmt_insert->bindParam(":id_propietario", $datos['id_propietario'], PDO::PARAM_INT);
            $stmt_insert->bindParam(":id_cafeteria", $cafeteria, PDO::PARAM_INT);
            $stmt_insert->execute();
            $id_propietario_producto = $conexion->lastInsertId();
        }
        
        // Si id_tamano es 0, actualizar precio_base, si no, insertar/actualizar en propietarios_productos_tamanos
        if ($datos['id_tamano'] == 0) {
            $stmt = $conexion->prepare("UPDATE propietarios_productos SET precio_base = :precio WHERE id = :id");
            $stmt->bindParam(":id", $id_propietario_producto, PDO::PARAM_INT);
            $stmt->bindParam(":precio", $datos['precio'], PDO::PARAM_STR);
        } else {
            // Verificar si ya existe el tamaño
            $stmt_check = $conexion->prepare("SELECT id FROM propietarios_productos_tamanos 
                WHERE id_propietario_producto = :id_propietario_producto 
                AND id_tamano = :id_tamano");
            $stmt_check->bindParam(":id_propietario_producto", $id_propietario_producto, PDO::PARAM_INT);
            $stmt_check->bindParam(":id_tamano", $datos['id_tamano'], PDO::PARAM_INT);
            $stmt_check->execute();
            $tamano_existe = $stmt_check->fetch();
            
            if ($tamano_existe) {
                $stmt = $conexion->prepare("UPDATE propietarios_productos_tamanos 
                    SET precio = :precio, estado = :estado
                    WHERE id = :id");
                $stmt->bindParam(":id", $tamano_existe['id'], PDO::PARAM_INT);
                $stmt->bindParam(":precio", $datos['precio'], PDO::PARAM_STR);
                $stmt->bindParam(":estado", $datos['estado'], PDO::PARAM_INT);
            } else {
                $stmt = $conexion->prepare("INSERT INTO propietarios_productos_tamanos 
                    (id_propietario_producto, id_tamano, precio, estado) 
                    VALUES (:id_propietario_producto, :id_tamano, :precio, :estado)");
                $stmt->bindParam(":id_propietario_producto", $id_propietario_producto, PDO::PARAM_INT);
                $stmt->bindParam(":id_tamano", $datos['id_tamano'], PDO::PARAM_INT);
                $stmt->bindParam(":precio", $datos['precio'], PDO::PARAM_STR);
                $stmt->bindParam(":estado", $datos['estado'], PDO::PARAM_INT);
            }
        }
    
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
        $conexion = Conexion::conectar();
        
        // Obtener el id_propietario_producto
        $stmt_producto = $conexion->prepare("SELECT id FROM propietarios_productos 
            WHERE id_producto = :id_producto
            AND id_propietario = :id_propietario
            AND id_cafeteria = :id_cafeteria");
        
        $stmt_producto->bindParam(':id_producto', $datos['id_producto'], PDO::PARAM_INT);
        $stmt_producto->bindParam(':id_propietario', $datos['id_propietario'], PDO::PARAM_INT);
        $stmt_producto->bindParam(':id_cafeteria', $cafeteria, PDO::PARAM_INT);
        $stmt_producto->execute();
        $producto = $stmt_producto->fetch();
        
        if (!$producto) {
            return 'error';
        }
        
        // Si id_tamano es 0, actualizar precio_base, si no, actualizar en propietarios_productos_tamanos
        if ($datos['id_tamano'] == 0) {
            $stmt = $conexion->prepare("UPDATE propietarios_productos 
                SET precio_base = :precio 
                WHERE id = :id");
            $stmt->bindParam(':id', $producto['id'], PDO::PARAM_INT);
            $stmt->bindParam(':precio', $datos['precio'], PDO::PARAM_STR);
        } else {
            $stmt = $conexion->prepare("UPDATE propietarios_productos_tamanos 
                SET precio = :precio 
                WHERE id_propietario_producto = :id_propietario_producto
                AND id_tamano = :id_tamano");
            $stmt->bindParam(':id_propietario_producto', $producto['id'], PDO::PARAM_INT);
            $stmt->bindParam(':id_tamano', $datos['id_tamano'], PDO::PARAM_INT);
            $stmt->bindParam(':precio', $datos['precio'], PDO::PARAM_STR);
        }
    
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
        $stmt = $conexion->prepare("INSERT INTO menu_subcategorias(nombre, id_categoria, id_propietario, registro_occu, fecha_alta) 
        VALUES (:nombre, :id_categoria, :id_propietario, 2, :fecha)");

        $stmt->bindParam(':nombre', $datos['nombre'], PDO::PARAM_STR);
        $stmt->bindParam(':id_categoria', $datos['id_categoria'], PDO::PARAM_INT);
        $stmt->bindParam(':id_propietario', $datos['id_propietario'], PDO::PARAM_INT);
        $stmt->bindParam(':fecha', $datos['fecha_alta'], PDO::PARAM_STR);
        
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
        $stmt = $conexion->prepare("INSERT INTO menu_productos(nombre, id_subcategoria, imagen, id_alta, registro_occu, fecha_alta) 
        VALUES (:nombre, :id_subcategoria, 'views/assets/img/cafeteria_default.png', :id_propietario, 2, :fecha)");

        $stmt->bindParam(':nombre', $datos['nombre'], PDO::PARAM_STR);
        $stmt->bindParam(':id_subcategoria', $datos['id_subcategoria'], PDO::PARAM_INT);
        $stmt->bindParam(':id_propietario', $datos['id_propietario'], PDO::PARAM_INT);
        $stmt->bindParam(':fecha', $datos['fecha_alta'], PDO::PARAM_STR);

        if ($stmt->execute()) {
            $id = $conexion->lastInsertId();

            $stmtInsertar = $conexion->prepare("INSERT INTO propietarios_productos (id_producto, id_propietario, id_cafeteria, precio_base, estado) VALUES (:id_producto, :id_propietario, 0, '0', 1)");
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


    static public function obtenerCategoriasingredientesModel($id_producto = null){
    
        $filtro_tipo = '';
        
        // Si se proporciona un id_producto, filtrar según el tipo de producto
        if ($id_producto) {
            // Primero obtener si el producto es bebida o alimento
            $stmt_producto = Conexion::conectar()->prepare("SELECT
                menu_categorias.es_bebida
                FROM
                    menu_productos
                INNER JOIN menu_subcategorias ON menu_productos.id_subcategoria = menu_subcategorias.id
                INNER JOIN menu_categorias ON menu_subcategorias.id_categoria = menu_categorias.id
                WHERE menu_productos.id = :id_producto
                ");
            
            $stmt_producto->bindParam(':id_producto', $id_producto, PDO::PARAM_INT);
            $stmt_producto->execute();
            $producto = $stmt_producto->fetch();
            
            if ($producto) {
                $es_bebida = ($producto['es_bebida'] == 'Si');
                
                // Filtrar según el tipo de producto
                // Si es bebida, mostrar solo categorías donde para_bebidas = 'Si'
                // Si es alimento, mostrar solo categorías donde para_alimentos = 'Si'
                if ($es_bebida) {
                    $filtro_tipo = "AND cat.para_bebidas = 'Si'";
                } else {
                    $filtro_tipo = "AND cat.para_alimentos = 'Si'";
                }
            }
        }
    
        $stmt = Conexion::conectar()->prepare("SELECT
        cat.*
        FROM
            menu_ingredientes_categorias cat
        WHERE
        cat.estado = 0
        $filtro_tipo");
    
        $stmt -> execute();
    
        return $stmt -> fetchAll();
    
        $stmt = null;
    
    }


    static public function obtenerIngredientesModel($categoria, $datos, $id_producto){
        // Determinar id_cafeteria
        $id_cafeteria = 0;
        if ($datos['cafeteria'] !== 'false' && $datos['cafeteria'] !== false) {
            $id_cafeteria = $datos['cafeteria'];
        }
        
        // Primero obtener el id_propietario_producto
        $stmt_producto = Conexion::conectar()->prepare("SELECT id FROM propietarios_productos
        WHERE id_producto = :id_producto
        AND id_propietario = :id_propietario
        AND id_cafeteria = :id_cafeteria");
        
        $stmt_producto->bindParam(':id_producto', $id_producto, PDO::PARAM_INT);
        $stmt_producto->bindParam(':id_propietario', $datos['id_propietario'], PDO::PARAM_INT);
        $stmt_producto->bindParam(':id_cafeteria', $id_cafeteria, PDO::PARAM_INT);
        $stmt_producto->execute();
        $producto = $stmt_producto->fetch();
        
        $id_propietario_producto = $producto ? $producto['id'] : null;
    
        $stmt = Conexion::conectar()->prepare("SELECT 
        menu_ingredientes.id,
        menu_ingredientes.nombre,
        menu_ingredientes.registro_occu,
        COALESCE(prop_ingre.estado, 'No') AS estado,
        COALESCE(prop_ingre.id, 'No') AS id_registro,
        prop_ingre.cantidad_gratis,
        prop_ingre.precio,
        prop_ingre.costo_extra
        FROM
            menu_ingredientes
        LEFT JOIN propietarios_ingredientes prop_ingre ON prop_ingre.id_ingrediente = menu_ingredientes.id
            AND prop_ingre.id_propietario_producto = :id_propietario_producto
        WHERE
            menu_ingredientes.estado = 0
            AND menu_ingredientes.id_ingrediente_categoria = :categoria
            AND (
                menu_ingredientes.registro_occu != 2
                OR (menu_ingredientes.registro_occu = 2 AND menu_ingredientes.id_alta = :id_propietario)
            )
        ");
    
        $stmt->bindParam(':categoria', $categoria,PDO::PARAM_INT);
        $stmt->bindParam(':id_propietario_producto', $id_propietario_producto,PDO::PARAM_INT);
        $stmt->bindParam(':id_propietario', $datos['id_propietario'],PDO::PARAM_INT);
    
        $stmt -> execute();
    
        return $stmt -> fetchAll();
    
        $stmt = null;
    
    }

    static public function cambiarEstadoIngredienteModel($datos){
        
        // Si se desactiva el ingrediente (estado = 0), también desactivar el costo_extra
        if($datos['estado'] == 0){
            $stmt = Conexion::conectar()->prepare("UPDATE propietarios_ingredientes SET estado = :estado, costo_extra = 'No' WHERE id = :id");
        } else {
            $stmt = Conexion::conectar()->prepare("UPDATE propietarios_ingredientes SET estado = :estado WHERE id = :id");
        }
    
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
        // Determinar id_cafeteria
        $id_cafeteria = 0;
        if ($datos['id_cafeteria'] !== false && $datos['id_cafeteria'] !== 'false') {
            $id_cafeteria = $datos['id_cafeteria'];
        }

        $conexion = Conexion::conectar();
        
        // Primero obtener el id_propietario_producto
        $stmt_producto = $conexion->prepare("SELECT id FROM propietarios_productos
        WHERE id_producto = :id_producto
        AND id_propietario = :id_propietario
        AND id_cafeteria = :id_cafeteria");
        
        $stmt_producto->bindParam(':id_producto', $datos['id_producto'], PDO::PARAM_INT);
        $stmt_producto->bindParam(':id_propietario', $datos['id_propietario'], PDO::PARAM_INT);
        $stmt_producto->bindParam(':id_cafeteria', $id_cafeteria, PDO::PARAM_INT);
        $stmt_producto->execute();
        $producto = $stmt_producto->fetch();
        
        if (!$producto) {
            return 'no_producto';
        }
        
        $id_propietario_producto = $producto['id'];
    
        // Validar si ya existe el ingrediente
        $stmtValidar = $conexion->prepare("SELECT COUNT(*) FROM propietarios_ingredientes 
        WHERE id_propietario_producto = :id_propietario_producto 
        AND id_ingrediente = :id_ingrediente");

        $stmtValidar->bindParam(':id_propietario_producto', $id_propietario_producto, PDO::PARAM_INT);
        $stmtValidar->bindParam(':id_ingrediente', $datos['id_ingrediente'], PDO::PARAM_INT);
        $stmtValidar->execute();
    
        if ($stmtValidar->fetchColumn() > 0) {
            // Ya existe, no se inserta
            return 'existe';
        }
    
        // Si no existe, se inserta
        $stmtInsertar = $conexion->prepare("INSERT INTO propietarios_ingredientes 
        (id_propietario_producto, id_ingrediente, estado, cantidad_gratis, precio, costo_extra) 
        VALUES 
        (:id_propietario_producto, :id_ingrediente, 1, 0, :precio, 'No')");

        $stmtInsertar->bindParam(':id_propietario_producto', $id_propietario_producto, PDO::PARAM_INT);
        $stmtInsertar->bindParam(':id_ingrediente', $datos['id_ingrediente'], PDO::PARAM_INT);
        $stmtInsertar->bindParam(':precio', $datos['precio'], PDO::PARAM_STR);

        if ($stmtInsertar->execute()) {
            return 'success';
        } else {
            return 'error';
        }
    }
    
    static public function actualizarIngredienteModel($datos){
        
        $stmt = Conexion::conectar()->prepare("UPDATE propietarios_ingredientes 
        SET 
            cantidad_gratis = :cantidad,
            precio = :precio
        WHERE id = :id");
    
        $stmt->bindParam(':id', $datos['idRegistro'], PDO::PARAM_INT);
        $stmt->bindParam(":cantidad", $datos['cantidad'], PDO::PARAM_INT);
        $stmt->bindParam(":precio", $datos['precio'], PDO::PARAM_STR);
    
        if($stmt->execute()){
            return 'success';
        }else{
            return 'error';
        }
    
        $stmt = null;
    
    }

    static public function checkCostoExtraIngredienteModel($datos){
        
        $stmt = Conexion::conectar()->prepare("UPDATE propietarios_ingredientes SET costo_extra = :costo_extra WHERE id = :id");
    
        $stmt->bindParam(":id", $datos['idRegistro'], PDO::PARAM_INT);
        $stmt->bindParam(":costo_extra", $datos['estatus'], PDO::PARAM_STR);
    
        if($stmt->execute()){
            return 'success';
        }else{
            return 'error';
        }
    
        $stmt = null;
    
    }

    static public function agregarPropietarioIngredienteModel($datos)
    {

        $conexion = Conexion::conectar();
        $stmt = $conexion->prepare("INSERT INTO menu_ingredientes(nombre, id_ingrediente_categoria, registro_occu, id_alta, fecha_alta) 
        VALUES (:nombre, :id_ingrediente_categoria, 2, :id_alta, :fecha)");

        $stmt->bindParam(':nombre', $datos['nombre'], PDO::PARAM_STR);
        $stmt->bindParam(':id_ingrediente_categoria', $datos['id_ingrediente_categoria'], PDO::PARAM_INT);
        $stmt->bindParam(':fecha', $datos['fecha_alta'], PDO::PARAM_STR);
        $stmt->bindParam(':id_alta', $datos['id_propietario'], PDO::PARAM_INT);

        if ($stmt->execute()) {
            return $conexion->lastInsertId();
        } else {
            return "error";
        }
        $stmt = null;
    }

    
    static public function buscarPropietarioPrecioIngredienteModel($datos){
    
        $stmt = Conexion::conectar()->prepare("SELECT precio FROM propietarios_ingredientes 
        INNER JOIN propietarios_productos ON propietarios_ingredientes.id_propietario_producto = propietarios_productos.id
        WHERE propietarios_ingredientes.id_ingrediente = :id_ingrediente
        AND propietarios_productos.id_propietario = :id_propietario
        AND propietarios_ingredientes.precio > 0
        LIMIT 1");
    
        $stmt->bindParam(':id_ingrediente', $datos['id_ingrediente'],PDO::PARAM_INT);
        $stmt->bindParam(':id_propietario', $datos['id_propietario'],PDO::PARAM_INT);
    
        $stmt -> execute();
    
        $ingre = $stmt -> fetch();
        return $ingre['precio']??0;
    
        $stmt = null;
    
    }

    static public function buscarPropietarioIngredienteModel($id_propietario){
    
        $stmt = Conexion::conectar()->prepare("SELECT 
        propietarios_ingredientes.*,
        propietarios_productos.id_producto,
        propietarios_productos.id_propietario
        FROM propietarios_ingredientes
        INNER JOIN propietarios_productos ON propietarios_ingredientes.id_propietario_producto = propietarios_productos.id
        WHERE propietarios_productos.id_propietario = :id_propietario
        AND propietarios_productos.id_cafeteria = 0");
    
        $stmt->bindParam(':id_propietario', $id_propietario,PDO::PARAM_INT);
    
        $stmt -> execute();
    
        $ingre = $stmt -> fetchAll();
        return $ingre;
    
        $stmt = null;
    
    }

        static public function actualizarIngredientesSucursalModel($datos, $cafeteria){
        $conexion = Conexion::conectar();
        
        // Primero obtener el id_propietario_producto del producto en la cafetería
        $stmt_producto = $conexion->prepare("SELECT id FROM propietarios_productos
        WHERE id_producto = :id_producto
        AND id_propietario = :id_propietario
        AND id_cafeteria = :id_cafeteria");
        
        $stmt_producto->bindParam(':id_producto', $datos['id_producto'], PDO::PARAM_INT);
        $stmt_producto->bindParam(':id_propietario', $datos['id_propietario'], PDO::PARAM_INT);
        $stmt_producto->bindParam(':id_cafeteria', $cafeteria, PDO::PARAM_INT);
        $stmt_producto->execute();
        $producto = $stmt_producto->fetch();
        
        if (!$producto) {
            return 'error';
        }
        
        $id_propietario_producto = $producto['id'];
        
        // Verificar si ya existe el ingrediente
        $stmt_check = $conexion->prepare("SELECT id FROM propietarios_ingredientes
        WHERE id_propietario_producto = :id_propietario_producto
        AND id_ingrediente = :id_ingrediente");
        
        $stmt_check->bindParam(':id_propietario_producto', $id_propietario_producto, PDO::PARAM_INT);
        $stmt_check->bindParam(':id_ingrediente', $datos['id_ingrediente'], PDO::PARAM_INT);
        $stmt_check->execute();
        $existe = $stmt_check->fetch();
        
        if ($existe) {
            // Actualizar
            $stmt = $conexion->prepare("UPDATE propietarios_ingredientes
            SET costo_extra = :costo_extra,
                cantidad_gratis = :cantidad_gratis,
                precio = :precio,
                estado = :estado
            WHERE id = :id");
            $stmt->bindParam(':id', $existe['id'], PDO::PARAM_INT);
        } else {
            // Insertar
            $stmt = $conexion->prepare("INSERT INTO propietarios_ingredientes
            (id_propietario_producto, id_ingrediente, costo_extra, cantidad_gratis, precio, estado) 
            VALUES 
            (:id_propietario_producto, :id_ingrediente, :costo_extra, :cantidad_gratis, :precio, :estado)");
            $stmt->bindParam(':id_propietario_producto', $id_propietario_producto, PDO::PARAM_INT);
        }
    
        $stmt->bindParam(':id_ingrediente', $datos['id_ingrediente'], PDO::PARAM_INT);
        $stmt->bindParam(':costo_extra', $datos['costo_extra'], PDO::PARAM_STR);
        $stmt->bindParam(':cantidad_gratis', $datos['cantidad_gratis'], PDO::PARAM_INT);
        $stmt->bindParam(':precio', $datos['precio'], PDO::PARAM_STR);
        $stmt->bindParam(':estado', $datos['estado'], PDO::PARAM_INT);
    
        if($stmt->execute()){
            return 'success';
        }else{
            return 'error';
        }
        $stmt = null;
    }

        static public function eliminarIngredientesSucursalModel($id_cafeteria){
        // Eliminar ingredientes de productos de la cafetería
        $stmt = Conexion::conectar()->prepare("DELETE pi FROM propietarios_ingredientes pi
        INNER JOIN propietarios_productos pp ON pi.id_propietario_producto = pp.id
        WHERE pp.id_cafeteria = :id_cafeteria");

        $stmt->bindParam(":id_cafeteria", $id_cafeteria, PDO::PARAM_INT);
    
        if($stmt->execute()){
            return 'success';
        }else{
            return 'error';
        }
    
        $stmt = null;
    
    }
    
    
}