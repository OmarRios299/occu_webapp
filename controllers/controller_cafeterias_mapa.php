<?php

class CafeteriasMapaController{
    
    /* OBTENER CAFETERIAS */
    
    static public function obtenerCafeteriasController($datos){
        
        $data = [];
        foreach (CafeteriasMapaModel::obtenerCafeteriasModel($datos) as $cafeteria){

            $horariosSimples = GeneralModel::obtenerHorariosSimplesCafeteriaModel($cafeteria['id']);
            $horariosDetallados = GeneralModel::obtenerHorariosDetalladosCafeteriaModel($cafeteria['id']);
            list($isOpen, $horarioDiaActual) = GeneralController::isOpen($horariosSimples, $horariosDetallados); 
            $statusClass = $isOpen ? 'text-success' : 'text-danger';

             // Filtrar por estado si es necesario
             if ($datos['horario'] === 'abierto' && !$isOpen) {
                continue; // Saltar cafeterías cerradas si se filtra por abiertas
            }
            if ($datos['horario'] === 'cerrado' && $isOpen) {
                continue; // Saltar cafeterías abiertas si se filtra por cerradas
            }
    
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