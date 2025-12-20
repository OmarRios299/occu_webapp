$(document).ready(function () {
    let paginaActual = 1; // Inicializar la página actual

    if (moduloActual == 'cafeterias_lista' || (moduloActual == 'cafeterias' && $('#div_lista_cafeterias').length && !$('#tabla_cafeterias').length)) {

        if ($('#titulo_cafeteria_ver').length) {
            CargarVerCafeteria();
            cargarComentarios(1);
        }

        if ($('#div_lista_cafeterias').length) {
            cargarListaCafeterias(paginaActual);
            //cargarServiciosFiltro();
            
            // Manejar click en las cards de cafetería para abrir offcanvas (reutilizar el del mapa)
            $(document).on('click', '.cafeteria-card-link', function(e) {
                e.preventDefault();
                const idCafeteria = $(this).data('cafeteria-id');
                if (idCafeteria) {
                    // Reutilizar la función del mapa para cargar y mostrar la cafetería
                    if (typeof CargarVerCafeteriaMapa === 'function') {
                        CargarVerCafeteriaMapa(idCafeteria);
                        // Abrir el offcanvas correspondiente según el tamaño de pantalla
                        const isMobile = window.innerWidth < 992;
                        const offcanvasId = isMobile ? 'offcanvasCafeteriaMobile' : 'offcanvasCafeteria';
                        const offcanvasEl = document.getElementById(offcanvasId);
                        if (offcanvasEl) {
                            const offcanvas = new bootstrap.Offcanvas(offcanvasEl);
                            offcanvas.show();
                        }
                    }
                }
            });
        }

        $('#boton-siguiente').on('click', function () {
            paginaActual++;
            cargarListaCafeterias(paginaActual);
        });

        $('#boton-anterior').on('click', function () {
            if (paginaActual > 1) {
                paginaActual--;
                cargarListaCafeterias(paginaActual);
            }
        });

        $(document).on('keyup', '#filtro-input', function () {
            const filtro = $(this).val();
            paginaActual = 1;
            cargarListaCafeterias(paginaActual, filtro);
        });
        
        // También permitir búsqueda con Enter
        $(document).on('keypress', '#filtro-input', function (e) {
            if (e.which === 13) {
                e.preventDefault();
                const filtro = $(this).val();
                paginaActual = 1;
                cargarListaCafeterias(paginaActual, filtro);
            }
        });
    }
});

function cargarListaCafeterias(pagina, filtro = '') {
    const limite = 9;

    const servicios = [];
    $(".seleccionar_servicio").each(function(){
        if ($(this).is(":checked")) {
            servicios.push({
                id:$(this).val()
            });
        }
    });
    
    let horario = $('input[name="horario_filtro"]:checked').val();
    let ciudad = $("#ciudades_filtro").val() || $("#ciudades_filtro_offcanvas").val() || $("#ciudades_filtro_mobile").val();
    
    var datos = new FormData();
    datos.append("cargar_lista", true);
    datos.append("pagina", pagina); 
    datos.append("busqueda", filtro); 
    datos.append("horario", horario);
    datos.append("ciudad", ciudad);
    datos.append("servicios", JSON.stringify(servicios)); 

    $.ajax({
        url: url + 'views/ajax/ajax_cafeterias_lista.php',
        method: 'POST',
        data: datos,
        cache: false,
        contentType: false,
        processData: false,
        beforeSend: cargaSistema(true),
        success: function (respuesta) {
            //console.log("Respuesta del servidor:", respuesta);
            try {
                respuesta = JSON.parse(respuesta);

                $('#div_lista_cafeterias').html(respuesta.html);

                if (pagina > 1) {
                    $('#boton-anterior').prop('disabled', false);
                } else {
                    $('#boton-anterior').prop('disabled', true);
                }

                if (respuesta.totalCafeterias > (pagina * limite)) {
                    $('#boton-siguiente').prop('disabled', false);
                } else {
                    $('#boton-siguiente').prop('disabled', true);
                }
                
                // Actualizar texto de paginación
                $('#pagination-text').text('Página ' + pagina);
            } catch (error) {
                console.error("Error al procesar la respuesta JSON:", error);
                swal("¡Error!", "Ha ocurrido un error al cargar las cafeterías", "error");
            }
            cargaSistema(false);
        },
        error: function () {
            swal("¡Error!", "No se pudo comunicar con el servidor", "error");
        }
    });
}


