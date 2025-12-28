$(document).ready(function(){
    if (moduloActual=='menu_productos') {
        cargarTablaProductos();
    }
});

function cargarTablaProductos(){
    let estatus = $('input[name="estatus"]:checked').val();
    let id_subcategoria = $("#subcategoria_filtro option:selected").val();
    let id_categoria = $("#categoria_filtro option:selected").val();

    let filtro = `?productos=${true}&id_subcategoria=${id_subcategoria}&id_categoria=${id_categoria}&estatus=${estatus}`;

    if ($.fn.DataTable.isDataTable($("#tabla_productos"))) {
        $("#tabla_productos").DataTable().destroy();
    }
   $('#tabla_productos').DataTable( {
    "ajax": {
            "url": url + 'views/ajax/ajax_menu_productos.php' + filtro,
            "dataSrc": function (json) {
                return json.data;
            },
        },
    "deferRender": true,
    "retrieve": true,
    "processing": true,
    dom: 'Bfrtip',
    responsive: true,
    ordering: true,
    "language":{"url": url+"views/assets/plugins/DataTables/Spanish.json"}
   });
}

$(document).on("submit",".form_agregar_producto",function(){
    let id_producto = $("#id_producto").val();
    var datos = new FormData();
    
    datos.append("agregar_producto", true);
    if(id_producto) datos.append("id_producto", id_producto);
    datos.append('nombre',$("#nombre_producto").val());
    datos.append("id_subcategoria", $("#select_subcategoria option:selected").val());
    datos.append('imagen_producto',$("#imagen_producto")[0].files[0]);
    
    $.ajax({
        url:url+'views/ajax/ajax_menu_productos.php',
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

$(document).on("click",".btn_editar_producto",function(){
    $(".imagen_editar").attr('src', $(this).attr("imagen"));
    $("#nombre_producto").val($(this).attr("nombre"));
    $("#id_producto").val($(this).attr("idRegistro"));
    $("#select_subcategoria").val($(this).attr("subcategoria"));
    $("#nombre_producto").removeAttr('validarCampo')
    .attr('validarCampoEditar', true)
    .attr('idRegistro', $(this).attr("idRegistro"));

    $("#modal_editar_productos").modal('show');
});

$(document).on("click","#btn_agregar_producto",function(){
    $(".input_productos").val('');
    $("#nombre_producto").removeAttr('validarCampoEditar').attr('validarCampo');
    $(".imagen_editar").attr('src',url+'views/assets/img/cafeteria_default.png');
    $("#modal_editar_productos").modal('show');
});

$(document).on("submit","#form_filtro_productos",function(){
    cargarTablaProductos();
});

// Abrir modal de categorías base
$(document).on("click",".btn_bases_producto",function(){
    let id_producto = $(this).attr("idRegistro");
    let nombre_producto = $(this).attr("nombre");
    
    $("#id_producto_bases").val(id_producto);
    $("#nombre_producto_bases").text(nombre_producto);
    
    // Cargar categorías disponibles y las seleccionadas
    cargarCategoriasBases(id_producto);
    
    $("#modal_bases_producto").modal('show');
});

// Cargar categorías de ingredientes base
function cargarCategoriasBases(id_producto){
    $.ajax({
        url: url + 'views/ajax/ajax_menu_productos.php',
        method: 'GET',
        data: {
            obtener_categorias_bases: true,
            id_producto: id_producto
        },
        beforeSend: cargaSistema(true),
        success: function(respuesta){
            if(respuesta != 'error' && respuesta != 'session_expired'){
                let categorias = JSON.parse(respuesta);
                
                // Obtener categorías ya seleccionadas para este producto
                obtenerBasesProducto(id_producto, categorias);
            } else {
                swal("¡Error!", "No se pudieron cargar las categorías", "error");
                cargaSistema(false);
            }
        }
    });
}

// Obtener categorías base ya seleccionadas del producto
function obtenerBasesProducto(id_producto, todas_categorias){
    $.ajax({
        url: url + 'views/ajax/ajax_menu_productos.php',
        method: 'GET',
        data: {
            obtener_bases_producto: true,
            id_producto: id_producto
        },
        success: function(respuesta){
            if(respuesta != 'error' && respuesta != 'session_expired'){
                let bases_seleccionadas = JSON.parse(respuesta);
                
                // Renderizar las categorías con las seleccionadas marcadas
                renderizarCategoriasBases(todas_categorias, bases_seleccionadas);
            } else {
                // Si hay error, renderizar sin selecciones
                renderizarCategoriasBases(todas_categorias, []);
            }
            cargaSistema(false);
        }
    });
}

// Renderizar las categorías en el modal
function renderizarCategoriasBases(categorias, seleccionadas){
    let html = '';
    
    categorias.forEach(function(categoria){
        let checked = seleccionadas.includes(parseInt(categoria.id)) ? 'checked' : '';
        let cardClass = checked ? 'border-primary shadow-sm' : '';
        
        html += `
            <div class="col-md-4 mb-3">
                <div class="card categoria-base-card ${cardClass}" style="cursor: pointer; transition: all 0.3s;">
                    <div class="card-body">
                        <div class="form-check">
                            <input class="form-check-input checkbox-categoria-base" type="checkbox" 
                                   value="${categoria.id}" id="cat_base_${categoria.id}" ${checked}>
                            <label class="form-check-label w-100" for="cat_base_${categoria.id}" style="cursor: pointer;">
                                <strong>${categoria.nombre}</strong>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        `;
    });
    
    $("#contenedor_categorias_bases").html(html);
    
    // Agregar efecto visual al hacer clic en la tarjeta
    $(".categoria-base-card").on("click", function(e){
        e.preventDefault();
        e.stopPropagation();
        
        // Prevenir selección de texto
        if (window.getSelection) {
            window.getSelection().removeAllRanges();
        } else if (document.selection) {
            document.selection.empty();
        }
        
        let checkbox = $(this).find('.checkbox-categoria-base');
        checkbox.prop('checked', !checkbox.prop('checked'));
        actualizarEstiloCard($(this), checkbox.prop('checked'));
    });
    
    // Prevenir propagación del evento en el checkbox para evitar doble toggle
    $(".checkbox-categoria-base").on("click", function(e){
        e.stopPropagation();
    });
    
    // Actualizar estilo cuando cambia el checkbox directamente
    $(".checkbox-categoria-base").on("change", function(){
        actualizarEstiloCard($(this).closest('.categoria-base-card'), $(this).prop('checked'));
    });
}

// Actualizar estilo visual de la tarjeta
function actualizarEstiloCard(card, checked){
    if(checked){
        card.addClass('border-primary shadow-sm');
        card.removeClass('border-secondary');
    } else {
        card.removeClass('border-primary shadow-sm');
        card.addClass('border-secondary');
    }
}

// Guardar categorías base seleccionadas
$(document).on("click", "#btn_guardar_bases", function(){
    let id_producto = $("#id_producto_bases").val();
    let categorias_seleccionadas = [];
    
    $(".checkbox-categoria-base:checked").each(function(){
        categorias_seleccionadas.push($(this).val());
    });
    
    // Preparar datos para enviar
    let formData = new FormData();
    formData.append('guardar_bases_producto', true);
    formData.append('id_producto', id_producto);
    
    // Agregar cada categoría seleccionada
    categorias_seleccionadas.forEach(function(cat){
        formData.append('categorias[]', cat);
    });
    
    $.ajax({
        url: url + 'views/ajax/ajax_menu_productos.php',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        beforeSend: cargaSistema(true),
        success: function(respuesta){
            if(respuesta == 'success'){
                swal("¡Éxito!", "Las categorías base se guardaron correctamente", "success");
                $("#modal_bases_producto").modal('hide');
            } else {
                swal("¡Error!", "No se pudieron guardar las categorías base", "error");
            }
            cargaSistema(false);
        }
    });
});