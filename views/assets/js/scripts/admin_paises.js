
$(document).ready(function () {
    if (moduloActual === 'admin_paises') {
        if ($('#grafica_ciudades').length) {
            cargarMetricosPaises();
        }
        // Este evento se maneja con delegación de eventos más abajo
        // Se mantiene aquí solo para compatibilidad si es necesario

        $('#btn_eliminar_poligono').on('click', removePolygon);
    }
});

// Función para cargar coordenadas de una ciudad vía Ajax
function cargarCoordenadasCiudad(idCiudad, callback) {
    if (!idCiudad) {
        // No hay ID, no hay coordenadas guardadas
        callback(null, []);
        return;
    }
    
    var datos = new FormData();
    datos.append("obtener_info_ciudad", true);
    datos.append('id_ciudad', idCiudad);
    
    $.ajax({
        url: url + 'views/ajax/ajax_admin_paises.php',
        method: 'POST',
        data: datos,
        cache: false,
        contentType: false,
        processData: false,
        success: function (respuesta) {
            try {
                const ciudad = JSON.parse(respuesta);
                let polygonCoordinates = [];
                
                if (ciudad && ciudad.coordenadas && ciudad.coordenadas.trim() !== '' && ciudad.coordenadas !== 'null') {
                    try {
                        polygonCoordinates = JSON.parse(ciudad.coordenadas);
                        if (!Array.isArray(polygonCoordinates) || polygonCoordinates.length < 3) {
                            polygonCoordinates = [];
                        }
                    } catch (e) {
                        console.error('Error al parsear coordenadas:', e);
                        polygonCoordinates = [];
                    }
                }
                
                callback(ciudad, polygonCoordinates);
            } catch (e) {
                console.error('Error al parsear respuesta:', e);
                callback(null, []);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error al cargar coordenadas:', error);
            callback(null, []);
        }
    });
}

$(document).on("click", "#delimitar_ciudad", function () {
    const idCiudad = $("#id_ciudad").val();
    
    // Cargar coordenadas vía Ajax si hay un ID de ciudad
    if (idCiudad) {
        console.log('Cargando coordenadas para ciudad ID:', idCiudad);
        cargarCoordenadasCiudad(idCiudad, function(ciudad, polygonCoordinates) {
            const center = polygonCoordinates.length > 0 ? getGeographicCentroid(polygonCoordinates) : [29.384, -107.006];
            
            // Guardar los datos para cuando se muestre el modal
            pendingCenter = center;
            pendingPolygonCoordinates = polygonCoordinates.length > 0 ? polygonCoordinates : null;
            
            // Actualizar el campo oculto con las coordenadas
            if (polygonCoordinates.length > 0) {
                $("#coordenadas_ciudad").val(JSON.stringify(polygonCoordinates));
            }
            
            console.log('Abriendo modal del mapa. Centro:', center, 'Polígono:', pendingPolygonCoordinates ? 'Sí' : 'No');
            $("#modal_mapa").modal('show');
        });
    } else {
        // No hay ID, es una ciudad nueva
        pendingCenter = [29.384, -107.006];
        pendingPolygonCoordinates = null;
        $("#coordenadas_ciudad").val('');
        console.log('Abriendo modal del mapa para nueva ciudad');
        $("#modal_mapa").modal('show');
    }
});

// Variables de mapa y dibujo
let map_delimitar_ciudad;
let allowedPolygon;
let drawFeature;
let editableLayers;
let initialized = false;
let pendingCenter = null;
let pendingPolygonCoordinates = null;

// Función para remover el polígono y resetear herramientas de dibujo
function removePolygon() {
    if (allowedPolygon && editableLayers) {
        editableLayers.removeLayer(allowedPolygon);
        allowedPolygon = null;
        $("#coordenadas_ciudad").val('');
        console.log('Polygon removed');
    }
}

// Función para verificar que Leaflet esté cargado
function waitForLeaflet(callback, maxAttempts = 50) {
    let attempts = 0;
    const checkLeaflet = () => {
        if (typeof L !== 'undefined' && typeof L.Control !== 'undefined' && typeof L.Control.Draw !== 'undefined') {
            callback();
        } else {
            attempts++;
            if (attempts < maxAttempts) {
                setTimeout(checkLeaflet, 100);
            } else {
                console.error('Leaflet no se pudo cargar después de varios intentos');
            }
        }
    };
    checkLeaflet();
}

// Función para inicializar el mapa
function initMap(center) {
    // Verificar que Leaflet esté disponible
    if (typeof L === 'undefined' || typeof L.Control === 'undefined' || typeof L.Control.Draw === 'undefined') {
        console.log('Esperando a que Leaflet se cargue...');
        waitForLeaflet(() => {
            initMap(center);
        });
        return;
    }

    if (!initialized) {
        // Verificar que el contenedor existe y está visible
        const mapContainer = document.getElementById('map');
        if (!mapContainer) {
            console.error('El contenedor del mapa no existe');
            return;
        }

        console.log('Inicializando mapa Leaflet...');

        // Inicializar el mapa de Leaflet
        map_delimitar_ciudad = L.map('map', {
            center: center || [29.384, -107.006],
            zoom: 15,
            zoomControl: true
        });

        // Agregar capa de tiles (OpenStreetMap)
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors',
            maxZoom: 19
        }).addTo(map_delimitar_ciudad);

        // Crear un FeatureGroup para las capas editables
        editableLayers = new L.FeatureGroup();
        map_delimitar_ciudad.addLayer(editableLayers);

        // Configurar las opciones de dibujo
        const drawControlOptions = {
            position: 'topleft',
            draw: {
                polygon: {
                    allowIntersection: false,
                    showArea: true,
                    shapeOptions: {
                        color: '#3388ff',
                        fillColor: '#3388ff',
                        fillOpacity: 0.2,
                        weight: 3
                    }
                },
                polyline: false,
                rectangle: false,
                circle: false,
                marker: false,
                circlemarker: false
            },
            edit: {
                featureGroup: editableLayers,
                remove: true
            }
        };

        // Crear el control de dibujo
        try {
            drawFeature = new L.Control.Draw(drawControlOptions);
            map_delimitar_ciudad.addControl(drawFeature);
            console.log('Control de dibujo agregado correctamente');
        } catch (e) {
            console.error('Error al crear el control de dibujo:', e);
        }

        // Manejar el evento cuando se completa el dibujo del polígono
        map_delimitar_ciudad.on(L.Draw.Event.CREATED, function (event) {
            console.log('Evento CREATED disparado - Polígono creado');
            console.log('Tipo de evento:', event.type);
            console.log('Layer:', event.layer);
            
            const layer = event.layer;
            removePolygon();
            allowedPolygon = layer;
            editableLayers.addLayer(allowedPolygon);
            updatePolygonCoordinates(allowedPolygon);
            
            console.log('Polígono agregado al mapa y FeatureGroup');
        });

        // Manejar eventos de edición del polígono
        map_delimitar_ciudad.on(L.Draw.Event.EDITED, function (event) {
            console.log('Evento EDITED disparado - Polígono editado');
            const layers = event.layers;
            layers.eachLayer(function (layer) {
                if (layer === allowedPolygon) {
                    console.log('Actualizando coordenadas del polígono editado');
                    updatePolygonCoordinates(layer);
                }
            });
        });
        
        // También escuchar cambios en los vértices del polígono directamente
        editableLayers.on('layeradd', function(e) {
            const layer = e.layer;
            if (layer === allowedPolygon) {
                // Escuchar cuando se mueven los vértices
                layer.on('edit', function() {
                    console.log('Polígono editado directamente');
                    updatePolygonCoordinates(layer);
                });
            }
        });
        
        // Manejar cuando se elimina un polígono
        map_delimitar_ciudad.on(L.Draw.Event.DELETED, function (event) {
            console.log('Polígono eliminado');
            const layers = event.layers;
            layers.eachLayer(function (layer) {
                if (layer === allowedPolygon) {
                    allowedPolygon = null;
                    $("#coordenadas_ciudad").val('');
                }
            });
        });

        // Forzar el redimensionamiento después de un breve delay
        setTimeout(() => {
            map_delimitar_ciudad.invalidateSize();
        }, 100);

        initialized = true;
        console.log('Mapa inicializado correctamente');
    } else {
        // Si el mapa ya fue inicializado, solo centramos en la nueva posición
        if (center) {
            map_delimitar_ciudad.setView(center, 15);
        }
        // Asegurarse de que el mapa se redimensione correctamente
        setTimeout(() => {
            map_delimitar_ciudad.invalidateSize();
        }, 100);
    }
}