function CargarVerCafeteria() {

    let id_cafeteria = $("#id_cafeteria").attr("idCafeteria");

    var datos = new FormData();

    datos.append("cargar_datos", true);
    datos.append("id", id_cafeteria);

    $.ajax({
        url: url + 'views/ajax/ajax_cafeterias_lista.php',
        method: 'POST',
        data: datos,
        cache: false,
        contentType: false,
        processData: false,
        success: function (respuesta) {
            respuesta = JSON.parse(respuesta);
            //console.log(respuesta);
            if (respuesta == 'error') {
                swal("¡Error!", "Ha ocurrido un error", "error");
            } else {
                $("#aux_validacion").val(respuesta.data.id);
                $("#titulo_cafeteria_ver").html(respuesta.data.nombre);
                
                // Inicializar la nueva galería con el array de imágenes
                if (respuesta.imagenesArray && respuesta.imagenesArray.length > 0) {
                    initGallery(respuesta.imagenesArray);
                }
                
                $('#carousel_servicios').append(respuesta.servicios);
                    new Splide('#splide', {
                        type   : 'loop',
                        perPage: 3,
                        perMove: 1,
                        gap    : '1rem',
                        breakpoints: {
                            768: {
                                perPage: 2,
                            },
                            480: {
                                perPage: 1,
                            },
                        },
                    }).mount();
                $("#info1").html(respuesta.data.direccion || '');
                $("#info2").html(respuesta.data.telefono ? '<a id="copiar_num" href="#">' + respuesta.data.telefono + '</a>' : '');
                $("#info3").html(respuesta.data.correo ? '<a id="copiar_correo" href="#">' + respuesta.data.correo + '</a>' : '');
                $("#info4").html((respuesta.data.horario || '') + (respuesta.data.horario && respuesta.data.status ? ' &bull; ' : '') + (respuesta.data.status || ''));
                $(".ir_googlemaps").attr("latitud", respuesta.data.latitud).attr('longitud', respuesta.data.longitud);
                $("#descripcion").html(respuesta.data.descripcion || '');
                
                // Ocultar items vacíos en información general
                ocultarItemsVacios();
            }
        }
    });


}

// Función para ocultar items vacíos en información general
function ocultarItemsVacios() {
    // Verificar cada item de información
    $('#info1').closest('.info-item').toggle($('#info1').text().trim() !== '');
    $('#info2').closest('.info-item').toggle($('#info2').text().trim() !== '');
    $('#info3').closest('.info-item').toggle($('#info3').text().trim() !== '');
    $('#info4').closest('.info-item').toggle($('#info4').text().trim() !== '');
    
    // Ocultar descripción si está vacía
    const $descripcionCard = $('#descripcion').closest('.modern-card');
    if ($descripcionCard.length && $('#descripcion').text().trim() === '') {
        $descripcionCard.hide();
    } else if ($descripcionCard.length) {
        $descripcionCard.show();
    }
}

// $(document).on("click", "#abrir_filtros", function () {
//     $('#filtros_div').toggle();
//     $(this).attr("open", $(this).attr("open") === 'si' ? 'no' : 'si');
//     cargarServiciosFiltro();
// });

$(document).on("click", ".ver_img_modal", function () {
    $("#carouselModal").modal("show")
});

function cargarComentarios(pagina) {
    let cafeteria = $("#id_cafeteria").attr('idCafeteria');
    let filtro = `?comentarios=${true}&pagina=${pagina}&cafeteria=${cafeteria}`;

    $.ajax({
        url: url + 'views/ajax/ajax_cafeterias_lista.php' + filtro, 
        method: "GET",
        success: function (response) {
            response = JSON.parse(response);
            if (response.comentarios == '' || !response.comentarios || response.comentarios.length === 0) {
                // Mostrar mensaje cuando no hay reseñas
                $(".coment-ocultar").show();
                $("#mensaje-sin-resenas").show();
                $("#comentariosLista").html('');
                $("#paginacionComentarios").html('');
            } else {
                // Ocultar mensaje y mostrar comentarios
                $("#mensaje-sin-resenas").hide();
                // Llenar la lista de comentarios con los datos recibidos
                var comentariosHtml = '';
                response.comentarios.forEach(function (comentario) {
                    var fechaSinHora = comentario.fecha_alta.substring(0, 10);

                    comentariosHtml += '<li><h3>' + comentario.nombre_usuario + '</h3>';
                    comentariosHtml += '<p>' + comentario.comentario + '</p>';
                    comentariosHtml += '<p><small>' + fechaSinHora + '</small></p></li>';
                });
                $('#comentariosLista').html(comentariosHtml);
                $("#total_coment").html(response.totalComentarios);
                // Actualizar controles de paginación
                actualizarPaginacion(response.totalPaginas, pagina);
            }

        }
    });
}

