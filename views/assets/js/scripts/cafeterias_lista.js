$(document).ready(function(){

    if (moduloActual=='cafeterias_lista') {
        CargarCiudades();
        if ($('#titulo_cafeteria_ver').length) {
            CargarVerCafeteria();
        }
    }
    if ($('#div_lista_cafeterias').length) {
        cargarListaCafeterias();
    }
});

function cargarListaCafeterias(){

    var datos = new FormData();
    datos.append("cargar_lista", true);
    
    $.ajax({
        url:url+'views/ajax/ajax_cafeterias_lista.php',
        method:'POST',
        data: datos,
        cache: false,
        contentType: false,
        processData: false,
        success:function(respuesta){
           // respuesta = JSON.parse(respuesta);
            //console.log(respuesta);
            $('#div_lista_cafeterias').html(respuesta);
    
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
                $("#info1").html('<b>Dirección: </b>'+respuesta.data.direccion);
                $("#info2").html('<b>Teléfono: </b>'+respuesta.data.telefono);
                $("#info3").html('<b>Correo: </b>'+respuesta.data.correo);
                $("#info4").html('<b>Horario: </b>'+respuesta.data.horario_apertura +' - '+ respuesta.data.horario_cierre);
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
