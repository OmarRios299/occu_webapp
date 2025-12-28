<?php
// Detectar si es modo edición o agregar
$esEdicion = isset($usuario) && isset($usuario['id']);
$tituloFormulario = $esEdicion ? 'Editar información de usuario' : 'Agregar información de usuario';
$tituloBreadcrumb = $esEdicion ? 'Editar usuario' : 'Nuevo usuario';
?>

<div class="titulo-boton mt-4">
    <h1 class="titulo-modulo">Usuarios</h1>
</div>

<div class="my-5">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb breadcrumb-chevron p-3 bg-body-tertiary rounded-3">
            <li class="breadcrumb-item">
                <a class="link-body-emphasis" href="<?= $url . 'admin_usuarios' ?>">
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
<form onsubmit="return false;" id="form_agregar_usuario">
    <div class="caja">
        <?php if ($esEdicion): ?>
            <input type="hidden" value="<?= $usuario['id'] ?>" id="id_usuario">
        <?php endif; ?>
        
        <div class="row mt-3">
            <div class="col-md-3">  
                <div class="form-group">
                    <label>Nombre:<?= $esEdicion ? '' : ' <span class="text-danger">*</span>' ?></label>
                    <input type="text" class="form-control input_usuario" id="nombre_usuario_registrar" 
                           value="<?= $esEdicion ? htmlspecialchars($usuario['nombre']) : '' ?>" 
                           <?= $esEdicion ? 'required' : 'required' ?>>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>Apellido:<?= $esEdicion ? '' : ' <span class="text-danger">*</span>' ?></label>
                    <input type="text" class="form-control input_usuario" id="apellido_usuario_registrar" 
                           value="<?= $esEdicion ? htmlspecialchars($usuario['apellido']) : '' ?>" 
                           <?= $esEdicion ? '' : 'required' ?>>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>Nivel: <span class="text-danger">*</span></label>
                    <select class="form-control input_usuario" id="nivel_usuario_registrar" required>
                        <option value="" <?= $esEdicion ? 'disabled' : 'selected disabled' ?>>Selecciona una opción</option>
                        <?php 
                        if ($esEdicion) {
                            $admin = $usuario['nivel'] == 'Administrador' ? 'selected' : '';
                            $prop = $usuario['nivel'] == 'Propietario' ? 'selected' : '';
                        } else {
                            $admin = '';
                            $prop = '';
                        }
                        ?>
                        <option value="Administrador" <?= $admin ?>>Administrador</option>
                        <option value="Propietario" <?= $prop ?>>Propietario</option>
                    </select>
                </div>
            </div>
            <div class="col-md-3 text-center">
                <div class="form-group">
                    <img src="<?= $url . ($esEdicion ? $usuario['imagen'] : 'views/assets/img/usuario_default.png') ?>" 
                         style="width:100px;height:120px;" id="imagen_previsualizar">
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    <label>País</label>
                    <select class="form-control input_usuario" id="pais_usuario_registrar">
                        <option value="" <?= $esEdicion ? 'disabled' : 'selected disabled' ?>>Selecciona un país</option>
                        <?php 
                        foreach (GeneralController::obtenerPaisesController() as $pais) { 
                            $selected = ($esEdicion && $pais['id'] == $usuario['id_pais']) ? 'selected' : '';
                        ?>
                            <option value="<?= $pais['id'] ?>" <?= $selected ?>><?= $pais['nombre'] ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>Entidad federativa:</label>
                    <select class="form-control input_usuario select2" id="estado_usuario_registrar">
                        <option value="" selected disabled>Selecciona un estado</option>
                        <?php 
                        foreach (GeneralController::obtenerEstadosController() as $estado) { 
                            $selected = ($esEdicion && $estado['id'] == $usuario['id_entidad_federativa']) ? 'selected' : '';
                        ?>
                            <option value="<?= $estado['id'] ?>" <?= $selected ?>><?= $estado['nombre'] ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>Ciudad:</label>
                    <select class="form-control input_usuario" id="ciudad_usuario_registrar">
                        <option value="" selected disabled>Selecciona una ciudad</option>
                        <?php 
                        foreach (GeneralController::obtenerCiudadesController() as $ciudad) { 
                            $selected = ($esEdicion && $ciudad['id'] == $usuario['id_ciudad']) ? 'selected' : '';
                        ?>
                            <option value="<?= $ciudad['id'] ?>" <?= $selected ?>><?= $ciudad['nombre'] ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>Foto: (opcional)</label>
                </div>
                <div class="input-group">
                    <input type="file" class="form-control input_usuario imagenPrevisualizar validarImagen" id="imagen_usuario_registrar" lang="esp">
                    <button class="btn btn-outline-secondary" type="button">Subir</button>
                </div>
            </div>
        </div>
        
        <div class="row mt-4">
            <div class="col-md-3">
                <div class="form-group">
                    <label>Teléfono:</label>
                    <input type="tel" class="form-control input_usuario" id="telefono_usuario_registrar" 
                           value="<?= $esEdicion ? htmlspecialchars($usuario['telefono']) : '' ?>">
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>Correo Electrónico:<?= $esEdicion ? '' : ' <span class="text-danger">*</span>' ?></label>
                    <input type="email" class="form-control input_usuario <?= $esEdicion ? 'validarCampoEditar' : 'validarCampo' ?>" 
                           id="correo_usuario_registrar" 
                           columna='correo_electronico' 
                           tabla='admin_usuarios' 
                           mensaje='Este correo ya se encuentra registrado'
                           <?= $esEdicion ? 'idRegistro="' . $usuario['id'] . '"' : '' ?>
                           value="<?= $esEdicion ? htmlspecialchars($usuario['correo_electronico']) : '' ?>" 
                           <?= $esEdicion ? '' : 'required' ?>>
                    <div class="invalid-feedback" style="display: none;"></div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>Contraseña:<?= $esEdicion ? '' : ' <span class="text-danger">*</span>' ?></label>
                    <input type="text" class="form-control input_usuario" id="contrasena_usuario_registrar"
                           <?= $esEdicion ? '' : 'required' ?>>
                    <?php if ($esEdicion): ?>
                        <small class="form-text text-muted">Dejar en blanco para mantener la contraseña actual</small>
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>Confirmar contraseña:<?= $esEdicion ? '' : ' <span class="text-danger">*</span>' ?></label>
                    <input class="form-control input_usuario" type="password" id="confirmar_contrasena_usuario_registrar"
                           <?= $esEdicion ? '' : 'required' ?>>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mt-3">
        <div class="col-md-12 text-center">
            <button type="submit" class="btn btn-primary">Aceptar</button>
        </div>
    </div>
</form>
<br>

<script>
// Configurar validaciones dinámicas según modo (agregar/editar)
$(document).ready(function() {
    const esEdicion = $("#id_usuario").length > 0;
    
    if (esEdicion) {
        // En modo edición, solo nivel y nombre son obligatorios
        $("#apellido_usuario_registrar").removeAttr('required');
        $("#correo_usuario_registrar").removeAttr('required');
        $("#contrasena_usuario_registrar").removeAttr('required');
        $("#confirmar_contrasena_usuario_registrar").removeAttr('required');
    }
    // En modo agregar, los campos ya tienen required en el HTML
});
</script>

