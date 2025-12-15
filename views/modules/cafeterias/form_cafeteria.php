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

<h6 class="subtitulo mt-3"><?= $tituloFormulario ?></h6>
<form onsubmit="return false;" id="form_agregar_cafeteria">
    <div class="caja">
        <div class="row mt-3">
            <div class="col-md-3">
                <div class="form-group">
                    <input type="hidden" id="id_cafeteria" value="<?= $idCafeteria ?>">
                    <label>Nombre:</label>
                    <input type="text" class="form-control input_usuario" id="nombre_cafeteria" required value="<?= $modoEdicion ? htmlspecialchars($cafeteria['nombre']) : '' ?>">
                </div>
            </div>
            <div class="col-md-3 mt-4">
                <div class="form-group">
                    <button type="button" class="btn con-icono btn-ubicacion" id="btn_seleccionar_ubicacion">Seleccionar ubicación</button>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Confirmar dirección:</label>
                    <input type="text" class="form-control input_usuario" id="direccion_cafeteria" placeholder="Primero selecciona una ubicación" required value="<?= $modoEdicion ? htmlspecialchars($cafeteria['direccion']) : '' ?>">
                    <input type="hidden" id="latitud_cafeteria" value="<?= $modoEdicion ? $cafeteria['latitud'] : '' ?>">
                    <input type="hidden" id="longitud_cafeteria" value="<?= $modoEdicion ? $cafeteria['longitud'] : '' ?>">
                </div>
            </div>
            <div class="col-md-2 text-center">
                <div class="form-group">
                    <img src="<?= $modoEdicion ? $url . $cafeteria['imagen'] : '../views/assets/img/cafeteria_default.png' ?>" style="width:100px;<?= $modoEdicion ? 'height:120px;' : '' ?>" id="imagen_previsualizar">
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label>País:</label>
                    <?php if ($modoEdicion): ?>
                        <input type="text" class="form-control" id="pais_cafeteria" disabled value="<?= htmlspecialchars($cafeteria['pais']) ?>">
                    <?php else: ?>
                        <select id="pais_select" class="form-select select_pais select2" disabled>
                            <option value="" disabled>Selecciona un pais</option>
                            <?php foreach (GeneralController::obtenerPaisesController() as $pais) {
                                if ($pais['id'] == $_SESSION['id_pais']) {
                            ?>
                                <option value="<?= $pais['id'] ?>" selected><?= $pais['nombre'] ?></option>
                            <?php } else { ?>
                                <option value="<?= $pais['id'] ?>"><?= $pais['nombre'] ?></option>
                            <?php }
                            } ?>
                        </select>
                        <input type="" class="form-control select_pais" id="pais_cafeteria" disabled hidden>
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Ciudad:</label>
                    <select id="ciudad_select" class="form-select select2 <?= $modoEdicion ? '' : 'select_ciudad' ?>">
                        <option value="" disabled>Selecciona un ciudad</option>
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
                            
                            // Obtener coordenadas (pueden venir de diferentes fuentes)
                            if (isset($ciudad['coordenadas'])) {
                                $coordenadasAttr = htmlspecialchars($ciudad['coordenadas']);
                            } else if (isset($ciudad['pais'])) {
                                $coordenadasAttr = '';
                            }
                            
                            $paisAttr = isset($ciudad['pais']) ? $ciudad['pais'] : (isset($ciudad['id_pais']) ? $ciudad['id_pais'] : '');
                        ?>
                            <option value='<?= $ciudad['id'] ?>' <?= $selected ?> coordenadas='<?= $coordenadasAttr ?>' pais='<?= $paisAttr ?>'><?= htmlspecialchars($ciudad['nombre']) ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Logo:(opcional)</label>
                </div>
                <div class="input-group">
                    <input type="file" class="form-control input_usuario imagenPrevisualizar validarImagen" id="imagen_cafeteria" lang="esp">
                    <button class="btn btn-outline-secondary" type="button">Subir</button>
                </div>
            </div>
            <div class="col-md-12 mt-3">
                <div class="form-group">
                    <label>Descripción:</label>
                    <textarea class="form-control" cols="30" rows="2" id="descripcion_cafeteria" placeholder="Una breve descripción de la cafetería y su ambiente acogedor."><?= $modoEdicion ? htmlspecialchars($cafeteria['descripcion']) : '' ?></textarea>
                </div>
            </div>
        </div>
        <div class="row mt-<?= $modoEdicion ? '5' : '4' ?>">
            <div class="col-md-4">
                <div class="row">
                    <div class="col-md-12 text-center">
                        <label for="">Mismo horario cada día<br>(abierto todos los días)</label>
                    </div>
                    <div class="col-md-12 d-flex justify-content-center">
                        <?php 
                        $checkedHorario = '';
                        if ($modoEdicion) {
                            $checkedHorario = ($cafeteria['horario_diferente'] != 'SI') ? 'checked' : '';
                        } else {
                            $checkedHorario = 'checked';
                        }
                        ?>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="switch_horario" name="" <?= $checkedHorario ?>>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Teléfono:(opcional)</label>
                    <input type="number" class="form-control input_usuario" id="telefono_cafeteria" value="<?= $modoEdicion ? htmlspecialchars($cafeteria['telefono']) : '' ?>">
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Correo electrónico:(opcional)</label>
                    <input type="email" class="form-control input_usuario validarCampo<?= $modoEdicion ? 'Editar' : '' ?>" id="correo_cafeteria" 
                           value="<?= $modoEdicion ? htmlspecialchars($cafeteria['correo_electronico']) : '' ?>"
                           <?= $modoEdicion ? 'idRegistro="' . $cafeteria['id'] . '"' : '' ?>
                           columna='correo_electronico' tabla='cafeterias' mensaje='Este correo ya se encuetra registrado'>
                    <div class="invalid-feedback" style="display: none;"></div>
                </div>
            </div>
        </div>
        <div class="row mt-4 inp_horario">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Horario de apertura:</label>
                    <input type="time" class="form-control <?= $modoEdicion ? 'inp_horario' : '' ?>" id="horario_apertura_cafeteria" value="<?= $modoEdicion ? htmlspecialchars($cafeteria['horario_apertura']) : '' ?>">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Horario de cierre:</label>
                    <input type="time" class="form-control <?= $modoEdicion ? 'inp_horario' : '' ?>" id="horario_cierre_cafeteria" value="<?= $modoEdicion ? htmlspecialchars($cafeteria['horario_cierre']) : '' ?>">
                </div>
            </div>
        </div>
    </div>
    <h6 class="subtitulo mt-3 tbl_horario" style="display: none;">Tabla agregar horarios</h6>
    <div class="caja tbl_horario" style="display: none;">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Día</th>
                    <th>Desbloquear</th>
                    <th>Hora de Apertura</th>
                    <th>Hora de Cierre</th>
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
                        <td><?= $diaSemana ?></td>
                        <td>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="switch_<?= strtolower($diaSemana) ?>" name="desbloquear_<?= strtolower($diaSemana) ?>" onclick="toggleFields(this, '<?= $diaSemana ?>')" <?= $datosDia['cheked'] ?>>
                            </div>
                        </td>
                        <td><input type="time" class="form-control" name="hora_apertura_<?= strtolower($diaSemana) ?>" id="hora_apertura_<?= strtolower($diaSemana) ?>" <?= $datosDia['disabled'] ?> value="<?= htmlspecialchars($datosDia['hora_apertura']) ?>"></td>
                        <td><input type="time" class="form-control" name="hora_cierre_<?= strtolower($diaSemana) ?>" id="hora_cierre_<?= strtolower($diaSemana) ?>" <?= $datosDia['disabled'] ?> value="<?= htmlspecialchars($datosDia['hora_cierre']) ?>"></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="row mt-3">
        <div class="col-md-12 text-center">
            <button type="submit" class="btn btn-primary">Aceptar</button>
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

