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
    let contrasena = $("#contrasena_usuario_registrar").val();
    let confirmar_contrasena = $("#confirmar_contrasena_usuario_registrar").val();
    
    if(
        !$("#correo_usuario_registrar").hasClass("is-invalid") &&
        ( //validación de contraseña
            (contrasena == confirmar_contrasena) || // la contraseña y su confirmación coinciden
            (id_usuario != "") // se está editando el usuario y no se toma en cuenta la contraseña
        )
    ) {
    
        let nombre = $("#nombre_usuario_registrar").val();
        let apellido = $("#apellido_usuario_registrar").val();
        let email = $("#correo_usuario_registrar").val();
        let celular = $("#telefono_usuario_registrar").val();
        let nivel = $("#nivel_usuario_registrar option:selected").val();
        let pais = $("#pais_usuario_registrar option:selected").val();
        let entidad_federativa = $("#estado_usuario_registrar option:selected").val();
        let ciudad = $("#ciudad_usuario_registrar option:selected").val();
        //let imagen_captura = $("#imagenCamara").val() == "si" ? $("#imagenCamara").attr("src") : false;
        let imagen_subir = $("#imagen_usuario_registrar")[0].files[0] ? $("#imagen_usuario_registrar")[0].files[0] : false;
        
        var datos = new FormData();
        
        datos.append("registrar_usuario", true)
        if(id_usuario) datos.append("id_usuario", id_usuario);
        datos.append("nombre", nombre);
        datos.append("apellido", apellido);
        datos.append("correo", email);
        if(contrasena == confirmar_contrasena) datos.append("contrasena", contrasena);
        datos.append("telefono", celular);
        datos.append("nivel", nivel);
        datos.append("pais", pais);
        datos.append("entidad_federativa", entidad_federativa);
        datos.append("ciudad", ciudad);
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
                    (id_usuario != "") ? alertaUpdate() : alertaInsert();
                    //window.history.back();
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