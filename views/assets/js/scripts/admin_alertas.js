$(document).ready(function () {
  // Solo cargar tabla si estamos en la página de listado
  if ($("#tabla_alertas").length && moduloActual == "admin_alertas") {
    // Esperar un momento para asegurar que el DOM está completamente listo
    setTimeout(function() {
      cargarTablaAlertas();
    }, 100);
  }

  if (moduloActual == "admin_alertas") {
    // Inicializar Select2 para el campo de módulo
    if ($("#id_modulo_alerta").length) {
      $("#id_modulo_alerta").select2({
        placeholder: "Selecciona un módulo o deja vacío para alerta general",
        allowClear: true,
        width: '100%'
      });
    }
    function actualizarCampos() {
      var plantilla = $("#plantilla_alerta").val();
      var tipoMostrar = $("#tipo_mostrar_alerta").val();

      // Mostrar HTML personalizado solo si plantilla es personalizado
      if (plantilla === "personalizado") {
        $("#contenedor_html_personalizado").show();
        $("#html_personalizado_alerta").prop("required", true);
        $("#contenedor_tour_guido").hide();
      } else {
        $("#contenedor_html_personalizado").hide();
        $("#html_personalizado_alerta").prop("required", false);
      }

      // Mostrar configuración de tour solo si plantilla es tour_guido
      if (plantilla === "tour_guido") {
        $("#contenedor_tour_guido").show();
        $("#contenedor_html_personalizado").hide();
        $("#html_personalizado_alerta").prop("required", false);
      } else {
        $("#contenedor_tour_guido").hide();
      }

      // Mostrar veces a mostrar solo si tipo es n_veces
      if (tipoMostrar === "n_veces") {
        $("#contenedor_veces_mostrar").show();
        $("#veces_mostrar_alerta").prop("required", true);
      } else {
        $("#contenedor_veces_mostrar").hide();
        $("#veces_mostrar_alerta").prop("required", false);
      }
    }

    // Solo ejecutar si los elementos existen (formulario de alerta)
    if ($("#plantilla_alerta").length) {
      $("#plantilla_alerta, #tipo_mostrar_alerta").on("change", actualizarCampos);
      actualizarCampos();
    }

    // Manejar checkbox "Todos los roles" (solo si existe)
    if ($("#rol_todos").length) {
      $("#rol_todos").on("change", function () {
        if ($(this).is(":checked")) {
          $(".rol-checkbox").prop("checked", false);
        }
      });

      $(".rol-checkbox").on("change", function () {
        if ($(this).is(":checked")) {
          $("#rol_todos").prop("checked", false);
        }
      });
    }

    // Agregar botón (solo si existe)
    if ($("#agregar_boton").length) {
      $("#agregar_boton").on("click", function () {
      var botonHtml =
        '<div class="boton-item mb-3 p-3 border rounded">' +
        '<div class="row">' +
        '<div class="col-md-4"><label>Texto:</label><input type="text" class="form-control boton-texto" placeholder="Texto del botón"></div>' +
        '<div class="col-md-3"><label>URL:</label><input type="text" class="form-control boton-url" placeholder="/ruta"></div>' +
        '<div class="col-md-2"><label>Tipo:</label><select class="form-control boton-tipo"><option value="primary">Primary</option><option value="secondary">Secondary</option><option value="success">Success</option><option value="danger">Danger</option></select></div>' +
        '<div class="col-md-2"><label>&nbsp;</label><button type="button" class="btn btn-danger btn-sm w-100 eliminar-boton">Eliminar</button></div>' +
        "</div></div>";
        $("#contenedor_botones").append(botonHtml);
      });
    }

    // Eliminar botón
    $(document).on("click", ".eliminar-boton", function () {
      $(this).closest(".boton-item").remove();
    });

    // Agregar paso del tour (solo si existe)
    if ($("#agregar_paso_tour").length) {
      $("#agregar_paso_tour").on("click", function () {
        var index = $(".paso-tour-item").length;
        var pasoHtml = 
          '<div class="paso-tour-item mb-3 p-3 border rounded" data-index="' + index + '">' +
          '<div class="d-flex justify-content-between align-items-center mb-2">' +
          '<h6 class="mb-0">Paso ' + (index + 1) + '</h6>' +
          '<button type="button" class="btn btn-sm btn-danger eliminar-paso-tour">Eliminar</button>' +
          '</div>' +
          '<div class="row">' +
          '<div class="col-md-6">' +
          '<label>Módulo/Página:</label>' +
          '<select class="form-control paso-modulo">' +
          '<option value="">Selecciona módulo...</option>' +
          getModulosOptions() +
          '</select>' +
          '</div>' +
          '<div class="col-md-6">' +
          '<label>Selector CSS del elemento:</label>' +
          '<input type="text" class="form-control paso-selector" placeholder="Ej: #boton-actualizar, .mi-clase, [data-id=\'123\']">' +
          '<small class="form-text text-muted">Selector CSS del elemento a destacar</small>' +
          '</div>' +
          '</div>' +
          '<div class="row mt-2">' +
          '<div class="col-md-12">' +
          '<label>Título del paso:</label>' +
          '<input type="text" class="form-control paso-titulo" placeholder="Ej: Activa tus categorías">' +
          '</div>' +
          '</div>' +
          '<div class="row mt-2">' +
          '<div class="col-md-12">' +
          '<label>Descripción/Mensaje:</label>' +
          '<textarea class="form-control paso-descripcion" rows="2" placeholder="Explicación detallada del paso..."></textarea>' +
          '</div>' +
          '</div>' +
          '<div class="row mt-2">' +
          '<div class="col-md-6">' +
          '<label>Posición del popup:</label>' +
          '<select class="form-control paso-posicion">' +
          '<option value="top">Arriba</option>' +
          '<option value="bottom">Abajo</option>' +
          '<option value="left">Izquierda</option>' +
          '<option value="right">Derecha</option>' +
          '</select>' +
          '</div>' +
          '<div class="col-md-6">' +
          '<div class="form-check mt-4">' +
          '<input class="form-check-input paso-redirigir" type="checkbox">' +
          '<label class="form-check-label">Redirigir automáticamente a esta página</label>' +
          '</div>' +
          '</div>' +
          '</div>' +
          '</div>';
        $("#contenedor_pasos_tour").append(pasoHtml);
        actualizarIndicesPasos();
      });
    }

    // Eliminar paso del tour
    $(document).on("click", ".eliminar-paso-tour", function () {
      $(this).closest(".paso-tour-item").remove();
      actualizarIndicesPasos();
    });

    // Función para actualizar índices de pasos
    function actualizarIndicesPasos() {
      $(".paso-tour-item").each(function(index) {
        $(this).attr("data-index", index);
        $(this).find("h6").text("Paso " + (index + 1));
      });
    }

    // Función para obtener opciones de módulos desde el select existente
    function getModulosOptions() {
      // Clonar las opciones del select de módulo principal
      var options = '';
      if ($("#id_modulo_alerta").length) {
        $("#id_modulo_alerta option").each(function() {
          if ($(this).val() !== '') {
            var text = $(this).text();
            var value = $(this).val();
            // Extraer la ruta del módulo del texto (formato: "Nombre (ruta)")
            var match = text.match(/\(([^)]+)\)/);
            if (match && match[1]) {
              value = match[1];
            }
            // Si es un optgroup, agregarlo
            if ($(this).parent().is('optgroup')) {
              var groupLabel = $(this).parent().attr('label');
              if (!options.includes('<optgroup label="' + groupLabel + '">')) {
                options += '<optgroup label="' + groupLabel + '">';
              }
              options += '<option value="' + value + '">' + text.replace(/\s*\([^)]+\)\s*$/, '') + '</option>';
            } else {
              options += '<option value="' + value + '">' + text + '</option>';
            }
          }
        });
        // Cerrar optgroups abiertos
        options += '</optgroup>';
      }
      
      // Si no hay opciones, usar valores por defecto
      if (!options || options.trim() === '') {
        options = '<optgroup label="Menú">' +
                 '<option value="menu_categorias">Categorías</option>' +
                 '<option value="menu_productos">Productos</option>' +
                 '<option value="propietarios_menu">Mi Menú</option>' +
                 '</optgroup>' +
                 '<optgroup label="Cafeterías">' +
                 '<option value="cafeterias">Cafeterías</option>' +
                 '</optgroup>';
      }
      
      return options;
    }
  }
});

