$(document).ready(function(){

	/* VARIABLE GLOBAL URL */
	
	url = $(".url").val();
	
	/* End of VARIABLE GLOBAL URL */

	/* VARIABLE GLOBAL MODULO ACTUAL */
	
	moduloActual = $(".moduloActual").val();
	
	/* End of VARIABLE GLOBAL MODULO ACTUAL */

	/* ACTIVAR INPUTS AL CARGAR DOM */
	
	$('.input-carga').prop("disabled",false);
	
	/* End of ACTIVAR INPUTS AL CARGAR DOM */

	/* INICIALIZAR DATA TABLE */
	
	$('.dataTable').DataTable({
	    dom: 'Bfrtip',

	    language: { url: url + "views/assets/plugins/DataTables/Spanish.json" },
	    responsive: true,
	    ordering: true,
        pagingType: "simple" // Tipo de paginación: simple, simple_numbers, full, full_numberss
	});


	
	/* End of INICIALIZAR DATA TABLE */

	/* SELECT EDITABLE */
	
	$('.selectEditable').editableSelect();
	
	/* End of SELECT EDITABLE */

	/* SELECT 2 */
	
	$('.select2').select2();
	
	/* End of SELECT 2 */

	/* APLICAR CLASES AL ENLACE DEL MODULO ACTUAL */
	
    $(function(){
        // 1) Ruta actual sin slash final
        const currentPath = window.location.pathname.replace(/\/$/, "");

        // 2) Seleccionamos TODOS los links que pueden quedar “active”:
        //    - desktop (.nav-btn)
        //    - dropdown (.dropdown-item)
        //    - móvil bottom (.mobile-bottom-nav .nav-item)
        $('a.nav-btn, a.dropdown-item, .mobile-bottom-nav .nav-item').each(function(){
            const linkPath = this.pathname.replace(/\/$/, "");

            if (linkPath === currentPath) {
            $(this).addClass('active');

            // Si está dentro de un dropdown, lo abrimos y marcamos el toggle
            const $drop = $(this).closest('.dropdown');
            if ($drop.length) {
                $drop.addClass('show');
                $drop.children('.dropdown-toggle').addClass('active');
            }
            }
        });
    });


	/* End of APLICAR CLASES AL ENLACE DEL MODULO ACTUAL */

	$('.btn').each(function(){
        // Captura el color de fondo actual
        var originalColor = $(this).css('background-color');
        
        // Aplica el color de fondo capturado al estado de hover
        $(this).hover(
            function() {
                $(this).css('background-color', originalColor);
            },
            function() {
                $(this).css('background-color', originalColor);
            }
        );
    });
	

    ocultar_marcadores = [ 
        {
            "featureType": "poi",
            "elementType": "all",
            "stylers": [
                {
                    "visibility": "off"
                }
            ]
        },
        {
            "featureType": "poi.business",
            "elementType": "labels",
            "stylers": [
                {
                    "visibility": "off"
                }
            ]
        },
        {
            "featureType": "poi.medical",
            "elementType": "labels",
            "stylers": [
                {
                    "visibility": "off"
                }
            ]
        },
        {
            "featureType": "poi.place_of_worship",
            "elementType": "labels",
            "stylers": [
                {
                    "visibility": "off"
                }
            ]
        },
        {
            "featureType": "poi.school",
            "elementType": "labels",
            "stylers": [
                {
                    "visibility": "off"
                }
            ]
        },
        {
            "featureType": "poi.sports_complex",
            "elementType": "labels",
            "stylers": [
                {
                    "visibility": "off"
                }
            ]
        },
        {
            "featureType": "transit",
            "elementType": "all",
            "stylers": [
                {
                    "visibility": "off"
                }
            ]
        }
    ];

    backgroundColor = [
        'rgba(255, 99, 132, 0.2)',
        'rgba(54, 162, 235, 0.2)',
        'rgba(255, 206, 86, 0.2)',
        'rgba(75, 192, 192, 0.2)',
        'rgba(153, 102, 255, 0.2)',
        'rgba(255, 159, 64, 0.2)'
    ];
    borderColor = [
        'rgba(255, 99, 132, 1)',
        'rgba(54, 162, 235, 1)',
        'rgba(255, 206, 86, 1)',
        'rgba(75, 192, 192, 1)',
        'rgba(153, 102, 255, 1)',
        'rgba(255, 159, 64, 1)'
    ];
});

