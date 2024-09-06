<div class="titulo-boton">
    <h1 class="titulo-modulo">Cafeterías</h1>
    <a class="btn btn-agregar con-icono" href="<?=$url.'cafeterias/agregar'?>">Agregar cafetería</a>
</div>
<h6 class="subtitulo mt-3">Tabla de cafeterías</h6>
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
                    <th>Horario de atención</th>
                    <th>Dirección</th>
                    <th>Teléfono</th>
                    <th>Correo electrónico</th>
                    <th>Ciudad</th>
                    <th>Entidad federativa</th>
                    <th>País</th>
                    <th>Usuario de alta</th>
                    <th>fecha de alta</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $i=0; 
                    foreach (CafeteriasController::obtenerCafeteriasController() as $cafeteria){ 
                        $checked = ($cafeteria['estado']==0) ? "checked" : "";?>
                <tr>
                    <td><?=++$i;?></td>
                    <td>
                        <a type="button" class="btn btn-icono btn-editar" href="<?=$url.'cafeterias/'.$cafeteria['id'].'/editar'?>"></a>
                        <button class="btn btn-icono btn-eliminar eliminarRegistro" tabla="cafeterias" idRegistro="<?= $cafeteria['id']; ?>"></button>
                    </td>
                    <td>
                        <div class="form-check form-switch">
                            <input type="checkbox"
                            class="form-check-input cambioEstado"
                            id="switch<?= $cafeteria['id']; ?>"
                            tabla="cafeterias"
                            idRegistro="<?= $cafeteria['id']; ?>"
                            <?= $checked; ?>>
                            <label class="custom-control-label" for="switch<?= $cafeteria['id']; ?>"></label>
                        </div>
                    </td>
                    <td><img width="60px;" src="<?=$cafeteria['imagen']?>" alt=""></td>
                    <td><?=$cafeteria['nombre']?></td>
                    <td><?=$cafeteria['horario_apertura'].' - '.$cafeteria['horario_cierre']?></td>
                    <td><?=$cafeteria['direccion']?></td>
                    <td><?=$cafeteria['telefono']?></td>
                    <td><?=$cafeteria['correo_electronico']?></td>
                    <td><?=$cafeteria['ciudad']?></td>
                    <td><?=$cafeteria['entidad_federativa']?></td>
                    <td><?=$cafeteria['pais']?></td>
                    <td><?=$cafeteria['usuario_alta']?></td>
                    <td><?=$cafeteria['fecha_alta']?></td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>
<br>