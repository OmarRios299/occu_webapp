
$(document).ready(function () {
    if (moduloActual === 'admin_paises') {
        if ($('#grafica_ciudades').length) {
            cargarMetricosPaises();
        }
        $('.btn_delimitar_ciudad').on('click', function () {
            removePolygon();

            // Verificar si el atributo 'coordenadas' existe y es válido
            let coordenadasAttr = $(this).attr('coordenadas');
            let polygonCoordinates = coordenadasAttr ? JSON.parse(coordenadasAttr) : [];

            const center = polygonCoordinates.length > 0 ? getGeographicCentroid(polygonCoordinates) : { lat: 29.384, lng: -107.006 };

            $('#modal_mapa').modal('show'); // Mostrar el modal primero
            initMap(center); // Inicializar el mapa

            if (polygonCoordinates.length > 0) {
                updatePolygonAndCenterMap(polygonCoordinates); // Actualizar el polígono si hay coordenadas
            } else {
                drawingManager.setDrawingMode(google.maps.drawing.OverlayType.POLYGON); // Activar la herramienta de dibujo si no hay coordenadas
            }
        });

        $('#btn_eliminar_poligono').on('click', removePolygon);
    }
});

$(document).on("click", "#delimitar_ciudad", function () {
    $("#modal_mapa").modal('show');
});

// Variables de mapa y dibujo
let map_delimitar_ciudad;
let allowedPolygon;
let drawingManager;
let initialized = false;

// Función para remover el polígono y resetear herramientas de dibujo
function removePolygon() {
    if (allowedPolygon) {
        allowedPolygon.setMap(null);
        allowedPolygon = null;
        console.log('Polygon removed');
    }
}

// Función para inicializar el mapa
function initMap(center) {
    if (!initialized) {
        map_delimitar_ciudad = new google.maps.Map(document.getElementById('map'), {
            center: center,
            zoom: 15,
            styles: ocultar_marcadores,
            streetViewControl: false,  // Desactivar Pegman
            zoomControl: true,        // Desactivar controles de zoom
            mapTypeControl: false,     // Desactivar el selector de tipo de mapa
            fullscreenControl: true   // Desactivar el control de pantalla completa
        });

        drawingManager = new google.maps.drawing.DrawingManager({
            drawingMode: google.maps.drawing.OverlayType.POLYGON,
            drawingControl: true,
            drawingControlOptions: {
                position: google.maps.ControlPosition.TOP_CENTER,
                drawingModes: ['polygon']
            },
            polygonOptions: {
                editable: true,
                draggable: false
            }
        });

        drawingManager.setMap(map_delimitar_ciudad);

        google.maps.event.addListener(drawingManager, 'overlaycomplete', handleOverlayComplete);

        initialized = true;
    } else {
        // Si el mapa ya fue inicializado, solo centramos en la nueva posición
        map_delimitar_ciudad.setCenter(center);
        map_delimitar_ciudad.setZoom(15);
        drawingManager.setMap(map_delimitar_ciudad); // Asegurarse de que el DrawingManager esté visible
    }
}

// Maneja el evento de completar el dibujo del polígono
function handleOverlayComplete(event) {
    if (event.type === google.maps.drawing.OverlayType.POLYGON) {
        removePolygon();
        allowedPolygon = event.overlay;
        drawingManager.setDrawingMode(null);
        updatePolygonCoordinates(allowedPolygon);
    }
}

// Actualiza las coordenadas del polígono
function updatePolygonCoordinates(polygon) {
    const coordinates = polygon.getPath().getArray().map(latlng => ({ lat: latlng.lat(), lng: latlng.lng() }));
    console.log('Current Polygon Coordinates:', coordinates);
    $("#coordenadas_ciudad").val(JSON.stringify(coordinates));

    google.maps.event.clearListeners(polygon.getPath(), 'set_at');
    google.maps.event.clearListeners(polygon.getPath(), 'insert_at');

    google.maps.event.addListener(polygon.getPath(), 'set_at', () => updatePolygonCoordinates(polygon));
    google.maps.event.addListener(polygon.getPath(), 'insert_at', () => updatePolygonCoordinates(polygon));
}

// Calcula el centro geográfico de las coordenadas
function getGeographicCentroid(coordinates) {
    const centroid = coordinates.reduce((acc, coord) => ({
        lat: acc.lat + coord.lat,
        lng: acc.lng + coord.lng
    }), { lat: 0, lng: 0 });

    return { lat: centroid.lat / coordinates.length, lng: centroid.lng / coordinates.length };
}

// Actualiza el polígono y centra el mapa
function updatePolygonAndCenterMap(polygonCoordinates) {
    removePolygon();
    allowedPolygon = new google.maps.Polygon({
        paths: polygonCoordinates,
        editable: true,
        draggable: false
    });
    allowedPolygon.setMap(map_delimitar_ciudad);
    updatePolygonCoordinates(allowedPolygon);

    const center = getGeographicCentroid(polygonCoordinates);
    map_delimitar_ciudad.setCenter(center);
    map_delimitar_ciudad.setZoom(13);
}

