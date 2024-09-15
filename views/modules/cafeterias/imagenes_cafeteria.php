<div class="titulo-boton mt-4">
    <h1 class="titulo-modulo">Cafeterías</h1>
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
            <li class="breadcrumb-item">
                <a class="link-body-emphasis fw-semibold text-decoration-none" href="<?= $url . 'cafeterias/' . $action[2] . '/editar' ?>">Mi cafetería</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
                Agregando imagenes
            </li>
        </ol>
    </nav>
</div>
<h6 class="subtitulo">Agregar imagenes</h6>
<form onsubmit="return false;" id="form_subir_imagenes">
    <input type="hidden" id="id_cafeteria" value="<?= $action[2] ?>">
    <input type="hidden" id="contador_items">
    <div class="caja">
        <div class="row">
            <div class="col-md-4 text-center">
                <div class="form-group">
                    <img src="<?= $url ?>/views/assets/img/cafeteria_default.png" style="width:100px;" id="imagen_previsualizar">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Agrega una imagen:</label>
                </div>
                <div class="input-group">

                    <input type="file" class="form-control input_usuario imagenPrevisualizar validarImagen" id="imagen_cafeteria" lang="esp">
                </div>
            </div>
            <div class="col-md-2 mt-3">
                <button type="submit" class="btn btn-icono btn-mas"></button>
            </div>
        </div>
    </div>
</form>
<h6 class="subtitulo mt-3">Tabla de imagenes</h6>
<div class="caja">
    <div class="table-responsive">
        <table class="table w-100" id="tabla_imagenes">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Botones</th>
                    <th>Estado</th>
                    <th>Imagen</th>
                    <th>Descripcion</th>
                </tr>
            </thead>
            <tbody>


            </tbody>
        </table>
    </div>
</div>