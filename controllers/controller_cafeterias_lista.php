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

            // Filtrar por estado si es necesario
            if ($datos['horario'] === 'abierto' && !$isOpen) {
                continue; // Saltar cafeterías cerradas si se filtra por abiertas
            }
            if ($datos['horario'] === 'cerrado' && $isOpen) {
                continue; // Saltar cafeterías abiertas si se filtra por cerradas
            }

            $html .= '
            <div class="cafeteria-card-wrapper">
                <div class="cafeteria-card">
                    <a href="' . $url . 'cafeterias_lista/' . $cafeteria['id'] . '" class="cafeteria-card-link">
                        <div class="cafeteria-card-image">
                            <img src="' . htmlspecialchars($cafeteria['imagen']) . '" alt="' . htmlspecialchars($cafeteria['nombre']) . '">
                            <div class="cafeteria-card-overlay">
                                <span class="status-badge ' . ($isOpen ? 'status-open' : 'status-closed') . '">
                                    <i class="bi bi-' . ($isOpen ? 'check-circle' : 'x-circle') . '"></i>
                                    ' . ($isOpen ? 'Abierto' : 'Cerrado') . '
                                </span>
                            </div>
                        </div>
                        <div class="cafeteria-card-content">
                            <h3 class="cafeteria-card-title">' . htmlspecialchars($cafeteria['nombre']) . '</h3>
                            <p class="cafeteria-card-address">
                                <i class="bi bi-geo-alt"></i>
                                ' . htmlspecialchars($cafeteria['direccion']) . '
                            </p>
                            <div class="cafeteria-card-footer">
                                <span class="cafeteria-card-schedule">
                                    <i class="bi bi-clock"></i>
                                    ' . htmlspecialchars($horarioDiaActual) . '
                                </span>
                            </div>
                        </div>
                    </a>
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
            'longitud' => $cafeteria['longitud'],
            'latitud' => $cafeteria['latitud'],
            'descripcion' => $cafeteria['descripcion'],
        );

        $active='active';
        $i=0;
        $imagenesArray = []; // Array para la nueva galería
        foreach(CafeteriasListaModel::obtenerImagenesModel($id) as $imagen){
            $active = ($i==0) ? $active='active' : $active='' ;
            $imagenUrl = $url.$imagen['imagen'];
            $imagenesArray[] = $imagenUrl; // Agregar URL al array
            $carousel .='
            <div class="carousel-item '.$active.' ver_img_modal" data-bs-interval="10000">
                <img src="'.$imagenUrl.'" class="d-block w-100 img-fluid" alt="...">
            </div>';
            ++$i;
        }

        $servicios = '';
        foreach(CafeteriasListaModel::buscarServiciosCafeteriasModel($id) as $servicio){
            $titulo = $servicio['nombre'];
            $imagen = $url . $servicio['imagen']; 
            $servicios .= '
            <li class="splide__slide">
                <div class="custom-carousel-item">
                    <div class="card card-cover overflow-hidden text-bg-dark rounded-4 shadow-lg" style="background-image: url(' . $imagen . ');">
                        <div class="d-flex flex-column p-3 pb-3 text-white text-center titulo-oscuro">
                            <h3 class="pt-5 mt-5 mb-4 display-7 lh-1 fw-bold">' . $titulo . '</h3>
                        </div>
                    </div>
                </div>
            </li>
            ';
        }
        

        return json_encode([
            'data' => $data, 
            'imagenes' => $carousel,
            'imagenesArray' => $imagenesArray,
            'servicios' => $servicios
        ]);
    }
    
    /* OBTENER DATOS DE CAFETERIA */
    

    
    /* OBTENER COMENTARIOS */
    
    static public function buscarComentariosController($datos = 1) {
        $comentariosPorPagina = 5; // Número de comentarios que deseas mostrar por página
    
        // Obtener el total de comentarios
        $totalComentarios = CafeteriasListaModel::contarTotalComentariosModel($datos['cafeteria']);
    
        // Calcular el número total de páginas
        $totalPaginas = ceil($totalComentarios / $comentariosPorPagina);
    
        // Calcular el offset para la consulta de comentarios
        $offset = ((int)($datos['pagina']) - 1) * $comentariosPorPagina;
    
        // Obtener los comentarios para la página actual
        $comentarios = CafeteriasListaModel::buscarComentariosModel($offset, $comentariosPorPagina, $datos['cafeteria']);
    
        // Devolver los comentarios y el total de páginas como respuesta JSON
        return json_encode([
            'comentarios' => $comentarios,
            'totalPaginas' => $totalPaginas,
            'totalComentarios' => $totalComentarios
        ]);
    }
    
    
    /* OBTENER COMENTARIOS */
    
    
    /* REGISTRAR COMENTARIO */
    
    static public function registrarComentarioController($datos){
        session_start();
        if (isset($_SESSION['id']) && $_SESSION['id']!='') {
            $datos['id_usuario'] = $_SESSION['id'];
            date_default_timezone_set("America/Tijuana");
            $datos['fecha_alta']= date("Y-m-d H:i:s");
            return CafeteriasListaModel::registrarComentarioModel($datos);
        }else{
            return 'sesion';
        }
    }
    
    /* REGISTRAR COMENTARIO */
    
    
    /* OBTENER SERVICIOS */
    
    static public function obtenerServiciosController(){

        $data ='';
        foreach (CafeteriasListaModel::obtenerServiciosModel() as $servicio){
            $data .='
                <div class="col-6 col-md-4 col-lg-3 mb-2">
                    <div class="form-check">
                        <input class="form-check-input seleccionar_servicio" type="checkbox" value="'.$servicio['id'].'">
                        <label class="form-check-label" for="flexCheckDefault">
                            '.$servicio['nombre'].'
                        </label>
                    </div>
                </div>
            ';
        }

        return json_encode($data);
    }
    
    /* OBTENER SERVICIOS */
    
}