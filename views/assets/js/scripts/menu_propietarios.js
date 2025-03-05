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
    
    datos.append("agregar_subcategoria", true);
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

$(document).on("click",".check_productos",function(){
    let estado = $(this).attr("estado");
    let id_registro = $(this).attr("idRegistro");
    var datos = new FormData();
    
    datos.append("agregar_producto", true);
    datos.append("id_producto", $(this).attr("id"));
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
    $("#id_producto").val($(this).attr('idProducto'));
    $("#modal_bebidas").modal('show');
});

$(document).on("change", ".switch_vasos", function () {
    let id_producto = $("#id_producto").val();
    let id_tamano = $(this).attr('id_tamano');
    let precio = $(this).closest('.opcion_vaso').find('.precio_vaso').val();

    let accion = $(this).prop("checked") ? "activar" : "desactivar";
    let filtro = `?switch_vasos=${accion}&id_producto=${id_producto}&id_tamano=${id_tamano}&precio=${precio}`;

    $.ajax({
        url: url + 'views/ajax/ajax_menu_propietarios.php' + filtro,
        method: 'POST',
        cache: false,
        contentType: false,
        success: function (respuesta) {
            console.log("Respuesta del servidor:", respuesta);
        },

    });
});
