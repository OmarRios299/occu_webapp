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

$(document).on("click",".check_productos",function(e){
    let checkbox = $(this);
    let estado = checkbox.attr("estado");
    let id_registro = checkbox.attr("idRegistro");
    let cafeteria = $("#cafeteria").val();
    
    // Guardar el estado original ANTES del cambio
    // Si el atributo estado es 1, el checkbox está marcado (activo)
    // Si el atributo estado es 0, el checkbox está desmarcado (inactivo)
    let estadoOriginal = (estado == 1); // Estado original basado en el atributo
    let nuevoEstadoVisual = !estadoOriginal; // El nuevo estado visual que queremos
    
    // Permitir que el checkbox cambie visualmente inmediatamente
    // Si hay error, lo revertiremos
    
    var datos = new FormData();
    
    datos.append("agregar_producto", true);
    datos.append("id_producto", checkbox.attr("id"));
    (estado) ? datos.append("estado", estado): false;
    (id_registro) ? datos.append("id_registro", id_registro) : false;
    datos.append("cafeteria", (cafeteria) ? cafeteria : false);
    datos.append("campo", checkbox.attr("campo"));

    $.ajax({
        url:url+'views/ajax/ajax_propietarios_menu.php',
        method:'POST',
        data: datos,
        cache: false,
        contentType: false,
        processData: false,
        success:function(respuesta){
            console.log("Respuesta del servidor:", respuesta);
            
            // Verificar si la respuesta es JSON (con información de copia)
            let respuestaObj = null;
            let esExitoso = false;
            
            try {
                respuestaObj = JSON.parse(respuesta);
                esExitoso = (respuestaObj && respuestaObj.status == 'success');
            } catch(e) {
                // No es JSON, es respuesta simple
                // 'success' y 'existe' son respuestas válidas (existe significa que ya estaba registrado)
                esExitoso = (respuesta == 'success' || respuesta == 'existe');
            }
            
            if(esExitoso){
                // Actualizar el atributo estado después del cambio exitoso
                // El controlador invierte el estado: si era 1 (activo), ahora es 0 (inactivo) y viceversa
                var nuevoEstado = (estado == 1) ? 0 : 1;
                checkbox.attr("estado", nuevoEstado);
                
                // Actualizar el estado visual del checkbox
                checkbox.prop("checked", nuevoEstadoVisual);
                
                // Si se copió información de un producto similar, mostrar notificación
                // Solo mostrar si el producto se activó (nuevoEstadoVisual es true)
                if(respuestaObj && respuestaObj.copia && respuestaObj.copia.copiado && nuevoEstadoVisual){
                    let mensaje = 'Producto activado correctamente.\n\n';
                    mensaje += 'Se copiaron automáticamente los datos del producto similar:\n';
                    mensaje += '📦 ' + respuestaObj.copia.producto_origen + '\n\n';
                    
                    let items_copiados = [];
                    if(respuestaObj.copia.ingredientes){
                        items_copiados.push('✓ Ingredientes');
                    }
                    if(respuestaObj.copia.tamanos){
                        items_copiados.push('✓ Tamaños');
                    }
                    
                    if(items_copiados.length > 0){
                        mensaje += items_copiados.join('\n');
                        mensaje += '\n\nPuedes revisar y ajustar estos datos si es necesario.';
                    }
                    
                    swal({
                        title: "¡Producto activado!",
                        text: mensaje,
                        icon: "success",
                        button: "Entendido"
                    });
                }
            } else {
                // Si hay error, revertir el estado del checkbox al original
                checkbox.prop("checked", estadoOriginal);
                swal("¡Error!", "No se pudo actualizar el estado del producto", "error");
            }
        },
        error: function(xhr, status, error){
            console.log("Error AJAX:", error);
            // Si hay error, revertir el estado del checkbox al original
            checkbox.prop("checked", estadoOriginal);
            swal("¡Error!", "Ocurrió un error al actualizar el estado del producto", "error");
        }
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

// Actualizar estado del checkbox de costo extra cuando cambia el checkbox de ingrediente
$(document).on("change", ".check_ingredientes", function(){
    var contenedor = $(this).closest(".divItemIngrediente");
    var checkCostoExtra = contenedor.find(".checkIng_precio_extra");
    var divExtra = contenedor.find(".divExtra");
    var isChecked = $(this).prop("checked");
    
    // Habilitar o deshabilitar el checkbox de costo extra según el estado del ingrediente
    if(isChecked){
        // Si se activa el ingrediente, habilitar el checkbox de costo extra
        checkCostoExtra.prop("disabled", false);
        // Si el costo extra estaba marcado antes, mostrar el div
        if(checkCostoExtra.prop("checked")){
            divExtra.show();
        }
    } else {
        // Si se desactiva el ingrediente:
        // 1. Desmarcar el checkbox de costo extra
        checkCostoExtra.prop("checked", false);
        // 2. Deshabilitar el checkbox de costo extra
        checkCostoExtra.prop("disabled", true);
        // 3. Ocultar el div de configuración de precio extra
        divExtra.hide();
    }
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
    
    // Validar que el checkbox no esté deshabilitado
    if(input.prop("disabled")){
        input.prop("checked", false);
        return;
    }
    
    // Validar que existe idRegistro y no es "No"
    if(!idRegistro || idRegistro == "No" || idRegistro == ""){
        // Si no hay registro, primero se debe activar el ingrediente
        swal("¡Atención!", "Primero debes activar el ingrediente antes de configurar el costo extra", "warning");
        input.prop("checked", false);
        return;
    }
    
    // Obtener el estado actual del atributo costo_extra (valor en BD antes del cambio)
    var costoExtraActual = input.attr("costo_extra") || "No";
    
    // Guardar el estado del checkbox antes de enviar
    var isChecked = input.prop("checked");
    
    // El controlador invierte el valor que recibe, así que enviamos el valor actual
    // Si costo_extra actual es "Si", el controlador lo cambiará a "No"
    // Si costo_extra actual es "No", el controlador lo cambiará a "Si"
    var costoExtra = costoExtraActual;

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
            if(respuesta == 'success'){
                // El controlador invierte el valor, así que actualizamos el atributo
                var nuevoCostoExtra = (costoExtraActual == "Si") ? "No" : "Si";
                input.attr("costo_extra", nuevoCostoExtra);
                
                // Mostrar u ocultar el div según el estado del checkbox
                var divExtra = contenedor.find(".divExtra");
                if(isChecked){
                    divExtra.show();
                } else {
                    divExtra.hide();
                }
            } else {
                // Si hay error, revertir el estado del checkbox
                input.prop("checked", !isChecked);
                swal("¡Error!", "No se pudo actualizar el costo extra", "error");
            }
        },
        error: function(){
            // Si hay error, revertir el estado del checkbox
            input.prop("checked", !isChecked);
            swal("¡Error!", "Ocurrió un error al actualizar el costo extra", "error");
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

/* ----- FUNCIONAMIENTO DEL MODAL DE PRECIO PARA ALIMENTOS ----- 
---------------------------------------------------------------------*/

// Abrir modal de precio para alimentos
$(document).on("click", ".btn_editar_precio_alimento", function () {
    if ($("#input_alerta_menu").length) {
        swal("¡Alerta!", `Para modificar el precio ve a "Mi menú".`, "warning");
        return;
    }

    let id_producto = $(this).attr('idProducto');
    let nombre_producto = $(this).attr('nombre');
    let cafeteria = $("#cafeteria").val();
    
    $("#id_producto_precio").val(id_producto);
    $("#nombre_producto_precio").text(nombre_producto);
    $("#precio_alimento").val('');
    
    // Obtener precio actual si existe
    let filtro = `?obtener_precio_alimento=${true}&id_producto=${id_producto}&cafeteria=${(cafeteria) ? cafeteria : false}`;
    
    $.ajax({
        url: url + 'views/ajax/ajax_propietarios_menu.php' + filtro,
        method: 'GET',
        cache: false,
        beforeSend: cargaSistema(true),
        success: function (respuesta) {
            let datos = JSON.parse(respuesta);
            if(datos.precio && datos.precio != ''){
                $("#precio_alimento").val(datos.precio);
            }
            cargaSistema(false);
        }
    });
    
    $("#modal_precio_alimento").modal('show');
});

// Guardar precio de alimento
$(document).on("submit", ".form_precio_alimento", function () {
    let id_producto = $("#id_producto_precio").val();
    let precio = $("#precio_alimento").val();
    let cafeteria = $("#cafeteria").val();
    
    if(!precio || precio == '' || precio <= 0){
        swal("¡Atención!", "Debes ingresar un precio válido", "warning");
        return;
    }
    
    var datos = new FormData();
    datos.append("guardar_precio_alimento", true);
    datos.append("id_producto", id_producto);
    datos.append("precio", precio);
    datos.append("cafeteria", (cafeteria) ? cafeteria : false);

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
            if(respuesta == 'success'){
                swal("¡Éxito!", "El precio se guardó correctamente", "success");
                $("#modal_precio_alimento").modal('hide');
            } else {
                swal("¡Error!", "No se pudo guardar el precio", "error");
            }
            cargaSistema(false);
        },
        error: function(){
            swal("¡Error!", "Ocurrió un error al guardar el precio", "error");
            cargaSistema(false);
        }
    });
});
