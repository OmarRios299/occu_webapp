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
                $("#titulo_cafeteria_ver").html(respuesta.nombre);
            }
        }
    });

    
};

$(document).on("click", "#abrir_filtros", function() {
    $('#filtros_div').toggle(); 
    $(this).attr("open", $(this).attr("open") === 'si' ? 'no' : 'si');
});
