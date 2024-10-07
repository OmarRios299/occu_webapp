<!-- Modulo / Login -->
<div class="login d-flex align-items-center">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-6 mx-auto">
                <div class="formulario">
                    <img src="<?php echo $url; ?>views/assets/css/img/logo/logo.png" class="logo" alt="">
                    <!-- Form -->
                    <form id="formularioIngreso" onsubmit="return false;">

                        <div class="form-group">
                            <input class="form-control" id="usuarioIngreso" name="" placeholder="Usuario" type="text" required>
                        </div>

                        <div class="form-group">
                            <input class="form-control" id="contrasenaIngreso" name="" placeholder="Contraseña" type="password" required>
                        </div>
                        <div class="btn-group d-flex justify-content-center" role="group">
                            <input class="btn active" type="submit" value="Ingresar">
                            <a type="button" class="btn btn-primary" id="btn_registrarme" href="<?=$url.'registrarme'?>">Registrarme</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>