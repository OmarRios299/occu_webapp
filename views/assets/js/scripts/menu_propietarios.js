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

$(document).on("click", ".btn_editar_tamanos", function () {
    $(".precio_vaso").val(0).prop("disabled",false);
    $(".switch_vasos").prop("checked", false);

    let id_producto = $(this).attr('idProducto');
    $("#id_producto").val(id_producto);

    let filtro = `?buscarProducto=${true}&id_producto=${id_producto}`;

    $.ajax({
        url: url + 'views/ajax/ajax_menu_propietarios.php' + filtro,
        method: 'POST',
        cache: false,
        contentType: false,
        success: function (respuesta) {
            respuesta = JSON.parse(respuesta);

            // Iterar sobre la respuesta (que es un array de objetos)
            respuesta.forEach(producto => {
                let tamanoSelector = `#tamano_${producto.id_tamano}`;
                let switchElement = $(tamanoSelector).closest('.opcion_vaso').find('.switch_vasos');
                let precioInput = $(tamanoSelector).closest('.opcion_vaso').find('.precio_vaso');

                // Actualizar el estado del switch
                let isChecked = producto.estado == 1;
                switchElement.prop("checked", isChecked);

                // Actualizar el precio
                precioInput.val(producto.precio !== "undefined" ? producto.precio : "");

                // Habilitar o deshabilitar el input del precio según el estado del switch
                precioInput.prop("disabled", isChecked);
            });
        }
    });

    $("#modal_bebidas").modal('show');
});


$(document).on("change", ".switch_vasos", function () {
    // Esto para deshabilitar o habilitar el input de precio al mover el switsh del tamano
    let precioInput = $(this).closest('.opcion_vaso').find('.precio_vaso');
    precioInput.prop("disabled", $(this).prop("checked"));

    // Activar o desactivar vaso
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
