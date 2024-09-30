<div class="titulo-boton">
    <h1 class="titulo-modulo">Usuarios</h1>
    <div class="row tetxt-end">
        <div class="col-md">
            <button type="button" class="btn con-icono btn-buscar" id="filtro_busqueda">Filtrar</button>
        </div>
        <div class="col-md">
            <a class="btn btn-agregar con-icono" href="<?= $url . "admin_usuarios/agregar" ?>" id="agregar_usuario">Agregar</a>
        </div>
    </div>
</div>
<h6 class="subtitulo filtro_busqueda" style="display: none;">Filtro de búsqueda</h6>
<div class="caja  filtro_busqueda" style="display: none;">
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label>Entidad federativa:</label>
                <select class="form-control select2" id="entidad_filtro" required>
                    <option value="" selected>Todos</option>
                    <?php foreach (GeneralController::obtenerEstadosController() as $estado) { ?>
                        <option value="<?= $estado['id'] ?>"><?= $estado['nombre'] ?></option>
                    <?php }  ?>
                </select>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label>Ciudad:</label>
                <select class="form-control" id="ciudad_filtro" required>
                    <option value="" selected>Todos</option>
                    <?php foreach (GeneralController::obtenerCiudadesController() as $ciudad) { ?>
                        <option value="<?= $ciudad['id'] ?>"><?= $ciudad['nombre'] ?></option>
                    <?php }  ?>
                </select>
            </div>
        </div>
        <div class="col-md-6 mt-3">
            <div class="form-group">
                <label>Tipo de usuario:</label>
                <select class="form-control" id="filtro_nivel">
                    <option value="" disabled selected>Todos</option>
                    <?php foreach (GeneralController::obtenerNivelesUsuarioControler() as $nivel) { ?>
                        <option value="<?= $nivel['nombre'] ?>"><?= $nivel['nombre'] ?></option>
                    <?php }  ?>
                </select>
            </div>
        </div>
        <div class="col-md-4 mt-3">
            <div class="form-group text-center">
                <label>Estatus:</label>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="estatus" value="Todos">
                            <label class="form-check-label">Todos</label>
                        </div>

                    </div>
                    <div class="col-md-4">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="estatus" value="Activas">
                            <label class="form-check-label">Activos</label>
                        </div>

                    </div>
                    <div class="col-md-4">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="estatus" value="Inactivas">
                            <label class="form-check-label">Inactivos</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-2 mt-3 text-center">
            <button type="button" class="btn btn-icono btn-buscar"></button>
        </div>
    </div>
</div>
<h6 class="subtitulo mt-3">Tabla de usuarios</h6>
<div class="caja">
    <div class="table-responsive">
        <table class="table w-100 dataTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Botones</th>
                    <th>Estado</th>
                    <th>Imagen</th>
                    <th>Nombre</th>
                    <th>Correo</th>
                    <th>Teléfono</th>
                    <th>Ciudad</th>
                    <th>Entidad federativa</th>
                    <th>País</th>
                    <th>Nivel</th>
                    <th>fecha de alta</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $i = 0;
                foreach (AdminUsuariosController::obtenerUsuariosController() as $usuario) {
                    $checked = ($usuario['estado'] == 0) ? "checked" : ""; ?>
                    <tr>
                        <td><?= ++$i; ?></td>
                        <td>
                            <a type="button" href="<?= $url . 'admin_usuarios/' . $usuario['id'] ?>/editar" class="btn btn-icono btn-editar"></a>
                            <a type="button" href="<?= $url . 'admin_usuarios/' . $usuario['id'] ?>/ver" class="btn btn-icono btn-ver"></a>
                            <button class="btn btn-icono btn-eliminar eliminarRegistro" tabla="admin_usuarios" idRegistro="<?= $usuario['id']; ?>"></button>
                        </td>
                        <td>
                            <div class="form-check form-switch">
                                <input type="checkbox"
                                    class="form-check-input cambioEstado"
                                    id="switch<?= $usuario['id']; ?>"
                                    tabla="admin_usuarios"
                                    idRegistro="<?= $usuario['id']; ?>"
                                    <?= $checked; ?>>
                                <label class="form-check-label" for="switch<?= $usuario['id']; ?>"></label>
                            </div>
                        </td>
                        <td><img width="60px;" src="<?= $usuario['imagen'] ?>" alt=""></td>
                        <td><?= $usuario['nombre_usuario'] ?></td>
                        <td><?= $usuario['correo_electronico'] ?></td>
                        <td><?= $usuario['telefono'] ?></td>
                        <td><?= $usuario['ciudad'] ?></td>
                        <td><?= $usuario['entidad_federativa'] ?></td>
                        <td><?= $usuario['pais'] ?></td>
                        <td><?= $usuario['nivel'] ?></td>
                        <td><?= $usuario['fecha_alta'] ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>
<br>