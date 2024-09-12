<?php
class CafeteriasListaController{
    

    /* OBTENER CAFETERIAS */
    static public function obtenerCafeteriasController() {
        $url = TemplateController::obtenerUrlController();
        $html = ''; 

        foreach (CafeteriasListaModel::obtenerCafeteriasModel() as $cafeteria) {
            // Obtener horarios simples
            $horariosSimples = CafeteriasListaModel::obtenerHorariosSimplesCafeteriaModel($cafeteria['id']);
            
            // Obtener horarios detallados desde la tabla de horarios
            $horariosDetallados = CafeteriasListaModel::obtenerHorariosDetalladosCafeteriaModel($cafeteria['id']);
            
            // Determinar si la cafetería está abierta y obtener el horario del día actual
            list($isOpen, $horarioDiaActual) = self::isOpen($horariosSimples, $horariosDetallados); 

            // Clase de color basada en el estado
            $statusClass = $isOpen ? 'text-success' : 'text-danger'; // Verde si está abierto, rojo si está cerrado

            // Construcción del HTML para cada tarjeta
            $html .= '
            <div class="col-12 col-md-6 col-lg-4 mb-4 modal_cafeteria">
                <div class="card">
                    <a href="' . $url . 'cafeterias_lista/' . $cafeteria['id'] . '">
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
                            <span class="' . $statusClass . '">' . htmlspecialchars($horarioDiaActual) . '</span>
                        </div>
                    </div>
                </div>
            </div>';
        }

        // Devolver solo el HTML generado
        return $html;
    }

    // Función para traducir el nombre del día de inglés a español
    private static function traducirDia($diaIngles) {
        $dias = [
            'Monday' => 'Lunes',
            'Tuesday' => 'Martes',
            'Wednesday' => 'Miércoles',
            'Thursday' => 'Jueves',
            'Friday' => 'Viernes',
            'Saturday' => 'Sábado',
            'Sunday' => 'Domingo'
        ];
        return $dias[$diaIngles] ?? $diaIngles; // Devolver la traducción o el mismo día si no se encuentra
    }

    // Función para determinar si la cafetería está abierta y el horario del día actual
    private static function isOpen($horariosSimples, $horariosDetallados) {
        date_default_timezone_set('America/Tijuana'); // Reemplaza con tu zona horaria correcta
        $currentTime = date('H:i'); // Obtener la hora actual en formato de 24 horas (HH:MM)
        $currentDayEnglish = date('l'); // Obtener el día actual en inglés
        $currentDay = self::traducirDia($currentDayEnglish); // Traducir el día al español

        // Si la cafetería tiene horarios en formato simple
        if (!empty($horariosSimples['horario_apertura']) && !empty($horariosSimples['horario_cierre'])) {
            $isOpen = ($currentTime >= $horariosSimples['horario_apertura'] && $currentTime <= $horariosSimples['horario_cierre']);
            $horarioDiaActual = $horariosSimples['horario_apertura'] . ' - ' . $horariosSimples['horario_cierre'];
            return [$isOpen, $horarioDiaActual];
        }

        // Si la cafetería tiene horarios en formato detallado
        if (!empty($horariosDetallados) && isset($horariosDetallados[$currentDay])) {
            $horarioDia = $horariosDetallados[$currentDay];

            // Verificar si el día está marcado como cerrado
            if ($horarioDia['cerrado'] === 'SI') {
                return [false, 'Cerrado hoy']; // Si el día está marcado como cerrado
            }

            // Verificar si hay horarios de apertura y cierre válidos
            if (empty($horarioDia['apertura']) || empty($horarioDia['cierre'])) {
                return [false, 'Horario no disponible']; // Si no hay horarios definidos
            }

            $isOpen = ($currentTime >= $horarioDia['apertura'] && $currentTime <= $horarioDia['cierre']);
            $horarioDiaActual = $horarioDia['apertura'] . ' - ' . $horarioDia['cierre'];
            return [$isOpen, $horarioDiaActual];
        }

        return [false, 'Horario no disponible']; // Si no hay horarios definidos, se asume que está cerrado
    }
    
    /* OBTENER DATOS DE CAFETERIA */
    
    static public function obtenerDatosCafeteriaController($id){
        $url = TemplateController::obtenerUrlController();
        $data=[];
        $carousel ='';
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
            'correo' => $cafeteria['correo_electronico'],
            'horario_apertura' => $cafeteria['horario_apertura'],
            'horario_cierre' => $cafeteria['horario_cierre'],
        );

        $active='active';
        $i=0;
        foreach(CafeteriasListaModel::obtenerImagenesModel($id) as $imagen){
            $active = ($i==1) ? $active='' : $active='active' ;
            $carousel .='
            <div class="carousel-item '.$active.' ver_img_modal" data-bs-interval="10000">
                <img src="../'.$imagen['imagen'].'" class="d-block w-100 img-fluid" alt="...">
            </div>';
            ++$i;
        }

        return json_encode(['data' => $data, 'imagenes' => $carousel]);
    }
    
    /* OBTENER DATOS DE CAFETERIA */
    
    
}