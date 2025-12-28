$(document).ready(function () {
    let paginaActual = 1; // Inicializar la página actual

    if (moduloActual == 'cafeterias_lista' || (moduloActual == 'cafeterias' && $('#div_lista_cafeterias').length && !$('#tabla_cafeterias').length)) {

        // La función CargarVerCafeteria se eliminó - ahora se usa CargarVerCafeteriaMapa del archivo cafeterias_mapa.js

        if ($('#div_lista_cafeterias').length) {
            cargarListaCafeterias(paginaActual);
            
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


// Funciones comentadas eliminadas - ahora se usa CargarVerCafeteriaMapa de cafeterias_mapa.js

$(document).on("click", ".ver_img_modal", function () {
    $("#carouselModal").modal("show")
});

// Funciones de comentarios eliminadas - ahora se usan las funciones del mapa (cargarComentariosMapa, etc.)

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

// Código de Splide eliminado - ya no se usa carousel para servicios