// Cargar tabla de alertas
function cargarTablaAlertas() {
  // Verificar que el elemento existe
  if (!$("#tabla_alertas").length) {
    return;
  }

  let activa = $("#filtro_activa option:selected").val() || "";
  let plantilla = $("#filtro_plantilla option:selected").val() || "";

  let filtro = `?tabla_alertas=${true}&activa=${activa}&plantilla=${plantilla}`;

  // Destruir DataTable si existe, con manejo de errores
  try {
    if ($.fn.DataTable.isDataTable($("#tabla_alertas"))) {
      $("#tabla_alertas").DataTable().destroy();
      // Limpiar el tbody antes de reinicializar
      $("#tabla_alertas tbody").empty();
    }
  } catch (e) {
    console.warn("Error al destruir DataTable:", e);
    // Si hay error, limpiar manualmente
    $("#tabla_alertas").empty();
  }

  // Inicializar DataTable
  $("#tabla_alertas").DataTable({
    ajax: {
      url: url + "views/ajax/ajax_admin_alertas.php" + filtro,
      dataSrc: function (json) {
        return json.data || [];
      },
      error: function(xhr, error, thrown) {
        console.error("Error al cargar datos:", error, thrown);
        swal("¡Error!", "Error al cargar los datos de la tabla", "error");
      }
    },
    deferRender: true,
    retrieve: true,
    processing: true,
    dom: "Bfrtip",
    responsive: true,
    ordering: true,
    language: { url: url + "views/assets/plugins/DataTables/Spanish.json" },
  });
}

