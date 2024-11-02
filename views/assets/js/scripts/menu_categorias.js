$(document).ready(function(){
    if (moduloActual=='menu_categorias') {
        cargarTablaCategorias();
    }
});

function cargarTablaCategorias(){

    let filtro = `?categorias=${true}`;
    if ($.fn.DataTable.isDataTable($("#tabla_categorias"))) {
        $("#tabla_categorias").DataTable().destroy();
    }
   $('#tabla_categorias').DataTable( {
    "ajax": {
            "url": url + 'views/ajax/ajax_menu_categorias.php' + filtro,
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

$(document).on("submit",".form_agregar_categoria",function(){
    let id_categoria = $("#id_categoria").val();
    var datos = new FormData();
    
    datos.append("agregar_categoria", true);
    if(id_categoria) datos.append("id_categoria", id_categoria);
    datos.append('nombre',$("#nombre_categoria").val());
    datos.append('imagen_categoria',$("#imagen_categoria")[0].files[0]);
    
    $.ajax({
        url:url+'views/ajax/ajax_menu_categorias.php',
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
                (id_categoria!='')? alertaUpdate() : alertaInsert();
            }
            cargaSistema(false);
        }
    });
});

$(document).on("click",".btn_editar_categoria",function(){
    $(".imagen_editar").attr('src', $(this).attr("imagen"));
    $("#nombre_categoria").val($(this).attr("nombre"));
    $("#id_categoria").val($(this).attr("idRegistro"));
    $("#nombre_categoria").removeAttr('validarCampo')
    .attr('validarCampoEditar', true)
    .attr('idRegistro', $(this).attr("idRegistro"));

    $("#modal_editar_categorias").modal('show');
});

$(document).on("click","#btn_agregar_categoria",function(){
    $(".input_categorias").val('');
    $("#nombre_categoria").removeAttr('validarCampoEditar').attr('validarCampo');
    $(".imagen_editar").attr('src','http://localhost/OCCU/occu_webApp/views/assets/img/cafeteria_default.png');
    $("#modal_editar_categorias").modal('show');
});