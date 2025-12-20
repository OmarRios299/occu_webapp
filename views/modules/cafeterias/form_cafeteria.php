<?php
// Archivo unificado para agregar y editar cafeterías
// Determina el modo basado en si existe $cafeteria

$modoEdicion = isset($cafeteria) && !empty($cafeteria);
$idCafeteria = $modoEdicion ? $cafeteria['id'] : '';
$tituloBreadcrumb = $modoEdicion ? 'Editar cafetería' : 'Nueva cafetería';
$tituloFormulario = $modoEdicion ? 'Editar información de Cafetería' : 'Agregar información de Cafetería';
?>

<div class="titulo-boton <?= $modoEdicion ? '' : 'mt-4' ?>">
    <h1 class="titulo-modulo">Cafeterías</h1>
    <?php if ($modoEdicion): ?>
        <a class="btn btn-agregar con-icono" href='<?= $url . 'cafeterias/agregar/' . $action[1] . '/imagenes/.' ?>'>Agregar imagenes</a>
    <?php endif; ?>
</div>

<div class="my-<?= $modoEdicion ? '5' : '4' ?>">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb breadcrumb-chevron p-3 bg-body-tertiary rounded-3">
            <li class="breadcrumb-item">
                <a class="link-body-emphasis" href="<?= $url . 'cafeterias' ?>">
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

<style>
/* Estilos para formulario de cafetería mejorado */
.form-section {
    background: #fff;
    border-radius: 20px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    padding: 2rem;
    margin-bottom: 2rem;
    transition: all 0.3s ease;
}

.form-section:hover {
    box-shadow: 0 8px 30px rgba(0,0,0,0.12);
}

.section-header {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 2rem;
    padding-bottom: 1rem;
    border-bottom: 2px solid var(--bg-body);
}

.section-header-icon {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, var(--principal) 0%, #e65a4a 100%);
    color: white;
    font-size: 1.5rem;
}

.section-header-content h3 {
    margin: 0;
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--cuarto);
}

.section-header-content p {
    margin: 0.25rem 0 0 0;
    font-size: 0.9rem;
    color: #666;
}

.form-group-modern {
    margin-bottom: 1.5rem;
}

.form-group-modern label {
    font-weight: 600;
    color: var(--cuarto);
    margin-bottom: 0.5rem;
    display: block;
    font-size: 0.95rem;
}

.form-group-modern label .required {
    color: var(--principal);
    margin-left: 0.25rem;
}

.form-control-modern {
    border: 2px solid #e0e0e0;
    border-radius: 12px;
    padding: 0.75rem 1rem;
    font-size: 1rem;
    transition: all 0.3s ease;
    width: 100%;
}

.form-control-modern:focus {
    border-color: var(--principal);
    box-shadow: 0 0 0 4px rgba(241, 108, 91, 0.1);
    outline: none;
}

.form-control-modern::placeholder {
    color: #999;
}

.select-modern {
    border: 2px solid #e0e0e0;
    border-radius: 12px;
    padding: 0.75rem 1rem;
    font-size: 1rem;
    transition: all 0.3s ease;
    background-color: #fff;
}

.select-modern:focus {
    border-color: var(--principal);
    box-shadow: 0 0 0 4px rgba(241, 108, 91, 0.1);
    outline: none;
}

/* Paso 1: Información Básica */
.paso-1-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 2rem;
}

.paso-1-item-nombre {
    grid-column: 1 / -1;
}

.paso-1-item-logo {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}

        .logo-preview-container {
            width: 100%;
            max-width: 200px;
            height: 200px;
            border-radius: 16px;
            overflow: hidden;
            border: 3px dashed #ddd;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f8f9fa;
            transition: all 0.3s ease;
            cursor: pointer;
            position: relative;
            margin: 0 auto;
        }

.logo-preview-container:hover {
    border-color: var(--principal);
    background: #fff;
}