// Filtrar tabla
$(document).on("submit", "#form_tabla_alertas", function (e) {
  e.preventDefault();
  cargarTablaAlertas();
});

// Limpiar filtros
$(document).on("click", "#limpiar_filtros", function () {
  $("#filtro_activa").val("");
  $("#filtro_plantilla").val("");
  cargarTablaAlertas();
});

// Mostrar/ocultar filtros
$(document).on("click", "#filtro_busqueda", function () {
  $(".filtro_busqueda").toggle();
});

// Enviar formulario de alerta
$(document).on("submit", "#form_agregar_alerta", function (e) {
  e.preventDefault();

  let id_alerta = $("#id_alerta").val();
  let esEdicion = id_alerta != "";

  // Validaciones básicas
  let codigo = $("#codigo_alerta").val().trim();
  let titulo = $("#titulo_alerta").val().trim();
  let mensaje = $("#mensaje_alerta").val().trim();
  let plantilla = $("#plantilla_alerta").val();
  let tipoMostrar = $("#tipo_mostrar_alerta").val();

  if (!codigo) {
    swal("¡Error!", "El código es obligatorio", "error");
    return false;
  }

  if (!titulo) {
    swal("¡Error!", "El título es obligatorio", "error");
    return false;
  }

  if (!mensaje) {
    swal("¡Error!", "El mensaje es obligatorio", "error");
    return false;
  }

  // Validar HTML personalizado si plantilla es personalizado
  if (plantilla === "personalizado") {
    let htmlPersonalizado = $("#html_personalizado_alerta").val().trim();
    if (!htmlPersonalizado) {
      swal(
        "¡Error!",
        "El HTML personalizado es obligatorio cuando la plantilla es 'Personalizado'",
        "error"
      );
      return false;
    }
  }

  // Validar pasos del tour si plantilla es tour_guido
  if (plantilla === "tour_guido") {
    let pasosTour = [];
    $(".paso-tour-item").each(function() {
      let modulo = $(this).find(".paso-modulo").val();
      let selector = $(this).find(".paso-selector").val().trim();
      let titulo = $(this).find(".paso-titulo").val().trim();
      let descripcion = $(this).find(".paso-descripcion").val().trim();
      let posicion = $(this).find(".paso-posicion").val();
      let redirigir = $(this).find(".paso-redirigir").is(":checked");

      if (modulo && selector && titulo) {
        pasosTour.push({
          modulo: modulo,
          selector: selector,
          titulo: titulo,
          descripcion: descripcion,
          posicion: posicion || "top",
          redirigir: redirigir
        });
      }
    });

    if (pasosTour.length === 0) {
      swal(
        "¡Error!",
        "Debes agregar al menos un paso al tour guiado",
        "error"
      );
      return false;
    }
  }

  // Validar veces a mostrar si tipo es n_veces
  if (tipoMostrar === "n_veces") {
    let vecesMostrar = $("#veces_mostrar_alerta").val();
    if (!vecesMostrar || vecesMostrar < 1) {
      swal(
        "¡Error!",
        "Debes especificar cuántas veces mostrar la alerta",
        "error"
      );
      return false;
    }
  }

  // Obtener módulo seleccionado
  let id_modulo = $("#id_modulo_alerta").val();
  if (id_modulo === "") {
    id_modulo = null;
  }

  // Obtener roles seleccionados
  let roles = [];
  if (!$("#rol_todos").is(":checked")) {
    $(".rol-checkbox:checked").each(function () {
      roles.push($(this).val());
    });
  }

  // Obtener botones
  let botones = [];
  $(".boton-item").each(function () {
    let texto = $(this).find(".boton-texto").val().trim();
    if (texto) {
      botones.push({
        texto: texto,
        url: $(this).find(".boton-url").val().trim() || null,
        tipo: $(this).find(".boton-tipo").val() || "primary",
        cerrar: false,
      });
    }
  });

  // Preparar datos
  let datos = new FormData();
  datos.append("registrar_alerta", true);
  if (esEdicion) datos.append("id_alerta", id_alerta);
  datos.append("codigo", codigo);
  datos.append("titulo", titulo);
  datos.append("mensaje", mensaje);
  datos.append("tipo", $("#tipo_alerta").val());
  datos.append("icono", $("#icono_alerta").val() || "");
  datos.append("tipo_mostrar", tipoMostrar);
  datos.append("veces_mostrar", $("#veces_mostrar_alerta").val() || 1);
  datos.append("veces_ignorar", $("#veces_ignorar_alerta").val() || 0);
  datos.append("fecha_inicio", $("#fecha_inicio_alerta").val() || "");
  datos.append("fecha_fin", $("#fecha_fin_alerta").val() || "");
  datos.append("activa", $("#activa_alerta").is(":checked") ? 1 : 0);
  datos.append("id_modulo", id_modulo || "");
  datos.append("prioridad", $("#prioridad_alerta").val() || 0);
  datos.append("plantilla", plantilla);
  
  // Si es tour guiado, guardar los pasos como JSON en html_personalizado
  if (plantilla === "tour_guido") {
    let pasosTour = [];
    $(".paso-tour-item").each(function() {
      let modulo = $(this).find(".paso-modulo").val();
      let selector = $(this).find(".paso-selector").val().trim();
      let titulo = $(this).find(".paso-titulo").val().trim();
      let descripcion = $(this).find(".paso-descripcion").val().trim();
      let posicion = $(this).find(".paso-posicion").val();
      let redirigir = $(this).find(".paso-redirigir").is(":checked");

      if (modulo && selector && titulo) {
        pasosTour.push({
          modulo: modulo,
          selector: selector,
          titulo: titulo,
          descripcion: descripcion,
          posicion: posicion || "top",
          redirigir: redirigir
        });
      }
    });
    datos.append("html_personalizado", JSON.stringify(pasosTour));
  } else {
    datos.append(
      "html_personalizado",
      $("#html_personalizado_alerta").val() || ""
    );
  }
  
  datos.append(
    "estilo_personalizado",
    $("#estilo_personalizado_alerta").val() || ""
  );

  // Agregar roles
  roles.forEach(function (rol, index) {
    datos.append("roles[]", rol);
  });

  // Agregar botones
  botones.forEach(function (boton, index) {
    datos.append("botones[" + index + "][texto]", boton.texto);
    datos.append("botones[" + index + "][url]", boton.url || "");
    datos.append("botones[" + index + "][tipo]", boton.tipo);
  });

  // Enviar
  $.ajax({
    url: url + "views/ajax/ajax_admin_alertas.php",
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
          swal("¡Éxito!", respuesta.mensaje, "success").then(() => {
            window.location.href = url + "admin_alertas";
          });
        } else {
          swal(
            "¡Error!",
            respuesta.mensaje || "Error al guardar la alerta",
            "error"
          );
        }
      } catch (e) {
        console.error("Error al parsear respuesta:", e, res);
        swal("¡Error!", "Error al procesar la respuesta del servidor", "error");
      }
    },
    error: function (xhr, status, error) {
      loading(false);
      console.error("Error AJAX:", status, error, xhr.responseText);
      swal("¡Error!", "Algo falló en la petición. Intenta de nuevo.", "error");
    },
  });

  return false;
});

