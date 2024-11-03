<?php
if (!isset($action[1])) {
?>
   <style>
      .input-group {
         display: flex;
         align-items: center;
      }

      .input-group-text {
         cursor: pointer;
         padding: 0.5em;
         background: none;
         border: none;
      }

      .imagen-arriba {
         border-top-right-radius: 25px !important;
         border-top-left-radius: 25px !important;
         width: 100% !important;
         clip-path: ellipse(80% 78% at 52% 22%);
      }
   </style>

   <form onsubmit="return false;" id="form_registrarme">
      <input type="hidden" id="select_nivel">
      <div class="" id="caja_nivel">
         <div class="col-md-12">
            <!-- <div class="form-group">
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
         </div> -->


            <div class="row">
               <div class="col">
                  <div class="h-100 overflow-hidden text-bg-dark rounded-4 shadow-lg" style="background-image: url('<?= $url ?>views/assets/img/utilidades/registro/fondo_crema4.jpg');">
                     <div class="d-flex flex-column h-100 p-5 pb-3 text-white text-shadow-1 text-center">
                        <h1 class="pt-3 mt-1 mb-4 display-5 fw-bold">¿Qué tipo de perfíl te identifica?</h1>
                     </div>
                  </div>
               </div>
            </div>
            <div class="row row-cols-1 row-cols-lg-3 align-items-stretch g- py-3">
               <!-- <div class="col">
                  <div class="card card-cover h-100 overflow-hidden text-bg-dark rounded-4 shadow-lg" style="background-image: url('<?= $url ?>views/assets/img/utilidades/registro/cliente.jpg');">
                     <div class="d-flex flex-column h-100 p-4 pb-3 text-white text-shadow-1 fondo-oscuro text-center">
                        <h3 class="pt-5 mt-1 display-7 lh-2">Busco una cafetería para disfrutar de un buen café</h3>
                     </div>
                  </div>
               </div> -->

               <div class="col-12 col-md-6 col-lg-4 mb-4 rounded-7 btn_siguiente1" nivel='Cliente' imagen='<?= $url ?>views/assets/img/utilidades/registro/cliente2.jpg'>
                  <div class="card h-100">
                     <img src='<?= $url ?>views/assets/img/utilidades/registro/cliente2.jpg' class="card-img-top imagen-arriba" alt="">
                     <div class="card-body">
                        <div class="card-header">
                           <h5 class="card-title mt-1 rounded-7">Amante del café</h5>
                        </div>
                        <p class="card-text mt-2">Busco una cafetería para disfrutar de un buen café</p>
                     </div>
                  </div>
               </div>

               <div class="col-12 col-md-6 col-lg-4 mb-4 rounded-7 btn_siguiente1" nivel='Barista' imagen='<?= $url ?>views/assets/img/utilidades/registro/barra1.jpg'>
                  <div class="card h-100">
                     <img src='<?= $url ?>views/assets/img/utilidades/registro/barra1.jpg' class="card-img-top imagen-arriba" alt="">
                     <div class="card-body">
                        <div class="card-header">
                           <h5 class="card-title mt-1 rounded-7">Barista</h5>
                        </div>
                        <p class="card-text mt-2">Soy barista y estoy interesado/a en oportunidades de trabajo</p>
                     </div>
                  </div>
               </div>

               <div class="col-12 col-md-6 col-lg-4 mb-4 rounded-7 btn_siguiente1" nivel='Propietario' imagen='<?= $url ?>views/assets/img/utilidades/registro/propietario.jpg'>
                  <div class="card h-100">
                     <img src='<?= $url ?>views/assets/img/utilidades/registro/propietario.jpg' class="card-img-top imagen-arriba" alt="">
                     <div class="card-body">
                        <div class="card-header">
                           <h5 class="card-title mt-1 rounded-7">Propietario</h5>
                        </div>
                        <p class="card-text mt-2">Tengo una o varias cafeterías y quiero conectarme con otros amantes del café</p>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <div class="row" id="caja_nombre" style="display: none;">
         <div class="col-md-12">
            <div class="h-100 overflow-hidden text-bg-dark rounded-4 shadow-lg" style="background-image: url('<?= $url ?>views/assets/img/utilidades/registro/fondo_crema4.jpg');">
               <div class="d-flex flex-column h-100 p-5 pb-3 text-white text-shadow-1 text-center">
                  <h1 class="pt-3 mt-1 mb-4 display-5 fw-bold">Completa los siguientes datos</h1>
               </div>
            </div>
         </div>
         <div class="col-md-12 d-flex justify-content-center align-items-center">
            <div class="col-lg-6 mb-4 rounded-7 mt-3">
               <div class="card h-100">
                  <img class="card-img-top imagen-arriba nivel_imagen" src='' alt="">
                  <div class="card-body">
                     <div class="card-header">
                        <h5 class="card-title mt-1 rounded-7 nivel_seleccionado"></h5>
                     </div>
                     <div class="row card-text mt-2">
                        <div class="col-md-12">
                           <div class="form-group">
                              <label>Nombre:</label>
                              <input type="text" class="form-control input_usuario" id="nombre_usuario_registrar">
                           </div>
                        </div>
                        <div class="col-md-12">
                           <div class="form-group">
                              <label>Apellido:</label>
                              <input type="text" class="form-control input_usuario" id="apellido_usuario_registrar">
                           </div>
                        </div>
                        <div class="col-md-12 text-center mt-3">
                           <button type="button" class="btn btn-primary" id="btn_regresar1">Regresar</button>
                           <button type="button" class="btn btn-primary" id="btn_siguiente2">Siguente</button>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <div id="caja_correo" style="display: none;">
         <div class="col-md-12">
            <div class="h-100 overflow-hidden text-bg-dark rounded-4 shadow-lg" style="background-image: url('<?= $url ?>views/assets/img/utilidades/registro/fondo_crema4.jpg');">
               <div class="d-flex flex-column h-100 p-5 pb-3 text-white text-shadow-1 text-center">
                  <h1 class="pt-3 mt-1 mb-4 display-5 fw-bold">Para finalizar</h1>
               </div>
            </div>
         </div>

         <div class="col-md-12 d-flex justify-content-center align-items-center">
            <div class="col-lg-6 mb-4 rounded-7 mt-3">
               <div class="card h-100">
                  <img class="card-img-top imagen-arriba nivel_imagen" src='' alt="">
                  <div class="card-body">
                     <div class="card-header">
                        <h5 class="card-title mt-1 rounded-7 nivel_seleccionado"></h5>
                     </div>
                     <div class="row card-text mt-2">
                        <div class="col-md-12">
                           <div class="form-group">
                              <label>Correo Electrónico:</label>
                              <input type="email" class="form-control input_usuario registroValidarCampo" id="correo_usuario_registrar" columna='correo_electronico' tabla='admin_usuarios' mensaje='Este correo ya se encuentra registrado' required>
                              <div class="invalid-feedback" style="display: none;"></div>
                           </div>
                        </div>
                        <div class="col-md-6">
                           <div class="form-group">
                              <div class="row">
                                 <div class="col-md-10">
                                    <label>Contraseña:</label>
                                 </div>
                                 <div class="col-md-2">
                                    <span class="input-group-text" id="togglePassword">
                                       <i class="fa fa-eye" aria-hidden="true"></i>
                                    </span>
                                 </div>
                              </div>
                              <div class="input-group">
                                 <input type="password" class="form-control input_usuario" id="contrasena_usuario_registrar" required>
                              </div>
                           </div>
                        </div>

                        <div class="col-md-6">
                           <div class="form-group mt-1">
                              <label for="">Confirmar contraseña:</label>
                              <input class="form-control input_usuario" type="password" id="confirmar_contrasena_usuario_registrar" required>
                           </div>
                        </div>
                        <div class="col-md-12 text-center mt-3">
                           <button type="button" class="btn btn-primary" id="btn_regresar2">Regresar</button>
                           <button type="submit" class="btn btn-success" id="btn_siguiente3">Registrarme</button>
                        </div>
                     </div>
                  </div>
               </div>
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
                        Hemos enviado un código de verificación a tu correo electronico. <br>
                        <span style="color:orange">En unos minutos llegará tu código</span>
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
                  <div class="col-md-12 d-flex justify-content-center mt-3">
                     <a type="button" href="#" id="brn_reenviar_codigo">Reenviar código de verificación</button>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
<?php
}

?>

<div class="modal fade" id="modal_reenviar_codigo" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header">
            <input type="hidden" id="id_cafeteria">
            <h5 class="modal-title" id="modalLabel">Reenviando código de verificación</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
         </div>
         <div class="modal-body modal_mapa">
            <div class="row">
               <div class="col-md-12">
                  <div class="form-group">
                     <label>Correo:</label>
                     <input type="email" class="form-control" id="reenviar_correo">
                  </div>
               </div>
            </div>
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            <button type="btn" class="btn btn-primary" id="reenviar">Reenviar</button>
         </div>
      </div>
   </div>
</div>

<!-- Falta poder reenviar el correo de verificacion -->