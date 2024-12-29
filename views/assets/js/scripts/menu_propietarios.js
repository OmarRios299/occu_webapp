$(document).ready(function(){
    if ($("#div_categorias").length) {
        cargarCategorias();
    }
});

function cargarCategorias() {
    var datos = new FormData();
    
    datos.append("cargar_categorias", true);
    
    $.ajax({
        url:url+'views/ajax/ajax_menu_propietarios.php',
        method:'POST',
        data: datos,
        cache: false,
        contentType: false,
        processData: false,
        beforeSend: cargaSistema(true),
        success:function(respuesta){
            console.log(respuesta);
            $("#div_categorias").html(JSON.parse(respuesta));
        },
        complete: cargaSistema(false)
    });
}

$(document).on("click",".check_subcategoria",function(){
    let estado = $(this).attr("estado");
    let id_registro = $(this).attr("idRegistro");
    var datos = new FormData();
    
    datos.append("cambiar_estado", true);
    datos.append("id_subcategoria", $(this).attr("id"));
    (estado) ? datos.append("estado", estado): false;
    (id_registro) ? datos.append("id_registro", id_registro) : false;

    $.ajax({
        url:url+'views/ajax/ajax_menu_propietarios.php',
        method:'POST',
        data: datos,
        cache: false,
        contentType: false,
        processData: false,
        success:function(respuesta){
            console.log(respuesta);
        },
    });
});

$(document).on("click",".btn_editar_tamanos",function(){
    $("#modal_bebidas").modal('show');
});