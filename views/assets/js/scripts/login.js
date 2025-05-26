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
