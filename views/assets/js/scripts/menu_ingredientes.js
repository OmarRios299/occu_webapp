$(document).ready(function(){
    if (moduloActual=='menu_ingredientes') {
        cargarTablaIngredientes();
    }
});

function cargarTablaIngredientes(){

    let filtro = `?ingredientes=${true}`;
    if ($.fn.DataTable.isDataTable($("#tabla_ingredientes"))) {
        $("#tabla_ingredientes").DataTable().destroy();
    }
   $('#tabla_ingredientes').DataTable( {
    "ajax": {
            "url": url + 'views/ajax/ajax_menu_ingredientes.php' + filtro,
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

$(document).on("submit",".form_agregar_ingrediente",function(){
    let id = $("#id_ingrediente").val();
    var datos = new FormData();
    
    datos.append("agregar_ingrediente", true);
    if(id) datos.append("id", id);
    datos.append('nombre',$("#nombre_ingrediente").val());
    datos.append("id_ingrediente_categoria", $("#select_categoria").val());
    
    $.ajax({
        url:url+'views/ajax/ajax_menu_ingredientes.php',
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
                (id!='')? alertaUpdate() : alertaInsert();
            }
            cargaSistema(false);
        }
    });
});

$(document).on("click",".btn_editar_ingrediente",function(){
    $("#nombre_ingrediente").val($(this).attr("nombre"));
    $("#id_ingrediente").val($(this).attr("idRegistro"));
    $("#select_categoria").val($(this).attr("categoria"));
    $("#nombre_ingrediente").removeAttr('validarCampo')
    .attr('validarCampoEditar', true)
    .attr('idRegistro', $(this).attr("idRegistro"));

    $("#modal_editar_ingredientes").modal('show');
    $("#modalLabel").html('Editando subcategoría');
});

$(document).on("click","#btn_agregar_ingrediente",function(){
    $(".input_ingredientes").val('');
    $("#nombre_ingrediente").removeAttr('validarCampoEditar').attr('validarCampo');
    $("#modal_editar_ingredientes").modal('show');
    $("#modalLabel").html('Agregando subcategoría');
});