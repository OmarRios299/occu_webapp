
var key = 'AIzaSyAsDthuyGWYSIyMtMPjmbq7Epz8ABUrrHY';

let map_registrar;
let currentMarker_registrar = null;
let allowedPolygon_registrar;

$(document).ready(function () {
    if (moduloActual == 'cafeterias') {
        initializePolygonAndMap(); // Inicializa el mapa y el polígono

        // Escuchar el cambio del select para actualizar el polígono y centrar el mapa
        $("#ciudad_select").change(function () {
            updatePolygonAndMap();
        });

        // Evento para mostrar el modal y cargar el mapa
        $('#modal_ubicacion').on('shown.bs.modal', function () {
            if (!map_registrar) {
                initializeMap();
            } else {
                setTimeout(() => google.maps.event.trigger(map_registrar, 'resize'), 10);
            }
        });
    }
});

// Función para inicializar el polígono y el mapa
function initializePolygonAndMap() {
    let polygonCoordinates = JSON.parse($("#ciudad_select option:selected").attr('coordenadas'));
    allowedPolygon_registrar = new google.maps.Polygon({ paths: polygonCoordinates });

    if (!map_registrar) {
        initializeMap();
    }
}

// Función para inicializar el mapa
function initializeMap() {
    const latitud = parseFloat($('#latitud_cafeteria').val()) || 32.624538;
    const longitud = parseFloat($('#longitud_cafeteria').val()) || -115.452263;

    map_registrar = new google.maps.Map(document.getElementById('map'), {
        center: { lat: latitud, lng: longitud },
        zoom: 13,
        disableDefaultUI: false
    });

    //allowedPolygon_registrar.setMap(map_registrar); // Mostrar el polígono en el mapa

    if (latitud && longitud) {
        currentMarker_registrar = new google.maps.Marker({
            position: { lat: latitud, lng: longitud },
            map: map_registrar
        });
    }

    // Agregar evento click al mapa para seleccionar ubicación
    map_registrar.addListener('click', function (e) {
        placeMarkerAndSaveData(e.latLng);
    });
}

// Función para actualizar el polígono y centrar el mapa al cambiar de ciudad
function updatePolygonAndMap() {
    // Obtener nuevas coordenadas del polígono basado en la ciudad seleccionada
    let polygonCoordinates = JSON.parse($("#ciudad_select option:selected").attr('coordenadas'));

    // Eliminar el polígono anterior si existe
    if (allowedPolygon_registrar) {
        allowedPolygon_registrar.setMap(null);
    }

    // Crear un nuevo polígono con las nuevas coordenadas
    allowedPolygon_registrar = new google.maps.Polygon({ paths: polygonCoordinates });
   // allowedPolygon_registrar.setMap(map_registrar); // Mostrar el nuevo polígono en el mapa

    // Calcular el centroide del polígono usando la función para el centroide geográfico
    let center = getGeographicCentroid(polygonCoordinates);

    // Centrar el mapa en el centroide calculado
    if (map_registrar && center) {
        map_registrar.setCenter(center);
        map_registrar.setZoom(10); // Opcional: Ajustar el nivel de zoom si es necesario
    }
}

// Función para calcular el centroide geográfico de un conjunto de coordenadas
function getGeographicCentroid(coordinates) {
    let latSum = 0;
    let lngSum = 0;
    let numPoints = coordinates.length;

    coordinates.forEach(coord => {
        latSum += coord.lat;
        lngSum += coord.lng;
    });

    let centroidLat = latSum / numPoints;
    let centroidLng = lngSum / numPoints;

    return { lat: centroidLat, lng: centroidLng };
}

// Función para colocar el marcador y guardar los datos
function placeMarkerAndSaveData(latlng) {
    if (currentMarker_registrar) {
        currentMarker_registrar.setMap(null); // Elimina el marcador anterior
    }

    // Verificar si la ubicación está dentro del polígono permitido
    if (!google.maps.geometry.poly.containsLocation(latlng, allowedPolygon_registrar)) {
        alert('La ubicación seleccionada está fuera del área permitida. Por favor, selecciona una ubicación dentro de los límites.');
        return; // Detener la función si está fuera del polígono
    }

    // Crear un nuevo marcador usando google.maps.Marker
    currentMarker_registrar = new google.maps.Marker({
        position: latlng,
        map: map_registrar
    });

    // Obtener y guardar la dirección
    fetch(`https://maps.googleapis.com/maps/api/geocode/json?latlng=${latlng.lat()},${latlng.lng()}&key=` + key)
        .then(response => response.json())
        .then(data => {
            if (data.results[0]) {
                const direccion = data.results[0].formatted_address;
                let country = "";
                let city = $("#ciudad_select option:selected").text();
                let id_city = $("#ciudad_select option:selected").val();

                data.results[0].address_components.forEach(component => {
                    const types = component.types;
                    if (types.includes("country")) {
                        country = component.long_name
                    }
                });

                // Guardar los datos en los campos correspondientes
                $("#direccion_cafeteria").val(direccion);
                $("#latitud_cafeteria").val(latlng.lat());
                $("#longitud_cafeteria").val(latlng.lng());
                $("#pais_cafeteria").val(country);
                $("#ciudad_cafeteria").val(city);
                $("#ciudad_cafeteria").attr('id_ciudad', id_city);

                console.log('Dirección guardada:', direccion);
                console.log('Ciudad:', city, 'País:', country, 'ID Ciudad:', id_city);
            }
        })
        .catch(error => console.error('Error al obtener la dirección:', error));
}


