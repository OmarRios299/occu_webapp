// Variables para Leaflet
let map_registrar;
let currentMarker_registrar = null;
let allowedPolygon_registrar = null;
let initialized_map = false;

// Función para verificar que Leaflet esté cargado
function waitForLeaflet(callback, maxAttempts = 50) {
  let attempts = 0;
  const checkLeaflet = () => {
    if (typeof L !== "undefined") {
      callback();
    } else {
      attempts++;
      if (attempts < maxAttempts) {
        setTimeout(checkLeaflet, 100);
      } else {
        console.error("Leaflet no se pudo cargar después de varios intentos");
      }
    }
  };
  checkLeaflet();
}

$(document).ready(function () {
  if (moduloActual == "cafeterias" && $("#ciudad_select").length) {
    // Inicializar polígono cuando se carga la página
    initializePolygonAndMap();

    // Escuchar el cambio del select para actualizar el polígono y centrar el mapa
    $("#ciudad_select").change(function () {
      updatePolygonAndMap();
    });

    // Evento para mostrar el modal y cargar el mapa
    $("#modal_ubicacion").on("shown.bs.modal", function () {
      if (!initialized_map) {
        waitForLeaflet(() => {
          initializeMap();
        });
      } else {
        setTimeout(() => {
          if (map_registrar) {
            map_registrar.invalidateSize();
          }
        }, 300);
      }
    });
  }
  // if ($('#tabla_imagenes').length) {
  //     cargarTablaImagenes();
  // }
  if ($("#tabla_cafeterias").length) {
    cargarTablaCafeterias();
  }

  if ($("#switch_horario").is(":checked")) {
    $(".inp_horario").show();
    $(".inp_horario").attr("required", true);
    $(".tbl_horario").hide();
    $(".tbl_horario").removeAttr("required");
  } else {
    $(".inp_horario").hide();
    $(".tbl_horario").show();
    $(".inp_horario").val("");
    $(".inp_horario").removeAttr("required");
    $(".tbl_horario").attr("required", true);
  }
});

// Función para inicializar el polígono (sin mapa aún)
function initializePolygonAndMap() {
  const coordenadasAttr = $("#ciudad_select option:selected").attr(
    "coordenadas"
  );
  if (
    coordenadasAttr &&
    coordenadasAttr.trim() !== "" &&
    coordenadasAttr !== "null"
  ) {
    try {
      const polygonCoordinates = JSON.parse(coordenadasAttr);
      // Guardar las coordenadas para usar cuando se inicialice el mapa
      allowedPolygon_registrar = polygonCoordinates;
    } catch (e) {
      console.error("Error al parsear coordenadas:", e);
      allowedPolygon_registrar = null;
    }
  } else {
    allowedPolygon_registrar = null;
  }
}

