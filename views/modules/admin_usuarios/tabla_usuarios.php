
<div class="titulo-boton">
    <h1 class="titulo-modulo">Usuarios</h1>
    <a class="btn btn-agregar con-icono" href="<?=$url."admin_usuarios/agregar"?>" id="agregar_usuario">Agregar usuario</a>
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
                            <a type="button" href="<?=$url.'admin_usuarios/'.$usuario['id']?>/editar" class="btn btn-icono btn-editar"></a>
                            <a type="button" href="<?=$url.'admin_usuarios/'.$usuario['id']?>/ver" class="btn btn-icono btn-ver"></a>
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