.logo-preview-container img {
    width: 100% !important;
    height: 100% !important;
    object-fit: cover !important;
    position: absolute;
    top: 0;
    left: 0;
    z-index: 2;
}

.logo-preview-placeholder {
    text-align: center;
    color: #999;
    padding: 1rem;
    position: relative;
    z-index: 1;
}

.logo-preview-placeholder i {
    font-size: 3rem;
    margin-bottom: 0.5rem;
    display: block;
}

.logo-upload-btn {
    margin-top: 1rem;
    padding: 0.75rem 2rem;
    border-radius: 12px;
    font-weight: 600;
    transition: all 0.3s ease;
}

        .logo-upload-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        
        /* Estilos para tabla de horarios */
        .table-hover tbody tr:hover {
            background-color: #f8f9fa;
            transition: background-color 0.2s ease;
        }
        
        .table-hover tbody tr td {
            padding: 1rem;
            vertical-align: middle;
        }
        
        .table-hover thead th {
            padding: 1rem;
            font-weight: 600;
            border-bottom: 2px solid #dee2e6;
        }

        /* Responsive */
@media (max-width: 768px) {
    .paso-1-grid {
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }
    
    .form-section {
        padding: 1.5rem;
    }
    
    .section-header {
        flex-direction: column;
        text-align: center;
    }
}
</style>