// Cambiar estado de alerta (activa/inactiva)
$(document).on("change", ".cambioEstado", function () {
  let $checkbox = $(this); // Guardar referencia al checkbox
  let idRegistro = $checkbox.attr("idRegistro");
  let tabla = $checkbox.attr("tabla");
  let campo = $checkbox.attr("campo") || "activa";
  let valor = $checkbox.is(":checked") ? 1 : 0;

  if (tabla === "sistema_alertas") {
    let datos = new FormData();
    datos.append("cambiar_estado", true);
    datos.append("id_alerta", idRegistro);
    datos.append("campo", campo);
    datos.append("valor", valor);

    $.ajax({
      url: url + "views/ajax/ajax_admin_alertas.php",
      method: "POST",
      data: datos,
      cache: false,
      contentType: false,
      processData: false,
      success: function (res) {
        try {
          let respuesta = JSON.parse(res);
          if (respuesta.success) {
            // Recargar tabla solo si existe
            if ($("#tabla_alertas").length) {
              cargarTablaAlertas();
            }
          } else {
            swal("¡Error!", "Error al cambiar el estado", "error");
            // Revertir checkbox
            $checkbox.prop("checked", !valor);
          }
        } catch (e) {
          console.error("Error:", e);
          swal("¡Error!", "Error al procesar la respuesta", "error");
          // Revertir checkbox en caso de error
          $checkbox.prop("checked", !valor);
        }
      },
      error: function () {
        swal("¡Error!", "Error en la petición", "error");
        // Revertir checkbox
        $checkbox.prop("checked", !valor);
      },
    });
  }
});

