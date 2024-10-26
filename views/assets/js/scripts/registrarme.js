$(document).on("click", "#btn_siguiente1, #btn_regresar1", function () {
    $("#caja_nivel").toggle();
    $("#caja_nombre").toggle();
});
$(document).on("click", "#btn_siguiente2, #btn_regresar2", function () {
    $("#caja_nombre").toggle();
    $("#caja_correo").toggle();
});

/*=====================================
=            VALIDAR CAMPO            =
=====================================*/

$(document).on("change", ".registroValidarCampo", function () {

    let input = $(this);
    let nombre = $(this).val();
    let columna = $(this).attr("columna");
    let tabla = $(this).attr("tabla");
    let mensaje = $(this).attr("mensaje");

    let datos = new FormData();

    datos.append("valorCampo", nombre);
    datos.append("columnaCampo", columna);
    datos.append("tablaCampo", tabla);

    $.ajax({
        url: url + "views/ajax/ajax_registrarme.php",
        method: "POST",
        data: datos,
        cache: false,
        contentType: false,
        processData: false,
        success: function (respuesta) {

            if (respuesta == 0) {

                $(input).addClass("is-invalid");
                $(input).next().html(mensaje);
                $(input).next().show();

            } else {

                $(input).removeClass("is-invalid");
                $(input).next().hide();

            }
        }
    });

});

/*=====  End of VALIDAR CAMPO  ======*/

$(document).on("submit", "#form_registrarme", function () {
    let id_usuario = $("#id_usuario").val();
    let contrasena = $("#contrasena_usuario_registrar").val();
    let confirmar_contrasena = $("#confirmar_contrasena_usuario_registrar").val();

    if (
        !$("#correo_usuario_registrar").hasClass("is-invalid") &&
        ( //validación de contraseña
            (contrasena == confirmar_contrasena) || // la contraseña y su confirmación coinciden
            (id_usuario != "") // se está editando el usuario y no se toma en cuenta la contraseña
        )
    ) {

        let nombre = $("#nombre_usuario_registrar").val();
        let apellido = $("#apellido_usuario_registrar").val();
        let email = $("#correo_usuario_registrar").val();
        //let celular = $("#telefono_usuario_registrar").val();
        let nivel = $("#select_nivel option:selected").val();

        var datos = new FormData();

        datos.append("registrar_usuario", true)
        datos.append("nombre", nombre);
        datos.append("apellido", apellido);
        datos.append("correo", email);
        if (contrasena == confirmar_contrasena) datos.append("contrasena", contrasena);
        // datos.append("telefono", celular);
        datos.append("nivel", nivel);

        $.ajax({
            url: url + 'views/ajax/ajax_registrarme.php',
            method: 'POST',
            data: datos,
            cache: false,
            contentType: false,
            processData: false,
            beforeSend: cargaSistema(true),  // manda a llamar loading y desactiva inputs submit
            success: function (respuesta) {
                console.log(respuesta);
                if (respuesta == "session_expired") {
                    // si la sesión con el servidor expiró, recarga el sitio
                    sesionExpirada();
                } else if (respuesta == "error_validacion_email") {
                    // error en validación de email por parte del servidor
                    $("#correo_usuario_registrar").addClass('is-invalid').next().show();
                    swal("¡Error!", "Por favor verifica el correo electrónico.", "error");
                } else if (respuesta == "success") {
                    // el registro se realizó exitosamente
                    //(id_usuario != "") ? alertaUpdate() : alertaInsert();
                    swal({
                       title: "¡Bien!",
                       text: "Tu registro se realizó con éxito.",
                       icon: "success",
                       button: "Aceptar",
                    }).then(function() {
                        window.location = url+"registrarme/verificacion";
                    });
                    
                } else {
                    // se recibió un mensaje diferente a success
                    swal("¡Error!", "Ha ocurrido un error.", "error");
                }
                // desactivamos el loading y habilitamos los inputs submit
                cargaSistema(false);
            }
        });
    }

});

$(document).on('submit', '#formularioVerificacion', function(){

    let usuario     = $("#usuarioVerificacion").val();
    let contrasena  = $("#contrasenaVerificacion").val();

    let datos = new FormData();

    datos.append("usuarioVerificacion", usuario);
    datos.append("contrasena", contrasena);
    datos.append("pin", $("#pinVerificacion").val());

    $.ajax({
        url:url+"views/ajax/ajax_registrarme.php",
        method:"POST",
        data: datos,
        cache: false,
        contentType: false,
        processData: false,
        beforeSend: function() {

            loading(true);

        },
        success:function(respuesta){
            
            console.log("respuesta", respuesta);

            loading(false);

            if(respuesta === "dashboard"){

                window.location = url+"dashboard";

            }else if(respuesta === "desactivado"){

                swal("¡Error!", "¡El usuario esta desactivado!", "error");

            }else if(respuesta === "verificacion"){

                swal("¡Error!", "¡El código de verificación es incorrecto!", "error");

            }else{

                swal("¡Error!", "¡Usuario o contraseña incorrectos!", "error");

            }

        }

    });

});

$(document).on("click","#brn_reenviar_codigo",function(){
    $("#modal_reenviar_codigo").modal('show');
});

$(document).on("click","#reenviar",function(){
    var datos = new FormData();
    
    datos.append("reenviar_codigo_correo", $("#reenviar_correo").val());
    
    $.ajax({
        url:url+'views/ajax/ajax_registrarme.php',
        method:'POST',
        data: datos,
        cache: false,
        contentType: false,
        processData: false,
        success:function(respuesta){
            console.log(respuesta);
            if(respuesta === "invalido"){

                swal("¡Error!", "¡Este correo no ha sido registrado anteriormente!", "error");

            }else if(respuesta === "verificado"){

                swal("¡Error!", "¡Este correo ya ha sido verificado anteriormente!", "error");

            }else{
                swal("¡Bien!", "El código se envió exitosamente", "success");
            }
        }
    });
});
$(document).on('click', '#togglePassword', function() {
    const passwordInput = $('#contrasena_usuario_registrar');
    
    // Alterna el tipo de input entre 'password' y 'text'
    const type = passwordInput.attr('type') === 'password' ? 'text' : 'password';
    passwordInput.attr('type', type);
    
    // Alterna el ícono entre "ojo abierto" y "ojo cerrado"
    $(this).find('i').toggleClass('fa-eye fa-eye-slash');
});