/// el mapa parece estar funcionando bien, hay que hacer mas pruebas
//  tratar de optener la ciuadad automaticamente
//   guardar el registro funcion


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
    let imagen_subir = $("#imagen_cafeteria")[0].files[0] ? $("#imagen_cafeteria")[0].files[0] : false;

    var datos = new FormData();

    datos.append("registrar_cafeteria", true);
    if (id_cafeteria) datos.append("id_cafeteria", id_cafeteria);
    datos.append("nombre", nombre);
    datos.append("correo", email);
    datos.append("telefono", celular);
    datos.append("direccion", direccion);
    datos.append("ciudad", ciudad);
    datos.append("latitud", latitud);
    datos.append("longitud", longitud);
    if (imagen_subir) datos.append("imagen_cafeteria", imagen_subir);

    // Verificar el estado del switch para determinar qué horarios enviar
    if ($("#switch_horario").is(':checked')) {
        // Enviar horarios en formato simple (apertura y cierre)
        let horario_apertura = $("#horario_apertura_cafeteria").val();
        let horario_cierre = $("#horario_cierre_cafeteria").val();
        datos.append("horario_apertura", horario_apertura);
        datos.append("horario_cierre", horario_cierre);
    } else {
        // Enviar horarios en formato JSON (detallado por día)
        const diasSemana = ['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado', 'domingo'];
        let horarios = {};
        diasSemana.forEach(function(dia) {
            let switchDia = $(`#switch_${dia}`);
            if (switchDia.is(':checked')) {
                let horaApertura = $(`#hora_apertura_${dia}`).val();
                let horaCierre = $(`#hora_cierre_${dia}`).val();
                horarios[dia] = {
                    apertura: horaApertura,
                    cierre: horaCierre,
                    cerrado: 'NO'
                };
            } else {
                // Si el switch no está activado, se considera que el día está cerrado
                horarios[dia] = {
                    cerrado: 'SI'
                };
            }
        });

        let horariosJSON = JSON.stringify(horarios);
        datos.append("horarios", horariosJSON); 
    }

    $.ajax({
        url: url + 'views/ajax/ajax_cafeterias.php',
        method: 'POST',
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
                $("#correo_usuario_registrar").addClass('is-invalid').next().show();
                swal("¡Error!", "Por favor verifica el correo electrónico.", "error");
            } else if (respuesta == "error_validacion_nombre") {
                $("#correo_usuario_registrar").addClass('is-invalid').next().show();
                swal("¡Error!", "Por favor verifica el nombre de cafetería.", "error");
            } else if (respuesta == "success") {
                if (id_cafeteria != "") {
                    alertaUpdate();
                } else {
                    window.location.href = url + 'cafeterias/agregar/' + id_cafeteria + 'imagenes/';
                }
            } else {
                swal("¡Error!", "Ha ocurrido un error.", "error");
            }
            cargaSistema(false);
        }
    });
});


// Mostrar el modal cuando se hace clic en el botón
$("#btn_seleccionar_ubicacion").on("click", function () {
    $("#modal_ubicacion").modal('show');
});

function toggleFields(checkbox, dia) {
    const apertura = document.getElementById(`hora_apertura_${dia}`);
    const cierre = document.getElementById(`hora_cierre_${dia}`);
    
    if (checkbox.checked) {
        apertura.disabled = false;
        cierre.disabled = false;
        apertura.setAttribute('required', 'required'); 
        cierre.setAttribute('required', 'required'); 
    } else {
        apertura.disabled = true;
        cierre.disabled = true;
        apertura.value = '';
        cierre.value = '';
        apertura.removeAttribute('required'); 
        cierre.removeAttribute('required'); 
    }
}



$(document).on("change", "#switch_horario", function() {
    if ($(this).is(':checked')) {
        $(".inp_horario").show();  
        $(".tbl_horario").hide();  
    } else {
        $(".inp_horario").hide(); 
        $(".tbl_horario").show(); 
    }
});
