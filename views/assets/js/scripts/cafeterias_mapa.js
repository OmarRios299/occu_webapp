// Variables para Leaflet
var mapa_ubicaciones_cafeterias;
var markersArray = []; // Arreglo para almacenar los marcadores
var initialized_map_mapa = false;

// Función para verificar que Leaflet esté cargado
function waitForLeafletMapa(callback, maxAttempts = 50) {
    let attempts = 0;
    const checkLeaflet = () => {
        if (typeof L !== 'undefined') {
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

$(document).ready(function() {
    if (moduloActual == 'cafeterias_mapa') {
        waitForLeafletMapa(() => {
        cargarMapaCafeterias();
        });
        //cargarServiciosFiltro();
        
        // Prevenir que el navbar se oculte al hacer scroll en el módulo de mapa
        const mobileLogoNav = document.querySelector('.mobile-logo-nav.hide-on-scroll');
        if (mobileLogoNav) {
            // Remover la clase 'hidden' si existe
            mobileLogoNav.classList.remove('hidden');
            
            // Prevenir que se agregue la clase 'hidden' al hacer scroll
            let lastScrollTop = 0;
            window.addEventListener('scroll', function() {
                const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
                if (mobileLogoNav.classList.contains('hidden')) {
                    mobileLogoNav.classList.remove('hidden');
                }
                lastScrollTop = scrollTop;
            }, { passive: true });
        }
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

            // Calcular centro basado en polígono o coordenadas por defecto
            let centerLat, centerLng;
            if (polygonCoordinates.length > 0) {
                const centroid = getGeographicCentroid(polygonCoordinates);
                centerLat = centroid[0];
                centerLng = centroid[1];
            } else {
                centerLat = 29.384;
                centerLng = -107.006;
            }

            if (!mapa_ubicaciones_cafeterias) {
                // Inicializar el mapa de Leaflet
                mapa_ubicaciones_cafeterias = L.map('map', {
                    center: [centerLat, centerLng],
                    zoom: 12,
                    zoomControl: true
                });

                // Agregar capa de tiles (OpenStreetMap)
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap contributors',
                    maxZoom: 19
                }).addTo(mapa_ubicaciones_cafeterias);

                initialized_map_mapa = true;
            } else {
                // Reubicar el centro del mapa en las nuevas coordenadas
                mapa_ubicaciones_cafeterias.setView([centerLat, centerLng], mapa_ubicaciones_cafeterias.getZoom());
            }

            // Iterar sobre los datos recibidos para crear marcadores
            respuesta.data.forEach(function(cafeteria) {
                var nombre = cafeteria.nombre;
                var id = cafeteria.id;
                var imagen = cafeteria.imagen;
                var status = cafeteria.status;
                var latitud = parseFloat(cafeteria.latitud);
                var longitud = parseFloat(cafeteria.longitud);

                // Crear un ícono personalizado con Leaflet
                var customIcon = L.icon({
                    iconUrl: url + imagen,
                    iconSize: [50, 50],
                    iconAnchor: [25, 50],
                    popupAnchor: [0, -50]
                });

                // Crear un marcador con el icono personalizado
                var marker = L.marker([latitud, longitud], {
                    icon: customIcon
                }).addTo(mapa_ubicaciones_cafeterias);

                // Guardar el marcador en el array para luego poder eliminarlo
                markersArray.push(marker);

                // Agregar evento para abrir el modal al hacer clic en el marcador
                marker.on('click', function() {
                    abrirModalCafeteria(id);
                });
            });
            
            // Ajustar vista para mostrar todos los marcadores si hay alguno
            if (markersArray.length > 0) {
                var group = new L.featureGroup(markersArray);
                mapa_ubicaciones_cafeterias.fitBounds(group.getBounds().pad(0.1));
            } else if (polygonCoordinates.length > 0) {
                // Si no hay marcadores pero hay polígono, centrar en el polígono (sin mostrarlo)
                const latlngs = polygonCoordinates.map(coord => [coord.lat, coord.lng]);
                const tempPolygon = L.polygon(latlngs);
                mapa_ubicaciones_cafeterias.fitBounds(tempPolygon.getBounds());
            }
            
            // Invalidar tamaño del mapa para asegurar renderizado correcto
            setTimeout(function() {
                mapa_ubicaciones_cafeterias.invalidateSize();
            }, 100);
            cargaSistema(false);
        }
    });
}