<h6 class="subtitulo mt-3"><?= $tituloFormulario ?></h6>
<form onsubmit="return false;" id="form_agregar_cafeteria">
    <input type="hidden" id="id_cafeteria" value="<?= $idCafeteria ?>">
    
    <!-- Paso 1: Información Básica (Nombre, País, Ciudad, Logo) -->
    <div class="form-section">
        <div class="section-header">
            <div class="section-header-icon">
                <i class="bi bi-info-circle-fill"></i>
            </div>
            <div class="section-header-content">
                <h3>Información Básica</h3>
                <p>Datos principales de tu cafetería</p>
            </div>
        </div>
        
        <div class="paso-1-grid">
            <!-- Nombre de la cafetería -->
            <div class="paso-1-item-nombre">
                <div class="form-group-modern">
                    <label>
                        <i class="bi bi-shop me-2"></i>
                        Nombre de la Cafetería
                        <span class="required">*</span>
                    </label>
                    <input type="text" 
                           class="form-control form-control-modern input_usuario" 
                           id="nombre_cafeteria" 
                           required 
                           placeholder="Ej: Café del Centro, Starbucks, etc."
                           value="<?= $modoEdicion ? htmlspecialchars($cafeteria['nombre']) : '' ?>">
                </div>
            </div>
            
            <!-- País -->
            <div class="form-group-modern">
                <label>
                    <i class="bi bi-globe me-2"></i>
                    País
                    <span class="required">*</span>
                </label>
                <?php if ($modoEdicion): ?>
                    <input type="text" 
                           class="form-control form-control-modern" 
                           id="pais_cafeteria" 
                           disabled 
                           value="<?= htmlspecialchars($cafeteria['pais']) ?>">
                <?php else: ?>
                    <select id="pais_select" class="form-select select-modern select_pais select2" disabled>
                        <option value="" disabled>Selecciona un país</option>
                        <?php foreach (GeneralController::obtenerPaisesController() as $pais) {
                            if ($pais['id'] == $_SESSION['id_pais']) {
                        ?>
                            <option value="<?= $pais['id'] ?>" selected><?= $pais['nombre'] ?></option>
                        <?php } else { ?>
                            <option value="<?= $pais['id'] ?>"><?= $pais['nombre'] ?></option>
                        <?php }
                        } ?>
                    </select>
                    <input type="hidden" class="form-control select_pais" id="pais_cafeteria" disabled>
                <?php endif; ?>
            </div>
            
            <!-- Ciudad -->
            <div class="form-group-modern">
                <label>
                    <i class="bi bi-geo-alt-fill me-2"></i>
                    Ciudad
                    <span class="required">*</span>
                </label>
                <select id="ciudad_select" class="form-select select-modern select2 <?= $modoEdicion ? '' : 'select_ciudad' ?>">
                    <option value="" disabled>Selecciona una ciudad</option>
                    <?php 
                    $ciudades = $modoEdicion 
                        ? CafeteriasController::obtenerCiudadesPaisController($_SESSION['pais'])
                        : GeneralController::obtenerCiudadesController();
                    
                    foreach ($ciudades as $ciudad) {
                        $selected = '';
                        $coordenadasAttr = '';
                        
                        if ($modoEdicion && $cafeteria['id_ciudad'] == $ciudad['id']) {
                            $selected = 'selected';
                        } else if (!$modoEdicion && $_SESSION['ciudad'] == $ciudad['id']) {
                            $selected = 'selected';
                        }
                        
                        if (isset($ciudad['coordenadas'])) {
                            $coordenadasAttr = htmlspecialchars($ciudad['coordenadas']);
                        }
                        
                        $paisAttr = isset($ciudad['pais']) ? $ciudad['pais'] : (isset($ciudad['id_pais']) ? $ciudad['id_pais'] : '');
                    ?>
                        <option value='<?= $ciudad['id'] ?>' <?= $selected ?> coordenadas='<?= $coordenadasAttr ?>' pais='<?= $paisAttr ?>'><?= htmlspecialchars($ciudad['nombre']) ?></option>
                    <?php } ?>
                </select>
            </div>
            
            <!-- Logo -->
            <div class="paso-1-item-logo">
                <div class="form-group-modern">
                    <label>
                        <i class="bi bi-image me-2"></i>
                        Logo de la Cafetería
                        <span style="color: #999; font-weight: normal; font-size: 0.85rem;">(opcional)</span>
                    </label>
                    <div class="logo-preview-container" style="cursor: pointer;" onclick="document.getElementById('imagen_cafeteria').click();">
                        <?php if ($modoEdicion && isset($cafeteria['imagen']) && $cafeteria['imagen']): ?>
                            <img src="<?= $url . $cafeteria['imagen'] ?>" id="imagen_previsualizar" alt="Logo" style="pointer-events: none; z-index: 2;">
                            <div class="logo-preview-placeholder" style="display: none; position: relative; z-index: 1;">
                                <i class="bi bi-image"></i>
                                <div>Click para subir logo</div>
                                <small style="font-size: 0.75rem;">PNG, JPG hasta 5MB</small>
                            </div>
                        <?php else: ?>
                            <!-- En modo agregar, mostrar imagen por defecto visible (similar a modo edición) -->
                            <img src="../views/assets/img/cafeteria_default.png" id="imagen_previsualizar" style="pointer-events: none; z-index: 2;" alt="Logo">
                            <div class="logo-preview-placeholder" style="display: none; position: relative; z-index: 1;">
                                <i class="bi bi-image"></i>
                                <div>Click para subir logo</div>
                                <small style="font-size: 0.75rem;">PNG, JPG hasta 5MB</small>
                            </div>
                        <?php endif; ?>
                    </div>
                    <input type="file" 
                           class="d-none input_usuario imagenPrevisualizar validarImagen" 
                           id="imagen_cafeteria" 
                           lang="esp"
                           accept="image/*">
                    <button type="button" 
                            class="btn btn-outline-primary logo-upload-btn" 
                            onclick="$('#imagen_cafeteria').click();">
                        <i class="bi bi-upload me-2"></i>
                        Seleccionar Imagen
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Paso 2: Ubicación -->
    <div class="form-section">
        <div class="section-header">
            <div class="section-header-icon" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%);">
                <i class="bi bi-geo-alt-fill"></i>
            </div>
            <div class="section-header-content">
                <h3>Ubicación</h3>
                <p>Selecciona la ubicación exacta de tu cafetería en el mapa</p>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-12 mb-3">
                <button type="button" 
                        class="btn btn-success btn-lg w-100" 
                        id="btn_seleccionar_ubicacion"
                        style="padding: 1rem; border-radius: 12px; font-weight: 600;">
                    <i class="bi bi-map-fill me-2"></i>
                    Seleccionar Ubicación en el Mapa
                </button>
            </div>
            <div class="col-md-12">
                <div class="form-group-modern">
                    <label>
                        <i class="bi bi-signpost-split me-2"></i>
                        Dirección
                        <span class="required">*</span>
                    </label>
                    <input type="text" 
                           class="form-control form-control-modern input_usuario" 
                           id="direccion_cafeteria" 
                           placeholder="Primero selecciona una ubicación en el mapa" 
                           required 
                           value="<?= $modoEdicion ? htmlspecialchars($cafeteria['direccion']) : '' ?>">
                    <input type="hidden" id="latitud_cafeteria" value="<?= $modoEdicion ? $cafeteria['latitud'] : '' ?>">
                    <input type="hidden" id="longitud_cafeteria" value="<?= $modoEdicion ? $cafeteria['longitud'] : '' ?>">
                </div>
            </div>
        </div>
    </div>
    
    <!-- Paso 3: Información Adicional -->
    <div class="form-section">
        <div class="section-header">
            <div class="section-header-icon" style="background: linear-gradient(135deg, #6f42c1 0%, #e83e8c 100%);">
                <i class="bi bi-card-text"></i>
            </div>
            <div class="section-header-content">
                <h3>Información Adicional</h3>
                <p>Cuéntanos más sobre tu cafetería</p>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-12">
                <div class="form-group-modern">
                    <label>
                        <i class="bi bi-text-paragraph me-2"></i>
                        Descripción
                    </label>
                    <textarea class="form-control form-control-modern" 
                              cols="30" 
                              rows="4" 
                              id="descripcion_cafeteria" 
                              placeholder="Una breve descripción de la cafetería y su ambiente acogedor. ¿Qué hace especial a tu cafetería?"><?= $modoEdicion ? htmlspecialchars($cafeteria['descripcion']) : '' ?></textarea>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Paso 4: Horarios y Contacto -->
    <div class="form-section">
        <div class="section-header">
            <div class="section-header-icon" style="background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);">
                <i class="bi bi-clock-history"></i>
            </div>
            <div class="section-header-content">
                <h3>Horarios y Contacto</h3>
                <p>Define los horarios de atención y medios de contacto</p>
            </div>
        </div>
        
        <div class="row">
            <!-- Switch de horario único -->
            <div class="col-md-12 mb-4">
                <div class="card border-0" style="background: #f8f9fa; border-radius: 12px; padding: 1.5rem;">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h5 class="mb-1">
                                <i class="bi bi-calendar-check me-2"></i>
                                Mismo horario todos los días
                            </h5>
                            <p class="text-muted mb-0" style="font-size: 0.9rem;">
                                Activa esta opción si tu cafetería tiene el mismo horario todos los días
                            </p>
                        </div>
                        <?php 
                        $checkedHorario = '';
                        if ($modoEdicion) {
                            $checkedHorario = ($cafeteria['horario_diferente'] != 'SI') ? 'checked' : '';
                        } else {
                            $checkedHorario = 'checked';
                        }
                        ?>
                        <div class="form-check form-switch" style="font-size: 1.5rem;">
                            <input class="form-check-input" 
                                   type="checkbox" 
                                   id="switch_horario" 
                                   style="width: 3rem; height: 1.5rem; cursor: pointer;" 
                                   <?= $checkedHorario ?>>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Horarios simples (cuando el switch está activo) -->
            <div class="inp_horario">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group-modern">
                            <label>
                                <i class="bi bi-clock-fill me-2"></i>
                                Horario de Apertura
                            </label>
                            <input type="time" 
                                   class="form-control form-control-modern <?= $modoEdicion ? 'inp_horario' : '' ?>" 
                                   id="horario_apertura_cafeteria" 
                                   value="<?= $modoEdicion ? htmlspecialchars($cafeteria['horario_apertura']) : '' ?>">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group-modern">
                            <label>
                                <i class="bi bi-clock-history me-2"></i>
                                Horario de Cierre
                            </label>
                            <input type="time" 
                                   class="form-control form-control-modern <?= $modoEdicion ? 'inp_horario' : '' ?>" 
                                   id="horario_cierre_cafeteria" 
                                   value="<?= $modoEdicion ? htmlspecialchars($cafeteria['horario_cierre']) : '' ?>">
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Información de contacto -->
            <div class="col-md-6 mt-3">
                <div class="form-group-modern">
                    <label>
                        <i class="bi bi-telephone-fill me-2"></i>
                        Teléfono
                        <span style="color: #999; font-weight: normal; font-size: 0.85rem;">(opcional)</span>
                    </label>
                    <input type="number" 
                           class="form-control form-control-modern input_usuario" 
                           id="telefono_cafeteria" 
                           placeholder="Ej: 5551234567"
                           value="<?= $modoEdicion ? htmlspecialchars($cafeteria['telefono']) : '' ?>">
                </div>
            </div>
            <div class="col-md-6 mt-3">
                <div class="form-group-modern">
                    <label>
                        <i class="bi bi-envelope-fill me-2"></i>
                        Correo Electrónico
                        <span style="color: #999; font-weight: normal; font-size: 0.85rem;">(opcional)</span>
                    </label>
                    <input type="email" 
                           class="form-control form-control-modern input_usuario validarCampo<?= $modoEdicion ? 'Editar' : '' ?>" 
                           id="correo_cafeteria" 
                           placeholder="contacto@tucafeteria.com"
                           value="<?= $modoEdicion ? htmlspecialchars($cafeteria['correo_electronico']) : '' ?>"
                           <?= $modoEdicion ? 'idRegistro="' . $cafeteria['id'] . '"' : '' ?>
                           columna='correo_electronico' tabla='cafeterias' mensaje='Este correo ya se encuetra registrado'>
                    <div class="invalid-feedback" style="display: none;"></div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Paso 5: Horarios por día (cuando el switch está desactivado) -->
    <div class="form-section tbl_horario" style="display: none;">
        <div class="section-header">
            <div class="section-header-icon" style="background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);">
                <i class="bi bi-calendar-week"></i>
            </div>
            <div class="section-header-content">
                <h3>Horarios por Día</h3>
                <p>Configura horarios específicos para cada día de la semana</p>
            </div>
        </div>
        
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th style="border-radius: 12px 0 0 0;">
                            <i class="bi bi-calendar3 me-2"></i>
                            Día
                        </th>
                        <th>
                            <i class="bi bi-toggle-on me-2"></i>
                            Desbloquear
                        </th>
                        <th>
                            <i class="bi bi-clock-fill me-2"></i>
                            Hora de Apertura
                        </th>
                        <th style="border-radius: 0 12px 0 0;">
                            <i class="bi bi-clock-history me-2"></i>
                            Hora de Cierre
                        </th>
                    </tr>
                </thead>
            <tbody>
                <?php
                // Función para procesar los horarios de cada día (solo en modo edición)
                function procesarHorarioDia($idCafeteria, $diaSemana) {
                    if (!$idCafeteria) {
                        return [
                            'cheked' => '',
                            'disabled' => 'disabled',
                            'hora_apertura' => '',
                            'hora_cierre' => ''
                        ];
                    }
                    
                    $dia = CafeteriasController::buscarHorarioDiferenteController($idCafeteria, $diaSemana);
                    if (!$dia) {
                        return [
                            'cheked' => '',
                            'disabled' => 'disabled',
                            'hora_apertura' => '',
                            'hora_cierre' => ''
                        ];
                    }
                    
                    return [
                        'cheked' => ($dia['cerrado'] != 'SI') ? 'checked' : '',
                        'disabled' => ($dia['cerrado'] == 'SI') ? 'disabled' : '',
                        'hora_apertura' => $dia['hora_apertura'] ?? '',
                        'hora_cierre' => $dia['hora_cierre'] ?? ''
                    ];
                }

                $diasSemana = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
                
                foreach ($diasSemana as $index => $diaSemana):
                    $datosDia = $modoEdicion ? procesarHorarioDia($idCafeteria, $diaSemana) : [
                        'cheked' => $index === 0 ? 'checked' : '', // Solo Lunes marcado por defecto en modo agregar
                        'disabled' => $index === 0 ? '' : 'disabled',
                        'hora_apertura' => '',
                        'hora_cierre' => ''
                    ];
                ?>
                    <tr>
                        <td class="align-middle">
                            <strong><?= $diaSemana ?></strong>
                        </td>
                        <td class="align-middle">
                            <div class="form-check form-switch d-flex justify-content-center">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       id="switch_<?= strtolower($diaSemana) ?>" 
                                       name="desbloquear_<?= strtolower($diaSemana) ?>" 
                                       onclick="toggleFields(this, '<?= $diaSemana ?>')" 
                                       style="width: 3rem; height: 1.5rem; cursor: pointer;"
                                       <?= $datosDia['cheked'] ?>>
                            </div>
                        </td>
                        <td>
                            <input type="time" 
                                   class="form-control form-control-modern" 
                                   name="hora_apertura_<?= strtolower($diaSemana) ?>" 
                                   id="hora_apertura_<?= strtolower($diaSemana) ?>" 
                                   <?= $datosDia['disabled'] ?> 
                                   value="<?= htmlspecialchars($datosDia['hora_apertura']) ?>">
                        </td>
                        <td>
                            <input type="time" 
                                   class="form-control form-control-modern" 
                                   name="hora_cierre_<?= strtolower($diaSemana) ?>" 
                                   id="hora_cierre_<?= strtolower($diaSemana) ?>" 
                                   <?= $datosDia['disabled'] ?> 
                                   value="<?= htmlspecialchars($datosDia['hora_cierre']) ?>">
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Botón de envío -->
    <div class="row mt-4 mb-4">
        <div class="col-md-12 text-center">
            <button type="submit" class="btn btn-primary btn-lg" style="padding: 1rem 3rem; border-radius: 12px; font-weight: 600; font-size: 1.1rem;">
                <i class="bi bi-check-circle-fill me-2"></i>
                <?= $modoEdicion ? 'Actualizar Cafetería' : 'Crear Cafetería' ?>
            </button>
        </div>
    </div>
</form>
<br>
<div class="modal fade" id="modal_ubicacion" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalLabel">Selecciona un ubicación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="map" style="height: 500px; width: 100%;"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="btn_guardar_ubicacion">Aceptar</button>
            </div>
        </div>
    </div>
</div>

<script>
// Mejorar la previsualización de imagen para el nuevo diseño
$(document).ready(function() {
    // Interceptar el cambio de imagen para actualizar el contenedor
    $(document).on('change', '#imagen_cafeteria', function() {
        let imagen = this.files[0];
        
        if (imagen && (imagen.type == "image/jpeg" || imagen.type == "image/png" || imagen.type == "image/jpg")) {
            // La imagen ya está visible, general.js actualiza el src automáticamente
            // Solo actualizamos los estilos del contenedor
            let previewContainer = $(".logo-preview-container");
            
            // Actualizar estilo del contenedor para indicar que hay una imagen seleccionada
            previewContainer.css({
                'border': '3px solid var(--principal)',
                'background': '#fff',
                'border-style': 'solid'
            });
        }
    });
});
</script>

