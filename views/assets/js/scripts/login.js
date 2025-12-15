$(document).on("submit", "#formularioIngreso", function (e) {
  e.preventDefault();
  let usuario = $("#usuarioIngreso").val();
  let contrasena = $("#contrasenaIngreso").val();

  let datos = new FormData();
  datos.append("usuarioIngreso", usuario);
  datos.append("contrasenaIngreso", contrasena);

  $.ajax({
    url: url + "views/ajax/ajax_login.php",
    method: "POST",
    data: datos,
    cache: false,
    contentType: false,
    processData: false,
    beforeSend: () => loading(true),
    success: function (res) {
      loading(false);

      let respuesta = JSON.parse(res);
      if (respuesta.success) {
        // redirige
        window.location = url + respuesta.redirect;
        return;
      }

      // errores
      switch (respuesta.mensaje) {
        case "desactivado":
          swal("¡Error!", "¡El usuario está desactivado!", "error");
          break;
        case "verificacion":
          swal("¡Error!", "¡Esta cuenta no está verificada!", "error").then(
            () => {
              window.location = url + "registrarme/verificacion";
            }
          );
          break;
        default: // 'invalido' u otro
          swal("¡Error!", "¡Usuario o contraseña incorrectos!", "error");
      }
    },
    error: function () {
      loading(false);
      swal("¡Error!", "Algo falló en la petición. Intenta de nuevo.", "error");
    },
  });
});

// Función callback para Google Sign In - debe estar en scope global
window.handleGoogleSignIn = function(response) {
  console.log("Google Sign In Response:", response);
  
  // Verificar si es un error
  if (!response || typeof response !== 'object' || Array.isArray(response)) {
    console.error("Error en respuesta de Google:", response);
    swal("¡Error!", "Error al autenticar con Google. Verifica tu configuración.", "error");
    return;
  }

  // Verificar que tenemos el credential (ID Token)
  if (!response.credential) {
    console.error("No se recibió credential en la respuesta:", response);
    swal("¡Error!", "No se pudo obtener el token de Google. Intenta de nuevo.", "error");
    return;
  }

  // Enviar el token al backend para validación
  let datos = new FormData();
  datos.append("googleToken", response.credential);
  datos.append("googleLogin", "true");

  $.ajax({
    url: url + "views/ajax/ajax_login.php",
    method: "POST",
    data: datos,
    cache: false,
    contentType: false,
    processData: false,
    beforeSend: () => loading(true),
    success: function (res) {
      loading(false);
      
      try {
        let respuesta = JSON.parse(res);
        if (respuesta.success) {
          // redirige
          window.location = url + respuesta.redirect;
          return;
        }

        // errores
        switch (respuesta.mensaje) {
          case "desactivado":
            swal("¡Error!", "¡El usuario está desactivado!", "error");
            break;
          case "token_invalido":
            swal("¡Error!", "Token de Google inválido. Intenta de nuevo.", "error");
            break;
          case "completar_info":
            // Mostrar modal para completar información
            if (respuesta.usuario_id) {
              mostrarModalCompletarInfo(respuesta.usuario_id);
            } else {
              swal("¡Error!", "Error al obtener información del usuario.", "error");
            }
            break;
          default:
            swal("¡Error!", respuesta.mensaje || "Error al iniciar sesión con Google", "error");
        }
      } catch (e) {
        console.error("Error al parsear respuesta:", e, res);
        swal("¡Error!", "Error al procesar la respuesta del servidor.", "error");
      }
    },
    error: function (xhr, status, error) {
      loading(false);
      console.error("Error AJAX:", status, error, xhr.responseText);
      swal("¡Error!", "Algo falló en la petición. Intenta de nuevo.", "error");
    },
  });
};