// Vista previa de alerta
$(document).on("click", ".btn-ver-preview", function() {
  let idAlerta = $(this).data("id-alerta");
  
  if (!idAlerta) {
    swal("¡Error!", "No se pudo obtener el ID de la alerta", "error");
    return;
  }
  
  // Obtener datos de la alerta
  $.ajax({
    url: url + "views/ajax/ajax_admin_alertas.php?obtener_alerta_preview=true&id_alerta=" + idAlerta,
    method: "GET",
    dataType: "json",
    beforeSend: () => loading(true),
    success: function(res) {
      loading(false);
      
      if (res.success && res.alerta) {
        // Mostrar la alerta usando las funciones de alertas.js
        mostrarAlertaPreview(res.alerta);
      } else {
        swal("¡Error!", res.mensaje || "Error al obtener la alerta", "error");
      }
    },
    error: function(xhr, status, error) {
      loading(false);
      console.error("Error AJAX:", status, error, xhr.responseText);
      swal("¡Error!", "Error al cargar la vista previa", "error");
    }
  });
});

// Función para mostrar vista previa de alerta (sin marcar como vista)
function mostrarAlertaPreview(alerta) {
  // Asegurar que el ID sea único para la vista previa
  alerta.id = 'preview_' + alerta.id;
  
  switch(alerta.plantilla) {
    case 'default':
      mostrarAlertaDefaultPreview(alerta);
      break;
    case 'modal':
      mostrarAlertaModalPreview(alerta);
      break;
    case 'banner':
      mostrarAlertaBannerPreview(alerta);
      break;
    case 'card':
      mostrarAlertaCardPreview(alerta);
      break;
    case 'personalizado':
      mostrarAlertaPersonalizadaPreview(alerta);
      break;
    default:
      mostrarAlertaDefaultPreview(alerta);
  }
}

