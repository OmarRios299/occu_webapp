/* FORMULARIO INGRESO */

$(document).on('submit', '#formularioIngreso', function(){

	let usuario     = $("#usuarioIngreso").val();
	let contrasena  = $("#contrasenaIngreso").val();

	let datos = new FormData();

    datos.append("usuarioIngreso", usuario);
    datos.append("contrasenaIngreso", contrasena);

    $.ajax({
        url:url+"views/ajax/ajax_login.php",
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

            }else{

                swal("¡Error!", "¡Usuario o contraseña incorrectos!", "error");

            }

        }

    });

});

/* End of FORMULARIO INGRESO */
