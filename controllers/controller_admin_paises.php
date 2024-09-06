<?php

class AdminPaisesController{
    
    /* OBTENER PAISES */
    
    static public function obtenerPaiseController(){
        return AdminPaisesModel::obtenerPaisesModel();
    }
    
    /* OBTENER PAISES */

    
    /* OBTENER INFO DE PAIS */
    
    static public function obtenerInfoPaisController($pais){
        return AdminPaisesModel::obtenerInfoPaisModel($pais);
    }
    
    /* OBTENER INFO DE PAIS */
    
    
    /* AGREGAR EDITAR PAIS */
    
    static public function agregarEditarPaisController($datos){
        date_default_timezone_set("America/Tijuana");
        $datos['id_alta'] = $_SESSION['id'];
        $datos['fecha_alta']= date("Y-m-d H:i:s");

        if (!$datos['id']) {
            return AdminPaisesModel::agregarPaisModel($datos);
        } else {
            return AdminPaisesModel::editarPaisModel($datos);
        }
        
    }
    
    /* AGREGAR EDITAR PAIS */

    
    /* OBTENER CIUDADES */
    
    static public function obtenerCiudadesController($datos) {

        return AdminPaisesModel::obtenerCiudadesPorPaisModel($datos);
    }
    
    
    
    /* OBTENER CIUDADES */

    
    /* OBTENER CAFETERIAS */
    
    static public function obtenerCafeteriasController($ciudad){
        return AdminPaisesModel::obtenerCafeteriasModel($ciudad);
    }
    
    /* OBTENER CAFETERIAS */
    

    
    /* AGREGAR CIUDADES */
    
    static public function agregarEditarCiudadController($datos){
        date_default_timezone_set("America/Tijuana");
        $datos['id_alta'] = $_SESSION['id'];
        $datos['fecha_alta']= date("Y-m-d H:i:s");

        if (!$datos['id']) {
            return AdminPaisesModel::agregarCiudadModel($datos);
        }else{
            return AdminPaisesModel::editarCiudadModel($datos);
        }
        
        
    }
    
    /* AGREGAR CIUDADES */
    
    
    /* OBTENER INFO GRAFICA Y CONTADORES */
    
    static public function obtenerDatosMetricosController($datos){
        $data=[];
        foreach (AdminPaisesModel::obtenerCiudadesPorPaisModel($datos['id_pais']) as $pais){
            $data[]=[
                'nombre' => $pais['nombre'],
                'cafeterias' => $pais['total_cafeterias']
            ];
        }
        $contadores = AdminPaisesModel::obtenerDatosContadoresPaisesModel();

        return json_encode(['data' => $data, 'contadores' => $contadores]);
    }
    
    /* OBTENER INFO GRAFICA Y CONTADORES */
    
    
}