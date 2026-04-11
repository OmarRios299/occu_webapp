<?php

require_once __DIR__ . '/../models/model_alertas.php';
require_once __DIR__ . '/controller_template.php';

class AdminAlertasController
{
    
    /* OBTENER ALERTAS PARA TABLA */
    
    static public function obtenerAlertasController($datos = []){
        $url = TemplateController::obtenerUrlController();
        $i = 0;
        $data = [];
        
        foreach (AlertaModel::obtenerTodasAlertasModel($datos) as $alerta) {
            // Determinar si el checkbox debe estar marcado
            $checked = ($alerta['activa'] == 1) ? "checked" : "";
            
            // Botones de acción con espaciado
            $botones = '<div class="gap-2">
                        <a href="' . $url . 'admin_alertas/' . $alerta['id'] . '/editar" class="btn btn-icono btn-editar" title="Editar"></a>
                        <a href="' . $url . 'admin_alertas/' . $alerta['id'] . '/ver" class="btn btn-icono btn-ver" title="Ver"></a>
                        <button class="btn btn-icono btn-preview btn-ver-preview" data-id-alerta="' . $alerta['id'] . '" title="Vista previa"></button>
                        <button class="btn btn-icono btn-eliminar eliminarRegistro" tabla="sistema_alertas" idRegistro="' . $alerta['id'] . '" title="Eliminar"></button>
                        </div>';
            
            // Estado activa/inactiva
            $estado = '<div class="form-check form-switch">
                        <input type="checkbox" class="form-check-input cambioEstado" id="switch' . $alerta['id'] . '" tabla="sistema_alertas" campo="activa" idRegistro="' . $alerta['id'] . '" ' . $checked . '>
                        <label class="form-check-label" for="switch' . $alerta['id'] . '"></label>
                        </div>';
            
            // Tipo de mostrar
            $tipo_mostrar = '';
            switch($alerta['tipo_mostrar']) {
                case 'una_vez':
                    $tipo_mostrar = 'Una vez';
                    break;
                case 'n_veces':
                    $tipo_mostrar = $alerta['veces_mostrar'] . ' veces';
                    break;
                case 'mientras_activa':
                    $tipo_mostrar = 'Mientras activa';
                    break;
                default:
                    $tipo_mostrar = $alerta['tipo_mostrar'];
            }
            
            // Roles aplicables
            $roles = 'Todos';
            if ($alerta['criterios_rol']) {
                $roles_array = json_decode($alerta['criterios_rol'], true);
                if (is_array($roles_array) && count($roles_array) > 0) {
                    $roles = implode(', ', $roles_array);
                }
            }
            
            // Plantilla
            $plantilla = ucfirst($alerta['plantilla'] ?: 'default');
            
            // Fechas
            $fecha_inicio = $alerta['fecha_inicio'] ? date('d/m/Y', strtotime($alerta['fecha_inicio'])) : 'Sin límite';
            $fecha_fin = $alerta['fecha_fin'] ? date('d/m/Y', strtotime($alerta['fecha_fin'])) : 'Sin límite';
            
            $data[] = [
                ++$i,
                $botones,
                $estado,
                $alerta['codigo'],
                $alerta['titulo'],
                $plantilla,
                $tipo_mostrar,
                $roles,
                $fecha_inicio,
                $fecha_fin,
                $alerta['prioridad'],
                $alerta['fecha_alta']
            ];
        }
        
        return json_encode(['data' => $data]);
    }
    
    /* End of OBTENER ALERTAS PARA TABLA */
    
    
    /* OBTENER INFORMACIÓN DE ALERTA */
    
    static public function obtenerInfoAlertaController($id_alerta){
        return AlertaModel::obtenerAlertaPorIdModel($id_alerta);
    }
    
    /* End of OBTENER INFORMACIÓN DE ALERTA */
    
    
    /* INSERTAR ALERTA */
    
    static public function insertarAlertaController($datos){
        return AlertaModel::insertarAlertaModel($datos);
    }
    
    /* End of INSERTAR ALERTA */
    
    
    /* ACTUALIZAR ALERTA */
    
    static public function actualizarAlertaController($id_alerta, $datos){
        return AlertaModel::actualizarAlertaModel($id_alerta, $datos);
    }
    
    /* End of ACTUALIZAR ALERTA */
    
    
    /* ELIMINAR ALERTA */
    
    static public function eliminarAlertaController($id_alerta){
        return AlertaModel::eliminarAlertaModel($id_alerta);
    }
    
    /* End of ELIMINAR ALERTA */
    
    
    /* CAMBIAR ESTADO DE ALERTA */
    
    static public function cambiarEstadoAlertaController($id_alerta, $campo, $valor){
        return AlertaModel::cambiarEstadoAlertaModel($id_alerta, $campo, $valor);
    }
    
    /* End of CAMBIAR ESTADO DE ALERTA */
    
}