// Función para eliminar los marcadores del mapa
function clearMarkers() {
    if (mapa_ubicaciones_cafeterias) {
    markersArray.forEach(function(marker) {
            mapa_ubicaciones_cafeterias.removeLayer(marker); // Eliminar el marcador del mapa
    });
    }
    markersArray = []; // Limpiar el array de marcadores
}

// Función para calcular el centroide geográfico de un conjunto de coordenadas
function getGeographicCentroid(coordinates) {
    if (!coordinates || coordinates.length === 0) {
        return [29.384, -107.006]; // Coordenadas por defecto
    }
    
    let latSum = 0;
    let lngSum = 0;
    let numPoints = coordinates.length;

    coordinates.forEach(coord => {
        latSum += coord.lat;
        lngSum += coord.lng;
    });

    let centroidLat = latSum / numPoints;
    let centroidLng = lngSum / numPoints;

    return [centroidLat, centroidLng];
}

function abrirModalCafeteria(id) {
    // Determinar qué offcanvas usar según el tamaño de pantalla
    const isMobile = window.innerWidth < 992;
    
    // Establecer el ID de la cafetería en ambos campos hidden
    $("#id_cafeteria_mapa").val(id);
    $("#id_cafeteria_mapa_mobile").val(id);
    
    // Ocultar botón de filtros
    $("#btn_filtro_mapa").hide();
    
    // Cargar información de la cafetería
    CargarVerCafeteriaMapa(id);
    
    // Abrir el offcanvas apropiado
    if (isMobile) {
        const offcanvasEl = document.getElementById("offcanvasCafeteriaMobile");
        const offcanvas = new bootstrap.Offcanvas(offcanvasEl);
        offcanvas.show();
        
        // Mostrar botón de filtros cuando se cierre el offcanvas móvil
        offcanvasEl.addEventListener('hidden.bs.offcanvas', function () {
            $("#btn_filtro_mapa").show();
        }, { once: true });
    } else {
        const offcanvasEl = document.getElementById("offcanvasCafeteria");
        const offcanvas = new bootstrap.Offcanvas(offcanvasEl);
        offcanvas.show();
        
        // Mostrar botón de filtros cuando se cierre el offcanvas desktop
        offcanvasEl.addEventListener('hidden.bs.offcanvas', function () {
            $("#btn_filtro_mapa").show();
        }, { once: true });
    }
}

