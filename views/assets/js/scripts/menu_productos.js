$(document).ready(function(){
    if (moduloActual=='menu_productos') {
        cargarTablaProductos();
    }
});

function cargarTablaProductos(){
    let estatus = $('input[name="estatus"]:checked').val();
    let categoria = $("#categoria_filtro option:selected").val();

    let filtro = `?productos=${true}&categoria=${categoria}&estatus=${estatus}`;

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
    datos.append("id_categoria", $("#select_categoria option:selected").val());
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
    $("#select_categoria").val($(this).attr("idRegistro"));
    $("#nombre_producto").removeAttr('validarCampo')
    .attr('validarCampoEditar', true)
    .attr('idRegistro', $(this).attr("idRegistro"));

    $("#modal_editar_productos").modal('show');
});

$(document).on("click","#btn_agregar_producto",function(){
    $(".input_productos").val('');
    $("#nombre_producto").removeAttr('validarCampoEditar').attr('validarCampo');
    $(".imagen_editar").attr('src','http://localhost/OCCU/occu_webApp/views/assets/img/cafeteria_default.png');
    $("#modal_editar_productos").modal('show');
});

$(document).on("submit","#form_filtro_productos",function(){
    cargarTablaProductos();
});