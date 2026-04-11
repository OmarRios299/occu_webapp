<!-- Modulo / Login - Diseño Profesional SaaS -->
<div class="login-container">
    <div class="login-wrapper">
        <div class="login-card">
            <!-- Logo -->
            <div class="login-header">
                <img src="<?php echo $url; ?>views/assets/img/logo_1.png" class="login-logo" alt="OCU">
            </div>

            <!-- Título -->
            <div class="login-title-section">
                <h1 class="login-title">Bienvenido a OCCU</h1>
                <p class="login-subtitle">Accede a tu cuenta</p>
            </div>

            <!-- Formulario -->
            <form id="formularioIngreso" onsubmit="return false;" class="login-form">
                <div class="form-group-login">
                    <label for="usuarioIngreso" class="form-label-login">Usuario</label>
                    <input 
                        class="form-control-login" 
                        id="usuarioIngreso" 
                        name="usuarioIngreso" 
                        placeholder="Ingresa tu usuario" 
                        type="text" 
                        required
                        autocomplete="username"
                    >
                </div>

                <div class="form-group-login">
                    <label for="contrasenaIngreso" class="form-label-login">Contraseña</label>
                    <input 
                        class="form-control-login" 
                        id="contrasenaIngreso" 
                        name="contrasenaIngreso" 
                        placeholder="Ingresa tu contraseña" 
                        type="password" 
                        required
                        autocomplete="current-password"
                    >
                </div>

                <button type="submit" class="btn-login-primary">
                    Ingresar
                </button>
                
                <?php 
                require_once __DIR__ . '/../../controllers/controller_template.php';
                $clientId = TemplateController::obtenerGoogleClientId();
                if ($clientId && !empty(trim($clientId))): 
                ?>
                <div class="login-divider">
                    <span class="divider-text">O</span>
                </div>
                
                <div class="google-signin-wrapper">
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
                
                <div class="login-footer">
                    <p class="login-register-text mt-3">
                        ¿No tienes una cuenta? 
                        <a href="<?=$url.'registrarme'?>" class="login-register-link">
                            Regístrate aquí
                        </a>
                    </p>
                </div>
            </form>
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
