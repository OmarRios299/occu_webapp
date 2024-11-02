$(document).ready(function(){
    if (moduloActual=='cafeterias_servicios') {
        cargarTablaServicios();
    }
});

function cargarTablaServicios(){

    let filtro = `?servicios=${true}`;
    if ($.fn.DataTable.isDataTable($("#tabla_servicios"))) {
        $("#tabla_servicios").DataTable().destroy();
    }
   $('#tabla_servicios').DataTable( {
    "ajax": {
            "url": url + 'views/ajax/ajax_cafeterias_servicios.php' + filtro,
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

$(document).on("submit",".form_agregar_servicios",function(){
    let id_servicio = $("#id_servicio").val();
    var datos = new FormData();
    
    datos.append("agregar_servicio", true);
    if(id_servicio) datos.append("id_servicio", id_servicio);
    datos.append('nombre',$("#nombre_servicio").val());
    datos.append('imagen_servicio',$("#imagen_servicio")[0].files[0]);
    
    $.ajax({
        url:url+'views/ajax/ajax_cafeterias_servicios.php',
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
                (id_servicio!='')? alertaUpdate() : alertaInsert();
            }
            cargaSistema(false);
        }
    });
});

$(document).on("click",".btn_editar_servicio",function(){
    $(".imagen_editar").attr('src', $(this).attr("imagen"));
    $("#nombre_servicio").val($(this).attr("nombre"));
    $("#id_servicio").val($(this).attr("idRegistro"));
    $("#nombre_servicio").removeAttr('validarCampo')
    .attr('validarCampoEditar', true)
    .attr('idRegistro', $(this).attr("idRegistro"));

    $("#modal_editar_servicios").modal('show');
});

$(document).on("click","#btn_agregar_servicio",function(){
    $(".input_servicios").val('');
    $("#nombre_servicio").removeAttr('validarCampoEditar').attr('validarCampo');
    $(".imagen_editar").attr('src','http://localhost/OCCU/occu_webApp/views/assets/img/cafeteria_default.png');
    $("#modal_editar_servicios").modal('show');
});