// Función para inicializar el mapa con Leaflet
function initializeMap() {
  if (typeof L === "undefined") {
    console.error("Leaflet no está disponible");
    waitForLeaflet(() => {
      initializeMap();
    });
    return;
  }

  // Asegurarse de que el polígono esté cargado antes de inicializar
  if (!allowedPolygon_registrar) {
    initializePolygonAndMap();
  }

  const latitud = parseFloat($("#latitud_cafeteria").val()) || null;
  const longitud = parseFloat($("#longitud_cafeteria").val()) || null;

  // Determinar el centro inicial
  let centerLat, centerLng;
  if (latitud && longitud) {
    centerLat = latitud;
    centerLng = longitud;
  } else if (allowedPolygon_registrar && allowedPolygon_registrar.length > 0) {
    const centroid = getGeographicCentroid(allowedPolygon_registrar);
    centerLat = centroid[0];
    centerLng = centroid[1];
  } else {
    centerLat = 32.624538;
    centerLng = -115.452263;
  }

  // Inicializar el mapa de Leaflet
  map_registrar = L.map("map", {
    center: [centerLat, centerLng],
    zoom: latitud && longitud ? 15 : 13,
    zoomControl: true,
  });

  // Agregar capa de tiles (OpenStreetMap)
  L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
    attribution: "© OpenStreetMap contributors",
    maxZoom: 19,
  }).addTo(map_registrar);

  // El polígono se mantiene en memoria para validación pero NO se muestra visualmente
  // Centrar el mapa basado en el polígono si existe, pero sin mostrarlo
  if (allowedPolygon_registrar && allowedPolygon_registrar.length > 0) {
    const latlngs = allowedPolygon_registrar.map((coord) => [
      coord.lat,
      coord.lng,
    ]);
    // Crear polígono temporal solo para calcular bounds, pero NO agregarlo al mapa
    const tempPolygon = L.polygon(latlngs);

    // Si hay marcador, ajustar vista para mostrar el marcador y el área del polígono
    if (latitud && longitud) {
      const markerBounds = L.latLngBounds([[latitud, longitud]]);
      const polygonBounds = tempPolygon.getBounds();
      map_registrar.fitBounds(markerBounds.extend(polygonBounds).pad(0.1));
    } else {
      // Centrar en el polígono sin mostrarlo
      map_registrar.fitBounds(tempPolygon.getBounds());
    }
  }

  // Agregar marcador si hay coordenadas guardadas
  if (latitud && longitud) {
    currentMarker_registrar = L.marker([latitud, longitud]).addTo(
      map_registrar
    );
  }

  // Agregar evento click al mapa para seleccionar ubicación
  map_registrar.on("click", function (e) {
    placeMarkerAndSaveData(e.latlng);
  });

  initialized_map = true;
  console.log("Mapa inicializado con Leaflet");
}

// Función para actualizar el polígono y centrar el mapa al cambiar de ciudad
function updatePolygonAndMap() {
  const coordenadasAttr = $("#ciudad_select option:selected").attr(
    "coordenadas"
  );

  if (
    coordenadasAttr &&
    coordenadasAttr.trim() !== "" &&
    coordenadasAttr !== "null"
  ) {
    try {
      const polygonCoordinates = JSON.parse(coordenadasAttr);
      allowedPolygon_registrar = polygonCoordinates;

      // Si el mapa ya está inicializado, actualizar el centro sin mostrar el polígono
      if (map_registrar && initialized_map) {
        // Eliminar polígonos anteriores si existen (por si acaso)
        map_registrar.eachLayer(function (layer) {
          if (layer instanceof L.Polygon) {
            map_registrar.removeLayer(layer);
          }
        });

        // Centrar el mapa en el polígono sin mostrarlo visualmente
        if (polygonCoordinates.length > 0) {
          const latlngs = polygonCoordinates.map((coord) => [
            coord.lat,
            coord.lng,
          ]);
          const tempPolygon = L.polygon(latlngs);

          // Si hay marcador, ajustar vista para mostrar ambos
          const latitud = parseFloat($("#latitud_cafeteria").val());
          const longitud = parseFloat($("#longitud_cafeteria").val());

          if (latitud && longitud && currentMarker_registrar) {
            const markerBounds = L.latLngBounds([[latitud, longitud]]);
            const polygonBounds = tempPolygon.getBounds();
            map_registrar.fitBounds(
              markerBounds.extend(polygonBounds).pad(0.1)
            );
          } else {
            map_registrar.fitBounds(tempPolygon.getBounds());
          }
        }
      }
    } catch (e) {
      console.error("Error al parsear coordenadas:", e);
      allowedPolygon_registrar = null;
    }
  } else {
    allowedPolygon_registrar = null;
  }
}