/* LOADING */

function loading(estado){

	if(estado){

		$("#body").LoadingOverlay("show", {

            image: url+"views/assets/img/loading.gif",

            imageAnimation: ""

        });

	}else{

		$("#body").LoadingOverlay("hide");
	}

}

/* End of LOADING */

/* DESACTIVAR INPUT */

function desactivarInput(estado) {
    $('.submit-carga').prop("disabled", estado);
}

/* End of DESACTIVAR INPUT */

function cargaSistema(estado) {
	loading(estado);
	desactivarInput(estado);
}

/* CAMBIO DE ESTADO EN UN ELEMENTO SWITCH */

$(document).on('change', '.cambioEstado', function(){

	let estado = $(this).is(":checked") ? 0 : 1; 
    let tabla = $(this).attr('tabla');  
    let idRegistro = $(this).attr('idRegistro');

    let datos = new FormData();

    datos.append("estadoSwitch", estado);
    datos.append("tablaSwitch", tabla);
    datos.append("idSwitch", idRegistro);

    $.ajax({
        url:url+"views/ajax/ajax_general.php",
        method:"POST",
        data: datos,
        cache: false,
        contentType: false,
        processData: false,
        success:function(respuesta){

			if(respuesta === "session_expired"){

				sesionExpirada();

			}
        	
        }
    });
});

/* End of CAMBIO DE ESTADO EN UN ELEMENTO SWITCH */

/* ELIMINAR REGISTRO */

$(document).on('click', '.eliminarRegistro', function(){

	let idRegistro = $(this).attr('idRegistro');
	let tabla = $(this).attr('tabla');

	swal({
		 title: '¡Cuidado!',
		 text: '¿Estás seguro que deseas eliminar el registro seleccionado?',
		 icon: 'warning',
		 buttons: true,
		 dangerMode: true,
	   })
	   .then((willDelete) => {
		 if (willDelete) {

			let datos = new FormData();

			datos.append("tablaEliminar", tabla);
			datos.append("idEliminar", idRegistro);

			$.ajax({
				url:url+"views/ajax/ajax_general.php",
				method:"POST",
				data: datos,
				cache: false,
				contentType: false,
				processData: false,
				success:function(respuesta){

					if(respuesta === "session_expired"){

						sesionExpirada();
		
					}else{

						swal({
							title: '¡Bien!',
							text: '¡El registro se eliminó exitosamente!',
							icon: 'success',
							button: 'Aceptar',
						 }).then(function() {
	 
							 location.reload();
	 
						 });

					}

				}
			});
	
		 }else{
	
		 }
	});

});

/* End of ELIMINAR REGISTRO */

/* ALERTA DE SESION EXPIRADA */

function sesionExpirada() {

	swal({
	   title: '¡La sesión ha expirado!',
	   text: '¡Inicia sesión nuevamente!',
	   icon: 'warning',
	   button: 'Aceptar',
	}).then(function() {
	   location.reload();
	});
	
}

/* End of ALERTA DE SESION EXPIRADA */

/* ALERTA PARA INSERT */

function alertaInsert() {

	swal({
	   title: '¡Bien!',
	   text: '¡Se realizó el registro exitosamente!',
	   icon: 'success',
	   button: 'Aceptar',
	}).then(function() {
	   location.reload();
	});
	
}

/* End of ALERTA PARA INSERT */

/* ALERTA PARA UPDATE */

function alertaUpdate() {

	swal({
	   title: '¡Bien!',
	   text: '¡Se modificó el registro exitosamente!',
	   icon: 'success',
	   button: 'Aceptar',
	}).then(function() {
	   location.reload();
	});
	
}

/* End of ALERTA PARA UPDATE */

/* VALIDAR PDF E IMAGEN */

$(document).on('change', '.validarPdfImagen', function(){

	if($(this)[0].files[0].type != "application/pdf" && $(this)[0].files[0].type != "image/jpeg" && $(this)[0].files[0].type != "image/png"){

		$(this).val("");

		swal("¡Error!", "¡Solo se permiten archivos en formato PDF, JPG y PNG!", "error");

    }

});

/* End of VALIDAR PDF E IMAGEN */

/* VALIDAR IMAGENES */