// Función para cargar información de la cafetería en el mapa
function CargarVerCafeteriaMapa(idCafeteria) {
    var datos = new FormData();
    datos.append("cargar_datos", true);
    datos.append("id", idCafeteria);

    $.ajax({
        url: url + 'views/ajax/ajax_cafeterias_lista.php',
        method: 'POST',
        data: datos,
        cache: false,
        contentType: false,
        processData: false,
        success: function (respuesta) {
            respuesta = JSON.parse(respuesta);
            if (respuesta == 'error') {
                swal("¡Error!", "Ha ocurrido un error", "error");
            } else {
                // Actualizar títulos
                $("#titulo_cafeteria_ver_mapa").html(respuesta.data.nombre);
                $("#titulo_cafeteria_ver_mapa_mobile").html(respuesta.data.nombre);
                
                // Inicializar la galería con el array de imágenes en ambos offcanvas
                if (respuesta.imagenesArray && respuesta.imagenesArray.length > 0) {
                    if (typeof initGalleryMapa === 'function') {
                        // Actualizar galería en ambos contenedores
                        initGalleryMapa(respuesta.imagenesArray);
                    }
                }
                
                // Limpiar y agregar servicios en ambos offcanvas usando contenedores padre
                const contenedoresServicios = ['#contenido_cafeteria_mapa', '#contenido_cafeteria_mapa_mobile'];
                
                contenedoresServicios.forEach(function(contenedor) {
                    const $contenedor = $(contenedor);
                    if ($contenedor.length) {
                        // Limpiar y agregar servicios
                        const $carousel = $contenedor.find('#carousel_servicios_mapa');
                        $carousel.html('');
                        $carousel.append(respuesta.servicios);
                        
                        // Reinicializar Splide para servicios
                        const $splide = $contenedor.find('#splide_mapa');
                        if ($splide.length) {
                            // Destruir instancia anterior si existe
                            if ($splide.data('splide')) {
                                $splide.data('splide').destroy();
                            }
                            
                            // Determinar perPage según el ancho del offcanvas padre
                            const offcanvasEl = $splide.closest('.offcanvas');
                            const offcanvasWidth = offcanvasEl.width() || (window.innerWidth < 992 ? window.innerWidth : 600);
                            let perPage = 2; // Por defecto 2 para offcanvas
                            
                            if (offcanvasWidth > 700) {
                                perPage = 3;
                            } else if (offcanvasWidth < 500 || window.innerWidth < 992) {
                                perPage = 1;
                            }
                            
                            // Pequeño delay para asegurar que el DOM esté listo
                            setTimeout(function() {
                                new Splide($splide[0], {
                                    type: 'loop',
                                    perPage: perPage,
                                    perMove: 1,
                                    gap: '0.75rem',
                                    padding: '0.5rem',
                                    breakpoints: {
                                        768: {
                                            perPage: 2,
                                        },
                                        480: {
                                            perPage: 1,
                                        },
                                    },
                                }).mount();
                            }, 100);
                        }
                    }
                });
                
                // Actualizar información en ambos offcanvas (desktop y mobile)
                // Actualizar explícitamente cada contenedor
                const contenedores = ['#contenido_cafeteria_mapa', '#contenido_cafeteria_mapa_mobile'];
                
                contenedores.forEach(function(contenedor) {
                    const $contenedor = $(contenedor);
                    if ($contenedor.length) {
                        // Actualizar información general
                        $contenedor.find("#info1_mapa").html(respuesta.data.direccion || '');
                        $contenedor.find("#info2_mapa").html(respuesta.data.telefono ? '<a class="copiar_num_mapa" href="#">' + respuesta.data.telefono + '</a>' : '');
                        $contenedor.find("#info3_mapa").html(respuesta.data.correo ? '<a class="copiar_correo_mapa" href="#">' + respuesta.data.correo + '</a>' : '');
                        $contenedor.find("#info4_mapa").html((respuesta.data.horario || '') + (respuesta.data.horario && respuesta.data.status ? ' &bull; ' : '') + (respuesta.data.status || ''));
                        $contenedor.find("#descripcion_mapa").html(respuesta.data.descripcion || '');
                        
                        // Ocultar items vacíos en información general
                        ocultarItemsVaciosMapa($contenedor);
                        
                        // Configurar botón de ubicación
                        $contenedor.find(".ir_googlemaps_mapa").attr("latitud", respuesta.data.latitud).attr('longitud', respuesta.data.longitud);
                        
                        // Configurar link del menú (abrir en nueva pestaña)
                        $contenedor.find("#link_menu_mapa").attr({
                            "href": url + "cafeterias_menu/" + respuesta.data.id,
                            "target": "_blank",
                            "rel": "noopener noreferrer"
                        });
                    }
                });
                
                // Cargar comentarios
                cargarComentariosMapa(1, idCafeteria);
            }
        }
    });
}

// Función para ocultar items vacíos en información general del mapa
function ocultarItemsVaciosMapa($contenedor) {
    if (!$contenedor || !$contenedor.length) return;
    
    // Verificar cada item de información
    $contenedor.find('#info1_mapa').closest('.info-item').toggle($contenedor.find('#info1_mapa').text().trim() !== '');
    $contenedor.find('#info2_mapa').closest('.info-item').toggle($contenedor.find('#info2_mapa').text().trim() !== '');
    $contenedor.find('#info3_mapa').closest('.info-item').toggle($contenedor.find('#info3_mapa').text().trim() !== '');
    $contenedor.find('#info4_mapa').closest('.info-item').toggle($contenedor.find('#info4_mapa').text().trim() !== '');
    
    // Ocultar descripción si está vacía
    const $descripcionCard = $contenedor.find('#descripcion_mapa').closest('.modern-card');
    if ($contenedor.find('#descripcion_mapa').text().trim() === '') {
        $descripcionCard.hide();
    } else {
        $descripcionCard.show();
    }
}

