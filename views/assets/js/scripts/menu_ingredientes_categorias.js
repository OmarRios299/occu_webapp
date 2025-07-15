$(document).ready(function(){
    if (moduloActual=='menu_ingredientes_categorias') {
        cargarTablaIngCategorias();
    }
});

function cargarTablaIngCategorias(){

    let filtro = `?categorias=${true}`;
    if ($.fn.DataTable.isDataTable($("#tabla_ing_categorias"))) {
        $("#tabla_ing_categorias").DataTable().destroy();
    }
   $('#tabla_ing_categorias').DataTable( {
    "ajax": {
            "url": url + 'views/ajax/ajax_menu_ingredientes_categorias.php' + filtro,
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


$(document).on("submit",".form_add_ing_categoria",function(){
    let id = $("#id_categoria").val();
    var datos = new FormData();
    
    datos.append("agregar_categoria", true);
    if(id) datos.append("id", id);
    datos.append('nombre',$("#nombre_categoria").val());
    
    $.ajax({
        url:url+'views/ajax/ajax_menu_ingredientes_categorias.php',
        method:'POST',
        data: datos,
        cache: false,
        contentType: false,
        processData: false,
        beforeSend: cargaSistema(true),
        success:function(respuesta){
            console.log(respuesta);
            if (respuesta=='error_validacion_nombre'){
                swal("¡Error!", "La categoría ya está registrada", "error");
            }else if (respuesta!='success') {
                swal("¡Error!", "Ha ocurrido un error", "error");
            } else {
                (id!='')? alertaUpdate() : alertaInsert();
            }
            cargaSistema(false);
        }
    });
});

$(document).on("click",".btn_editar_ing_categoria",function(){
    $("#nombre_categoria").val($(this).attr("nombre"));
    $("#id_categoria").val($(this).attr("idRegistro"));
    $("#nombre_categoria").removeAttr('validarCampo')
    .attr('validarCampoEditar', true)
    .attr('idRegistro', $(this).attr("idRegistro"));

    $("#modal_ing_categorias").modal('show');
    $("#modalLabel").html('Editando categoría');
});

$(document).on("click","#btn_agregar_ing_categoria",function(){
    $(".input_categorias").val('');
    $("#nombre_categoria").removeAttr('validarCampoEditar').attr('validarCampo');
    $("#modal_ing_categorias").modal('show');
    $("#modalLabel").html('Agregando categoría');
});