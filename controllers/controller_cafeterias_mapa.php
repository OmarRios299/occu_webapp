<?php

class CafeteriasMapaController{
    
    /* OBTENER CAFETERIAS */
    
    static public function obtenerCafeteriasController(){
        
        $data = [];
        foreach (CafeteriasMapaModel::obtenerCafeteriasModel() as $cafeteria){

            $horariosSimples = GeneralModel::obtenerHorariosSimplesCafeteriaModel($cafeteria['id']);
            $horariosDetallados = GeneralModel::obtenerHorariosDetalladosCafeteriaModel($cafeteria['id']);
            list($isOpen, $horarioDiaActual) = GeneralController::isOpen($horariosSimples, $horariosDetallados); 
            $statusClass = $isOpen ? 'text-success' : 'text-danger';
    
            $status = '<span class="' . $statusClass . '">' . ($isOpen ? 'Abierto' : 'Cerrado') . '</span>';
            $horario = '<span class="' . $statusClass . '">' . htmlspecialchars($horarioDiaActual) . '</span>';

            $data[]=[
                'id' => $cafeteria['id'],
                'nombre' => $cafeteria['nombre'],
                'imagen' => $cafeteria['imagen'],
                'direccion' => $cafeteria['direccion'],
                'telefono' => $cafeteria['telefono'],
                'correo' => $cafeteria['correo_electronico'],
                'status' => $status,
                'horario' => $horario,
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