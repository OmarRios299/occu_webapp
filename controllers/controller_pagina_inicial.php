<?php 

class PaginaInicialController{

    
    /* OBTENER BANERS PAGINA INICIAL */
    
    static public function obtenerCarouselController(){
        return PaginaInicialModel::obtenerCarouselModel();

    }

    static public function obtenerCardsController(){
        return PaginaInicialModel::obtenerCardsModel();

    }
    
    /* OBTENER BANERS PAGINA INICIAL */
    
}