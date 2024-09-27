$(document).ready(function () {
    let paginaActual = 1; // Inicializar la página actual

    if (moduloActual == 'cafeterias_lista') {

        if ($('#titulo_cafeteria_ver').length) {
            CargarVerCafeteria();
            cargarComentarios(1);
        }

        if ($('#div_lista_cafeterias').length) {
            cargarListaCafeterias(paginaActual);
            cargarServiciosFiltro();
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

        $('#filtro-input').on('keyup', function () {
            const filtro = $(this).val();
            paginaActual = 1;
            cargarListaCafeterias(paginaActual, filtro);
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
    let ciudad = $("#select_ciudades_filtro option:selected").val();
    
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
            } catch (error) {
                console.error("Error al procesar la respuesta JSON:", error);
                swal("¡Error!", "Ha ocurrido un error al cargar las cafeterías", "error");
            }
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
                $(".carousel_imagenes").html(respuesta.imagenes);
                $("#carousel_servicios").html(respuesta.servicios);
                $("#info1").html('<b>Dirección: </b>' + respuesta.data.direccion);
                $("#info2").html('<b>Teléfono: </b><a id="copiar_num" href="#">' + respuesta.data.telefono + '</a>');
                $("#info3").html('<b>Correo: </b><a id="copiar_correo" href="#">' + respuesta.data.correo + '</a>');
                $("#info4").html('<b>Horario: </b>' + respuesta.data.horario + ' <br/> ' + respuesta.data.status);
                $(".ir_googlemaps").attr("latitud", respuesta.data.latitud).attr('longitud', respuesta.data.longitud);
            }
        }
    });


};

$(document).on("click", "#abrir_filtros", function () {
    $('#filtros_div').toggle();
    $(this).attr("open", $(this).attr("open") === 'si' ? 'no' : 'si');
});

$(document).on("click", ".ver_img_modal", function () {
    $("#carouselModal").modal("show")
});

const carousel = document.getElementById('carousel_servicios');
let isTransitioning = false;

function scrollCarousel(direction) {
    if (isTransitioning) return;
    isTransitioning = true;

    const itemWidth = document.querySelector('.custom-carousel-item').clientWidth + 10; // Incluye el margen

    // Desplazamos el carrusel
    carousel.style.transition = 'transform 0.5s ease-in-out';
    carousel.style.transform = `translateX(${direction * -itemWidth}px)`;

    setTimeout(() => {
        carousel.style.transition = 'none';

        if (direction === 1) {
            // Mueve el primer elemento al final
            carousel.appendChild(carousel.firstElementChild);
        } else {
            // Mueve el último elemento al inicio
            carousel.insertBefore(carousel.lastElementChild, carousel.firstElementChild);
        }

        // Resetea la posición del carrusel
        carousel.style.transform = 'translateX(0)';
        isTransitioning = false;
    }, 500); // Duración de la transición
}

function cargarComentarios(pagina) {
    let cafeteria = $("#id_cafeteria").attr('idCafeteria');
    let filtro = `?comentarios=${true}&pagina=${pagina}&cafeteria=${cafeteria}`;

    $.ajax({
        url: url + 'views/ajax/ajax_cafeterias_lista.php' + filtro, // Reemplaza con tu ruta de API o backend
        method: "GET",
        success: function (response) {
            response = JSON.parse(response);
            if (response.comentarios == '') {
                $(".comentarios").hide();
            } else {
                // Llenar la lista de comentarios con los datos recibidos
                var comentariosHtml = '';
                response.comentarios.forEach(function (comentario) {
                    comentariosHtml += '<li><h3>' + comentario.nombre_usuario + '</h3>';
                    comentariosHtml += '<p>' + comentario.comentario + '</p></li>';
                });
                $('#comentariosLista').html(comentariosHtml);

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
$(document).on("click", "#btn_agregar_comentario", function () {
    $(".comentario-area").toggle();
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
        success: function (respuesta) {
            // console.log(respuesta);
            if (respuesta == 'success') {
                swal({
                    title: "¡Ok!",
                    text: "Tu comentario se registro correctamente.",
                    icon: "success",
                    button: "Aceptar",
                }).then(function () {
                    window.location = "";
                });
            } else {
                swal("¡Error!", "Ha ocurrido un error.", "error");
            }
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

$(document).on("click", "#buscar_filtro", function () {
    $("#filtro-input").toggle();
    $("#div_servicios").hide();
    $(".cambiar-clase").removeClass('col-md-6').addClass('col-md-5');
    $("#check_servicios").prop('checked', false);
    $('#filtros_div').hide();
});

$(document).on("change", "#check_servicios", function () {
    if ($(this).prop('checked')) {
        $("#div_servicios").show();
        $("#filtro-input").hide();
        $(".cambiar-clase").removeClass('col-md-5').addClass('col-md-6');
    } else {
        $("#div_servicios").hide();
        $(".cambiar-clase").removeClass('col-md-6').addClass('col-md-5');
    }
});

function cargarServiciosFiltro() {
    var datos = new FormData();

    datos.append("cargar_servicios", true);

    $.ajax({
        url: url + 'views/ajax/ajax_cafeterias_lista.php',
        method: 'POST',
        data: datos,
        cache: false,
        contentType: false,
        processData: false,
        success: function (respuesta) {
            respuesta = JSON.parse(respuesta);
            // console.log(respuesta);
            $("#caja_servicios").html(respuesta);
        }
    });
}

$(document).on("submit", "#aplicar_filtros", function () {
    cargarListaCafeterias(1, filtro = '');
});