// Función para mostrar modal de completar información
function mostrarModalCompletarInfo(usuarioId) {
  $("#id_usuario_google").val(usuarioId);
  $("#nivel_seleccionado_google").val('');
  $("#btnGuardarInfo").prop('disabled', true);
  
  // Limpiar selección previa
  $(".card-nivel-google").removeClass('seleccionado');
  
  // Cargar niveles (excluyendo Administrador)
  $.ajax({
    url: url + "views/ajax/ajax_general.php?obtenerNivelesPublico",
    type: "GET",
    dataType: 'json',
    success: function(response) {
      $("#opciones_nivel_google").empty();
      
      if (response.niveles) {
        // Mapeo de niveles a imágenes y descripciones
        const nivelInfo = {
          'Cliente': {
            imagen: url + 'views/assets/img/utilidades/registro/cliente2.jpg',
            titulo: 'Amante del café',
            descripcion: 'Busco una cafetería para disfrutar de un buen café'
          },
          'Barista': {
            imagen: url + 'views/assets/img/utilidades/registro/barra1.jpg',
            titulo: 'Barista',
            descripcion: 'Soy barista y estoy interesado/a en oportunidades de trabajo'
          },
          'Propietario': {
            imagen: url + 'views/assets/img/utilidades/registro/propietario.jpg',
            titulo: 'Propietario',
            descripcion: 'Tengo una o varias cafeterías y quiero conectarme con otros amantes del café'
          }
        };
        
        response.niveles.forEach(function(nivel) {
          // Excluir Administrador
          if (nivel.nombre === 'Administrador') {
            return;
          }
          
          const info = nivelInfo[nivel.nombre] || {
            imagen: url + 'views/assets/img/usuario_default.png',
            titulo: nivel.nombre,
            descripcion: nivel.nombre
          };
          
          const cardHtml = `
            <div class="col">
              <div class="card h-100 card-nivel-google" data-nivel="${nivel.nombre}">
                <img src="${info.imagen}" class="card-img-top imagen-arriba-modal" alt="${info.titulo}">
                <div class="card-body">
                  <div class="card-header">
                    <h5 class="card-title mt-1">${info.titulo}</h5>
                  </div>
                  <p class="card-text mt-2">${info.descripcion}</p>
                </div>
              </div>
            </div>
          `;
          $("#opciones_nivel_google").append(cardHtml);
        });
      }
    }
  });
  
  // Mostrar modal
  var modal = new bootstrap.Modal(document.getElementById('modalCompletarInfo'));
  modal.show();
}

// Seleccionar nivel al hacer clic en la tarjeta
$(document).on("click", ".card-nivel-google", function() {
  $(".card-nivel-google").removeClass('seleccionado');
  $(this).addClass('seleccionado');
  const nivel = $(this).data('nivel');
  $("#nivel_seleccionado_google").val(nivel);
  
  // Habilitar botón si también hay ciudad seleccionada
  verificarCamposCompletos();
});

// Función para verificar si los campos están completos
function verificarCamposCompletos() {
  const nivel = $("#nivel_seleccionado_google").val();
  
  if (nivel) {
    $("#btnGuardarInfo").prop('disabled', false);
  } else {
    $("#btnGuardarInfo").prop('disabled', true);
  }
}

// Guardar información completada
$(document).on("click", "#btnGuardarInfo", function() {
  let id_usuario = $("#id_usuario_google").val();
  let nivel = $("#nivel_seleccionado_google").val();
  
  if (!nivel) {
    swal("¡Error!", "Por favor selecciona un tipo de usuario.", "error");
    return;
  }
  
  let datos = new FormData();
  datos.append("completarInfoGoogle", "true");
  datos.append("id_usuario", id_usuario);
  datos.append("id_ciudad", 0); // Ciudad por defecto (sin ciudad)
  datos.append("nivel", nivel);
  
  $.ajax({
    url: url + "views/ajax/ajax_login.php",
    method: "POST",
    data: datos,
    cache: false,
    contentType: false,
    processData: false,
    beforeSend: () => loading(true),
    success: function (res) {
      loading(false);
      
      try {
        let respuesta = JSON.parse(res);
        if (respuesta.success) {
          // Cerrar modal y redirigir
          bootstrap.Modal.getInstance(document.getElementById('modalCompletarInfo')).hide();
          window.location = url + respuesta.redirect;
          return;
        }
        
        swal("¡Error!", respuesta.mensaje || "Error al completar la información", "error");
      } catch (e) {
        console.error("Error al parsear respuesta:", e, res);
        swal("¡Error!", "Error al procesar la respuesta del servidor.", "error");
      }
    },
    error: function (xhr, status, error) {
      loading(false);
      console.error("Error AJAX:", status, error, xhr.responseText);
      swal("¡Error!", "Algo falló en la petición. Intenta de nuevo.", "error");
    },
  });
});