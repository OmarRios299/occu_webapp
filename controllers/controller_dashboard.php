<?php 

class DashboardController{
    
    /* OBTENER DATOS PARA GRAFICA DE CIUDADES */
    
    static public function cafeteriasPorCiudadController(){
        return DashboardModel::obtenerCiudadesPorPaisModel();
    }
    
    /* OBTENER DATOS PARA GRAFICA DE CIUDADES */

    
    /* OBTENER CONTADORES */
    
    static public function obtenerDatosContadoresController(){
        return DashboardModel::obtenerDatosContadoresModel();
    }
    
    /* OBTENER CONTADORES */
    
    
}