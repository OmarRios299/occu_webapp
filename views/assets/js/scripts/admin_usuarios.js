$(document).ready(function(){
   if ($("#tabla_usuarios").length) {
        cargarTablaUsuarios();
   } 
});

$(document).on("click",".btn-editar",function(){
    $("#modal_editar_usuario").modal('show');
});
$(document).on("submit", "#form_agregar_usuario", function() {
    let id_usuario = $("#id_usuario").val();
    let esEdicion = id_usuario != "";
    let contrasena = $("#contrasena_usuario_registrar").val();
    let confirmar_contrasena = $("#confirmar_contrasena_usuario_registrar").val();
    
    // Validaciones según modo
    let validacionOk = true;
    let mensajeError = '';
    
    // Validación de correo (solo si no tiene clase invalid)
    if ($("#correo_usuario_registrar").hasClass("is-invalid")) {
        validacionOk = false;
        mensajeError = "Por favor verifica el correo electrónico.";
    }
    
    // Validaciones para modo AGREGAR
    if (!esEdicion) {
        let nombre = $("#nombre_usuario_registrar").val().trim();
        let apellido = $("#apellido_usuario_registrar").val().trim();
        let email = $("#correo_usuario_registrar").val().trim();
        let nivel = $("#nivel_usuario_registrar option:selected").val();
        
        if (!nombre) {
            validacionOk = false;
            mensajeError = "El nombre es obligatorio.";
        } else if (!apellido) {
            validacionOk = false;
            mensajeError = "El apellido es obligatorio.";
        } else if (!nivel) {
            validacionOk = false;
            mensajeError = "El nivel es obligatorio.";
        } else if (!email) {
            validacionOk = false;
            mensajeError = "El correo electrónico es obligatorio.";
        } else if (!contrasena) {
            validacionOk = false;
            mensajeError = "La contraseña es obligatoria.";
        } else if (contrasena != confirmar_contrasena) {
            validacionOk = false;
            mensajeError = "Las contraseñas no coinciden.";
        }
    }
    // Validaciones para modo EDICIÓN
    else {
        let nombre = $("#nombre_usuario_registrar").val().trim();
        let nivel = $("#nivel_usuario_registrar option:selected").val();
        
        if (!nombre) {
            validacionOk = false;
            mensajeError = "El nombre es obligatorio.";
        } else if (!nivel) {
            validacionOk = false;
            mensajeError = "El nivel es obligatorio.";
        } else if (contrasena && contrasena != confirmar_contrasena) {
            // Si se ingresó contraseña, debe coincidir con la confirmación
            validacionOk = false;
            mensajeError = "Las contraseñas no coinciden.";
        }
    }
    
    if (!validacionOk) {
        swal("¡Error!", mensajeError, "error");
        return false;
    }
    
    // Si todas las validaciones pasan, proceder con el envío
    let nombre = $("#nombre_usuario_registrar").val();
    let apellido = $("#apellido_usuario_registrar").val();
    let email = $("#correo_usuario_registrar").val();
    let celular = $("#telefono_usuario_registrar").val();
    let nivel = $("#nivel_usuario_registrar option:selected").val();
    let pais = $("#pais_usuario_registrar option:selected").val();
    let entidad_federativa = $("#estado_usuario_registrar option:selected").val();
    let ciudad = $("#ciudad_usuario_registrar option:selected").val();
    let imagen_subir = $("#imagen_usuario_registrar")[0].files[0] ? $("#imagen_usuario_registrar")[0].files[0] : false;
        
        var datos = new FormData();
        
        datos.append("registrar_usuario", true);
        if(id_usuario) datos.append("id_usuario", id_usuario);
        datos.append("nombre", nombre);
        if(apellido) datos.append("apellido", apellido);
        if(email) datos.append("correo", email);
        // Solo agregar contraseña si se ingresó y coincide
        if(contrasena && contrasena == confirmar_contrasena) {
            datos.append("contrasena", contrasena);
        }
        if(celular) datos.append("telefono", celular);
        datos.append("nivel", nivel);
        if(pais) datos.append("pais", pais);
        if(entidad_federativa) datos.append("entidad_federativa", entidad_federativa);
        if(ciudad) datos.append("ciudad", ciudad);
        if(imagen_subir) datos.append("imagen_usuario_subir", imagen_subir);
        // if(imagen_captura) datos.append("imagen_usuario_captura", imagen_captura);
        
        $.ajax({
            url: url + 'views/ajax/ajax_admin_usuarios.php',
            method: 'POST',
            data: datos,
            cache: false,
            contentType: false,
            processData: false,
            beforeSend: cargaSistema(true),  // manda a llamar loading y desactiva inputs submit
            success: function(respuesta) {
                console.log(respuesta);
                if(respuesta == "session_expired") {
                    // si la sesión con el servidor expiró, recarga el sitio
                    sesionExpirada();
                } else if(respuesta == "error_validacion_email") {
                    // error en validación de email por parte del servidor
                    $("#correo_usuario_registrar").addClass('is-invalid').next().show();
                    swal("¡Error!", "Por favor verifica el correo electrónico.", "error");
                } else if(respuesta == "success") {
                    // el registro se realizó exitosamente
                    if(id_usuario != "") {
                        swal({
                            title: "¡Bien!",
                            text: "El usuario se ha actualizado correctamente.",
                            icon: "success",
                            button: "Aceptar",
                        }).then(function() {
                            window.location.href = url + 'admin_usuarios/' + id_usuario+'/editar';
                        });
                    } else {
                        swal({
                            title: "¡Bien!",
                            text: "El usuario se ha registrado correctamente.",
                            icon: "success",
                            button: "Aceptar",
                        }).then(function() {
                            window.location.href = url + 'admin_usuarios';
                        });
                    }
                } else {
                    // se recibió un mensaje diferente a success
                    swal("¡Error!", "Ha ocurrido un error.", "error");
                }
                // desactivamos el loading y habilitamos los inputs submit
                cargaSistema(false);
            }
        });
});



function cargarTablaUsuarios(){
    let entidad = $("#entidad_filtro option:selected").val();
    let ciudad = $("#ciudad_filtro option:selected").val();
    let estatus = $('input[name="estatus"]:checked').val();
    let nivel = $("#filtro_nivel option:selected").val();

    let filtro = `?tabla_usuarios=${true}&entidad=${entidad}&ciudad=${ciudad}&estatus=${estatus}&nivel=${nivel}`;

    if ($.fn.DataTable.isDataTable($("#tabla_usuarios"))) {
        $("#tabla_usuarios").DataTable().destroy();
    }
   $('#tabla_usuarios').DataTable( {
    "ajax": {
            "url": url + 'views/ajax/ajax_admin_usuarios.php' + filtro,
            "dataSrc": function (json) {
                return json.data;
            },
        },
    "deferRender": true,
    "retrieve": true,
    "processing": true,
    dom: 'Bfrtip',
    responsive: true,
    ordering: true,
    "language":{"url": url+"views/assets/plugins/DataTables/Spanish.json"}
   });
}

$(document).on("submit","#form_tabla_usuario",function(){
    cargarTablaUsuarios();
});