function actualizarPaginacion(totalPaginas, paginaActual) {
    var paginacionHtml = '';
    for (var i = 1; i <= totalPaginas; i++) {
        paginacionHtml += '<button class="btn-paginacion ' + (i === paginaActual ? 'active' : '') + '" data-pagina="' + i + '">' + i + '</button>';
    }
    $('#paginacionComentarios').html(paginacionHtml);

    // Asignar evento a los botones de paginación
    $('.btn-paginacion').on('click', function () {
        var pagina = $(this).data('pagina');
        cargarComentarios(pagina);
    });
}
// Evento para mostrar/ocultar el área de comentarios
$(document).on("click", "#btn_agregar_comentario", function () {
    $("#comment-form-area").toggle();
    // Limpiar el textarea cuando se oculta
    if (!$("#comment-form-area").is(":visible")) {
        $("#agregar_comentario").val('');
    }
});

// Evento para cancelar y cerrar el área de comentarios
$(document).on("click", ".btn-cancelar-comentario", function () {
    $("#comment-form-area").hide();
    $("#agregar_comentario").val('');
});

$(document).on("click", "#btn_aceptar_comentario", function () {
    if ($("#agregar_comentario").val() == '') {
        swal("¡Alerta!", "Agrega un comentario.", "warning");
        return '';
    }
    var datos = new FormData();
    datos.append("registrar_comentario", true);
    datos.append('comentario', $("#agregar_comentario").val());
    datos.append('id_cafeteria', $("#id_cafeteria").attr('idCafeteria'));

    $.ajax({
        url: url + 'views/ajax/ajax_cafeterias_lista.php',
        method: 'POST',
        data: datos,
        cache: false,
        contentType: false,
        processData: false,
        beforeSend: cargaSistema(true),
        success: function (respuesta) {
            // console.log(respuesta);
            if (respuesta == 'success') {
                swal({
                    title: "¡Ok!",
                    text: "Tu comentario se registró correctamente.",
                    icon: "success",
                    button: "Aceptar",
                }).then(function () {
                    $("#agregar_comentario").val('');
                    $("#comment-form-area").hide();
                    cargarComentarios(1);
                });
            }else if(respuesta == 'sesion'){
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

$(document).on("click", ".ir_googlemaps", function () {
    const latitude = $(this).attr("latitud");
    const longitude = $(this).attr("longitud");
    console.log(latitude);

    // URL para abrir Google Maps con la ubicación específica y permitir obtener indicaciones
    const googleMapsUrl = `https://www.google.com/maps/dir/?api=1&destination=${latitude},${longitude}`;

    // Abrir la URL en una nueva pestaña
    window.open(googleMapsUrl, '_blank');
});

// Función para copiar el texto
function copyToClipboard(text, feedbackId) {
    navigator.clipboard.writeText(text).then(function () {
        // Mostrar mensaje de éxito correspondiente
        const feedbackElement = document.getElementById(feedbackId);
        feedbackElement.style.display = 'block';

        // Ocultar mensaje después de 5 segundos
        setTimeout(() => {
            feedbackElement.style.display = 'none';
        }, 5000);
    }, function (err) {
        console.error('Error al copiar el texto: ', err);
    });
}

// Asignar eventos de click a los elementos dinámicos para copiar teléfono y correo
$(document).on('click', '#copiar_num', function (event) {
    event.preventDefault(); // Evitar comportamiento predeterminado de enlace
    const telefono = $(this).text(); // Obtener el texto del número de teléfono
    copyToClipboard(telefono, 'copy-feedback-tel'); // Llamar a la función de copiar con el feedback del teléfono
});

$(document).on('click', '#copiar_correo', function (event) {
    event.preventDefault(); // Evitar comportamiento predeterminado de enlace
    const correo = $(this).text(); // Obtener el texto del correo
    copyToClipboard(correo, 'copy-feedback-email'); // Llamar a la función de copiar con el feedback del correo
});

$(document).ready(function(){
    if (moduloActual=='cafeterias_lista') {
        $(document).on("click", "#buscar_filtro", function () {
            $("#search-container").slideToggle(300);
            
            // Focus en el input cuando se muestra
            if ($("#search-container").is(":visible")) {
                setTimeout(function() {
                    $("#filtro-input").focus();
                }, 350);
            }
        });
        
        // Abrir offcanvas de filtros (diferente según tamaño de pantalla)
        $(document).on("click", "#abrir_filtros, .menu_offcanvas", function (e) {
            e.preventDefault();
            
            // Determinar qué offcanvas usar según el tamaño de pantalla
            const isMobile = window.innerWidth < 992;
            
            if (isMobile) {
                // Usar offcanvas inferior para móvil
                const offcanvasEl = document.getElementById("offcanvasFiltrosMobile");
                const offcanvas = new bootstrap.Offcanvas(offcanvasEl);
                offcanvas.show();
            } else {
                // Usar offcanvas lateral para desktop
                const offcanvasEl = document.getElementById("offcanvasFiltros");
                const offcanvas = new bootstrap.Offcanvas(offcanvasEl);
                offcanvas.show();
            }
            
            // Cargar servicios si ya está activado el checkbox
            if ($('.check_servicios').is(':checked')) {
                cargarServiciosFiltro();
            }
        });
        
        // Detectar cambios de tamaño de pantalla y cerrar el offcanvas incorrecto
        let resizeTimer;
        $(window).on('resize', function() {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function() {
                const isMobile = window.innerWidth < 992;
                
                if (isMobile) {
                    // Cerrar offcanvas desktop si está abierto
                    const desktopOffcanvas = bootstrap.Offcanvas.getInstance(document.getElementById("offcanvasFiltros"));
                    if (desktopOffcanvas) {
                        desktopOffcanvas.hide();
                    }
                } else {
                    // Cerrar offcanvas móvil si está abierto
                    const mobileOffcanvas = bootstrap.Offcanvas.getInstance(document.getElementById("offcanvasFiltrosMobile"));
                    if (mobileOffcanvas) {
                        mobileOffcanvas.hide();
                    }
                }
            }, 250);
        });
        
        $(document).on("change", ".check_servicios", function () {
            if ($(this).prop('checked')) {
                $(".div_servicios").show();
                $(".search-input-modern").hide();
                cargarServiciosFiltro();
            } else {
                $(".div_servicios").hide();
            }
        });
        
        $(document).on("submit", "#aplicar_filtros, #aplicar_filtros_offcanvas, #aplicar_filtros_mobile", function () {
            cargarListaCafeterias(1, filtro = '');
        });
        
        $(document).on("change", ".seleccionar_servicio", function () {
            cargarListaCafeterias(1, filtro = '');
        });
        
        $(document).on("input", "input[name='horario_filtro']:checked", function () {
            cargarListaCafeterias(1, '');
        });
        
        $(document).on("change",".select_ciudades_filtro",function(){
            var ciudadVal = $(this).val();
            $("#ciudades_filtro").val(ciudadVal);
            $("#ciudades_filtro_offcanvas").val(ciudadVal);
            $("#ciudades_filtro_mobile").val(ciudadVal);
        });
        
        // Función global para aplicar filtros desde el offcanvas desktop
        window.aplicarFiltros = function() {
            // Sincronizar valores antes de aplicar
            var ciudadVal = $('.select_ciudades_filtro').val();
            $("#ciudades_filtro").val(ciudadVal);
            $("#ciudades_filtro_offcanvas").val(ciudadVal);
            $("#ciudades_filtro_mobile").val(ciudadVal);
            
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
            $("#ciudades_filtro").val(ciudadVal);
            $("#ciudades_filtro_offcanvas").val(ciudadVal);
            $("#ciudades_filtro_mobile").val(ciudadVal);
            
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
            $("#ciudades_filtro").val('');
            $("#ciudades_filtro_offcanvas").val('');
            $("#ciudades_filtro_mobile").val('');
            cargarListaCafeterias(1, '');
        };
        
    }
});

$(document).ready(function () {
if (moduloActual=='cafeterias_lista' || moduloActual=='cafeterias_mapa') {
    new Splide('#splide', {
        type   : 'loop',
        perPage: 3,
        perMove: 1,
        gap    : '1rem',
        breakpoints: {
            768: {
                perPage: 2,
            },
            480: {
                perPage: 1,
            },
        },
    }).mount();
}
});