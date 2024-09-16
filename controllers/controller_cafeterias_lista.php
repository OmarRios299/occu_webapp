<?php
class CafeteriasListaController{
    

    /* OBTENER CAFETERIAS */
    static public function obtenerCafeteriasController($datos) {
        $url = TemplateController::obtenerUrlController();
        $html = ''; 

        $cafeterias = CafeteriasListaModel::obtenerCafeteriasModel($datos);

        foreach ($cafeterias as $cafeteria) {

            $horariosSimples = GeneralModel::obtenerHorariosSimplesCafeteriaModel($cafeteria['id']);
            $horariosDetallados = GeneralModel::obtenerHorariosDetalladosCafeteriaModel($cafeteria['id']);
            list($isOpen, $horarioDiaActual) = GeneralController::isOpen($horariosSimples, $horariosDetallados); 
            $statusClass = $isOpen ? 'text-success' : 'text-danger';

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
                            <span class="' . $statusClass . '">' . ($isOpen ? 'Abierto' : 'Cerrado') . '</span>
                            <span class="' . $statusClass . '">' . htmlspecialchars($horarioDiaActual) . '</span>
                        </div>
                    </div>
                </div>
            </div>';
        }

        $totalCafeterias = CafeteriasListaModel::contarTotalCafeterias($datos['busqueda']);

        return json_encode([
            'html' => $html,
            'totalCafeterias' => $totalCafeterias
        ]);
    }
    
    /* OBTENER DATOS DE CAFETERIA */
    
    static public function obtenerDatosCafeteriaController($id){
        $url = TemplateController::obtenerUrlController();
        $data=[];
        $carousel ='';
        $cafeteria =CafeteriasListaModel::obtenerDatosCafeteriaModel($id);

        $horariosSimples = GeneralModel::obtenerHorariosSimplesCafeteriaModel($cafeteria['id']);
        $horariosDetallados = GeneralModel::obtenerHorariosDetalladosCafeteriaModel($cafeteria['id']);
        list($isOpen, $horarioDiaActual) = GeneralController::isOpen($horariosSimples, $horariosDetallados); 
        $statusClass = $isOpen ? 'text-success' : 'text-danger';

        $status = '<span class="' . $statusClass . '">' . ($isOpen ? 'Abierto' : 'Cerrado') . '</span>';
        $horario = '<span class="' . $statusClass . '">' . htmlspecialchars($horarioDiaActual) . '</span>';

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
            'status' => $status,
            'horario' => $horario,
        );

        $active='active';
        $i=0;
        foreach(CafeteriasListaModel::obtenerImagenesModel($id) as $imagen){
            $active = ($i==0) ? $active='active' : $active='' ;
            $carousel .='
            <div class="carousel-item '.$active.' ver_img_modal" data-bs-interval="10000">
                <img src="'.$url.''.$imagen['imagen'].'" class="d-block w-100 img-fluid" alt="...">
            </div>';
            ++$i;
        }

        return json_encode(['data' => $data, 'imagenes' => $carousel]);
    }
    
    /* OBTENER DATOS DE CAFETERIA */
    
    
}