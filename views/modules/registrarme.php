<?php
if (!isset($action[1])) {
?>
   <div class="titulo-boton registrarme-content">
      <h1 class="titulo-modulo">Registrarme</h1>
   </div>
   <form onsubmit="return false;" id="form_registrarme">
      <div class="caja" id="caja_nivel">
         <div class="row">
            <div class="col-md-12 text-center">
               <h1>¿Qué tipo de perfíl te identifica? </h1>
            </div>
            <div class="col-md-12">
               <div class="form-group">
                  <select class="form-control" id="select_nivel">
                     <option value="" disabled selected>Selecciona una opción</option>
                     <option value="Cliente">Busco una cafetería para disfrutar de un buen café</option>
                     <option value="Barista">Soy barista y estoy interesado/a en oportunidades de trabajo</option>
                     <option value="Propietario">Tengo una o varias cafeterías y quiero conectarme con otros amantes del café</option>
                  </select>
               </div>
            </div>
            <div class="col-md-12 text-center mt-3">
               <button type="button" class="btn btn-primary" id="btn_siguiente1">Siguente</button>
            </div>
         </div>
      </div>
      <div class="caja" id="caja_nombre" style="display: none;">
         <div class="row mt-3">
            <div class="col-md-12 text-center">
               <h1>LLena los siguientes datos</h1>
            </div>
            <div class="col-md-12">
               <div class="form-group">
                  <label>Nombre:</label>
                  <input type="text" class="form-control input_usuario" id="nombre_usuario_registrar" required>
               </div>
            </div>
            <div class="col-md-12">
               <div class="form-group">
                  <label>Apellido:</label>
                  <input type="text" class="form-control input_usuario" id="apellido_usuario_registrar" required>
               </div>
            </div>
            <div class="col-md-12 text-center mt-3">
               <button type="button" class="btn btn-primary" id="btn_regresar1">Regresar</button>
               <button type="button" class="btn btn-primary" id="btn_siguiente2">Siguente</button>
            </div>
         </div>
      </div>
      <div class="caja" id="caja_correo" style="display: none;">
         <div class="row mt-4">
            <div class="col-md-12 text-center">
               <h1>Para finalizar</h1>
            </div>
            <div class="col-md-12">
               <div class="form-group">
                  <label>Correo Electrónico:</label>
                  <input type="email" class="form-control input_usuario registroValidarCampo" id="correo_usuario_registrar" columna='correo_electronico' tabla='admin_usuarios' mensaje='Este correo ya se encuentra registrado' required>
                  <div class="invalid-feedback" style="display: none;"></div>
               </div>
            </div>
            <div class="col-md-6">
               <div class="form-group">
                  <label>Contraseña:</label>
                  <input type="text" class="form-control input_usuario" id="contrasena_usuario_registrar" required>
               </div>
            </div>
            <div class="col-md-6">
               <div class="form-group">
                  <label for="">Confirmar contraseña:</label>
                  <input class="form-control input_usuario" type="password" id="confirmar_contrasena_usuario_registrar" required>
               </div>
            </div>
            <div class="col-md-12 text-center mt-3">
               <button type="button" class="btn btn-primary" id="btn_regresar2">Regresar</button>
               <button type="submit" class="btn btn-green" id="btn_siguiente3">Registrarme</button>
            </div>
         </div>
      </div>
   </form>
<?php
} else if ($action[1] == 'verificacion') {
?>
   <div class="login d-flex align-items-center">
      <div class="container-fluid">
         <div class="row imagen">
            <div class="col-md-8 mx-auto">
               <div class="formulario caja">
                  <!-- Form -->
                  <form id="formularioVerificacion" onsubmit="return false;">
                     <div class="alert alert-primary" role="alert">
                     Hemos enviado un código de verificación a tu correo electronico.
                     </div>
                     <div class="form-group">
                        <input class="form-control" id="usuarioVerificacion" name="" placeholder="Correo" type="text" required>
                     </div>
                     <div class="form-group">
                        <input class="form-control" id="contrasenaVerificacion" name="" placeholder="Contraseña" type="password" required>
                     </div>
                     <div class="form-group">
                        <input class="form-control" id="pinVerificacion" placeholder="Código de verificación" required>
                     </div>
                     <div class="btn-group d-flex justify-content-center" role="group">
                        <input class="btn active" type="submit" value="Ingresar">
                     </div>
                  </form>
               </div>
            </div>
         </div>
      </div>
   </div>
<?php
}

?>