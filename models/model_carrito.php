<?php 

require_once "conexion.php";

class CarritoModel extends Conexion {

    
    static public function buscarCarritoModel($datos){
    
        $stmt = Conexion::conectar()->prepare("SELECT vc.*, 
        c.imagen AS imagen_cafeteria 
        FROM ventas_carrito vc
        INNER JOIN cafeterias c ON c.id = vc.id_cafeteria
        WHERE vc.estado = 0
        AND vc.id_usuario = :id_usuario
       -- AND vc.id_cafeteria = :id_cafeteria
        ;");
    
        $stmt->bindParam(':id_usuario', $datos['id_usuario'],PDO::PARAM_INT);
       // $stmt->bindParam(':id_cafeteria', $datos['id_cafeteria'],PDO::PARAM_INT);

        $stmt -> execute();
    
        return $stmt -> fetch();
    
    }

    
    static public function buscarCarritoItemsModel($datos){
    
        $stmt = Conexion::conectar()->prepare("SELECT 
        vci.id AS id_item,
        vci.id_producto,
        mp.nombre AS nombre,
        mp.imagen AS imagen,
        vci.cantidad,
        mpt.nombre AS tamano,
        mpt.medida,
        mpt.unidad_medida,
        pmc.precio
        FROM ventas_carrito_items vci
        INNER JOIN ventas_carrito vc ON vc.id = vci.id_carrito
        INNER JOIN menu_productos mp ON mp.id = vci.id_producto
        LEFT JOIN propietarios_menu_cafeterias pmc ON pmc.id_producto = vci.id_producto
        AND pmc.id_tamano = vci.id_tamano AND pmc.id_cafeteria = vc.id_cafeteria 
        LEFT JOIN menu_productos_tamanos mpt ON mpt.id = vci.id_tamano
        WHERE vci.estado = 0
        AND vci.id_carrito = :id_carrito
        ;");
    
        $stmt->bindParam(':id_carrito', $datos['id_carrito'],PDO::PARAM_INT);

        $stmt -> execute();
    
        return $stmt -> fetchAll();
    
    }
    

    static public function buscarCarritoItemsIngredientesModel($datos){
    
        $stmt = Conexion::conectar()->prepare("SELECT 
        ii.id AS id_item_ingre, 
        ii.id_ingrediente, 
        mi.nombre,
        ii.cantidad,
        CASE 
            WHEN pmi.costo_extra = 'Si' 
                THEN (ii.cantidad * pmi.precio) - (pmi.cantidad_gratis * pmi.precio)
            ELSE 0
        END AS total
        FROM ventas_carrito_items_ingredientes ii
        INNER JOIN menu_ingredientes mi ON mi.id = ii.id_ingrediente
        INNER JOIN ventas_carrito_items vci ON vci.id = ii.id_carrito_item
        INNER JOIN ventas_carrito vc ON vc.id = vci.id_carrito
        INNER JOIN propietarios_menu_ingredientes pmi ON pmi.id_cafeteria = vc.id_cafeteria
        AND pmi.id_producto = vci.id_producto AND pmi.id_ingrediente = ii.id_ingrediente
        WHERE ii.estado = 0
        AND ii.id_carrito_item = :id_carrito_item
        ;");
    
        $stmt->bindParam(':id_carrito_item', $datos['id_item'],PDO::PARAM_INT);

        $stmt -> execute();
    
        return $stmt -> fetchAll();
    
    }
    
    
    static public function actualizarItemCantidadModel($datos){
    
        $stmt = Conexion::conectar()->prepare("UPDATE ventas_carrito_items SET cantidad = :cantidad WHERE id = :id_item");
    
        $stmt->bindParam(":id_item", $datos['id_item'], PDO::PARAM_INT);
         $stmt->bindParam(":cantidad", $datos['cantidad'], PDO::PARAM_INT);
    
        if($stmt->execute()){
            return 'success';
        }else{
            return 'error';
        }
    
        $stmt = null;
    
    }
    
    static public function eliminarItemCantidadModel($datos){
    
        $stmt = Conexion::conectar()->prepare("UPDATE ventas_carrito_items SET estado = 2 WHERE id = :id_item");
    
        $stmt->bindParam(":id_item", $datos['id_item'], PDO::PARAM_INT);

        if($stmt->execute()){
            return 'success';
        }else{
            return 'error';
        }
    
        $stmt = null;
    
    }

    static public function carritoContadorItemsModel($id_usuario)
    {
        $stmt = Conexion::conectar()->prepare("SELECT COUNT(*) AS total
        FROM ventas_carrito_items ci
        INNER JOIN ventas_carrito vc ON vc.id = ci.id_carrito
        WHERE vc.id_usuario = :id_usuario
            AND vc.estado = 0
            AND ci.estado = 0
        ");

        $stmt->bindParam(":id_usuario", $id_usuario, PDO::PARAM_INT);
        $stmt->execute();
        $res = $stmt->fetch();

        return $res['total'];
    }
}