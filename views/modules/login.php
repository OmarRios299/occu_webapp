<!-- Modulo / Login -->
<div class="login d-flex align-items-center">
    <div class="container-fluid">
        <div class="row imagen">
            <div class="col-md-6 mx-auto">
                <div class="formulario caja">
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
                        </div>
                        
                        <?php 
                        require_once __DIR__ . '/../../controllers/controller_template.php';
                        $clientId = TemplateController::obtenerGoogleClientId();
                        if ($clientId && !empty(trim($clientId))): 
                        ?>
                        <div class="divider d-flex align-items-center my-2">
                            <hr class="flex-grow-1">
                            <span class="mx-2 text-muted fw-bold">O</span>
                            <hr class="flex-grow-1">
                        </div>
                        
                        <div class="d-flex justify-content-center mb-2 google-signin-container">
                            <div id="g_id_onload"
                                data-client_id="<?php echo htmlspecialchars($clientId); ?>"
                                data-callback="handleGoogleSignIn"
                                data-auto_prompt="false">
                            </div>
                            <div class="g_id_signin" 
                                data-type="standard"
                                data-size="large"
                                data-theme="outline"
                                data-text="sign_in_with"
                                data-shape="rectangular"
                                data-logo_alignment="left">
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <div class="d-flex justify-content-center mt-3">
                            <a type="button" class="btn btn-primary btn-sm px-2 py-1" id="btn_registrarme" href="<?=$url.'registrarme'?>" style="font-size: 0.85rem; width: 38%; min-width:170px;">
                                <i class="fas fa-user-plus me-1" style="font-size: 1em;"></i>
                                Registrarme
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para completar información de usuario Google -->
<div class="modal fade" id="modalCompletarInfo" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header" style="background-color: var(--principal); color: white;">
                <h5 class="modal-title">Completar Información</h5>
            </div>
            <div class="modal-body">
                <p class="mb-4 text-center">Selecciona el tipo de usuario que mejor te identifica:</p>
                <form id="formCompletarInfo">
                    <input type="hidden" id="id_usuario_google">
                    <input type="hidden" id="nivel_seleccionado_google">
                    
                    <!-- Tipo de usuario con tarjetas -->
                    <div class="form-group mb-3">
                        <div class="row row-cols-1 row-cols-md-3 g-4" id="opciones_nivel_google">
                            <!-- Las tarjetas se cargarán dinámicamente -->
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" disabled id="btnCancelarInfo">Cancelar</button>
                <button type="button" class="btn" id="btnGuardarInfo" style="background-color: var(--principal); color: white; border-color: var(--principal);" disabled>Continuar</button>
            </div>
        </div>
    </div>
</div>

<style>
.imagen-arriba-modal {
    border-top-right-radius: 25px !important;
    border-top-left-radius: 25px !important;
    width: 100% !important;
    clip-path: ellipse(80% 78% at 52% 22%);
    height: 150px;
    object-fit: cover;
}

.card-nivel-google {
    cursor: pointer;
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.card-nivel-google:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}

.card-nivel-google.seleccionado {
    border-color: var(--principal);
    box-shadow: 0 4px 12px rgba(241, 108, 91, 0.3);
}

.card-nivel-google .card-title {
    font-weight: 600;
}

</style>