// Variable para almacenar la instancia del mapa
var mapa_ubicaciones_cafeterias;
var markersArray = []; // Arreglo para almacenar los marcadores

$(document).ready(function() {
    if (moduloActual == 'cafeterias_mapa') {
        cargarMapaCafeterias();
        //cargarServiciosFiltro();
    }
});

function cargarMapaCafeterias(){

    const servicios = [];
    $(".seleccionar_servicio").each(function(){
        if ($(this).is(":checked")) {
            servicios.push({
                id:$(this).val()
            });
        }
    });
    
    let horario = $('input[name="horario_filtro"]:checked').val();
    // Obtener ciudad del select de filtros (funciona en desktop y móvil)
    let ciudad = $('.select_ciudades_filtro option:selected').val();
    if (!ciudad) {
        ciudad = $("#ciudades_filtro_offcanvas").val() || $("#ciudades_filtro_mobile").val() || '';
    }
    
    var datos = new FormData();
    datos.append("obtenerCafeterias", true);
    datos.append("horario", horario);
    datos.append("ciudad", ciudad);
    datos.append("servicios", JSON.stringify(servicios)); 
    
    $.ajax({
        url: url + 'views/ajax/ajax_cafeterias_mapa.php',
        method: 'POST',
        data: datos,
        cache: false,
        contentType: false,
        processData: false,
        beforeSend: cargaSistema(true),
        success: function(respuesta) {
            respuesta = JSON.parse(respuesta);

            // Eliminar marcadores previos si ya existen
            clearMarkers();

            // Verificar si el atributo 'coordenadas' existe y es válido
            let coordenadasAttr = '';
            // Intentar obtener coordenadas del select actual
            coordenadasAttr = $('.select_ciudades_filtro option:selected').attr('coordenadas') || '';
            // Si no hay coordenadas, intentar desde los campos hidden
            if (!coordenadasAttr) {
                coordenadasAttr = $("#ciudades_filtro_offcanvas").attr('coordenadas') || '';
            }
            if (!coordenadasAttr) {
                coordenadasAttr = $("#ciudades_filtro_mobile").attr('coordenadas') || '';
            }
            
            let polygonCoordinates = coordenadasAttr ? JSON.parse(coordenadasAttr) : [];

            const center = polygonCoordinates.length > 0 ? getGeographicCentroid(polygonCoordinates) : { lat: 29.384, lng: -107.006 };

            if (!mapa_ubicaciones_cafeterias) {
                // Inicializar el mapa de Google
                mapa_ubicaciones_cafeterias = new google.maps.Map(document.getElementById('map'), {
                    center: center, // Coordenadas iniciales
                    zoom: 12,
                    disableDefaultUI: false,
                    styles: ocultar_marcadores,
                    streetViewControl: false,
                    zoomControl: true,
                    mapTypeControl: true,
                    fullscreenControl: true,
                    fullscreenControlOptions: {
                        position: google.maps.ControlPosition.TOP_CENTER
                    },
                });
            } else {
                // Reubicar el centro del mapa en las nuevas coordenadas
                mapa_ubicaciones_cafeterias.setCenter(center);
            }

            // Iterar sobre los datos recibidos para crear marcadores
            respuesta.data.forEach(function(cafeteria) {
                var nombre = cafeteria.nombre;
                var id = cafeteria.id;
                var imagen = cafeteria.imagen;
                var status = cafeteria.status;
                var latitud = parseFloat(cafeteria.latitud);
                var longitud = parseFloat(cafeteria.longitud);

                // Crear un ícono personalizado
                var customIcon = {
                    url: url + imagen,
                    scaledSize: new google.maps.Size(50, 50),
                    anchor: new google.maps.Point(25, 50)
                };

                // Crear un marcador con el icono personalizado
                var marker = new google.maps.Marker({
                    position: { lat: latitud, lng: longitud },
                    map: mapa_ubicaciones_cafeterias,
                    icon: customIcon
                });

                // Guardar el marcador en el array para luego poder eliminarlo
                markersArray.push(marker);

                // Agregar evento para abrir el modal al hacer clic en el marcador
                marker.addListener('click', function() {
                    abrirModalCafeteria(id);
                });
            });
            
            // Trigger de redimensionar el mapa si ya estaba inicializado
            setTimeout(function() {
                google.maps.event.trigger(mapa_ubicaciones_cafeterias, 'resize');
            }, 10);
            cargaSistema(false);
        }
    });
}