// Función para calcular el centroide geográfico de un conjunto de coordenadas
function getGeographicCentroid(coordinates) {
  if (!coordinates || coordinates.length === 0) {
    return [32.624538, -115.452263]; // Coordenadas por defecto
  }

  let latSum = 0;
  let lngSum = 0;
  let numPoints = coordinates.length;

  coordinates.forEach((coord) => {
    latSum += coord.lat;
    lngSum += coord.lng;
  });

  let centroidLat = latSum / numPoints;
  let centroidLng = lngSum / numPoints;

  return [centroidLat, centroidLng];
}

// Función para verificar si un punto está dentro de un polígono (algoritmo ray casting)
function isPointInPolygon(point, polygon) {
  if (!polygon || polygon.length < 3) return false;

  let x = point.lat;
  let y = point.lng;
  let inside = false;

  for (let i = 0, j = polygon.length - 1; i < polygon.length; j = i++) {
    let xi = polygon[i].lat,
      yi = polygon[i].lng;
    let xj = polygon[j].lat,
      yj = polygon[j].lng;

    let intersect =
      yi > y !== yj > y && x < ((xj - xi) * (y - yi)) / (yj - yi) + xi;
    if (intersect) inside = !inside;
  }

  return inside;
}

// Función para colocar el marcador y guardar los datos
function placeMarkerAndSaveData(latlng) {
  // Verificar si la ubicación está dentro del polígono permitido
  if (allowedPolygon_registrar && allowedPolygon_registrar.length > 0) {
    const point = { lat: latlng.lat, lng: latlng.lng };
    if (!isPointInPolygon(point, allowedPolygon_registrar)) {
      alert(
        "La ubicación seleccionada está fuera del área permitida. Por favor, selecciona una ubicación dentro de los límites."
      );
      return;
    }
  }

  // Eliminar marcador anterior si existe
  if (currentMarker_registrar) {
    map_registrar.removeLayer(currentMarker_registrar);
  }

  // Crear nuevo marcador
  currentMarker_registrar = L.marker([latlng.lat, latlng.lng]).addTo(
    map_registrar
  );

  // Obtener dirección usando Nominatim (geocodificación inversa de OpenStreetMap)
  fetch(
    `https://nominatim.openstreetmap.org/reverse?format=json&lat=${latlng.lat}&lon=${latlng.lng}&zoom=18&addressdetails=1`
  )
    .then((response) => response.json())
    .then((data) => {
      if (data && data.address) {
        // Construir dirección formateada
        let direccion = "";
        if (data.address.road) direccion += data.address.road;
        if (data.address.house_number)
          direccion += " " + data.address.house_number;
        if (data.address.suburb) direccion += ", " + data.address.suburb;
        if (data.address.city || data.address.town || data.address.village) {
          direccion +=
            ", " +
            (data.address.city || data.address.town || data.address.village);
        }
        if (data.address.state) direccion += ", " + data.address.state;
        if (data.address.country) direccion += ", " + data.address.country;

        // Si no hay dirección construida, usar display_name
        if (!direccion || direccion.trim() === "") {
          direccion = data.display_name || "";
        }

        let country = data.address.country || "";
        let city = $("#ciudad_select option:selected").text();
        let id_city = $("#ciudad_select option:selected").val();

        // Guardar los datos en los campos correspondientes
        $("#direccion_cafeteria").val(direccion);
        $("#latitud_cafeteria").val(latlng.lat);
        $("#longitud_cafeteria").val(latlng.lng);
        $("#pais_cafeteria").val(country);

        console.log("Dirección guardada:", direccion);
        console.log("Ciudad:", city, "País:", country, "ID Ciudad:", id_city);
      } else {
        // Si no se puede obtener la dirección, al menos guardar las coordenadas
        $("#latitud_cafeteria").val(latlng.lat);
        $("#longitud_cafeteria").val(latlng.lng);
        $("#direccion_cafeteria").val(`Lat: ${latlng.lat}, Lng: ${latlng.lng}`);
        console.log(
          "No se pudo obtener la dirección, guardando solo coordenadas"
        );
      }
    })
    .catch((error) => {
      console.error("Error al obtener la dirección:", error);
      // Guardar coordenadas aunque falle la geocodificación
      $("#latitud_cafeteria").val(latlng.lat);
      $("#longitud_cafeteria").val(latlng.lng);
      $("#direccion_cafeteria").val(`Lat: ${latlng.lat}, Lng: ${latlng.lng}`);
    });
}

