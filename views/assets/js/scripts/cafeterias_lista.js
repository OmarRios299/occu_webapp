$(document).ready(function() {
    let paginaActual = 1; // Inicializar la página actual

    if (moduloActual == 'cafeterias_lista') {
        
        if ($('#titulo_cafeteria_ver').length) {
            CargarVerCafeteria();
        }

        if ($('#div_lista_cafeterias').length) {
            cargarListaCafeterias(paginaActual);
        }

        $('#boton-siguiente').on('click', function() {
            paginaActual++;
            cargarListaCafeterias(paginaActual);
        });

        $('#boton-anterior').on('click', function() {
            if (paginaActual > 1) {
                paginaActual--;
                cargarListaCafeterias(paginaActual);
            }
        });

        $('#filtro-input').on('keyup', function() {
            const filtro = $(this).val();
            paginaActual = 1;
            cargarListaCafeterias(paginaActual, filtro);
        });
    }
});

function cargarListaCafeterias(pagina, filtro = '') {
    const limite = 10;
    var datos = new FormData();
    datos.append("cargar_lista", true);
    datos.append("pagina", pagina);
    datos.append("busqueda", filtro);

    $.ajax({
        url: url + 'views/ajax/ajax_cafeterias_lista.php', 
        method: 'POST',
        data: datos,
        cache: false,
        contentType: false,
        processData: false,
        success: function(respuesta) {
            console.log("Respuesta del servidor:", respuesta); 
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
        error: function() {
            swal("¡Error!", "No se pudo comunicar con el servidor", "error");
        }
    });
}


function CargarVerCafeteria(){

    let id_cafeteria = $("#id_cafeteria").attr("idCafeteria"); 

    var datos = new FormData();
    
    datos.append("cargar_datos", true);
    datos.append("id", id_cafeteria);

    $.ajax({
        url:url+'views/ajax/ajax_cafeterias_lista.php',
        method:'POST',
        data: datos,
        cache: false,
        contentType: false,
        processData: false,
        success:function(respuesta){
            respuesta = JSON.parse(respuesta);
            console.log(respuesta);
            if (respuesta=='error') {
                swal("¡Error!", "Ha ocurrido un error", "error");
            }else{
                $("#aux_validacion").val(respuesta.data.id);
                $("#titulo_cafeteria_ver").html(respuesta.data.nombre);
                $(".carousel_imagenes").html(respuesta.imagenes);
                $("#carousel_servicios").html(respuesta.servicios);
                $("#info1").html('<b>Dirección: </b>'+respuesta.data.direccion);
                $("#info2").html('<b>Teléfono: </b>'+respuesta.data.telefono);
                $("#info3").html('<b>Correo: </b>'+respuesta.data.correo);
                $("#info4").html('<b>Horario: </b>'+respuesta.data.horario +' <br/> '+ respuesta.data.status);
            }
        }
    });

    
};

$(document).on("click", "#abrir_filtros", function() {
    $('#filtros_div').toggle(); 
    $(this).attr("open", $(this).attr("open") === 'si' ? 'no' : 'si');
});

$(document).on("click",".ver_img_modal",function(){
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
