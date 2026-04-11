<div class="titulo-boton">
    <h1 class="titulo-modulo">Gestión de Alertas</h1>
    <div class="row text-end">
        <div class="col-md">
            <button type="button" class="btn con-icono btn-buscar" id="filtro_busqueda">Filtrar</button>
        </div>
        <div class="col-md">
            <a class="btn btn-agregar con-icono" href="<?= $url . "admin_alertas/agregar" ?>" id="agregar_alerta">Agregar</a>
        </div>
    </div>
</div>

<h6 class="subtitulo filtro_busqueda" style="display: none;">Filtro de búsqueda</h6>
<form onsubmit="return false;" id="form_tabla_alertas">
    <div class="caja filtro_busqueda" style="display: none;">
        <div class="row">
            <div class="col-md-4 mt-3">
                <div class="form-group">
                    <label>Estado:</label>
                    <select class="form-control filtro" id="filtro_activa">
                        <option value="" selected>Todas</option>
                        <option value="1">Activas</option>
                        <option value="0">Inactivas</option>
                    </select>
                </div>
            </div>
            <div class="col-md-4 mt-3">
                <div class="form-group">
                    <label>Plantilla:</label>
                    <select class="form-control filtro" id="filtro_plantilla">
                        <option value="" selected>Todas</option>
                        <option value="default">Default</option>
                        <option value="modal">Modal</option>
                        <option value="banner">Banner</option>
                        <option value="card">Card</option>
                        <option value="personalizado">Personalizado</option>
                    </select>
                </div>
            </div>
            <div class="col-md-2 mt-3 text-center">
                <button type="submit" class="btn btn-icono btn-buscar"></button>
            </div>
            <div class="col-md-2 mt-3 text-center">
                <button type="button" class="btn btn-icono btn-basura" id="limpiar_filtros"></button>
            </div>
        </div>
    </div>
</form>

<h6 class="subtitulo mt-3">Tabla de alertas</h6>
<div class="caja">
    <div class="table-responsive">
        <table class="table w-100" id="tabla_alertas">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Botones</th>
                    <th>Estado</th>
                    <th>Código</th>
                    <th>Título</th>
                    <th>Plantilla</th>
                    <th>Tipo mostrar</th>
                    <th>Roles</th>
                    <th>Fecha inicio</th>
                    <th>Fecha fin</th>
                    <th>Prioridad</th>
                    <th>Fecha alta</th>
                </tr>
            </thead>
            <tbody>
                <!-- Los datos se cargan mediante DataTables AJAX -->
            </tbody>
        </table>
    </div>
</div>

