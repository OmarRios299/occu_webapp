<div class="titulo-boton mt-4">
    <h1 class="titulo-modulo">Usuarios</h1>
</div>

<div class="container my-5">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb breadcrumb-chevron p-3 bg-body-tertiary rounded-3">
            <li class="breadcrumb-item">
                <a class="link-body-emphasis" href="<?= $url . 'admin_usuarios' ?>">
                    <i class="fas fa-home" style="color: black;"></i>
                    <span class="visually-hidden">Home</span>
                </a>
            </li>
            <!-- <li class="breadcrumb-item">
        <a class="link-body-emphasis fw-semibold text-decoration-none" href="#">Library</a>
      </li> -->
            <li class="breadcrumb-item active" aria-current="page">
                Nuevo usuario
            </li>
        </ol>
    </nav>
</div>


<h6 class="subtitulo mt-3">Agregar información de usuario</h6>
<form onsubmit="return false;" id="form_agregar_usuario">
    <div class="caja">
        <input type="hidden" value="<?=$usuario['id']?>" id="id_usuario">
        <div class="row mt-3">
            <div class="col-md-3">  
                <div class="form-group">
                    <label>Nombre:</label>
                    <input type="text" class="form-control input_usuario" id="nombre_usuario_registrar" value="<?=$usuario['nombre']?>" required>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>Apellido:</label>
                    <input type="text" class="form-control input_usuario" id="apellido_usuario_registrar" value="<?=$usuario['apellido']?>" required>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>Nivel:</label>
                    <select class="form-control input_usuario" id="nivel_usuario_registrar" required>
                        <option value="" disabled>Selecciona una opción</option>
                        <?php $admin=''; $prop = 'selected';
                        if($usuario['nivel'] != 'Propietario') {$admin='selected'; $prop = '';} ?>
                        <option value="Administrador" <?=$admin?>>Administrador</option>
                        <option value="Propietario" <?=$prop?>>Propietario</option>
                    </select>
                </div>
            </div>
            <div class="col-md-3 text-center">
                <div class="form-group">
                    <img src="<?=$url.$usuario['imagen']?>" style="width:100px;heigth:120px;" id="imagen_previsualizar">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    <label>País</label>
                    <select class="form-control input_usuario" id="pais_usuario_registrar" required>
                        <option value="" disabled>Selecciona un país</option>
                        <option value="<?=$usuario['id_pais']?>" selected><?=$usuario['pais']?></option>
                        <?php foreach (GeneralController::obtenerPaisesController() as $pais) { 
                            if ($pais['id']!=$usuario['id_pais']) { ?>
                            <option value="<?= $pais['id'] ?>"><?= $pais['nombre'] ?></option>
                        <?php }}  ?>
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>Entidad federativa:</label>
                    <select class="form-control input_usuario" id="estado_usuario_registrar" required>
                        <option value="" selected disabled>Selecciona un estado</option>
                        <option value="<?=$usuario['id_entidad_federativa']?>" selected><?=$usuario['entidad_federativa']?></option>
                        <?php foreach (GeneralController::obtenerEstadosController() as $estado) { 
                            if ($estado['id']!=$usuario['id_entidad_federativa']) { ?>
                            <option value="<?= $estado['id'] ?>"><?= $estado['nombre'] ?></option>
                        <?php }}  ?>
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>Ciudad:</label>
                    <select class="form-control input_usuario" id="ciudad_usuario_registrar" required>
                        <option value="" selected disabled>Selecciona una ciudad</option>
                        <option value="<?=$usuario['id_ciudad']?>" selected><?=$usuario['ciudad']?></option>
                        <?php foreach (GeneralController::obtenerCiudadesController() as $ciudad) { 
                            if ($ciudad['id']!=$usuario['id_ciudad']) { ?>
                            <option value="<?= $ciudad['id'] ?>"><?= $ciudad['nombre'] ?></option>
                        <?php }}  ?>
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>Foto:(opcional)</label>
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
                    <input type="tel" class="form-control input_usuario" id="telefono_usuario_registrar" required value='<?=$usuario['telefono']?>'>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>Correo Electrónico:</label>
                    <input type="email" class="form-control input_usuario validarCampoEditar" id="correo_usuario_registrar" columna='correo_electronico' tabla='admin_usuarios' mensaje='Este correo ya se encuentra registrado' idRegistro='<?=$usuario['id']?>' required value='<?=$usuario['correo_electronico']?>'>
                            <div class="invalid-feedback" style="display: none;"></div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>Contraseña:</label>
                    <input type="text" class="form-control input_usuario" id="contrasena_usuario_registrar">
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="">Confirmar contraseña:</label>
                    <input class="form-control input_usuario" type="password" id="confirmar_contrasena_usuario_registrar">
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