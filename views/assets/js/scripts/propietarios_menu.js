$(document).ready(function(){
    if ($("#div_categorias").length) {
        cargarCategorias();
    }
});

function cargarCategorias() {
    var datos = new FormData();
    
    datos.append("cargar_categorias", true);
    
    $.ajax({
        url:url+'views/ajax/ajax_propietarios_menu.php',
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
        url:url+'views/ajax/ajax_propietarios_menu.php',
        method:'POST',
        data: datos,
        cache: false,
        contentType: false,
        processData: false,
        success:function(respuesta){
            console.log(respuesta);
            cargarCategorias();
        },
    });
});

$(document).on("click",".check_productos",function(){
    let estado = $(this).attr("estado");
    let id_registro = $(this).attr("idRegistro");
    let cafeteria = $("#cafeteria").val();
    var datos = new FormData();
    
    datos.append("agregar_producto", true);
    datos.append("id_producto", $(this).attr("id"));
    (estado) ? datos.append("estado", estado): false;
    (id_registro) ? datos.append("id_registro", id_registro) : false;
    datos.append("cafeteria", (cafeteria) ? cafeteria : false);
    datos.append("campo", $(this).attr("campo"));

    $.ajax({
        url:url+'views/ajax/ajax_propietarios_menu.php',
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
    if ($("#input_alerta_menu").length) {
        swal("¡Alerta!", `Para modificar los tamaños ve a "Mi menú".`, "warning");
        return;
    }
    $(".precio_vaso").val(0).prop("disabled",false);
    $(".switch_vasos").prop("checked", false);

    let id_producto = $(this).attr('idProducto');
    let cafeteria = $("#cafeteria").val();
    $("#id_producto").val(id_producto);

    let filtro = `?buscarProducto=${true}&id_producto=${id_producto}&cafeteria=${(cafeteria) ? cafeteria : false}`;

    $.ajax({
        url: url + 'views/ajax/ajax_propietarios_menu.php' + filtro,
        method: 'POST',
        cache: false,
        contentType: false,
        beforeSend: cargaSistema(true),
        success: function (respuesta) {
            respuesta = JSON.parse(respuesta);

            // Iterar sobre la respuesta (que es un array de objetos)
            respuesta.producto.forEach(producto => {
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
            cargaSistema(false);
        }
    });

    $("#modal_bebidas").modal('show');
});


$(document).on("change", ".switch_vasos", function () {
    // Esto para deshabilitar o habilitar el input de precio al mover el switsh del tamano
    let precioInput = $(this).closest('.opcion_vaso').find('.precio_vaso');
    precioInput.prop("disabled", $(this).prop("checked"));
    let cafeteria = $("#cafeteria").val();

    // Activar o desactivar vaso
    let id_producto = $("#id_producto").val();
    let id_tamano = $(this).attr('id_tamano');
    let precio = $(this).closest('.opcion_vaso').find('.precio_vaso').val();
    

    let accion = $(this).prop("checked") ? "activar" : "desactivar";
    let filtro = `?switch_vasos=${accion}&id_producto=${id_producto}&id_tamano=${id_tamano}&precio=${precio}&cafeteria=${(cafeteria) ? cafeteria : false}`;

    $.ajax({
        url: url + 'views/ajax/ajax_propietarios_menu.php' + filtro,
        method: 'POST',
        cache: false,
        contentType: false,
        success: function (respuesta) {
            console.log("Respuesta del servidor:", respuesta);
        },

    });
});

$(document).on("click","#actualizarMenu, #actualizarPrecios",function(){
    let actualizar = $(this).attr("actualizar");
    let sucursal = $(this).attr("idSucursal")??false;

    let filtro = `?actualizarMenu=${actualizar}&sucursal=${sucursal}`;

    $.ajax({
        url: url + 'views/ajax/ajax_propietarios_menu.php' + filtro,
        method: 'POST',
        cache: false,
        contentType: false,
        beforeSend: cargaSistema(true),
        success: function (respuesta) {
            console.log("Respuesta del servidor:", respuesta);
            cargaSistema(false);
        },

    });
});

$(document).on("submit",".form_agregar_subcategoria_extra",function(){
    var datos = new FormData();
    datos.append("agregar_subcategoria_extra", true);
    datos.append('nombre',$("#nombre_subcategoria").val());
    datos.append("id_categoria", $("#select_categoria").val());
    
    $.ajax({
        url:url+'views/ajax/ajax_propietarios_menu.php',
        method:'POST',
        data: datos,
        cache: false,
        contentType: false,
        processData: false,
        beforeSend: cargaSistema(true),
        success:function(respuesta){
            console.log(respuesta);
            if (respuesta=='error_validacion_nombre'){
                swal("¡Error!", "La subcategoría ya está registrada", "error");
            }else if (respuesta!='success') {
                swal("¡Error!", "Ha ocurrido un error", "error");
            } else {
                alertaInsert();
            }
            cargaSistema(false);
        }
    });
});

$(document).on("click","#btn_agregar_subcategoria_extra",function(){
    $(".input_subcategorias").val('');
    $("#nombre_subcategoria").removeAttr('validarCampoEditar').attr('validarCampo');
    $("#modal_agregar_subcategorias_extra").modal('show');
    $("#modalLabel").html('Agregando subcategoría');
});

$(document).on("submit",".form_add_producto_extra",function(){
    var datos = new FormData();
    
    datos.append("agregar_producto_extra", true);
    datos.append('nombre',$("#nombre_producto").val());
    datos.append("id_subcategoria", $("#select_subcategoria option:selected").val());
    datos.append("campo", $("#select_subcategoria option:selected").attr('campo'));
    datos.append('imagen_producto',$("#imagen_producto")[0].files[0]);

    $.ajax({
        url:url+'views/ajax/ajax_propietarios_menu.php',
        method:'POST',
        data: datos,
        cache: false,
        contentType: false,
        processData: false,
        beforeSend: cargaSistema(true),
        success:function(respuesta){
            console.log(respuesta);
            if (respuesta=='error') {
                swal("¡Error!", "Ha ocurrido un error", "error");
            }else if(respuesta=='error_validacion_nombre'){
                swal("¡Error!", "El nombre de categoría ya esta registrado", "error");
            } else {
                (id_producto!='')? alertaUpdate() : alertaInsert();
            }
            cargaSistema(false);
        }
    });
});


/* ----- FUNCIONAMIENTO DEL MODAL DE SELECCION DE INGREDIENTES ----- 
---------------------------------------------------------------------*/

$(document).on("click", ".btn_editar_ingre", function () {
    if ($("#input_alerta_menu").length) {
        swal("¡Alerta!", `Para modificar los ingredientes ve a "Mi menú".`, "warning");
        return;
    }

    let id_producto = $(this).attr('idProducto');
    let cafeteria = $("#cafeteria").val();
    $("#id_producto_ingre").val(id_producto);

    cargarModalIngredientes(id_producto,cafeteria);

});

function cargarModalIngredientes(id_producto,cafeteria){
    let filtro = `?buscarProducto=${true}&id_producto=${id_producto}&cafeteria=${(cafeteria) ? cafeteria : false}`;

    $.ajax({
        url: url + 'views/ajax/ajax_propietarios_menu.php' + filtro,
        method: 'POST',
        cache: false,
        contentType: false,
        beforeSend: cargaSistema(true),
        success: function (respuesta) {
            respuesta = JSON.parse(respuesta);

           $("#cont_ingre").html(respuesta.ingredientes);
            cargaSistema(false);
        }
    });

    $("#modal_ingredientes").modal('show');
}

$(document).on("click",".check_ingredientes",function(){
    let estado = $(this).attr("estado");
    let id_registro = $(this).attr("idRegistro");
    let cafeteria = $("#cafeteria").val();
    let id_producto = $("#id_producto_ingre").val();
    var datos = new FormData();
    
    datos.append("agregar_ingrediente", true);
    datos.append("id_producto", id_producto);
    datos.append("id_ingrediente", $(this).attr("id"));
    (estado) ? datos.append("estado", estado): false;
    (cafeteria) ? datos.append("cafeteria", cafeteria): false;
    (id_registro) ? datos.append("id_registro", id_registro) : false;

    $.ajax({
        url:url+'views/ajax/ajax_propietarios_menu.php',
        method:'POST',
        data: datos,
        cache: false,
        contentType: false,
        processData: false,
        success:function(respuesta){
            console.log(respuesta);
            cargarModalIngredientes(id_producto,cafeteria);
        },
    });
});

$(document).on("change", ".ingred_cantidad_gratis, .ingred_precio_extra", function () {

    var input = $(this);

    var contenedor = input.closest(".divItemIngrediente");

    var idRegistro = contenedor.find(".ingredItem").attr("idRegistroItem");
    var cantidad = contenedor.find(".ingred_cantidad_gratis").val();
    var precio = contenedor.find(".ingred_precio_extra").val();

    var datos = new FormData();
    datos.append("registroPrecioExtra", true);
    datos.append("idRegistro", idRegistro);
    datos.append("cantidad", cantidad);
    datos.append("precio", precio);

    $.ajax({
        url: url + 'views/ajax/ajax_propietarios_menu.php',
        method: 'POST',
        data: datos,
        cache: false,
        contentType: false,
        processData: false,
        success: function (respuesta) {
            console.log(respuesta);
        }
    });
});


$(document).on("change", ".checkIng_precio_extra", function () {

    var input = $(this);
    var contenedor = input.closest(".divItemIngrediente");

    var idRegistro = input.attr("idRegistro");

    var costoExtra = contenedor.find("input[costo_extra]").attr("costo_extra") || "0";

    var datos = new FormData();
    datos.append("checkPrecioExtra", true);
    datos.append("idRegistro", idRegistro);
    datos.append("costo_extra", costoExtra);

    $.ajax({
        url: url + 'views/ajax/ajax_propietarios_menu.php',
        method: 'POST',
        data: datos,
        cache: false,
        contentType: false,
        processData: false,
        success: function (respuesta) {
            console.log(respuesta);
            contenedor.find(".divExtra").toggle();
        }
    });
});


$(document).on("click","#btnCerrarModalIngExtra",function(){
    $("#modal_ingredientes").modal('show');
});

$(document).on("submit",".form_add_ing_extra",function(){
    
    var datos = new FormData();
    datos.append("agregarPropietarioIngrediente", true);
    datos.append("nombre", $("#input_nombre_ingre").val());
    datos.append("id_ingrediente_categoria", $("#select_ing_categoria").val());

    $.ajax({
        url: url + 'views/ajax/ajax_propietarios_menu.php',
        method: 'POST',
        data: datos,
        cache: false,
        contentType: false,
        processData: false,
        beforeSend: cargaSistema(true),
        success: function (respuesta) {
            console.log(respuesta);
            cargaSistema(false);
            $("#addIngredienteExtraModal").modal('hide');
            $(".input_ingre").val('');
            cargarModalIngredientes();
        }
    });
});