// Vista previa - Alerta simple (SweetAlert)
function mostrarAlertaDefaultPreview(alerta) {
  var botones = {};
  var tieneBotones = alerta.botones && alerta.botones.length > 0;
  
  if (tieneBotones) {
    alerta.botones.forEach(function(boton, index) {
      var key = boton.texto.toLowerCase().replace(/\s+/g, '_') || 'btn_' + index;
      botones[key] = {
        text: boton.texto,
        value: boton.url || 'cerrar'
      };
    });
  } else {
    botones = { confirm: 'Cerrar vista previa' };
  }
  
  var icono = alerta.icono ? alerta.icono : alerta.tipo;
  
  swal({
    title: alerta.titulo + ' (Vista Previa)',
    text: alerta.mensaje,
    icon: icono,
    buttons: botones,
    className: 'alerta-sistema'
  }).then(function(value) {
    // No marcar como vista, solo cerrar
    if (value && value !== 'cerrar' && value !== true && value.startsWith('/')) {
      swal("Vista previa", "En la versión real, esto redirigiría a: " + value, "info");
    }
  });
}

// Vista previa - Modal Bootstrap
function mostrarAlertaModalPreview(alerta) {
  var modalId = 'alertaModalPreview' + alerta.id;
  var tipoClase = 'alert-' + (alerta.tipo || 'info');
  var iconoHTML = alerta.icono ? '<i class="' + alerta.icono + '"></i> ' : '';
  
  var modalHTML = '<div class="modal fade" id="' + modalId + '" tabindex="-1" aria-labelledby="' + modalId + 'Label" aria-hidden="true">' +
    '<div class="modal-dialog modal-dialog-centered">' +
    '<div class="modal-content">' +
    '<div class="modal-header ' + tipoClase + '">' +
    '<h5 class="modal-title" id="' + modalId + 'Label">' + iconoHTML + alerta.titulo + ' <small>(Vista Previa)</small></h5>' +
    '<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>' +
    '</div>' +
    '<div class="modal-body">' +
    '<p>' + alerta.mensaje + '</p>';
  
  if (alerta.botones && alerta.botones.length > 0) {
    modalHTML += '<div class="d-flex gap-2 mt-3">';
    alerta.botones.forEach(function(boton) {
      var clase = 'btn btn-' + (boton.tipo || 'primary');
      modalHTML += '<button type="button" class="' + clase + '" onclick="swal(\'Vista previa\', \'En la versión real, esto redirigiría a: ' + (boton.url || '#') + '\', \'info\');">' + boton.texto + '</button>';
    });
    modalHTML += '</div>';
  }
  
  modalHTML += '</div>' +
    '<div class="modal-footer">' +
    '<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar vista previa</button>' +
    '</div>' +
    '</div>' +
    '</div>' +
    '</div>';
  
  // Remover modal anterior si existe
  $('#' + modalId).remove();
  
  $('body').append(modalHTML);
  var modal = new bootstrap.Modal(document.getElementById(modalId));
  modal.show();
  
  // Limpiar al cerrar
  $('#' + modalId).on('hidden.bs.modal', function() {
    $(this).remove();
  });
}

// Vista previa - Banner
function mostrarAlertaBannerPreview(alerta) {
  var bannerId = 'alertaBannerPreview' + alerta.id;
  var tipoClase = 'alert-' + (alerta.tipo || 'info');
  var iconoHTML = alerta.icono ? '<i class="' + alerta.icono + '"></i> ' : '';
  
  var bannerHTML = '<div class="alert ' + tipoClase + ' alert-dismissible fade show" id="' + bannerId + '" role="alert" style="position: fixed; top: 0; left: 0; right: 0; z-index: 10000; margin: 0; border-radius: 0;">' +
    iconoHTML + '<strong>' + alerta.titulo + '</strong> (Vista Previa) - ' + alerta.mensaje +
    '<button type="button" class="btn-close" onclick="$(\'#' + bannerId + '\').fadeOut(300, function(){$(this).remove();});"></button>' +
    '</div>';
  
  $('#' + bannerId).remove();
  $('body').prepend(bannerHTML);
  
  // Ajustar padding del body
  $('body').css('padding-top', $('#' + bannerId).outerHeight() + 'px');
}

