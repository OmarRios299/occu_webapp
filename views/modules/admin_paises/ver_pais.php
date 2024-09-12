<div class="titulo-boton">
    <h1 class="titulo-modulo">Países</h1>
</div>
<div class="my-5">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb breadcrumb-chevron p-3 bg-body-tertiary rounded-3">
            <li class="breadcrumb-item">
                <a class="link-body-emphasis" href="<?= $url . 'admin_paises' ?>">
                    <i class="fas fa-home" style="color: black;"></i>
                    <span class="visually-hidden">Home</span>
                </a>
            </li>
            <!-- <li class="breadcrumb-item">
        <a class="link-body-emphasis fw-semibold text-decoration-none" href="#">Library</a>
      </li> -->
            <li class="breadcrumb-item active" aria-current="page">
                <?= $pais['nombre'] ?>
            </li>
        </ol>
    </nav>
</div>
<div class="row">
    <div class="col-md-6">
        <div class="caja h-100">
            <div class=" card-metricos">
                <div class="card card-metricos solido estilo-principal">
                    <div class="card-body">
                        <h5 class="card-title" id="total_cafeterias"></h5>
                        <p class="card-text">Cafeterías registradas</p>
                    </div>
                </div>
                <div class="card card-metricos solido estilo-secundario mt-3">
                    <div class="card-body">
                        <h5 class="card-title" id="total_ciudades"></h5>
                        <p class="card-text">Ciudades registradas</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="caja h-100">
            <div class="row h-100">
                <div class="col-md-12">
                <canvas id="grafica_ciudades"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="text-center mt-3">
    <button class="btn btn-agregar con-icono" id="agregar_ciudad">Agregar ciudad</button>
</div>
<h6 class="subtitulo">Tabla de ciudades</h6>
<div class="caja">
    <div class="table-responsive">
        <input type="hidden" id="id_ciudad" value="<?=$action[1]?>">
        <table class="table w-100 dataTable" id="tabla_ciudades">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nombre</th>
                    <th>Botones</th>
                    <th>Estado</th>
                    <th>Entidad Federativa</th>
                    <th>Cafeterías</th>
                    <th>Usuario de alta</th>
                    <th>Fecha de alta</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $i = 0;
                foreach (AdminPaisesController::obtenerCiudadesController($action[1]) as $ciudad) { 
                    $checked = ($ciudad['estado'] == 0) ? "checked" : ""; 
                    ?>
                    <tr>
                        <td><?= ++$i ?></td>
                        <td><?= $ciudad['nombre'] ?></td>
                        <td>
                            <!-- <button type="button" class="btn btn-icono btn-lista btn_mostrar_cafeterias" ciudad='<?= $ciudad['id']; ?>' ></button>
                            <button type="button" class="btn btn-icono btn-ver"></button> -->
                            <button type="button" class="btn btn-icono btn-editar editar_ciudad" estado='<?=$ciudad['id_entidad_federativa']?>' coordenadas='<?= $ciudad['coordenadas']; ?>' nombre='<?= $ciudad['nombre']; ?>' idRegistro='<?= $ciudad['id']; ?>'></button>
                            <button type="button" class="btn btn-icono btn-eliminar eliminarRegistro" tabla='ciudades' idRegistro='<?=$ciudad['id']?>'></button>
                        </td>
                        <td>
                            <div class="form-check form-switch">
                                <input type="checkbox"
                                    class="form-check-input cambioEstado"
                                    id="switch<?= $ciudad['id']; ?>"
                                    tabla="ciudades"
                                    idRegistro="<?= $ciudad['id']; ?>"
                                    <?= $checked; ?>>
                                <label class="form-check-label" for="switch<?= $ciudad['id']; ?>"></label>
                            </div>
                        </td>
                        <td><?= $ciudad['entidad_federativa'] ?></td>
                        <td><?= $ciudad['total_cafeterias'] ?></td>
                        <td><?= $ciudad['usuario_alta'] ?></td>
                        <td><?= $ciudad['fecha_alta'] ?></td>
                    </tr>
                <?php 
                    } ?>
            </tbody>
        </table>
    </div>
</div>
<div class="modal fade" id="modal_agregar_ciudad" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalLabel">Agregando ciudad</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form onsubmit="return false;" id="form_agregar_ciudad">
                <div class="modal-body">

                    <input type="hidden" class="input_pais" id="id_pais">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Nombre:</label>
                                <input type="text" class="form-control validarCampo input_ciudad" id="nombre_ciudad" columna='nombre' tabla='ciudades' mensaje='Esta ciudad ya está registrada'>
                                <div class="invalid-feedback" style="display: none;"></div>
                                <input type="hidden" id="coordenadas_ciudad">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Entidad federativa:</label>
                                <select class="form-control input_ciudad" id="select_estado" required>
                                    <option value="" selected disabled>Selecciona un estado</option>
                                    <?php foreach (GeneralController::obtenerEstadosController() as $estado) { ?>
                                        <option value="<?= $estado['id'] ?>"><?= $estado['nombre'] ?></option>
                                    <?php }  ?>
                                </select>
                                <div class="invalid-feedback" style="display: none;">Selecciona un estado para continuar</div>
                            </div>
                        </div>
                        <div class="col-md-12 mt-3">
                            <div class="form-group text-center">
                                <button type="button" class="btn con-icono btn-ubicacion btn_delimitar_ciudad" id="delimitar_ciudad">Delimitar ciudad</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary" data-bs-dismiss="modal">Aceptar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_mapa" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
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
                <button class="btn btn-eliminar" id="btn_eliminar_poligono">Eliminar poligono</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="submit" class="btn btn-primary" id="btn_guardar_ubicacion" data-bs-dismiss="modal">Aceptar</button>
            </div>
        </div>
    </div>
</div>