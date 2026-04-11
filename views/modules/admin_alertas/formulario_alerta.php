<?php
// Detectar si es modo edición o agregar
$esEdicion = isset($alerta) && isset($alerta['id']);
$tituloFormulario = $esEdicion ? 'Editar alerta' : 'Agregar nueva alerta';
$tituloBreadcrumb = $esEdicion ? 'Editar alerta' : 'Nueva alerta';

// Decodificar JSONs si existen
$criterios_rol = $esEdicion && $alerta['criterios_rol'] ? json_decode($alerta['criterios_rol'], true) : [];
$botones = $esEdicion && $alerta['botones'] ? json_decode($alerta['botones'], true) : [];
?>

<div class="titulo-boton mt-4">
    <h1 class="titulo-modulo">Gestión de Alertas</h1>
</div>

<div class="my-5">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb breadcrumb-chevron p-3 bg-body-tertiary rounded-3">
            <li class="breadcrumb-item">
                <a class="link-body-emphasis" href="<?= $url . 'admin_alertas' ?>">
                    <i class="fas fa-home" style="color: black;"></i>
                    <span class="visually-hidden">Home</span>
                </a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
                <?= $tituloBreadcrumb ?>
            </li>
        </ol>
    </nav>
</div>

<h6 class="subtitulo mt-3"><?= $tituloFormulario ?></h6>
<form onsubmit="return false;" id="form_agregar_alerta">
    <div class="caja">
        <?php if ($esEdicion): ?>
            <input type="hidden" value="<?= $alerta['id'] ?>" id="id_alerta">
        <?php endif; ?>
        
        <!-- Información básica -->
        <div class="row mt-3">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Código único: <span class="text-danger">*</span></label>
                    <input type="text" class="form-control input_alerta" id="codigo_alerta" 
                           value="<?= $esEdicion ? htmlspecialchars($alerta['codigo']) : '' ?>" 
                           required placeholder="ej: primera_vez_bienvenida">
                    <small class="form-text text-muted">Identificador único para la alerta (sin espacios, usar guiones bajos)</small>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Título: <span class="text-danger">*</span></label>
                    <input type="text" class="form-control input_alerta" id="titulo_alerta" 
                           value="<?= $esEdicion ? htmlspecialchars($alerta['titulo']) : '' ?>" 
                           required placeholder="Título de la alerta">
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label>Mensaje: <span class="text-danger">*</span></label>
                    <textarea class="form-control input_alerta" id="mensaje_alerta" rows="3" 
                              required placeholder="Mensaje principal de la alerta"><?= $esEdicion ? htmlspecialchars($alerta['mensaje']) : '' ?></textarea>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    <label>Tipo: <span class="text-danger">*</span></label>
                    <select class="form-control input_alerta" id="tipo_alerta" required>
                        <option value="info" <?= $esEdicion && $alerta['tipo'] == 'info' ? 'selected' : '' ?>>Info</option>
                        <option value="success" <?= $esEdicion && $alerta['tipo'] == 'success' ? 'selected' : '' ?>>Success</option>
                        <option value="warning" <?= $esEdicion && $alerta['tipo'] == 'warning' ? 'selected' : '' ?>>Warning</option>
                        <option value="error" <?= $esEdicion && $alerta['tipo'] == 'error' ? 'selected' : '' ?>>Error</option>
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>Icono (FontAwesome):</label>
                    <input type="text" class="form-control input_alerta" id="icono_alerta" 
                           value="<?= $esEdicion ? htmlspecialchars($alerta['icono']) : '' ?>" 
                           placeholder="fas fa-info-circle">
                    <small class="form-text text-muted">Ej: fas fa-info-circle</small>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>Prioridad: <span class="text-danger">*</span></label>
                    <input type="number" class="form-control input_alerta" id="prioridad_alerta" 
                           value="<?= $esEdicion ? $alerta['prioridad'] : '0' ?>" 
                           required min="0" max="100">
                    <small class="form-text text-muted">Mayor número = mayor prioridad</small>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>Activa:</label>
                    <div class="form-check form-switch mt-2">
                        <input type="checkbox" class="form-check-input" id="activa_alerta" 
                               <?= $esEdicion && $alerta['activa'] == 1 ? 'checked' : 'checked' ?>>
                        <label class="form-check-label" for="activa_alerta">Alerta activa</label>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Configuración de visualización -->
        <hr class="my-4">
        <h6 class="subtitulo">Configuración de visualización</h6>
        
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label>Plantilla: <span class="text-danger">*</span></label>
                    <select class="form-control input_alerta" id="plantilla_alerta" required>
                        <option value="default" <?= $esEdicion && $alerta['plantilla'] == 'default' ? 'selected' : '' ?>>Default (SweetAlert)</option>
                        <option value="modal" <?= $esEdicion && $alerta['plantilla'] == 'modal' ? 'selected' : '' ?>>Modal (Bootstrap)</option>
                        <option value="banner" <?= $esEdicion && $alerta['plantilla'] == 'banner' ? 'selected' : '' ?>>Banner (Superior)</option>
                        <option value="card" <?= $esEdicion && $alerta['plantilla'] == 'card' ? 'selected' : '' ?>>Card (Flotante)</option>
                        <option value="personalizado" <?= $esEdicion && $alerta['plantilla'] == 'personalizado' ? 'selected' : '' ?>>Personalizado (HTML)</option>
                        <option value="tour_guido" <?= $esEdicion && $alerta['plantilla'] == 'tour_guido' ? 'selected' : '' ?>>Tour Guiado (Driver.js)</option>
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Tipo de mostrar: <span class="text-danger">*</span></label>
                    <select class="form-control input_alerta" id="tipo_mostrar_alerta" required>
                        <option value="una_vez" <?= $esEdicion && $alerta['tipo_mostrar'] == 'una_vez' ? 'selected' : '' ?>>Una vez</option>
                        <option value="n_veces" <?= $esEdicion && $alerta['tipo_mostrar'] == 'n_veces' ? 'selected' : '' ?>>N veces</option>
                        <option value="mientras_activa" <?= $esEdicion && $alerta['tipo_mostrar'] == 'mientras_activa' ? 'selected' : '' ?>>Mientras esté activa</option>
                    </select>
                </div>
            </div>
            <div class="col-md-4" id="contenedor_veces_mostrar" style="display: none;">
                <div class="form-group">
                    <label>Veces a mostrar: <span class="text-danger">*</span></label>
                    <input type="number" class="form-control input_alerta" id="veces_mostrar_alerta" 
                           value="<?= $esEdicion ? $alerta['veces_mostrar'] : '1' ?>" 
                           min="1">
                </div>
            </div>
        </div>
        
        <!-- Fechas -->
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Fecha de inicio:</label>
                    <input type="datetime-local" class="form-control input_alerta" id="fecha_inicio_alerta" 
                           value="<?= $esEdicion && $alerta['fecha_inicio'] ? date('Y-m-d\TH:i', strtotime($alerta['fecha_inicio'])) : '' ?>">
                    <small class="form-text text-muted">Dejar vacío para que esté siempre activa</small>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Fecha de fin:</label>
                    <input type="datetime-local" class="form-control input_alerta" id="fecha_fin_alerta" 
                           value="<?= $esEdicion && $alerta['fecha_fin'] ? date('Y-m-d\TH:i', strtotime($alerta['fecha_fin'])) : '' ?>">
                    <small class="form-text text-muted">Dejar vacío para que no expire</small>
                </div>
            </div>
        </div>
        
        <!-- Configuración de contador de ignorar -->
        <hr class="my-4">
        <h6 class="subtitulo">Configuración de contador de ignorar</h6>
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i> 
            <strong>Nota:</strong> Esta opción permite que la alerta se ignore varias veces antes de mostrarse.
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Veces a ignorar antes de mostrar:</label>
                    <input type="number" class="form-control input_alerta" id="veces_ignorar_alerta" 
                           value="<?= $esEdicion && isset($alerta['veces_ignorar']) ? $alerta['veces_ignorar'] : '0' ?>" 
                           min="0" placeholder="0">
                    <small class="form-text text-muted">
                        Número de veces que se ignorará la alerta antes de mostrarla.<br>
                        <strong>0</strong> = Se muestra inmediatamente (comportamiento normal).<br>
                        <strong>2</strong> = Se ignora 2 veces, se muestra en el tercer intento.
                    </small>
                </div>
            </div>
        </div>
        
        <!-- Módulo específico -->
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label>Módulo donde aparecerá la alerta:</label>
                    <select class="form-control input_alerta select2" id="id_modulo_alerta">
                        <option value="">General (aparece en login)</option>
                        <?php
                        require_once __DIR__ . '/../../../controllers/controller_general.php';
                        $modulos = GeneralController::obtenerTodosModulosController();
                        $modulos_agrupados = [];
                        foreach ($modulos as $modulo) {
                            if (!isset($modulos_agrupados[$modulo['area']])) {
                                $modulos_agrupados[$modulo['area']] = [];
                            }
                            $modulos_agrupados[$modulo['area']][] = $modulo;
                        }
                        foreach ($modulos_agrupados as $area => $mods) {
                            echo '<optgroup label="' . htmlspecialchars($area) . '">';
                            foreach ($mods as $mod) {
                                $selected = ($esEdicion && isset($alerta['id_modulo']) && $alerta['id_modulo'] == $mod['id']) ? 'selected' : '';
                                echo '<option value="' . $mod['id'] . '" ' . $selected . '>' . htmlspecialchars($mod['nombre']) . ' (' . htmlspecialchars($mod['ruta']) . ')</option>';
                            }
                            echo '</optgroup>';
                        }
                        ?>
                    </select>
                    <small class="form-text text-muted">
                        <strong>General:</strong> La alerta aparecerá al hacer login.<br>
                        <strong>Módulo específico:</strong> La alerta aparecerá cuando el usuario entre a ese módulo.
                    </small>
                </div>
            </div>
        </div>
        
        <!-- Roles aplicables -->
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label>Roles aplicables:</label>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="rol_todos" checked>
                        <label class="form-check-label" for="rol_todos">Todos los roles</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input rol-checkbox" type="checkbox" id="rol_administrador" 
                               value="Administrador" <?= $esEdicion && in_array('Administrador', $criterios_rol) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="rol_administrador">Administrador</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input rol-checkbox" type="checkbox" id="rol_propietario" 
                               value="Propietario" <?= $esEdicion && in_array('Propietario', $criterios_rol) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="rol_propietario">Propietario</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input rol-checkbox" type="checkbox" id="rol_barista" 
                               value="Barista" <?= $esEdicion && in_array('Barista', $criterios_rol) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="rol_barista">Barista</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input rol-checkbox" type="checkbox" id="rol_cliente" 
                               value="Cliente" <?= $esEdicion && in_array('Cliente', $criterios_rol) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="rol_cliente">Cliente</label>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- HTML personalizado (solo si plantilla es personalizado) -->
        <div class="row" id="contenedor_html_personalizado" style="display: none;">
            <div class="col-md-12">
                <div class="form-group">
                    <label>HTML Personalizado: <span class="text-danger">*</span></label>
                    <textarea class="form-control input_alerta" id="html_personalizado_alerta" rows="10" 
                              placeholder="Ingresa el HTML completo de la alerta..."><?= $esEdicion ? htmlspecialchars($alerta['html_personalizado']) : '' ?></textarea>
                    <small class="form-text text-muted">HTML completo que se mostrará. Puedes usar variables como ALERTA_ID que se reemplazarán automáticamente.</small>
                </div>
            </div>
        </div>
        
        <!-- Configuración de Tour Guiado (solo si plantilla es tour_guido) -->
        <div class="row" id="contenedor_tour_guido" style="display: none;">
            <div class="col-md-12">
                <div class="form-group">
                    <label>Pasos del Tour: <span class="text-danger">*</span></label>
                    <div id="contenedor_pasos_tour">
                        <?php
                        $pasos_tour = [];
                        if ($esEdicion && $alerta['html_personalizado']) {
                            $pasos_tour = json_decode($alerta['html_personalizado'], true);
                            if (!is_array($pasos_tour)) {
                                $pasos_tour = [];
                            }
                        }
                        if (count($pasos_tour) > 0):
                            foreach ($pasos_tour as $index => $paso):
                        ?>
                            <div class="paso-tour-item mb-3 p-3 border rounded" data-index="<?= $index ?>">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h6 class="mb-0">Paso <?= $index + 1 ?></h6>
                                    <button type="button" class="btn btn-sm btn-danger eliminar-paso-tour">Eliminar</button>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <label>Módulo/Página:</label>
                                        <select class="form-control paso-modulo">
                                            <option value="">Selecciona módulo...</option>
                                            <?php
                                            require_once __DIR__ . '/../../../controllers/controller_general.php';
                                            $modulos = GeneralController::obtenerTodosModulosController();
                                            $modulos_agrupados = [];
                                            foreach ($modulos as $modulo) {
                                                if (!isset($modulos_agrupados[$modulo['area']])) {
                                                    $modulos_agrupados[$modulo['area']] = [];
                                                }
                                                $modulos_agrupados[$modulo['area']][] = $modulo;
                                            }
                                            foreach ($modulos_agrupados as $area => $mods) {
                                                echo '<optgroup label="' . htmlspecialchars($area) . '">';
                                                foreach ($mods as $mod) {
                                                    $selected = (isset($paso['modulo']) && $paso['modulo'] == $mod['ruta']) ? 'selected' : '';
                                                    echo '<option value="' . htmlspecialchars($mod['ruta']) . '" ' . $selected . '>' . htmlspecialchars($mod['nombre']) . '</option>';
                                                }
                                                echo '</optgroup>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label>Selector CSS del elemento:</label>
                                        <input type="text" class="form-control paso-selector" 
                                               placeholder="Ej: #boton-actualizar, .mi-clase, [data-id='123']" 
                                               value="<?= htmlspecialchars($paso['selector'] ?? '') ?>">
                                        <small class="form-text text-muted">Selector CSS del elemento a destacar</small>
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-md-12">
                                        <label>Título del paso:</label>
                                        <input type="text" class="form-control paso-titulo" 
                                               placeholder="Ej: Activa tus categorías" 
                                               value="<?= htmlspecialchars($paso['titulo'] ?? '') ?>">
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-md-12">
                                        <label>Descripción/Mensaje:</label>
                                        <textarea class="form-control paso-descripcion" rows="2" 
                                                  placeholder="Explicación detallada del paso..."><?= htmlspecialchars($paso['descripcion'] ?? '') ?></textarea>
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-md-6">
                                        <label>Posición del popup:</label>
                                        <select class="form-control paso-posicion">
                                            <option value="top" <?= (isset($paso['posicion']) && $paso['posicion'] == 'top') ? 'selected' : '' ?>>Arriba</option>
                                            <option value="bottom" <?= (isset($paso['posicion']) && $paso['posicion'] == 'bottom') ? 'selected' : '' ?>>Abajo</option>
                                            <option value="left" <?= (isset($paso['posicion']) && $paso['posicion'] == 'left') ? 'selected' : '' ?>>Izquierda</option>
                                            <option value="right" <?= (isset($paso['posicion']) && $paso['posicion'] == 'right') ? 'selected' : '' ?>>Derecha</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check mt-4">
                                            <input class="form-check-input paso-redirigir" type="checkbox" 
                                                   <?= (isset($paso['redirigir']) && $paso['redirigir']) ? 'checked' : '' ?>>
                                            <label class="form-check-label">Redirigir automáticamente a esta página</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php
                            endforeach;
                        endif;
                        ?>
                    </div>
                    <button type="button" class="btn btn-sm btn-primary mt-2" id="agregar_paso_tour">
                        <i class="fas fa-plus"></i> Agregar Paso
                    </button>
                    <small class="form-text text-muted d-block mt-2">
                        <strong>Nota:</strong> El tour se ejecutará en el orden de los pasos. Si un paso tiene "Redirigir automáticamente", 
                        el sistema navegará a esa página antes de mostrar el paso.
                    </small>
                </div>
            </div>
        </div>
        
        <!-- CSS personalizado -->
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label>CSS Personalizado:</label>
                    <textarea class="form-control input_alerta" id="estilo_personalizado_alerta" rows="5" 
                              placeholder="Estilos CSS adicionales..."><?= $esEdicion ? htmlspecialchars($alerta['estilo_personalizado']) : '' ?></textarea>
                </div>
            </div>
        </div>
        
        <!-- Botones configurables -->
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label>Botones configurables:</label>
                    <div id="contenedor_botones">
                        <?php if ($esEdicion && count($botones) > 0): ?>
                            <?php foreach ($botones as $index => $boton): ?>
                                <div class="boton-item mb-3 p-3 border rounded">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label>Texto:</label>
                                            <input type="text" class="form-control boton-texto" value="<?= htmlspecialchars($boton['texto']) ?>">
                                        </div>
                                        <div class="col-md-3">
                                            <label>URL:</label>
                                            <input type="text" class="form-control boton-url" value="<?= htmlspecialchars($boton['url'] ?? '') ?>">
                                        </div>
                                        <div class="col-md-2">
                                            <label>Tipo:</label>
                                            <select class="form-control boton-tipo">
                                                <option value="primary" <?= ($boton['tipo'] ?? 'primary') == 'primary' ? 'selected' : '' ?>>Primary</option>
                                                <option value="secondary" <?= ($boton['tipo'] ?? '') == 'secondary' ? 'selected' : '' ?>>Secondary</option>
                                                <option value="success" <?= ($boton['tipo'] ?? '') == 'success' ? 'selected' : '' ?>>Success</option>
                                                <option value="danger" <?= ($boton['tipo'] ?? '') == 'danger' ? 'selected' : '' ?>>Danger</option>
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label>&nbsp;</label>
                                            <button type="button" class="btn btn-danger btn-sm w-100 eliminar-boton">Eliminar</button>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-primary mt-2" id="agregar_boton">+ Agregar botón</button>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mt-3">
        <div class="col-md-12 text-center">
            <button type="submit" class="btn btn-primary">Guardar alerta</button>
            <a href="<?= $url . 'admin_alertas' ?>" class="btn btn-secondary">Cancelar</a>
        </div>
    </div>
</form>