// Función para cargar comentarios en el mapa
function cargarComentariosMapa(pagina, cafeteria) {
    let filtro = `?comentarios=${true}&pagina=${pagina}&cafeteria=${cafeteria}`;

    $.ajax({
        url: url + 'views/ajax/ajax_cafeterias_lista.php' + filtro, 
        method: "GET",
        success: function (response) {
            response = JSON.parse(response);
            
            // Actualizar comentarios en ambos contenedores
            const contenedores = ['#contenido_cafeteria_mapa', '#contenido_cafeteria_mapa_mobile'];
            
            contenedores.forEach(function(contenedor) {
                const $contenedor = $(contenedor);
                if ($contenedor.length) {
                    if (response.comentarios == '' || !response.comentarios || response.comentarios.length === 0) {
                        // Mostrar mensaje cuando no hay reseñas
                        $contenedor.find(".coment-ocultar").show();
                        $contenedor.find("#mensaje-sin-resenas_mapa").show();
                        $contenedor.find("#comentariosLista_mapa").html('');
                        $contenedor.find("#paginacionComentarios_mapa").html('');
                    } else {
                        // Ocultar mensaje y mostrar comentarios
                        $contenedor.find("#mensaje-sin-resenas_mapa").hide();
                        $contenedor.find(".coment-ocultar").show();
                        // Llenar la lista de comentarios con los datos recibidos
                        var comentariosHtml = '';
                        response.comentarios.forEach(function (comentario) {
                            var fechaSinHora = comentario.fecha_alta.substring(0, 10);
                            comentariosHtml += '<li><h3>' + comentario.nombre_usuario + '</h3>';
                            comentariosHtml += '<p>' + comentario.comentario + '</p>';
                            comentariosHtml += '<p><small>' + fechaSinHora + '</small></p></li>';
                        });
                        $contenedor.find('#comentariosLista_mapa').html(comentariosHtml);
                        if ($contenedor.find("#total_coment_mapa").length) {
                            $contenedor.find("#total_coment_mapa").html(response.totalComentarios);
                        }
                    }
                }
            });
            
            // Actualizar controles de paginación (solo si hay comentarios)
            if (response.comentarios && response.comentarios.length > 0) {
                actualizarPaginacionMapa(response.totalPaginas, pagina);
            }
        }
    });
}

// Función para actualizar paginación de comentarios en el mapa
function actualizarPaginacionMapa(totalPaginas, paginaActual) {
    var paginacionHtml = '';
    for (var i = 1; i <= totalPaginas; i++) {
        paginacionHtml += '<button class="btn-paginacion ' + (i === paginaActual ? 'active' : '') + '" data-pagina="' + i + '">' + i + '</button>';
    }
    
    // Actualizar paginación en ambos contenedores
    const contenedores = ['#contenido_cafeteria_mapa', '#contenido_cafeteria_mapa_mobile'];
    contenedores.forEach(function(contenedor) {
        const $contenedor = $(contenedor);
        if ($contenedor.length) {
            $contenedor.find('#paginacionComentarios_mapa').html(paginacionHtml);
        }
    });

    // Asignar evento a los botones de paginación (usar delegación de eventos)
    $(document).off('click', '.btn-paginacion').on('click', '.btn-paginacion', function () {
        var pagina = $(this).data('pagina');
        var idCafeteria = $("#id_cafeteria_mapa").val() || $("#id_cafeteria_mapa_mobile").val();
        cargarComentariosMapa(pagina, idCafeteria);
    });
}

// Eventos para copiar teléfono y correo en el mapa (usando clases en lugar de IDs para evitar duplicados)
$(document).on('click', '.copiar_num_mapa', function (event) {
    event.preventDefault();
    const telefono = $(this).text();
    // Encontrar el feedback más cercano dentro del mismo offcanvas
    const feedback = $(this).closest('.cafeteria-content-offcanvas').find('#copy-feedback-tel_mapa');
    if (feedback.length) {
        copyToClipboard(telefono, feedback.attr('id'));
    }
});

$(document).on('click', '.copiar_correo_mapa', function (event) {
    event.preventDefault();
    const correo = $(this).text();
    // Encontrar el feedback más cercano dentro del mismo offcanvas
    const feedback = $(this).closest('.cafeteria-content-offcanvas').find('#copy-feedback-email_mapa');
    if (feedback.length) {
        copyToClipboard(correo, feedback.attr('id'));
    }
});

// Evento para agregar comentario desde el mapa (usar delegación de eventos para ambos offcanvas)
$(document).on("click", "#btn_agregar_comentario_mapa", function () {
    // Toggle en el contenedor más cercano
    $(this).closest('.cafeteria-content-offcanvas').find("#comment-form-area_mapa").toggle();
});

