<div class="titulo-boton">
    <h1 class="titulo-modulo">Cafeterías</h1>
    <a class="btn btn-agregar con-icono" href='<?= $url . 'cafeterias/agregar/' . $action[1] . '/imagenes/.' ?>'>Agregar imagenes</a>
</div>

<div class="my-5">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb breadcrumb-chevron p-3 bg-body-tertiary rounded-3">
            <li class="breadcrumb-item">
                <a class="link-body-emphasis" href="<?= $url . 'cafeterias' ?>">
                    <i class="fas fa-home" style="color: black;"></i>
                    <span class="visually-hidden">Home</span>
                </a>
            </li>
            <!-- <li class="breadcrumb-item">
        <a class="link-body-emphasis fw-semibold text-decoration-none" href="#">Library</a>
      </li> -->
            <li class="breadcrumb-item active" aria-current="page">
                Nueva cafeterías
            </li>
        </ol>
    </nav>
</div>

<h6 class="subtitulo mt-3">Agregar información de Cafetería</h6>
<form onsubmit="return false;" id="form_agregar_cafeteria">
    <div class="caja">
        <div class="row mt-3">
            <div class="col-md-3">
                <div class="form-group">
                    <label>Nombre:</label>
                    <input type="text" class="form-control input_usuario" id="nombre_cafeteria" required value="<?= $cafeteria['nombre'] ?>">
                    <input type="hidden" id="id_cafeteria" value="<?= $cafeteria['id'] ?>">
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
                    <input type="text" class="form-control input_usuario" id="direccion_cafeteria" placeholder="Primero selecciona una ubicación" required value="<?= $cafeteria['direccion'] ?>">
                    <input type="hidden" id="latitud_cafeteria" value="<?= $cafeteria['latitud'] ?>">
                    <input type="hidden" id="longitud_cafeteria" value="<?= $cafeteria['longitud'] ?>">
                </div>
            </div>
            <div class="col-md-2 text-center">
                <div class="form-group">
                    <img src="<?= $url . $cafeteria['imagen'] ?>" style="width:100px;heigth:120px;" id="imagen_previsualizar">
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label>País:</label>
                    <input type="" class="form-control" id="pais_cafeteria" disabled id="" value="<?= $cafeteria['pais'] ?>">
                </div>
            </div>
            <div class="col-md-4">
                <label>Ciudad:</label>
                <select id="ciudad_select" class="form-select select2">
                    <option value="" disabled>Selecciona un ciudad</option>
                    <?php foreach (CafeteriasController::obtenerCiudadesPaisController($_SESSION['pais']) as $ciudad) {
                        if ($cafeteria['id_ciudad'] == $ciudad['id']) {
                    ?>
                            <option value='<?= $ciudad['id'] ?>' selected coordenadas='<?= $ciudad['coordenadas'] ?>' pais='<?= $ciudad['pais'] ?>'><?= $ciudad['nombre'] ?></option>
                        <?php
                        } else {
                        ?>
                            <option value='<?= $ciudad['id'] ?>' coordenadas='<?= $ciudad['coordenadas'] ?>' pais='<?= $ciudad['pais'] ?>'><?= $ciudad['nombre'] ?></option>
                    <?php }
                    } ?>
                </select>
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
        </div>
        <div class="row mt-5">
            <div class="col-md-4">
                <div class="row">
                    <div class="col-md-12 text-center">
                        <label for="">Mismo horario cada día<br>(abierto todos los días)</label>
                    </div>
                    <div class="col-md-12 d-flex justify-content-center">
                        <?php $cheked = ($cafeteria['horario_diferente'] != 'SI') ? 'checked' : '' ?>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="switch_horario" name="" <?= $cheked ?>>
                        </div>
                    </div>
                </div>

            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Teléfono:(opcional)</label>
                    <input type="number" class="form-control input_usuario" id="telefono_cafeteria" value="<?= $cafeteria['telefono'] ?>">
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Correo electrónico:(opcional)</label>
                    <input type="email" class="form-control input_usuario validarCampoEditar" id="correo_cafeteria" value="<?= $cafeteria['correo_electronico'] ?>" idRegistro='<?= $cafeteria['id'] ?>' columna='correo_electronico' tabla='cafeterias' mensaje='Este correo ya se encuetra registrado'>
                    <div class="invalid-feedback" style="display: none;"></div>
                </div>
            </div>

        </div>
        <div class="row mt-4 inp_horario">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Horario de apertura:</label>
                    <input type="time" class="form-control inp_horario" id="horario_apertura_cafeteria" value="<?= $cafeteria['horario_apertura'] ?>">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Horario de cierre:</label>
                    <input type="time" class="form-control inp_horario" id="horario_cierre_cafeteria" value="<?= $cafeteria['horario_cierre'] ?>">
                </div>
            </div>
        </div>
    </div>

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
                    <button type="submit" class="btn btn-primary" id="btn_guardar_ubicacion" data-bs-dismiss="modal">Aceptar</button>
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
            <?php
            // Función para procesar los horarios de cada día
            function procesarHorarioDia($action, $diaSemana)
            {
                $dia = CafeteriasController::buscarHorarioDiferenteController($action[1], $diaSemana);

                // Validar si no hay registros
                if (!$dia) {
                    $cheked = ''; // Sin valores predeterminados
                    $disabled = 'disabled'; // Deshabilitado si no hay registro
                    $dia['hora_apertura'] = ''; // Valor por defecto
                    $dia['hora_cierre'] = '';   // Valor por defecto
                } else {
                    $cheked = ($dia['cerrado'] != 'SI') ? 'checked' : '';
                    $disabled = ($dia['cerrado'] == 'SI') ? 'disabled' : '';
                }

                // Retornamos un array con la información
                return [
                    'cheked' => $cheked,
                    'disabled' => $disabled,
                    'hora_apertura' => $dia['hora_apertura'],
                    'hora_cierre' => $dia['hora_cierre']
                ];
            }

            // Array con los días de la semana
            $diasSemana = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
            ?>

            <tbody>
                <?php foreach ($diasSemana as $diaSemana):
                    // Obtener datos del día actual
                    $datosDia = procesarHorarioDia($action, $diaSemana);
                ?>
                    <tr>
                        <td><?= $diaSemana ?></td>
                        <td>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="switch_<?= strtolower($diaSemana) ?>" name="desbloquear_<?= strtolower($diaSemana) ?>" onclick="toggleFields(this, '<?= $diaSemana ?>')" <?= $datosDia['cheked'] ?>>
                            </div>
                        </td>
                        <td><input type="time" class="form-control" name="hora_apertura_<?= strtolower($diaSemana) ?>" id="hora_apertura_<?= strtolower($diaSemana) ?>" <?= $datosDia['disabled'] ?> value="<?= $datosDia['hora_apertura'] ?>"></td>
                        <td><input type="time" class="form-control" name="hora_cierre_<?= strtolower($diaSemana) ?>" id="hora_cierre_<?= strtolower($diaSemana) ?>" <?= $datosDia['disabled'] ?> value="<?= $datosDia['hora_cierre'] ?>"></td>
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
                    <button type="submit" class="btn btn-primary" id="btn_guardar_ubicacion" data-bs-dismiss="modal">Aceptar</button>
                </div>
            </div>
        </div>
    </div>
</form>