$(document).on("submit", "#form_agregar_cafeteria", function (e) {
  e.preventDefault(); // Prevenir el comportamiento por defecto del formulario

  let id_cafeteria = $("#id_cafeteria").val();
  let nombre = $("#nombre_cafeteria").val();
  let email = $("#correo_cafeteria").val();
  let celular = $("#telefono_cafeteria").val();
  let direccion = $("#direccion_cafeteria").val();
  let ciudad = $("#ciudad_select option:selected").val();
  let latitud = $("#latitud_cafeteria").val();
  let longitud = $("#longitud_cafeteria").val();
  let horario_diferente = $("#switch_horario").is(":checked") ? "NO" : "SI";
  let imagen_subir = $("#imagen_cafeteria")[0].files[0]
    ? $("#imagen_cafeteria")[0].files[0]
    : false;
  let descripcion = $("#descripcion_cafeteria").val();

  var datos = new FormData();

  datos.append("registrar_cafeteria", true);
  if (id_cafeteria) datos.append("id_cafeteria", id_cafeteria);
  datos.append("nombre", nombre);
  if (email) datos.append("correo", email);
  datos.append("telefono", celular);
  datos.append("direccion", direccion);
  datos.append("ciudad", ciudad);
  datos.append("latitud", latitud);
  datos.append("longitud", longitud);
  datos.append("horario_diferente", horario_diferente);
  datos.append("descripcion", descripcion);
  if (imagen_subir) datos.append("imagen_cafeteria", imagen_subir);

  // Verificar el estado del switch para determinar qué horarios enviar
  if ($("#switch_horario").is(":checked")) {
    // Enviar horarios en formato simple (apertura y cierre)
    let horario_apertura = $("#horario_apertura_cafeteria").val();
    let horario_cierre = $("#horario_cierre_cafeteria").val();
    datos.append("horario_apertura", horario_apertura);
    datos.append("horario_cierre", horario_cierre);
  } else {
    // Enviar horarios en formato JSON (detallado por día)
    const diasSemana = [
      "Lunes",
      "Martes",
      "Miércoles",
      "Jueves",
      "Viernes",
      "Sábado",
      "Domingo",
    ]; // Días con acentos y mayúscula inicial
    let horarios = {};
    let i = 0;
    diasSemana.forEach(function (dia) {
      let diaMinuscula = dia.toLowerCase(); // Convertir a minúsculas y remover acentos para coincidir con los IDs
      let switchDia = $(`#switch_${diaMinuscula}`); // Usar nombres en minúsculas y sin acentos
      if (switchDia.is(":checked")) {
        let horaApertura = $(`#hora_apertura_${diaMinuscula}`).val();
        let horaCierre = $(`#hora_cierre_${diaMinuscula}`).val();
        horarios[dia] = {
          apertura: horaApertura,
          cierre: horaCierre,
          cerrado: "NO",
        };
      } else {
        // Si el switch no está activado, se considera que el día está cerrado
        horarios[dia] = {
          cerrado: "SI",
        };
        i++;
      }
    });

    if (i === 7) {
      swal("¡Error!", "Debes elegir al menos un horario", "error");
      return;
    }

    let horariosJSON = JSON.stringify(horarios);
    datos.append("horarios", horariosJSON);
  }

  $.ajax({
    url: url + "views/ajax/ajax_cafeterias.php",
    method: "POST",
    data: datos,
    cache: false,
    contentType: false,
    processData: false,
    beforeSend: cargaSistema(true),
    success: function (respuesta) {
      console.log(respuesta);
      if (respuesta == "session_expired") {
        sesionExpirada();
      } else if (respuesta == "error_validacion_email") {
        $("#correo_usuario_registrar").addClass("is-invalid").next().show();
        swal("¡Error!", "Por favor verifica el correo electrónico.", "error");
      } else if (respuesta == "error_validacion_nombre") {
        $("#correo_usuario_registrar").addClass("is-invalid").next().show();
        swal("¡Error!", "Por favor verifica el nombre de cafetería.", "error");
      } else {
        respuesta = JSON.parse(respuesta);
        if (respuesta.success == true) {
          if (id_cafeteria != "") {
            alertaUpdate();
          } else {
            swal({
              title: "¡Bien!",
              text: "Se guardó el registro exitosamente.",
              icon: "success",
              button: "Aceptar",
            }).then(function () {
              window.location.href =
                url +
                "cafeterias/agregar/" +
                respuesta.id_cafeteria +
                "/imagenes/";
            });
          }
        } else {
          swal("¡Error!", "Ha ocurrido un error.", "error");
        }
      }
      cargaSistema(false);
    },
  });
});

