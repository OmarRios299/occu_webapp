// Variable para almacenar la instancia del mapa
var mapa_ubicaciones_cafeterias;

$(document).ready(function() {
    if (moduloActual == 'cafeterias_mapa') {
        cargarMapaCafeterias();
    }
});

function cargarMapaCafeterias(){
    var datos = new FormData();
    
    datos.append("obtenerCafeterias", true);
    
    $.ajax({
        url: url + 'views/ajax/ajax_cafeterias_mapa.php',
        method: 'POST',
        data: datos,
        cache: false,
        contentType: false,
        processData: false,
        success: function(respuesta) {
            respuesta = JSON.parse(respuesta);
            
            if (!mapa_ubicaciones_cafeterias) {
                // Inicializar el mapa de Google
                mapa_ubicaciones_cafeterias = new google.maps.Map(document.getElementById('map'), {
                    center: { lat: 32.10, lng: -114.80 }, // Coordenadas iniciales
                    zoom: 9,
                    disableDefaultUI: false, // Mantiene los controles del mapa predeterminados
                    styles: ocultar_marcadores,
                    streetViewControl: false,  // Desactivar Pegman
                    zoomControl: true,        // Desactivar controles de zoom
                    mapTypeControl: true,     // Desactivar el selector de tipo de mapa
                    fullscreenControl: true,   // Desactivar el control de pantalla completa
                    fullscreenControlOptions: {
                        position: google.maps.ControlPosition.TOP_CENTER, // Posiciona las herramientas de dibujo en el centro superior
                        drawingModes: ['polygon']
                    },
                });

                // Iterar sobre los datos recibidos para crear marcadores
                respuesta.data.forEach(function(cafeteria) {
                    var nombre = cafeteria.nombre;
                    var id = cafeteria.id;
                    var imagen = cafeteria.imagen;
                    var latitud = parseFloat(cafeteria.latitud);
                    var longitud = parseFloat(cafeteria.longitud);

                    // Crear un ícono personalizado
                    var customIcon = {
                        url: url + imagen, // URL de tu icono
                        scaledSize: new google.maps.Size(50, 50), // Tamaño del icono
                        anchor: new google.maps.Point(25, 50) // Punto del icono que se alineará con el marcador
                    };

                    // Crear un marcador con el icono personalizado
                    var marker = new google.maps.Marker({
                        position: { lat: latitud, lng: longitud },
                        map: mapa_ubicaciones_cafeterias,
                        icon: customIcon
                    });

                    // Crear una ventana de información para el marcador
                    var infowindow = new google.maps.InfoWindow({
                        content: `
                            <div style="text-align: center;">
                                <h6>${nombre}</h6>
                                <img src="${url}${imagen}" alt="Imagen del Marcador" class='imagen' idCafeteria='${id}' style="width: 50px; height: 50px;"/>
                                <p>Horario de 7:00 a 22:00.</p>
                            </div>
                        `
                    });

                    // Agregar evento para mostrar la ventana de información al hacer clic en el marcador
                    marker.addListener('click', function() {
                        infowindow.open(mapa_ubicaciones_cafeterias, marker);
                    });
                });
            } else {
                setTimeout(function() {
                    google.maps.event.trigger(mapa_ubicaciones_cafeterias, 'resize');
                }, 10);
            }
        }
    });
}


$(document).on("click",".imagen",function(){
    $("#id_cafeteria").attr('idCafeteria',($(this).attr('idCafeteria')));
    $("#modal_cafeteria").modal('show');
    CargarVerCafeteria();
});

    
