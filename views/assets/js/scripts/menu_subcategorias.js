$(document).ready(function(){
    if (moduloActual=='menu_subcategorias') {
        cargarTablaSubategorias();
    }
});

function cargarTablaSubategorias(){

    let filtro = `?subcategorias=${true}`;
    if ($.fn.DataTable.isDataTable($("#tabla_subcategorias"))) {
        $("#tabla_subcategorias").DataTable().destroy();
    }
   $('#tabla_subcategorias').DataTable( {
    "ajax": {
            "url": url + 'views/ajax/ajax_menu_subcategorias.php' + filtro,
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

$(document).on("submit",".form_agregar_subcategoria",function(){
    let id = $("#id_subcategoria").val();
    var datos = new FormData();
    
    datos.append("agregar_subcategoria", true);
    if(id) datos.append("id", id);
    datos.append('nombre',$("#nombre_subcategoria").val());
    datos.append("id_categoria", $("#select_categoria").val());
    
    $.ajax({
        url:url+'views/ajax/ajax_menu_subcategorias.php',
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

$(document).on("click",".btn_editar_subcategoria",function(){
    $("#nombre_subcategoria").val($(this).attr("nombre"));
    $("#id_subcategoria").val($(this).attr("idRegistro"));
    $("#select_categoria").val($(this).attr("categoria"));
    $("#nombre_subcategoria").removeAttr('validarCampo')
    .attr('validarCampoEditar', true)
    .attr('idRegistro', $(this).attr("idRegistro"));

    $("#modal_editar_subcategorias").modal('show');
    $("#modalLabel").html('Editando subcategoría');
});

$(document).on("click","#btn_agregar_subcategoria",function(){
    $(".input_subcategorias").val('');
    $("#nombre_subcategoria").removeAttr('validarCampoEditar').attr('validarCampo');
    $("#modal_editar_subcategorias").modal('show');
    $("#modalLabel").html('Agregando subcategoría');
});