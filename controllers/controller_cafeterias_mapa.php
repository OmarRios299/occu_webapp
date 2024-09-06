<?php

class CafeteriasMapaController{
    
    /* OBTENER CAFETERIAS */
    
    static public function obtenerCafeteriasController(){
        
        $data = [];
        foreach (CafeteriasMapaModel::obtenerCafeteriasModel() as $cafeteria){
            $data[]=[
                'nombre' => $cafeteria['nombre'],
                'imagen' => $cafeteria['imagen'],
                'direccion' => $cafeteria['direccion'],
                'telefono' => $cafeteria['telefono'],
                'correo' => $cafeteria['correo_electronico'],
                'horario' => $cafeteria['horario_apertura']. ' - '. $cafeteria['horario_cierre'],
                'latitud' => $cafeteria['latitud'],
                'longitud' => $cafeteria['longitud']
            ];
        }
        return json_encode([
            'data' => $data
        ]);
    }
    
    /* OBTENER CAFETERIAS */
    
}