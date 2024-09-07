<?php
class CafeteriasListaController{
    
    /* OBTENER CAFETERIAS */
    
    static public function obtenerCafeteriasController(){
        $url = TemplateController::obtenerUrlController();
        $html = ''; 
        $i=0;
        foreach (CafeteriasListaModel::obtenerCafeteriasModel() as $cafeteria){
            $isOpen = self::isOpen($cafeteria['horario_apertura'], $cafeteria['horario_cierre']); // Determinar si está abierto
    
            // Clase de color basada en el estado
            $statusClass = $isOpen ? 'text-success' : 'text-danger'; // Verde si está abierto, rojo si está cerrado
    
            // Construcción del HTML para cada tarjeta
            $html .= '
            <div class="col-12 col-md-6 col-lg-4 mb-4 modal_cafeteria">
                <div class="card">
                    <a   href="'.$url.'cafeterias_lista/'.$cafeteria['id'].'">
                        <img src="' . htmlspecialchars($cafeteria['imagen']) . '" class="card-img-top" alt="' . htmlspecialchars($cafeteria['nombre']) . '">
                    </a>
                    <div class="card-body">
                        <div class="card-header">
                            <h5 class="card-title mt-1">' . htmlspecialchars($cafeteria['nombre']) . '</h5>
                            <i class="bi bi-heart favorite-icon"></i>
                        </div>
                        <p class="card-text">' . htmlspecialchars($cafeteria['direccion']) . '</p>
                        <div class="d-flex justify-content-between">
                            <span class="' . $statusClass . '">' . ($isOpen ? 'abierto' : 'cerrado') . '</span>
                            <span class="' . $statusClass . '">' . htmlspecialchars($cafeteria['horario_apertura']) . ' - ' . htmlspecialchars($cafeteria['horario_cierre']) . '</span>
                        </div>
                    </div>
                </div>
            </div>';
        }
    
        // Devolver solo el HTML generado
        return $html;
    }
    
    // Función para determinar si la cafetería está abierta
    private static function isOpen($horario_apertura, $horario_cierre) {
        date_default_timezone_set('America/Tijuana'); // Reemplaza con tu zona horaria correcta
        $currentTime = date('H:i'); // Obtener la hora actual en formato de 24 horas (HH:MM)
        return ($currentTime >= $horario_apertura && $currentTime <= $horario_cierre);
    }
    
    
    /* OBTENER CAFETERIAS */

    
    /* OBTENER DATOS DE CAFETERIA */
    
    static public function obtenerDatosCafeteriaController($id){
        $data=[];
        $cafeteria =CafeteriasListaModel::obtenerDatosCafeteriaModel($id);
        $data=array(
            'id' => $cafeteria['id'],
            'nombre' => $cafeteria['nombre'],
            'logo' => $cafeteria['imagen'],
            'ciudad' => $cafeteria['ciudad'],
            'entidad_federativa' => $cafeteria['entidad_federativa'],
            'pais' => $cafeteria['pais'],
            'direccion' => $cafeteria['direccion'],
            'telefono' => $cafeteria['telefono'],
            'ciudad' => $cafeteria['correo_electronico'],
            'horario_apertura' => $cafeteria['horario_apertura'],
            'horario_cierre' => $cafeteria['horario_cierre'],
        );

        echo json_encode($data);
    }
    
    /* OBTENER DATOS DE CAFETERIA */
    
    
}