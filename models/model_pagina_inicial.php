<?php 


require_once "conexion.php";

class PaginaInicialModel extends Conexion {

/* OBTENER INFO DE PAGINA INICIAL */

static public function obtenerCarouselModel(){

    $stmt = Conexion::conectar()->prepare("SELECT *
    FROM pagina_inicial
    WHERE area = 'carousel'
    AND estado = 0 
    ");

    $stmt -> execute();

    return $stmt -> fetchAll();

    $stmt = null;

}

static public function obtenerCardsModel(){

    $stmt = Conexion::conectar()->prepare("SELECT *
    FROM pagina_inicial
    WHERE area = 'card'
    AND estado = 0 
    ");

    $stmt -> execute();

    return $stmt -> fetchAll();

    $stmt = null;

}


/* OBTENER INFO DE PAGINA INICIAL */

}