$(document).on('change', '.validarImagen', function(){

	let tipoArchivo = $(this)[0].files[0].type;
	let tiposPermitidos = [
		"image/jpeg",
		"image/jpg", 
		"image/png",
		"image/webp",
		"image/gif",
		"image/bmp",
		"image/svg+xml"
	];
	
	if(tiposPermitidos.indexOf(tipoArchivo) === -1){
		$(this).val("");
		swal("¡Error!", "¡Solo se permiten archivos en formato JPG, PNG, WEBP, GIF, BMP o SVG!", "error");
	}

});

/* End of VALIDAR IMAGENES */

/* VALIDAR PDF */

$(document).on('change', '.validarPdf', function(){

	if($(this)[0].files[0].type != "application/pdf"){

		$(this).val("");

		swal("¡Error!", "¡Solo se permiten archivos en formato PDF!", "error");

    }

});

/* End of VALIDAR PDF */

/*====================================================
=            VALIDAR IMAGEN PREVISUALIZAR            =
====================================================*/

$(document).on('change', '.imagenPrevisualizar', function(){

	let imagen = this.files[0];
	let input = $(this);

	if((imagen.type == "image/jpeg" || imagen.type == "image/png" || imagen.type == "image/svg+xml" || imagen.type == "image/svg")){

		let datosImagen = new FileReader;
        datosImagen.readAsDataURL(imagen);

        $(datosImagen).on("load", function(event){

            let rutaImagen = event.target.result;

			$("#imagen_previsualizar").attr("src", rutaImagen);

        });

	}

});

/*=====  End of VALIDAR IMAGEN PREVISUALIZAR  ======*/

/* FUNCION OBTENER FECHA ACTUAL */

function obtenerFechaActual(){
    let fecha = new Date();
    let dd = fecha.getDate();
    let mm = fecha.getMonth()+1;
    let yyyy = fecha.getFullYear();
    
    dd = agregarCero(dd);
    mm = agregarCero(mm);

    return yyyy+'-'+mm+'-'+dd;

}

function agregarCero(i) {
    if (i < 10) {
        i = '0' + i;
    }
    return i;
}

/* End of FUNCION OBTENER FECHA ACTUAL */

/* VALIDAR 0 */

$(document).on("change", ".validar0", function () {

	let valor = Number($(this).val());
  
	if(valor <= 0){
	  $(this).addClass("is-invalid");
	  $(this).next().show();
	}else{
	  $(this).removeClass("is-invalid");
	  $(this).next().hide();
	}
  
});

/* End of VALIDAR 0 */

/* FORMATO DINERO */

const FORMATTER = new Intl.NumberFormat('en-US', {
  style: 'currency',
  currency: 'USD',
  minimumFractionDigits: 2
});

/* End of FORMATO DINERO */

/*=====================================
=            VALIDAR CAMPO            =
=====================================*/

$(document).on("change", ".validarCampo", function () {

	let input = $(this);
    let nombre = $(this).val();
    let columna = $(this).attr("columna");
    let tabla = $(this).attr("tabla");
    let mensaje = $(this).attr("mensaje");

	let datos = new FormData();

    datos.append("valorCampo", nombre);
    datos.append("columnaCampo", columna);
    datos.append("tablaCampo", tabla);

    $.ajax({
        url:url+"views/ajax/ajax_general.php",
        method:"POST",
        data: datos,
        cache: false,
        contentType: false,
        processData: false,
        success:function(respuesta){

            if(respuesta === "session_expired"){

                sesionExpirada();

            }else{

                if(respuesta == 0){
                
                    $(input).addClass("is-invalid");
                    $(input).next().html(mensaje);
                    $(input).next().show();
    
                }else{
                    
                    $(input).removeClass("is-invalid");
                    $(input).next().hide();

                }

            }

        }
    });

});

/*=====  End of VALIDAR CAMPO  ======*/



/*=====================================
=            VALIDAR CAMPO PRIMARIO        =
=====================================*/