// Mostrar el modal cuando se hace clic en el botón
$("#btn_seleccionar_ubicacion").on("click", function () {
  // Asegurarse de que el polígono esté actualizado antes de abrir el modal
  initializePolygonAndMap();
  $("#modal_ubicacion").modal("show");
});

// Evento para el botón de guardar ubicación en el modal
$(document).on("click", "#btn_guardar_ubicacion", function () {
  // El modal se cierra automáticamente, las coordenadas ya están guardadas
  // No necesitamos hacer nada adicional aquí
});

function toggleFields(checkbox, dia) {
  // Convertir el nombre del día a minúsculas para coincidir con los IDs generados en PHP
  const diaNormalizado = dia.toLowerCase();

  // Buscar los elementos de apertura y cierre usando el nombre normalizado
  const apertura = document.getElementById(`hora_apertura_${diaNormalizado}`);
  const cierre = document.getElementById(`hora_cierre_${diaNormalizado}`);

  // Verificar si los elementos existen
  if (apertura && cierre) {
    if (checkbox.checked) {
      apertura.disabled = false;
      cierre.disabled = false;
      apertura.setAttribute("required", "required");
      cierre.setAttribute("required", "required");
    } else {
      apertura.disabled = true;
      cierre.disabled = true;
      apertura.value = "";
      cierre.value = "";
      apertura.removeAttribute("required");
      cierre.removeAttribute("required");
    }
  } else {
    console.error(`Elementos no encontrados para el día: ${diaNormalizado}`);
  }
}

$(document).on("change", "#switch_horario", function () {
  if ($(this).is(":checked")) {
    $(".inp_horario").show();
    $(".inp_horario").attr("required", true);
    $(".tbl_horario").hide();
    $(".tbl_horario").removeAttr("required");
  } else {
    $(".inp_horario").hide();
    $(".tbl_horario").show();
    $(".inp_horario").val("");
    $(".inp_horario").removeAttr("required");
    $(".tbl_horario").attr("required", true);
  }
});

// function cargarTablaImagenes(){
//     let id = $("#id_cafeteria").val();

//     let filtro = `?imagenes_cafeteria=${id}`;
//     if ($.fn.DataTable.isDataTable($("#tabla_imagenes"))) {
//         $("#tabla_imagenes").DataTable().destroy();
//     }
//    $('#tabla_imagenes').DataTable( {
//     "ajax": {
//             "url": url + 'views/ajax/ajax_cafeterias.php' + filtro,
//             "dataSrc": function (json) {
//                 $('#contador_items').val(json.i);
//                 return json.data;
//             },
//         },
//     "deferRender": true,
//     "retrieve": true,
//     "processing": true,
//     dom: 'Bfrtip',
//     responsive: true,
//     ordering: true,
//     "language":{"url": url+"views/assets/plugins/DataTables/Spanish.json"}
//    });
// }