// Manejo del evento cuando se muestra el modal
$('#modal_mapa').on('shown.bs.modal', function () {
    if (map_delimitar_ciudad) {
        google.maps.event.trigger(map_delimitar_ciudad, 'resize'); // Redimensiona el mapa
        map_delimitar_ciudad.setCenter(map_delimitar_ciudad.getCenter());
    }
});




$(document).on("click",".btn_mostrar_cafeterias",function(){
    
});

$(document).on("click", "#agregar_pais", function () {
    $(".input_pais").val('').removeClass("is-invalid");
    $('#nombre_pais').removeClass('validarCampoEditar').addClass('validarCampo');
    $("#modal_agregar_pais").modal('show');
});

$(document).on("click", ".btn_delimitar_ciudad", function () {
    $("#modal_mapa").modal('show');
});

$(document).on("click", ".editar_pais", function () {
    $(".input_pais").val('').removeClass("is-invalid");
    $('#nombre_pais').addClass('validarCampoEditar').removeClass('validarCampo');
    $("#nombre_pais").val($(this).attr("idRegistro"));
    $("#id_pais").val($(this).attr("idRegistro"));
    $("#nombre_pais").val($(this).attr("nombre"));
    $("#modal_agregar_pais").modal('show');
});

$(document).on("submit", "#form_agregar_pais", function () {
    var datos = new FormData();

    datos.append("agregarPais", true);
    datos.append('id', $("#id_pais").val())
    datos.append('nombre', $("#nombre_pais").val())

    $.ajax({
        url: url + 'views/ajax/ajax_admin_paises.php',
        method: 'POST',
        data: datos,
        cache: false,
        contentType: false,
        processData: false,
        beforeSend: cargaSistema(true),
        success: function (respuesta) {
            console.log(respuesta);
            if (respuesta == 'success') {
                $("#id_pais").val() ? alertaUpdate() : alertaInsert();
            }
            cargaSistema(false);
        }
    });
});

// hacer funcionar la grafica y los contadores
function cargarMetricosPaises() {
    var datos = new FormData();
    
    datos.append("cargarMetricos", true);
    datos.append('pais', $("#id_ciudad").val());
    $.ajax({
        url:url+'views/ajax/ajax_admin_paises.php',
        method:'POST',
        data: datos,
        cache: false,
        contentType: false,
        processData: false,
        beforeSend: cargaSistema(true),
        success:function(respuesta){
            respuesta=JSON.parse(respuesta)
            console.log(respuesta);
            $("#total_cafeterias").html(respuesta.contadores.total_cafeterias);
            $("#total_ciudades").html(respuesta.contadores.total_ciudades);
            cargarGraficaDiasOTs(respuesta.data);
            cargaSistema(false);
        }
    });
}

function cargarGraficaDiasOTs(respuesta) {


    if (grafica_ciudades && grafica_ciudades.destroy) {
        grafica_ciudades.destroy();
    }

    grafica_ciudades = new Chart(document.getElementById('grafica_ciudades').getContext('2d'), {
        type: 'bar',
        data: {
            labels: respuesta.map(cafeteria => cafeteria.nombre),
            datasets: [
                {
                label : 'Cafeterias',
                data: respuesta.map(cafeteria => cafeteria.cafeterias),
                backgroundColor: backgroundColor,
                borderColor: borderColor,
                borderWidth: 1,
                },
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false, // Permite que la gráfica se ajuste al tamaño del contenedor
            plugins: {
                title: {
                    display: true,
                    text: "Comparativa de cafeterías por ciudad"
                },
                legend: {
                    display: true
                }
            },
            scales: {
                y: {
                    beginAtZero: true // Asegura que el eje y comience en 0
                }
            }
        }
    });

}

$(document).on("click", "#agregar_ciudad, .editar_ciudad", function () {
        $("#id_pais").val('');
        $("#nombre_ciudad").val('');
        $("#select_estado option:selected").val('');
        $("#coordenadas_ciudad").val('');
        $("#delimitar_ciudad").removeAttr('coordenadas');
    if (!$(this).attr('idRegistro')) {
        $("#modal_agregar_ciudad").modal('show');
    } else {
        $("#id_pais").val($(this).attr("idRegistro"));
        $("#nombre_ciudad").val($(this).attr("nombre"));
        $("#select_estado option:selected").val($(this).attr("estado"));
        $("#coordenadas_ciudad").val($(this).attr("coordenadas"));
        $("#modal_agregar_ciudad").modal('show');
        $("#delimitar_ciudad").attr('coordenadas',$(this).attr("coordenadas"));
    }
});

$(document).on("submit", "#form_agregar_ciudad", function () {
    var datos = new FormData();

    datos.append("agregar_ciudad", true);
    datos.append('id',$("#id_pais").val())
    datos.append('nombre', $("#nombre_ciudad").val());
    datos.append('entidad_federativa', $("#select_estado option:selected").val())
    datos.append('coordenadas', $("#coordenadas_ciudad").val())

    $.ajax({
        url: url + 'views/ajax/ajax_admin_paises.php',
        method: 'POST',
        data: datos,
        cache: false,
        contentType: false,
        processData: false,
        beforeSend: cargaSistema(true),
        success: function (respuesta) {
            console.log(respuesta);
            if (respuesta == 'success') {
                alertaInsert();
            } else {

            }
            cargaSistema(false);
        }
    });
});