$(document).on("click","#agregar_imagen_inicio",function(){
    $("#modal_agregar_imagen").modal('show');
    $(".input_imagen").val('');
    $("#modalLabel").html('Agregando imagen');
    $("#imagen_previsualizar").attr('src','views/assets/img/cafeteria_default.png');
});

$(document).on("click",".editar_imagen",function(){
    $(".input_imagen").val('');
    $("#modal_agregar_imagen").modal('show');
    $("#modalLabel").html('Editando imagen');
    let registro = $(this).attr('registro');
    registro = JSON.parse(registro);

    $("#id_imagen").val(registro.id);
    $("#titulo_imagen").val(registro.titulo);
    $("#imagen_previsualizar").attr('src',registro.imagen);
    $("#descripcion_imagen").val(registro.descripcion);
    $("#enlace_imagen").val(registro.enlace);
    $("#select_tipo_imagen").val(registro.area);
    $("#nombre_boton").val(registro.nombre_enlace);
});

$(document).on("submit","#modal_agregar_imagen",function(){
    let imagen_subir = $("#imagen_inicio")[0].files[0] ? $("#imagen_inicio")[0].files[0] : false;
    var datos = new FormData();
    
    datos.append("registrar_imagen", true);
    if($("#id_imagen").val()) datos.append("id", $("#id_imagen").val());
    datos.append("titulo", $("#titulo_imagen").val());
    datos.append("descripcion", $("#descripcion_imagen").val());
    datos.append("enlace", $("#enlace_imagen").val());
    datos.append("area", $("#select_tipo_imagen option:selected").val());
    datos.append("nombre_enlace", $("#nombre_boton").val());
    if(imagen_subir) datos.append("imagen_subir", imagen_subir);
    
    $.ajax({
        url:url+'views/ajax/ajax_admin_pagina_inicial.php',
        method:'POST',
        data: datos,
        cache: false,
        contentType: false,
        processData: false,
        success:function(respuesta){
            console.log(respuesta);
            if (respuesta=='error') {
                swal("¡Error!", "Ha ocurrido un error", "error");
            } else {
                (id_imagen != "") ? alertaUpdate() : alertaInsert();
            }
        }
    });
});