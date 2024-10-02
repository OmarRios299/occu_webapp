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
<form onsubmit="return false;" id="form_tabla_usuario">
    <div class="caja  filtro_busqueda" style="display: none;">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Entidad federativa:</label>
                    <select class="form-control select2 select_estado filtro" opcion_todos='SI' id="entidad_filtro">
                        <option value="" selected>Todos los estados</option>
                        <?php foreach (GeneralController::obtenerEstadosController() as $estado) { ?>
                            <option value="<?= $estado['id'] ?>"><?= $estado['nombre'] ?></option>
                        <?php }  ?>
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Ciudad:</label>
                    <select class="form-control select_ciudad select2 filtro" id="ciudad_filtro">
                        <option value="" selected>Todas las ciudades</option>
                        <?php foreach (GeneralController::obtenerCiudadesController() as $ciudad) { ?>
                            <option value="<?= $ciudad['id'] ?>"><?= $ciudad['nombre'] ?></option>
                        <?php }  ?>
                    </select>
                </div>
            </div>
            <div class="col-md-6 mt-3">
                <div class="form-group">
                    <label>Tipo de usuario:</label>
                    <select class="form-control filtro" id="filtro_nivel">
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
                                <input class="form-check-input" checked type="radio" name="estatus" value="Todos">
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
            <div class="col-md-1 mt-3 text-center">
                <button type="submit" class="btn btn-icono btn-buscar"></button>
            </div>
            <div class="col-md-1 mt-3 text-center">
                <button type="button" class="btn btn-icono btn-basura" id="limpiar_filtros"></button>
            </div>
        </div>
    </div>
</form>
<h6 class="subtitulo mt-3">Tabla de usuarios</h6>
<div class="caja">
    <div class="table-responsive">
        <table class="table w-100" id="tabla_usuarios">
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

            </tbody>
        </table>
    </div>
</div>
<br>