$(document).on("submit", "#form_subir_imagenes", function () {
  let imagen_subir = $("#imagen_cafeteria")[0].files[0];
  var datos = new FormData();

  datos.append("subir_imagen", true);
  datos.append("id", $("#id_cafeteria").val());
  datos.append("contador", $("#contador_items").val());
  datos.append("imagen", imagen_subir);

  $.ajax({
    url: url + "views/ajax/ajax_cafeterias.php",
    method: "POST",
    data: datos,
    cache: false,
    contentType: false,
    processData: false,
    beforeSend: cargaSistema(true),
    success: function (respuesta) {
      //console.log(respuesta);
      if (respuesta == "success") {
        swal({
          title: "¡OK!",
          text: "La imagen se agrego correctamente.",
          icon: "success",
          button: "Aceptar",
        }).then(function () {
          window.location = "";
        });
      } else {
      }
      cargaSistema(false);
    },
  });
});

$(document).on("click", ".agregar_servicios", function () {
  $("#id_cafeteria").val($(this).attr("idRegistro"));
  $("#modal_agregar_servicios").modal("show");
  cargarServicios();
});

function cargarServicios() {
  var datos = new FormData();

  datos.append("cargar_servicios", true);
  datos.append("id_cafeteria", $("#id_cafeteria").val());

  $.ajax({
    url: url + "views/ajax/ajax_cafeterias.php",
    method: "POST",
    data: datos,
    cache: false,
    contentType: false,
    processData: false,
    beforeSend: cargaSistema(true),
    success: function (respuesta) {
      //console.log(respuesta);
      respuesta = JSON.parse(respuesta);
      if (respuesta == "error") {
        swal("¡Error!", "Ha ocurrido un error", "error");
      } else {
        $("#servicios").html(respuesta);
      }
      cargaSistema(false);
    },
  });
}

$(document).on("submit", "#form_servicios", function () {
  const servicios = [];
  $(".chbx_servicios").each(function () {
    if ($(this).prop("checked")) {
      const id_servicio = $(this).attr("idServicio");
      servicios.push({ id: id_servicio });
    }
  });
  var datos = new FormData();

  datos.append("registrar_servicios", true);
  datos.append("id_cafeteria", $("#id_cafeteria").val());
  datos.append("servicios", JSON.stringify(servicios));
  $.ajax({
    url: url + "views/ajax/ajax_cafeterias.php",
    method: "POST",
    data: datos,
    cache: false,
    contentType: false,
    processData: false,
    beforeSend: cargaSistema(true),
    success: function (respuesta) {
      console.log(respuesta);
      if (respuesta == "error") {
        swal("¡Erro!", "Ha ocurrido un error", "error");
      } else {
        alertaUpdate();
      }
      cargaSistema(false);
    },
  });
});