$(document).on("change", ".validarCampoPrimario", function () {

	let input = $(this);
    let nombre = $(this).val();
    let columna = $(this).attr("columna");
	let valorCampoSecundario = $(this).attr("valorCampoSecundario");
	let columnaSecundario = $(this).attr("columnaSecundario");
    let tabla = $(this).attr("tabla");
    let mensaje = $(this).attr("mensaje");

	let datos = new FormData();

    datos.append("valorCampoPrimario", nombre);
    datos.append("columnaCampoPrimario", columna);
	datos.append("valorCampoSecundario", valorCampoSecundario);
    datos.append("columnaCampoSecundario", columnaSecundario);
    datos.append("tablaCampo", tabla);

    $.ajax({
        url:url+"views/ajax/ajax_general.php",
        method:"POST",
        data: datos,
        cache: false,
        contentType: false,
        processData: false,
        success:function(respuesta){

            if(respuesta === "session_expired"){

                sesionExpirada();

            }else{

                if(respuesta == 0){
                
                    $(input).addClass("is-invalid");
                    $(input).next().html(mensaje);
                    $(input).next().show();
    
                }else{
                    
                    $(input).removeClass("is-invalid");
                    $(input).next().hide();

                }

            }

        }
    });

});

/*=====  End of VALIDAR CAMPO PRIMARIO======*/

/*============================================
=            VALIDAR CAMPO EDITAR            =
============================================*/

$(document).on("change", ".validarCampoEditar", function () {

	let input = $(this);
	let nombre = $(this).val();
	let columna = $(this).attr("columna");
	let tabla = $(this).attr("tabla");
	let mensaje = $(this).attr("mensaje");
    let idRegistro = $(this).attr("idRegistro");

    let datos = new FormData();

    datos.append("valorCampoEditar", nombre);
    datos.append("columnaCampoEditar", columna);
    datos.append("tablaCampoEditar", tabla);
    datos.append("idRegistro", idRegistro);

    $.ajax({
        url:url+"views/ajax/ajax_general.php",
        method:"POST",
        data: datos,
        cache: false,
        contentType: false,
        processData: false,
        success:function(respuesta){

            if(respuesta === "session_expired"){

                sesionExpirada();

            }else{

                if(respuesta == 0){
                
                    $(input).addClass("is-invalid");
                    $(input).next().html(mensaje);
                    $(input).next().show();
    
                }else{
                    
                    $(input).removeClass("is-invalid");
                    $(input).next().hide();
                }

            }

        }
    });

});

/*=====  End of VALIDAR CAMPO EDITAR  ======*/


/*=====================================
=            VALIDAR CAMPO PRIMARIO EDITAR      =
=====================================*/

$(document).on("change", ".validarCampoPrimarioEditar", function () {

	let input = $(this);
    let nombre = $(this).val();
    let columna = $(this).attr("columna");
	let valorCampoSecundario = $(this).attr("valorCampoSecundario");
	let columnaSecundario = $(this).attr("columnaSecundario");
    let tabla = $(this).attr("tabla");
    let mensaje = $(this).attr("mensaje");
	let idRegistro=$(this).attr("idRegistro");

	let datos = new FormData();

    datos.append("valorCampoPrimarioEditar", nombre);
    datos.append("columnaCampoPrimarioEditar", columna);
	datos.append("valorCampoSecundarioEditar", valorCampoSecundario);
    datos.append("columnaCampoSecundarioEditar", columnaSecundario);
    datos.append("tablaCampoEditar", tabla);
	datos.append("idRegistro", idRegistro);

    $.ajax({
        url:url+"views/ajax/ajax_general.php",
        method:"POST",
        data: datos,
        cache: false,
        contentType: false,
        processData: false,
        success:function(respuesta){

            if(respuesta === "session_expired"){

                sesionExpirada();

            }else{

                if(respuesta == 0){
                
                    $(input).addClass("is-invalid");
                    $(input).next().html(mensaje);
                    $(input).next().show();
    
                }else{
                    
                    $(input).removeClass("is-invalid");
                    $(input).next().hide();

                }

            }

        }
    });

});

/*=====  End of VALIDAR CAMPO PRIMARIO EDITAR======*/




/* ABRIR CAMARA */

$(document).on('click', '#abrirCamara', function(){
    $("#div_abrir_camara").hide();
    $("#div_capturar_foto").show();
    Webcam.reset();
    Webcam.attach('#contenedorCamara');
});

/* End of ABRIR CAMARA */

/* REALIZAR CAPTURA */