// Función para eliminar los marcadores del mapa
function clearMarkers() {
    markersArray.forEach(function(marker) {
        marker.setMap(null); // Eliminar el marcador del mapa
    });
    markersArray = []; // Limpiar el array de marcadores
}

function abrirModalCafeteria(id) {
    $("#id_cafeteria").attr('idCafeteria',id);
    $("#modal_cafeteria").modal('show');
    CargarVerCafeteria();
    cargarComentarios(1);
}

$(document).ready(function(){
    if (moduloActual=='cafeterias_mapa') {
        // Eventos para los nuevos formularios de filtros
        $(document).on("submit", "#aplicar_filtros_offcanvas, #aplicar_filtros_mobile", function () {
            $("#modal_cafeteria").modal('hide');
            cargarMapaCafeterias();
        });
        
        $(document).on("change", ".check_servicios", function () {
            if ($(this).prop('checked')) {
                $(".div_servicios").show();
                cargarServiciosFiltro();
            } else {
                $(".div_servicios").hide();
            }
        });
        
        $(document).on("change", ".seleccionar_servicio", function () {
            cargarMapaCafeterias();
            
        });
        
        $(document).on("input", "input[name='horario_filtro']:checked", function () {
            cargarMapaCafeterias();
        });
        
        $(document).on("change",".select_ciudades_filtro",function(){
            var ciudadVal = $(this).val();
            var coordenadas = $(this).find('option:selected').attr('coordenadas') || '';
            // Sincronizar valores en todos los campos hidden
            $("#ciudades_filtro_offcanvas").val(ciudadVal);
            $("#ciudades_filtro_offcanvas").attr('coordenadas', coordenadas);
            $("#ciudades_filtro_mobile").val(ciudadVal);
            $("#ciudades_filtro_mobile").attr('coordenadas', coordenadas);
        });
        
        // Función global para aplicar filtros desde el offcanvas desktop
        window.aplicarFiltros = function() {
            // Sincronizar valores antes de aplicar
            var ciudadVal = $('.select_ciudades_filtro').val();
            var coordenadas = $('.select_ciudades_filtro option:selected').attr('coordenadas') || '';
            $("#ciudades_filtro_offcanvas").val(ciudadVal);
            $("#ciudades_filtro_offcanvas").attr('coordenadas', coordenadas);
            $("#ciudades_filtro_mobile").val(ciudadVal);
            $("#ciudades_filtro_mobile").attr('coordenadas', coordenadas);
            
            $('#aplicar_filtros_offcanvas').submit();
            const offcanvasEl = document.getElementById("offcanvasFiltros");
            const offcanvas = bootstrap.Offcanvas.getInstance(offcanvasEl);
            if (offcanvas) {
                setTimeout(function() {
                    offcanvas.hide();
                }, 300);
            }
        };
        
        // Función global para aplicar filtros desde el offcanvas móvil
        window.aplicarFiltrosMobile = function() {
            // Sincronizar valores antes de aplicar
            var ciudadVal = $('.select_ciudades_filtro').val();
            var coordenadas = $('.select_ciudades_filtro option:selected').attr('coordenadas') || '';
            $("#ciudades_filtro_offcanvas").val(ciudadVal);
            $("#ciudades_filtro_offcanvas").attr('coordenadas', coordenadas);
            $("#ciudades_filtro_mobile").val(ciudadVal);
            $("#ciudades_filtro_mobile").attr('coordenadas', coordenadas);
            
            $('#aplicar_filtros_mobile').submit();
            const offcanvasEl = document.getElementById("offcanvasFiltrosMobile");
            const offcanvas = bootstrap.Offcanvas.getInstance(offcanvasEl);
            if (offcanvas) {
                setTimeout(function() {
                    offcanvas.hide();
                }, 300);
            }
        };
        
        // Función global para limpiar filtros
        window.limpiarFiltros = function() {
            $('input[name="rating"]').prop('checked', false);
            $('#star3-filtros, #star3-mobile').prop('checked', true);
            $('input[name="horario_filtro"][value="todos"]').prop('checked', true);
            $('.select_ciudades_filtro').val('').trigger('change');
            $('.check_servicios').prop('checked', false);
            $('.div_servicios').hide();
            $('.seleccionar_servicio').prop('checked', false);
            $("#ciudades_filtro_offcanvas").val('');
            $("#ciudades_filtro_offcanvas").attr('coordenadas', '');
            $("#ciudades_filtro_mobile").val('');
            $("#ciudades_filtro_mobile").attr('coordenadas', '');
            cargarMapaCafeterias();
        };
        
        // Usando matchMedia para comprobar el tamaño de pantalla
        const mediaQuery = window.matchMedia("(min-width: 992px)");
        
        // Verifica si la condición se cumple de entrada
        checkScreenSize(mediaQuery);
        
        // Detecta cambios en el tamaño de pantalla y aplica la función
        mediaQuery.addEventListener("change", checkScreenSize);
        
        function filtro_pantalla_grande() {
            // Añadir eventos solo si la pantalla es mayor a 991px
            $(document).on("click", "#btn_filtro_mapa, .menu_offcanvas", function (e) {
                e.preventDefault();
                // Cerrar offcanvas móvil si está abierto
                var mobileOffcanvas = document.getElementById("offcanvasFiltrosMobile");
                var bsMobileOffcanvas = bootstrap.Offcanvas.getInstance(mobileOffcanvas);
                if (bsMobileOffcanvas) {
                    bsMobileOffcanvas.hide();
                }
                // Abrir offcanvas desktop
                var desktopOffcanvas = document.getElementById("offcanvasFiltros");
                var bsDesktopOffcanvas = new bootstrap.Offcanvas(desktopOffcanvas);
                bsDesktopOffcanvas.show();
                
                // Cargar servicios si ya está activado el checkbox
                if ($('.check_servicios').is(':checked')) {
                    cargarServiciosFiltro();
                }
            });
        }
        
        // Función para gestionar la activación y desactivación de los eventos
        function checkScreenSize(e) {
            if (e.matches) {
                // Si la pantalla es mayor a 991px, ejecutar la función de desktop
                var mobileOffcanvas = document.getElementById("offcanvasFiltrosMobile");
                var bsMobileOffcanvas = bootstrap.Offcanvas.getInstance(mobileOffcanvas);
                if (bsMobileOffcanvas) {
                    bsMobileOffcanvas.hide();
                }
                $(document).off("click", ".menu_offcanvas");
                filtro_pantalla_grande();
            } else {
                // Si la pantalla es menor o igual a 991px, usar móvil
                var desktopOffcanvas = document.getElementById("offcanvasFiltros");
                var bsDesktopOffcanvas = bootstrap.Offcanvas.getInstance(desktopOffcanvas);
                if (bsDesktopOffcanvas) {
                    bsDesktopOffcanvas.hide();
                }
                $(document).off("click", "#btn_filtro_mapa, .menu_offcanvas");
                filtro_pantalla_chico();
            }
        }
        
        function filtro_pantalla_chico() {
            $(document).on("click", ".menu_offcanvas, #btn_filtro_mapa", function(e) {
                e.preventDefault();
                // Cerrar offcanvas desktop si está abierto
                var desktopOffcanvas = document.getElementById("offcanvasFiltros");
                var bsDesktopOffcanvas = bootstrap.Offcanvas.getInstance(desktopOffcanvas);
                if (bsDesktopOffcanvas) {
                    bsDesktopOffcanvas.hide();
                }
                // Abrir offcanvas móvil
                var mobileOffcanvas = document.getElementById("offcanvasFiltrosMobile");
                var bsMobileOffcanvas = new bootstrap.Offcanvas(mobileOffcanvas);
                bsMobileOffcanvas.show();
                
                // Cargar servicios si ya está activado el checkbox
                if ($('.check_servicios').is(':checked')) {
                    cargarServiciosFiltro();
                }
            });  
        }
            
        
    }
});