function cargarTablaCafeterias() {
  let entidad = $("#entidad_filtro option:selected").val();
  let ciudad = $("#ciudad_filtro option:selected").val();
  let estatus = $('input[name="estatus"]:checked').val();

  let filtro = `?tabla_cafeterias=${true}&entidad=${entidad}&ciudad=${ciudad}&estatus=${estatus}`;

  if ($.fn.DataTable.isDataTable($("#tabla_cafeterias"))) {
    $("#tabla_cafeterias").DataTable().destroy();
  }
  $("#tabla_cafeterias").DataTable({
    ajax: {
      url: url + "views/ajax/ajax_cafeterias.php" + filtro,
      dataSrc: function (json) {
        return json.data;
      },
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

$(document).on("submit", "#form_filtro_cafeterias", function () {
  cargarTablaCafeterias();
});

function cargarImagenesExistentes() {
  const idCafeteria = $("#id_cafeteria").val();
  if (!idCafeteria) return;

  const filtro = `?imagenes_cafeteria=${idCafeteria}`;

  $.ajax({
    url: url + "views/ajax/ajax_cafeterias.php" + filtro,
    method: "GET",
    success: function (respuesta) {
      try {
        const data = JSON.parse(respuesta);
        $("#contador_items").val(data.i || 0);

        if (data.data && data.data.length > 0) {
          let html = "";
          data.data.forEach(function (imagen, index) {
            html += `
                            <div class="image-card">
                                ${imagen[3]}
                                <div class="image-card-body">
                                    <div class="image-card-actions d-flex align-items-center">
                                        
                                        <div class=""> 
                                            <label class="form-check-label">
                                                Activar/desactivar
                                            </label>    
                                        
                                            ${imagen[2]}
                                        </div>
                                       
                                        <div>
                                            ${imagen[1]}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
          });
          $("#imagesGrid").html(html);
        } else {
          $("#imagesGrid").html(`
                        <div class="col-12 text-center py-5">
                            <i class="bi bi-images" style="font-size: 4rem; color: #ccc;"></i>
                            <p class="mt-3 text-muted">No hay imágenes aún. Agrega algunas arriba.</p>
                        </div>
                    `);
        }
      } catch (e) {
        console.error("Error al cargar imágenes:", e);
      }
    },
  });
}

$(document).ready(function () {
  if ($("#imagesGrid").length) {
    cargarImagenesExistentes();
  }
  

  // Configurar drag & drop solo si los elementos existen
  const dropZone = document.getElementById("dropZone");
  const fileInput = document.getElementById("imagen_cafeteria");

  if (!dropZone || !fileInput) {
    console.error(
      "Error: No se encontraron los elementos necesarios para drag & drop"
    );
    return;
  }

  const imagesPreviewContainer = $("#imagesPreviewContainer");
  const imagesPreviewGrid = $("#imagesPreviewGrid");
  const imagesCount = $("#imagesCount");
  const btnUploadAll = $("#btnUploadAll");
  let selectedFiles = [];

  // Evento click en drop zone
  dropZone.addEventListener("click", function () {
    fileInput.click();
  });

  // Evento change del input file (selección múltiple)
  fileInput.addEventListener("change", function (e) {
    handleFiles(e.target.files);
  });

  // Drag & Drop events
  dropZone.addEventListener("dragover", function (e) {
    e.preventDefault();
    dropZone.classList.add("drag-over");
  });

  dropZone.addEventListener("dragleave", function (e) {
    e.preventDefault();
    dropZone.classList.remove("drag-over");
  });

  dropZone.addEventListener("drop", function (e) {
    e.preventDefault();
    dropZone.classList.remove("drag-over");
    handleFiles(e.dataTransfer.files);
  });

  // Función para manejar los archivos seleccionados
  function handleFiles(files) {
    const validFiles = Array.from(files).filter((file) => {
      return file.type.startsWith("image/");
    });

    if (validFiles.length === 0) {
      swal("¡Error!", "Por favor selecciona solo archivos de imagen.", "error");
      return;
    }

    // Agregar a la lista de archivos seleccionados
    validFiles.forEach((file) => {
      if (
        !selectedFiles.find((f) => f.name === file.name && f.size === file.size)
      ) {
        selectedFiles.push(file);
      }
    });

    // Mostrar preview
    mostrarPreview();

    // Limpiar el input para permitir seleccionar los mismos archivos de nuevo
    fileInput.value = "";
  }

  // Función para mostrar preview de imágenes
  function mostrarPreview() {
    imagesCount.text(selectedFiles.length);

    if (selectedFiles.length === 0) {
      imagesPreviewContainer.hide();
      return;
    }

    imagesPreviewContainer.show();
    imagesPreviewGrid.html("");

    selectedFiles.forEach((file, index) => {
      const reader = new FileReader();
      reader.onload = function (e) {
        const previewCard = `
                    <div class="image-preview-card" data-index="${index}">
                        <img src="${e.target.result}" alt="${file.name}">
                        <div class="image-preview-actions">
                            <button type="button" class="image-preview-remove" onclick="removerPreview(${index})">
                            x
                            </button>
                        </div>
                        <div class="image-preview-info">
                            <div class="image-preview-name">${file.name}</div>
                            <div class="image-preview-status" id="status_${index}">Pendiente</div>
                            <div class="image-preview-progress">
                                <div class="image-preview-progress-bar" id="progress_${index}"></div>
                            </div>
                        </div>
                    </div>
                `;
        imagesPreviewGrid.append(previewCard);
      };
      reader.readAsDataURL(file);
    });
  }

  // Función para remover una imagen del preview
  window.removerPreview = function (index) {
    selectedFiles.splice(index, 1);
    mostrarPreview();
  };

  // Función para subir todas las imágenes
  btnUploadAll.on("click", function () {
    if (selectedFiles.length === 0) {
      swal(
        "¡Atención!",
        "No hay imágenes seleccionadas para subir.",
        "warning"
      );
      return;
    }

    // Confirmar antes de subir
    swal({
      title: "¿Subir imágenes?",
      text: `Se subirán ${selectedFiles.length} imagen(es). ¿Deseas continuar?`,
      icon: "info",
      buttons: ["Cancelar", "Sí, subir"],
    }).then((confirm) => {
      if (confirm) {
        subirImagenes();
      }
    });
  });

  // Función para subir imágenes (una por una)
  function subirImagenes() {
    btnUploadAll
      .prop("disabled", true)
      .html('<i class="bi bi-hourglass-split me-2"></i>Subiendo...');

    let uploadIndex = 0;

    function uploadNext() {
      if (uploadIndex >= selectedFiles.length) {
        // Todas las imágenes se subieron
        swal({
          title: "¡Éxito!",
          text: "Todas las imágenes se subieron correctamente.",
          icon: "success",
          button: "Aceptar",
        }).then(function () {
          // Recargar la página para ver las imágenes subidas (como lo hacía antes)
          window.location.reload();
        });
        return;
      }

      const file = selectedFiles[uploadIndex];
      const formData = new FormData();

      formData.append("subir_imagen", true);
      formData.append("id", $("#id_cafeteria").val());
      formData.append("contador", $("#contador_items").val() || "0");
      formData.append("imagen", file);

      // Actualizar estado
      $(`#status_${uploadIndex}`).text("Subiendo...").addClass("uploading");

      $.ajax({
        url: url + "views/ajax/ajax_cafeterias.php",
        method: "POST",
        data: formData,
        cache: false,
        contentType: false,
        processData: false,
        xhr: function () {
          const xhr = new window.XMLHttpRequest();
          xhr.upload.addEventListener(
            "progress",
            function (evt) {
              if (evt.lengthComputable) {
                const percentComplete = (evt.loaded / evt.total) * 100;
                $(`#progress_${uploadIndex}`).css(
                  "width",
                  percentComplete + "%"
                );
              }
            },
            false
          );
          return xhr;
        },
        success: function (respuesta) {
          if (respuesta == "success") {
            $(`#status_${uploadIndex}`)
              .text("Subida exitosa")
              .removeClass("uploading")
              .addClass("success");
            // Actualizar contador para la siguiente imagen
            let currentCount = parseInt($("#contador_items").val()) || 0;
            $("#contador_items").val(currentCount + 1);
          } else {
            $(`#status_${uploadIndex}`)
              .text("Error al subir")
              .removeClass("uploading")
              .addClass("error");
          }

          uploadIndex++;
          setTimeout(uploadNext, 300); // Pequeño delay entre subidas
        },
        error: function () {
          $(`#status_${uploadIndex}`)
            .text("Error al subir")
            .removeClass("uploading")
            .addClass("error");
          uploadIndex++;
          setTimeout(uploadNext, 500);
        },
      });
    }

    uploadNext();
  }
});