// Actualiza las coordenadas del polígono
function updatePolygonCoordinates(polygon) {
    if (!polygon) {
        console.error('Polygon no está definido');
        return;
    }
    
    try {
        const latlngs = polygon.getLatLngs();
        console.log('LatLngs obtenidos:', latlngs);
        
        // Leaflet devuelve un array de arrays para polígonos, necesitamos el primer nivel
        if (latlngs && latlngs.length > 0 && latlngs[0]) {
            const coordinates = latlngs[0].map(latlng => {
                // Asegurarse de obtener lat y lng correctamente
                const lat = typeof latlng.lat === 'function' ? latlng.lat() : latlng.lat;
                const lng = typeof latlng.lng === 'function' ? latlng.lng() : latlng.lng;
                return { lat: parseFloat(lat), lng: parseFloat(lng) };
            });
            
            const coordenadasJSON = JSON.stringify(coordinates);
            console.log('Coordenadas actualizadas:', coordinates);
            console.log('JSON guardado:', coordenadasJSON);
            
            $("#coordenadas_ciudad").val(coordenadasJSON);
            
            // Verificar que se guardó correctamente
            const verificacion = $("#coordenadas_ciudad").val();
            if (verificacion === coordenadasJSON) {
                console.log('✓ Coordenadas guardadas correctamente en el campo oculto');
            } else {
                console.error('✗ Error: Las coordenadas no se guardaron correctamente');
            }
        } else {
            console.warn('No se pudieron obtener las coordenadas del polígono');
        }
    } catch (e) {
        console.error('Error al actualizar coordenadas del polígono:', e);
        console.error('Stack:', e.stack);
    }
}