// Evento para cancelar comentario (cerrar el área de comentarios)
$(document).on("click", ".btn-cancelar-comentario-mapa", function () {
    const $contenedor = $(this).closest('.cafeteria-content-offcanvas');
    $contenedor.find("#comment-form-area_mapa").hide();
    $contenedor.find("#agregar_comentario_mapa").val('');
});

$(document).on("click", "#btn_aceptar_comentario_mapa", function () {
    const $contenedor = $(this).closest('.cafeteria-content-offcanvas');
    const $textarea = $contenedor.find("#agregar_comentario_mapa");
    
    if ($textarea.val() == '') {
        swal("¡Alerta!", "Agrega un comentario.", "warning");
        return '';
    }
    var datos = new FormData();
    datos.append("registrar_comentario", true);
    datos.append('comentario', $textarea.val());
    var idCafeteria = $("#id_cafeteria_mapa").val() || $("#id_cafeteria_mapa_mobile").val();
    datos.append('id_cafeteria', idCafeteria);

    $.ajax({
        url: url + 'views/ajax/ajax_cafeterias_lista.php',
        method: 'POST',
        data: datos,
        cache: false,
        contentType: false,
        processData: false,
        beforeSend: cargaSistema(true),
        success: function (respuesta) {
            if (respuesta == 'success') {
                swal({
                    title: "¡Ok!",
                    text: "Tu comentario se registró correctamente.",
                    icon: "success",
                    button: "Aceptar",
                }).then(function () {
                    // Limpiar ambos textareas
                    $("#agregar_comentario_mapa").val('');
                    $("#comment-form-area_mapa").hide();
                    var idCafeteria = $("#id_cafeteria_mapa").val() || $("#id_cafeteria_mapa_mobile").val();
                    cargarComentariosMapa(1, idCafeteria);
                });
            } else if(respuesta == 'sesion'){
                swal({
                    title: "¡Inicia sesión!",
                    text: "Inicia sesión para registrar tu comentario.",
                    icon: "warning",
                    button: "Aceptar",
                }).then(function () {
                    window.location = url+"login";
                });
            } else {
                swal("¡Error!", "Ha ocurrido un error.", "error");
            }
            cargaSistema(false);
        }
    });
});

// Evento para ir a Google Maps desde el mapa
$(document).on("click", ".ir_googlemaps_mapa", function () {
    const latitude = $(this).attr("latitud");
    const longitude = $(this).attr("longitud");
    const googleMapsUrl = `https://www.google.com/maps/dir/?api=1&destination=${latitude},${longitude}`;
    window.open(googleMapsUrl, '_blank');
});

$(document).ready(function(){
    if (moduloActual=='cafeterias_mapa') {
        // Limpiar backdrop cuando se cierre cualquier offcanvas de filtros
        $('#offcanvasFiltrosMobile').on('hidden.bs.offcanvas', function () {
            // Limpiar backdrop inmediatamente y con delays adicionales para asegurar limpieza completa
            limpiarBackdrop();
            setTimeout(function() {
                limpiarBackdrop();
            }, 100);
            setTimeout(function() {
                limpiarBackdrop();
            }, 300);
        });
        
        $('#offcanvasFiltros').on('hidden.bs.offcanvas', function () {
            // Limpiar backdrop inmediatamente y con delays adicionales para asegurar limpieza completa
            limpiarBackdrop();
            setTimeout(function() {
                limpiarBackdrop();
            }, 100);
            setTimeout(function() {
                limpiarBackdrop();
            }, 300);
        });
        
        // Función para limpiar backdrop
        function limpiarBackdrop() {
            // Remover todos los backdrops
            $('.modal-backdrop').remove();
            
            // Remover clases del body
            $('body').removeClass('modal-open');
            
            // Resetear estilos del body
            $('body').css({
                'overflow': '',
                'padding-right': ''
            });
            
            // Asegurar que no queden backdrops ocultos
            setTimeout(function() {
                $('.modal-backdrop').remove();
                if ($('body').hasClass('modal-open')) {
                    $('body').removeClass('modal-open');
                }
            }, 50);
        }
        
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
                    // Limpiar backdrop después de cerrar
                    setTimeout(function() {
                        limpiarBackdrop();
                    }, 350);
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
                
                // Limpiar cualquier backdrop residual antes de abrir
                limpiarBackdrop();
                
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