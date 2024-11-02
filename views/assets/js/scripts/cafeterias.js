let googleApiKey;
let map_registrar;
let currentMarker_registrar = null;
let allowedPolygon_registrar;

$(document).ready(function () {
    // optione la api key
    fetch(`${url}/config/googleApiKey.php`)
    .then(response => {
        if (!response.ok) {
            throw new Error('Error al obtener la clave de API');
        }
        return response.json();
    })
    .then(data => {
        googleApiKey = data.apiKey;
    })
    .catch(error => console.error(error));

    if (moduloActual == 'cafeterias' && $('#ciudad_select').length) {
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
    if ($('#tabla_imagenes').length) {
        cargarTablaImagenes();
    }
    if ($("#tabla_cafeterias").length) {
        cargarTablaCafeterias();
    }

    if ($('#switch_horario').is(':checked')) {
        $(".inp_horario").show();
        $(".inp_horario").attr("required",true);  
        $(".tbl_horario").hide();  
        $(".tbl_horario").removeAttr("required");  
    } else {
        $(".inp_horario").hide(); 
        $(".tbl_horario").show(); 
        $(".inp_horario").val('');
        $(".inp_horario").removeAttr("required"); 
        $(".tbl_horario").attr("required",true);  
 
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
    fetch(`https://maps.googleapis.com/maps/api/geocode/json?latlng=${latlng.lat()},${latlng.lng()}&key=` + googleApiKey)
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
    let horario_diferente = ($("#switch_horario").is(':checked'))?'NO':'SI';
    let imagen_subir = $("#imagen_cafeteria")[0].files[0] ? $("#imagen_cafeteria")[0].files[0] : false;
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
    if ($("#switch_horario").is(':checked')) {
        // Enviar horarios en formato simple (apertura y cierre)
        let horario_apertura = $("#horario_apertura_cafeteria").val();
        let horario_cierre = $("#horario_cierre_cafeteria").val();
        datos.append("horario_apertura", horario_apertura);
        datos.append("horario_cierre", horario_cierre);
    } else {
        // Enviar horarios en formato JSON (detallado por día)
        const diasSemana = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo']; // Días con acentos y mayúscula inicial
        let horarios = {};
        let i = 0;
        diasSemana.forEach(function(dia) {
            let diaMinuscula =dia.toLowerCase(); // Convertir a minúsculas y remover acentos para coincidir con los IDs
            let switchDia = $(`#switch_${diaMinuscula}`); // Usar nombres en minúsculas y sin acentos
            if (switchDia.is(':checked')) {
                let horaApertura = $(`#hora_apertura_${diaMinuscula}`).val();
                let horaCierre = $(`#hora_cierre_${diaMinuscula}`).val();
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
    } else {
        console.error(`Elementos no encontrados para el día: ${diaNormalizado}`);
    }
}




$(document).on("change", "#switch_horario", function() {
    if ($(this).is(':checked')) {
        $(".inp_horario").show();
        $(".inp_horario").attr("required",true);  
        $(".tbl_horario").hide();  
        $(".tbl_horario").removeAttr("required");  
    } else {
        $(".inp_horario").hide(); 
        $(".tbl_horario").show(); 
        $(".inp_horario").val('');
        $(".inp_horario").removeAttr("required"); 
        $(".tbl_horario").attr("required",true);  
 
    }
});

function cargarTablaImagenes(){
    let id = $("#id_cafeteria").val();

    let filtro = `?imagenes_cafeteria=${id}`;
    if ($.fn.DataTable.isDataTable($("#tabla_imagenes"))) {
        $("#tabla_imagenes").DataTable().destroy();
    }
   $('#tabla_imagenes').DataTable( {
    "ajax": {
            "url": url + 'views/ajax/ajax_cafeterias.php' + filtro,
            "dataSrc": function (json) {
                $('#contador_items').val(json.i);
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

$(document).on("submit","#form_subir_imagenes",function(){
    let imagen_subir = $("#imagen_cafeteria")[0].files[0];
    var datos = new FormData();
    
    datos.append("subir_imagen", true);
    datos.append("id", $("#id_cafeteria").val());
    datos.append("contador", $("#contador_items").val());
    datos.append("imagen", imagen_subir);
    
    $.ajax({
        url:url+'views/ajax/ajax_cafeterias.php',
        method:'POST',
        data: datos,
        cache: false,
        contentType: false,
        processData: false,
        beforeSend: cargaSistema(true),
        success:function(respuesta){
            //console.log(respuesta);
            if (respuesta=='success') {
                swal({
                   title: "¡OK!",
                   text: "La imagen se agrego correctamente.",
                   icon: "success",
                   button: "Aceptar",
                }).then(function() {
                   window.location = "";
                });
            } else {
                
            }
            cargaSistema(false);
        }
    });

});

$(document).on("click",".agregar_servicios",function(){
    $("#id_cafeteria").val($(this).attr("idRegistro"));
    $("#modal_agregar_servicios").modal('show');
    cargarServicios();
});

function cargarServicios() {
    var datos = new FormData();
    
    datos.append("cargar_servicios", true);
    datos.append('id_cafeteria', $("#id_cafeteria").val())
    
    $.ajax({
        url:url+'views/ajax/ajax_cafeterias.php',
        method:'POST',
        data: datos,
        cache: false,
        contentType: false,
        processData: false,
        beforeSend: cargaSistema(true),
        success:function(respuesta){
            //console.log(respuesta);
            respuesta = JSON.parse(respuesta);
            if (respuesta=='error') {
                swal("¡Error!", "Ha ocurrido un error", "error");
            } else {
                $("#servicios").html(respuesta);
            }
            cargaSistema(false);
        }
    });
}

$(document).on("submit","#form_servicios",function(){
    const servicios = [];
    $(".chbx_servicios").each(function(){
        if ($(this).prop("checked")) {  
            const id_servicio = $(this).attr("idServicio"); 
            servicios.push({ id: id_servicio }); 
        }
    });
    var datos = new FormData();
    
    datos.append("registrar_servicios", true);
    datos.append('id_cafeteria', $("#id_cafeteria").val())
    datos.append('servicios', JSON.stringify(servicios));
    $.ajax({
        url:url+'views/ajax/ajax_cafeterias.php',
        method:'POST',
        data: datos,
        cache: false,
        contentType: false,
        processData: false,
        beforeSend: cargaSistema(true),
        success:function(respuesta){
            console.log(respuesta);
            if (respuesta=='error') {
                swal("¡Erro!", "Ha ocurrido un error", "error");
            } else {
                alertaUpdate();
            }
            cargaSistema(false);
        }
    });
});

function cargarTablaCafeterias(){
    let entidad = $("#entidad_filtro option:selected").val();
    let ciudad = $("#ciudad_filtro option:selected").val();
    let estatus = $('input[name="estatus"]:checked').val();

    let filtro = `?tabla_cafeterias=${true}&entidad=${entidad}&ciudad=${ciudad}&estatus=${estatus}`;

    if ($.fn.DataTable.isDataTable($("#tabla_cafeterias"))) {
        $("#tabla_cafeterias").DataTable().destroy();
    }
   $('#tabla_cafeterias').DataTable( {
    "ajax": {
            "url": url + 'views/ajax/ajax_cafeterias.php' + filtro,
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

$(document).on("submit","#form_filtro_cafeterias",function(){
    cargarTablaCafeterias();
});