// Calcula el centro geográfico de las coordenadas
function getGeographicCentroid(coordinates) {
    const centroid = coordinates.reduce((acc, coord) => ({
        lat: acc.lat + coord.lat,
        lng: acc.lng + coord.lng
    }), { lat: 0, lng: 0 });

    return [centroid.lat / coordinates.length, centroid.lng / coordinates.length];
}

// Actualiza el polígono y centra el mapa
function updatePolygonAndCenterMap(polygonCoordinates) {
    if (!editableLayers) {
        console.error('editableLayers no está inicializado');
        return;
    }
    
    if (!polygonCoordinates || polygonCoordinates.length === 0) {
        console.log('No hay coordenadas para cargar');
        return;
    }
    
    // Validar que las coordenadas tengan el formato correcto
    if (!Array.isArray(polygonCoordinates)) {
        console.error('Las coordenadas no son un array válido');
        return;
    }
    
    removePolygon();
    
    // Convertir coordenadas de formato {lat, lng} a [lat, lng] para Leaflet
    const latlngs = polygonCoordinates.map(coord => {
        if (coord && typeof coord === 'object' && 'lat' in coord && 'lng' in coord) {
            return [parseFloat(coord.lat), parseFloat(coord.lng)];
        }
        return null;
    }).filter(coord => coord !== null);
    
    if (latlngs.length < 3) {
        console.error('Se necesitan al menos 3 puntos para crear un polígono');
        return;
    }
    
    try {
        allowedPolygon = L.polygon(latlngs, {
            color: '#3388ff',
            fillColor: '#3388ff',
            fillOpacity: 0.2,
            weight: 3
        });
        
        editableLayers.addLayer(allowedPolygon);
        updatePolygonCoordinates(allowedPolygon);

        const center = getGeographicCentroid(polygonCoordinates);
        map_delimitar_ciudad.setView(center, 13);
        
        console.log('Polígono cargado correctamente con', latlngs.length, 'puntos');
        
        // Escuchar cambios cuando se edita el polígono cargado
        allowedPolygon.on('edit', function() {
            console.log('Polígono cargado editado');
            updatePolygonCoordinates(allowedPolygon);
        });
        
        // El polígono ya es editable porque está en editableLayers
        // Los cambios se detectarán a través del evento L.Draw.Event.EDITED
    } catch (e) {
        console.error('Error al crear el polígono:', e);
    }
}