$(document).on('click', '#realizarCaptura', function(){
    Webcam.snap(function(data_uri){
        $("#imagenCamara").attr("src", data_uri).val("si").show();
    });
});

/* REALIZAR CAPTURA */


$(document).on("click","#filtro_busqueda",function(){
    $(".filtro_busqueda").toggle();
});

$(document).on("change", ".select_pais", function() {
    let pais = $('.select_pais option:selected').val();    
    let opcion_todos = $(".select_pais").attr("opcion_todos");
    let filtro = `?select_pais=${true}&pais=${pais}&todos=${opcion_todos}`;

    if ($('.select_estado').data('select2')) {
        $('.select_estado').select2('destroy').empty();
    }
    if ($('.select_ciudad').data('select2')) {
        $('.select_ciudad').select2('destroy').empty();
    }

    // Llamada AJAX para obtener estados y ciudades iniciales
    $.ajax({
        url: url + 'views/ajax/ajax_general.php' + filtro,
        type: "post",
        dataType: 'json',
        success: function(response) {
            // Llenar el select de estados
            let estados = response.estados;
            $(".select_estado").select2({
                data: $.map(estados, function(item) {
                    return {
                        id: item.id, 
                        text: item.nombre
                    };
                })
            });

            // Llenar el select de ciudades
            let ciudades = response.ciudades;
            $(".select_ciudad").select2({
                data: $.map(ciudades, function(item) {
                    return {
                        id: item.id, 
                        text: item.nombre, 
                        coordenadas: item.coordenadas,
                        pais: item.pais
                    };
                })
            });
        }
    });
});

// Filtrar las ciudades según el estado seleccionado
$(document).on("change", ".select_estado", function() {
    select_ciudades();
});

function select_ciudades() {
    let estado = $(".select_estado option:selected").val();
    let pais = '';
    let opcion_todos = $(".select_estado").attr("opcion_todos");

    if ($('.select_ciudad').data('select2')) {
        $('.select_ciudad').select2('destroy').empty();
    }

    let filtro = `?select_estado=${true}&estado=${estado}&pais=${pais}&todos=${opcion_todos}`;
    
    // Llamada AJAX para obtener las ciudades basadas en el estado seleccionado
    $.ajax({
        url: url + 'views/ajax/ajax_general.php' + filtro,
        type: "post",
        dataType: 'json',
        success: function(response) {
            // Llenar el select de ciudades
            let ciudades = response.ciudades;
            $(".select_ciudad").select2({
                data: $.map(ciudades, function(item) {
                    return {
                        id: item.id, 
                        text: item.nombre, 
                        coordenadas: item.coordenadas,
                        pais: item.pais
                    };
                })
            });
        }
    });
}

$(document).on("click", "#limpiar_filtros", function() {
    $(".filtro").val("").trigger('change');
    $('input[name="estatus"]').prop('checked', false); 
    $('input[name="estatus"][value="Todos"]').prop('checked', true); 
});

function cargarServiciosFiltro() {
    // Esta funcion se utiliza en cafeterias_mapa
    var datos = new FormData();

    datos.append("cargar_servicios", true);

    $.ajax({
        url: url + 'views/ajax/ajax_cafeterias_lista.php',
        method: 'POST',
        data: datos,
        cache: false,
        contentType: false,
        processData: false,
        beforeSend: cargaSistema(true),
        success: function (respuesta) {
            respuesta = JSON.parse(respuesta);
            // console.log(respuesta);
            $(".caja_servicios").html(respuesta);
            cargaSistema(false);
        }
    });
}

// Filtrar las subcategorias según la categoria seleccionada
$(document).on("change", ".select_categoria", function() {
    select_subcategoria();
});

function select_subcategoria() {
    let categoria = $(".select_categoria option:selected").val();

    if ($('.select_subcategoria').data('select2')) {
        $('.select_subcategoria').select2('destroy').empty();
    }

    let filtro = `?select_subcategoria=${true}&categoria=${categoria}`;
    
    $.ajax({
        url: url + 'views/ajax/ajax_general.php' + filtro,
        type: "post",
        dataType: 'json',
        success: function(response) {
            let subcategorias = response.subcategorias;
            $(".select_subcategoria").select2({
                data: $.map(subcategorias, function(item) {
                    return {
                        id: item.id, 
                        text: item.nombre,
                    };
                })
            });
        }
    });
}