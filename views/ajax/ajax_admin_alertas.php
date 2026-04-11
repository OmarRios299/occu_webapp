<?php 
require_once '../../controllers/controller_admin_alertas.php';
require_once '../../controllers/controller_template.php';
require_once '../../models/model_alertas.php';

session_start();

if(isset($_SESSION['iniciarSesion']) && $_SESSION['iniciarSesion'] == 'ok'){

    if(isset($_POST['registrar_alerta'])){
        
        // Obtener roles seleccionados
        $roles = [];
        if (isset($_POST['roles']) && is_array($_POST['roles'])) {
            $roles = $_POST['roles'];
        }
        $criterios_rol = !empty($roles) ? json_encode($roles) : null;
        
        // Obtener botones
        $botones_array = [];
        if (isset($_POST['botones']) && is_array($_POST['botones'])) {
            foreach ($_POST['botones'] as $boton) {
                if (!empty($boton['texto'])) {
                    $botones_array[] = [
                        'texto' => $boton['texto'],
                        'url' => $boton['url'] ?? null,
                        'tipo' => $boton['tipo'] ?? 'primary',
                        'cerrar' => isset($boton['cerrar']) ? (bool)$boton['cerrar'] : false
                    ];
                }
            }
        }
        $botones = !empty($botones_array) ? json_encode($botones_array) : null;
        
        $datos = array(
            "id" => isset($_POST['id_alerta']) ? $_POST['id_alerta'] : false,
            "codigo" => $_POST['codigo'],
            "titulo" => $_POST['titulo'],
            "mensaje" => $_POST['mensaje'],
            "tipo" => $_POST['tipo'],
            "icono" => isset($_POST['icono']) ? $_POST['icono'] : null,
            "tipo_mostrar" => $_POST['tipo_mostrar'],
            "veces_mostrar" => isset($_POST['veces_mostrar']) && $_POST['tipo_mostrar'] == 'n_veces' ? (int)$_POST['veces_mostrar'] : 1,
            "veces_ignorar" => isset($_POST['veces_ignorar']) ? (int)$_POST['veces_ignorar'] : 0,
            "fecha_inicio" => !empty($_POST['fecha_inicio']) ? $_POST['fecha_inicio'] : null,
            "fecha_fin" => !empty($_POST['fecha_fin']) ? $_POST['fecha_fin'] : null,
            "activa" => isset($_POST['activa']) ? 1 : 0,
            "id_modulo" => isset($_POST['id_modulo']) && !empty($_POST['id_modulo']) ? (int)$_POST['id_modulo'] : null,
            "criterios_rol" => $criterios_rol,
            "criterios_version" => isset($_POST['criterios_version']) ? $_POST['criterios_version'] : null,
            "criterios_permisos" => isset($_POST['criterios_permisos']) ? $_POST['criterios_permisos'] : null,
            "prioridad" => (int)$_POST['prioridad'],
            "plantilla" => $_POST['plantilla'],
            "html_personalizado" => isset($_POST['html_personalizado']) && $_POST['plantilla'] == 'personalizado' ? $_POST['html_personalizado'] : null,
            "botones" => $botones,
            "estilo_personalizado" => isset($_POST['estilo_personalizado']) ? $_POST['estilo_personalizado'] : null,
            "estado" => 0,
            "id_alta" => $_SESSION['id']
        );
        
        if ($datos['id']) {
            // Actualizar
            $resultado = AdminAlertasController::actualizarAlertaController($datos['id'], $datos);
        } else {
            // Insertar
            $resultado = AdminAlertasController::insertarAlertaController($datos);
        }
        
        if ($resultado == 'success') {
            echo json_encode([
                'success' => true,
                'mensaje' => $datos['id'] ? 'Alerta actualizada correctamente' : 'Alerta creada correctamente'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'mensaje' => 'Error al guardar la alerta'
            ]);
        }

    }else if(isset($_GET['tabla_alertas'])){
        
        $datos = array(
            'activa' => isset($_GET['activa']) ? $_GET['activa'] : '',
            'estado' => 0, // Solo no eliminadas
            'plantilla' => isset($_GET['plantilla']) ? $_GET['plantilla'] : ''
        );
        
        echo AdminAlertasController::obtenerAlertasController($datos);
        
    }else if(isset($_POST['cambiar_estado'])){
        
        $id_alerta = (int)$_POST['id_alerta'];
        $campo = $_POST['campo'];
        $valor = (int)$_POST['valor'];
        
        $resultado = AdminAlertasController::cambiarEstadoAlertaController($id_alerta, $campo, $valor);
        
        if ($resultado == 'success') {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false]);
        }
        
    }else if(isset($_GET['obtener_alerta_preview']) && isset($_GET['id_alerta'])){
        
        $id_alerta = (int)$_GET['id_alerta'];
        $alerta = AdminAlertasController::obtenerInfoAlertaController($id_alerta);
        
        if ($alerta) {
            // Procesar la alerta para el frontend (igual que en controller_alertas.php)
            $alerta_procesada = [
                'id' => $alerta['id'],
                'codigo' => $alerta['codigo'],
                'titulo' => $alerta['titulo'],
                'mensaje' => $alerta['mensaje'],
                'tipo' => $alerta['tipo'],
                'icono' => $alerta['icono'],
                'plantilla' => $alerta['plantilla'] ?: 'default',
                'html_personalizado' => $alerta['html_personalizado'],
                'botones' => $alerta['botones'] ? json_decode($alerta['botones'], true) : null,
                'estilo_personalizado' => $alerta['estilo_personalizado'],
                'prioridad' => (int)$alerta['prioridad']
            ];
            
            echo json_encode([
                'success' => true,
                'alerta' => $alerta_procesada
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'mensaje' => 'Alerta no encontrada'
            ]);
        }
        
    }else{
        
        echo json_encode(['success' => false, 'mensaje' => 'Acción no válida']);
        
    }

}else{
    echo json_encode(['success' => false, 'mensaje' => 'Sesión expirada']);
}
?>