// Manejo del evento cuando se muestra el modal
$('#modal_mapa').on('shown.bs.modal', function () {
    // Asegurarse de que el contenedor del mapa esté visible
    if ($('#map').length === 0) {
        console.error('El contenedor del mapa no existe');
        return;
    }

    // Función para inicializar o actualizar el mapa
    const initializeOrUpdateMap = () => {
        if (!initialized) {
            // Inicializar el mapa por primera vez
            console.log('Inicializando mapa por primera vez. Centro:', pendingCenter);
            if (pendingCenter) {
                initMap(pendingCenter);
                
                // Cargar polígono si existe después de que el mapa esté completamente inicializado
                if (pendingPolygonCoordinates && pendingPolygonCoordinates.length > 0) {
                    setTimeout(() => {
                        if (editableLayers && map_delimitar_ciudad) {
                            console.log('Cargando polígono existente con', pendingPolygonCoordinates.length, 'puntos');
                            updatePolygonAndCenterMap(pendingPolygonCoordinates);
                        } else {
                            console.error('editableLayers o map_delimitar_ciudad no están disponibles');
                        }
                    }, 500);
                } else {
                    console.log('No hay polígono para cargar - puedes dibujar uno nuevo');
                }
            }
        } else {
            // El mapa ya está inicializado, solo actualizar
            console.log('Actualizando mapa existente');
            setTimeout(() => {
                if (map_delimitar_ciudad) {
                    map_delimitar_ciudad.invalidateSize();
                    
                    if (pendingCenter) {
                        const zoom = (pendingPolygonCoordinates && pendingPolygonCoordinates.length > 0) ? 13 : 15;
                        map_delimitar_ciudad.setView(pendingCenter, zoom);
                    }
                    
                    // Recargar polígono si existe
                    if (pendingPolygonCoordinates && pendingPolygonCoordinates.length > 0) {
                        console.log('Recargando polígono existente con', pendingPolygonCoordinates.length, 'puntos');
                        updatePolygonAndCenterMap(pendingPolygonCoordinates);
                    } else {
                        console.log('No hay polígono para cargar, limpiando');
                        removePolygon();
                    }
                }
            }, 300);
        }
    };

    // Verificar que Leaflet esté disponible
    if (typeof L === 'undefined' || typeof L.Control === 'undefined' || typeof L.Control.Draw === 'undefined') {
        console.log('Esperando a que Leaflet se cargue...');
        waitForLeaflet(() => {
            initializeOrUpdateMap();
        });
    } else {
        initializeOrUpdateMap();
    }
});

// Limpiar variables cuando se cierra el modal
$('#modal_mapa').on('hidden.bs.modal', function () {
    pendingCenter = null;
    pendingPolygonCoordinates = null;
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
    // Limpiar formulario
    $("#id_ciudad").val('');
    $("#nombre_ciudad").val('');
    $("#select_estado").val('');
    $("#coordenadas_ciudad").val('');
    $("#modalLabel").text('Agregando ciudad');
    
    const idCiudad = $(this).attr('idCiudad') || $(this).attr('idRegistro');
    
    if (!idCiudad) {
        // Agregar nueva ciudad
        $("#modal_agregar_ciudad").modal('show');
    } else {
        // Editar ciudad existente - cargar datos vía Ajax
        console.log('Cargando datos de ciudad ID:', idCiudad);
        cargaSistema(true);
        
        cargarCoordenadasCiudad(idCiudad, function(ciudad, polygonCoordinates) {
            if (ciudad) {
                $("#id_ciudad").val(ciudad.id);
                $("#nombre_ciudad").val(ciudad.nombre);
                $("#select_estado").val(ciudad.id_entidad_federativa);
                
                // Guardar coordenadas en el campo oculto
                if (polygonCoordinates.length > 0) {
                    $("#coordenadas_ciudad").val(JSON.stringify(polygonCoordinates));
                } else {
                    $("#coordenadas_ciudad").val('');
                }
                
                $("#modalLabel").text('Editando ciudad');
                $("#modal_agregar_ciudad").modal('show');
            } else {
                console.error('No se pudo cargar la información de la ciudad');
                alert('Error al cargar la información de la ciudad');
            }
            cargaSistema(false);
        });
    }
});