// Vista previa - Card flotante
function mostrarAlertaCardPreview(alerta) {
  var cardId = 'alertaCardPreview' + alerta.id;
  var tipoClase = 'alert-' + (alerta.tipo || 'info');
  var iconoHTML = alerta.icono ? '<i class="' + alerta.icono + '"></i> ' : '';
  
  var cardHTML = '<div class="card alerta-card shadow-lg" id="' + cardId + '" style="position: fixed; top: 20px; right: 20px; z-index: 10000; max-width: 400px; min-width: 300px;">' +
    '<div class="card-header ' + tipoClase + ' d-flex justify-content-between align-items-center">' +
    '<h6 class="mb-0">' + iconoHTML + alerta.titulo + ' <small>(Vista Previa)</small></h6>' +
    '<button type="button" class="btn-close btn-close-white" onclick="$(\'#' + cardId + '\').fadeOut(300, function(){$(this).remove();});"></button>' +
    '</div>' +
    '<div class="card-body">' +
    '<p class="card-text">' + alerta.mensaje + '</p>';
  
  if (alerta.botones && alerta.botones.length > 0) {
    cardHTML += '<div class="d-flex gap-2">';
    alerta.botones.forEach(function(boton) {
      var clase = 'btn btn-sm btn-' + (boton.tipo || 'primary');
      cardHTML += '<button type="button" class="' + clase + '" onclick="swal(\'Vista previa\', \'En la versión real, esto redirigiría a: ' + (boton.url || '#') + '\', \'info\');">' + boton.texto + '</button>';
    });
    cardHTML += '</div>';
  }
  
  cardHTML += '</div></div>';
  
  $('#' + cardId).remove();
  $('body').append(cardHTML);
}

// Vista previa - HTML personalizado
function mostrarAlertaPersonalizadaPreview(alerta) {
  if (!alerta.html_personalizado) {
    swal("¡Error!", "Esta alerta no tiene HTML personalizado", "error");
    return;
  }
  
  var contenedorId = 'alertaPersonalizadaPreview' + alerta.id;
  
  // Reemplazar ALERTA_ID con el ID de preview
  var htmlPersonalizado = alerta.html_personalizado.replace(/ALERTA_ID/g, alerta.id);
  
  var contenedor = $('<div>')
    .attr('id', contenedorId)
    .addClass('alerta-personalizada')
    .html(htmlPersonalizado)
    .css({
      'position': 'fixed',
      'top': '50%',
      'left': '50%',
      'transform': 'translate(-50%, -50%)',
      'z-index': '10000',
      'max-width': '90%',
      'max-height': '90vh',
      'overflow': 'auto'
    });
  
  // Agregar overlay
  var overlay = $('<div>')
    .addClass('alerta-overlay')
    .css({
      'position': 'fixed',
      'top': 0,
      'left': 0,
      'right': 0,
      'bottom': 0,
      'background': 'rgba(0,0,0,0.5)',
      'z-index': '9999'
    })
    .on('click', function() {
      $('.alerta-overlay').remove();
      $('#' + contenedorId).fadeOut(300, function() {
        $(this).remove();
      });
    });
  
  // Agregar botón de cerrar si no existe
  if (!htmlPersonalizado.includes('cerrarAlertaPersonalizada')) {
    contenedor.append('<div class="text-center mt-3"><button class="btn btn-secondary" onclick="$(\'.alerta-overlay\').remove(); $(\'#' + contenedorId + '\').fadeOut(300, function(){$(this).remove();});">Cerrar vista previa</button></div>');
  }
  
  $('#' + contenedorId).remove();
  $('.alerta-overlay').remove();
  $('body').append(overlay).append(contenedor);
  
  // Agregar estilos personalizados
  if (alerta.estilo_personalizado) {
    var styleId = 'estilo-alerta-preview-' + alerta.id;
    if (!$('#' + styleId).length) {
      $('<style id="' + styleId + '">').text(alerta.estilo_personalizado).appendTo('head');
    }
  }
}