// Evento para el botón de guardar ubicación en el modal del mapa
$(document).on("click", "#btn_guardar_ubicacion", function (e) {
    // Prevenir el cierre automático del modal
    e.preventDefault();
    
    console.log('Botón Aceptar presionado');
    
    const idCiudad = $("#id_ciudad").val();
    
    // Asegurarse de que las coordenadas estén actualizadas
    if (allowedPolygon && editableLayers.hasLayer(allowedPolygon)) {
        console.log('Actualizando coordenadas antes de cerrar');
        updatePolygonCoordinates(allowedPolygon);
        
        const coordenadasGuardadas = $("#coordenadas_ciudad").val();
        console.log('Coordenadas guardadas en campo oculto:', coordenadasGuardadas);
        
        // Si hay un ID de ciudad, guardar directamente las coordenadas (modo edición)
        if (idCiudad && idCiudad.trim() !== '') {
            console.log('Modo edición: Guardando coordenadas directamente');
            cargaSistema(true);
            
            var datos = new FormData();
            datos.append("guardar_coordenadas_ciudad", true);
            datos.append('id_ciudad', idCiudad);
            datos.append('coordenadas', coordenadasGuardadas || '');
            
            $.ajax({
                url: url + 'views/ajax/ajax_admin_paises.php',
                method: 'POST',
                data: datos,
                cache: false,
                contentType: false,
                processData: false,
                success: function (respuesta) {
                    console.log('Respuesta del servidor:', respuesta);
                    if (respuesta == 'success') {
                        //alertaUpdate();
                        $('#modal_mapa').modal('hide');
                       
                       
                    } else {
                        console.error('Error al guardar coordenadas:', respuesta);
                        alert('Error al guardar las coordenadas');
                    }
                    cargaSistema(false);
                },
                error: function(xhr, status, error) {
                    console.error('Error en la petición:', error);
                    alert('Error al guardar las coordenadas');
                    cargaSistema(false);
                }
            });
        } else {
            // No hay ID, es una ciudad nueva - solo cerrar el modal (el formulario manejará el guardado)
            console.log('Modo agregar: Solo cerrando modal, el formulario guardará');
            $('#modal_mapa').modal('hide');
        }
    } else {
        // Si no hay polígono
        if (idCiudad && idCiudad.trim() !== '') {
            // Modo edición sin polígono - guardar coordenadas vacías
            console.log('Modo edición sin polígono: Guardando coordenadas vacías');
            cargaSistema(true);
            
            var datos = new FormData();
            datos.append("guardar_coordenadas_ciudad", true);
            datos.append('id_ciudad', idCiudad);
            datos.append('coordenadas', '');
            
            $.ajax({
                url: url + 'views/ajax/ajax_admin_paises.php',
                method: 'POST',
                data: datos,
                cache: false,
                contentType: false,
                processData: false,
                success: function (respuesta) {
                    if (respuesta == 'success') {
                        alertaUpdate();
                        $('#modal_mapa').modal('hide');
                        setTimeout(() => {
                            location.reload();
                        }, 1500);
                    }
                    cargaSistema(false);
                },
                error: function(xhr, status, error) {
                    console.error('Error en la petición:', error);
                    cargaSistema(false);
                }
            });
        } else {
            // Modo agregar sin polígono - solo cerrar
            console.log('Modo agregar sin polígono: Limpiando coordenadas y cerrando');
            $("#coordenadas_ciudad").val('');
            $('#modal_mapa').modal('hide');
        }
    }
});

$(document).on("submit", "#form_agregar_ciudad", function () {
    // Asegurarse de que las coordenadas estén actualizadas antes de enviar
    if (allowedPolygon && editableLayers && editableLayers.hasLayer(allowedPolygon)) {
        console.log('Actualizando coordenadas antes de enviar formulario');
        updatePolygonCoordinates(allowedPolygon);
    }
    
    var datos = new FormData();

    datos.append("agregar_ciudad", true);
    datos.append('id', $("#id_ciudad").val() || '');
    datos.append('nombre', $("#nombre_ciudad").val());
    datos.append('entidad_federativa', $("#select_estado").val());
    
    // Obtener las coordenadas actualizadas
    let coordenadas = $("#coordenadas_ciudad").val();
    console.log('Coordenadas a enviar al servidor:', coordenadas);
    
    if (!coordenadas || coordenadas.trim() === '') {
        coordenadas = '';
    }
    datos.append('coordenadas', coordenadas);

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
                if ($("#id_ciudad").val()) {
                    alertaUpdate();
                } else {
                    alertaInsert();
                }
                // Recargar la página después de un breve delay
                setTimeout(() => {
                    location.reload();
                }, 1500);
            } else {
                console.error('Error al guardar:', respuesta);
            }
            cargaSistema(false);
        },
        error: function(xhr, status, error) {
            console.error('Error en la petición:', error);
            cargaSistema(false);
        }
    });
});