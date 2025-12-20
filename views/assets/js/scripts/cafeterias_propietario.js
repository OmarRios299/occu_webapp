// JavaScript para la vista de Propietarios
$(document).ready(function () {
    if ($('#div_cafeterias_propietario').length) {
        cargarCafeteriasPropietario();
    }
});

function cargarCafeteriasPropietario() {
    var datos = new FormData();
    datos.append("obtener_cafeterias_propietario", true);

    $.ajax({
        url: url + 'views/ajax/ajax_cafeterias.php',
        method: 'POST',
        data: datos,
        cache: false,
        contentType: false,
        processData: false,
        beforeSend: cargaSistema(true),
        success: function (respuesta) {
            try {
                respuesta = JSON.parse(respuesta);
                if (respuesta.success && respuesta.data && respuesta.data.length > 0) {
                    mostrarCafeteriasPropietario(respuesta.data);
                } else {
                    $('#div_cafeterias_propietario').html(`
                        <div class="text-center py-5">
                            <i class="bi bi-cup-hot" style="font-size: 4rem; color: #ccc;"></i>
                            <p class="mt-3 text-muted">No tienes cafeterías registradas</p>
                            <a href="${url}cafeterias/agregar" class="btn btn-primary mt-3">
                                <i class="bi bi-plus-circle me-2"></i>Agregar tu primera cafetería
                            </a>
                        </div>
                    `);
                }
            } catch (e) {
                console.error('Error al parsear respuesta:', e, respuesta);
                $('#div_cafeterias_propietario').html(`
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        Error al cargar las cafeterías. Por favor, recarga la página.
                    </div>
                `);
            }
            cargaSistema(false);
        },
        error: function (xhr, status, error) {
            console.error('Error AJAX:', error);
            cargaSistema(false);
            $('#div_cafeterias_propietario').html(`
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    Error al cargar las cafeterías. Por favor, recarga la página.
                </div>
            `);
        }
    });
}

function mostrarCafeteriasPropietario(cafeterias) {
    if (!cafeterias || cafeterias.length === 0) {
        $('#div_cafeterias_propietario').html(`
            <div class="text-center py-5">
                <i class="bi bi-cup-hot" style="font-size: 4rem; color: #ccc;"></i>
                <p class="mt-3 text-muted">No tienes cafeterías registradas</p>
                <a href="${url}cafeterias/agregar" class="btn btn-primary mt-3">
                    <i class="bi bi-plus-circle me-2"></i>Agregar tu primera cafetería
                </a>
            </div>
        `);
        return;
    }

    let html = '';
    cafeterias.forEach(function(cafeteria) {
        const estado = cafeteria.estado == 0 ? 'Activa' : 'Inactiva';
        const estadoClass = cafeteria.estado == 0 ? 'status-active' : 'status-inactive';
        const imagen = cafeteria.imagen || url + 'views/assets/img/default-cafeteria.jpg';
        
        html += `
            <div class="cafeteria-card-propietario" data-id="${cafeteria.id}">
                <div class="card-image">
                    <img src="${url}${imagen}" alt="${cafeteria.nombre}">
                    <span class="card-status ${estadoClass}">${estado}</span>
                </div>
                <div class="card-body">
                    <h5 class="card-title">${cafeteria.nombre}</h5>
                    <div class="card-info">
                        <i class="bi bi-geo-alt-fill"></i>
                        <span>${cafeteria.direccion || 'Sin dirección'}</span>
                    </div>
                    <div class="card-info">
                        <i class="bi bi-telephone-fill"></i>
                        <span>${cafeteria.telefono || 'Sin teléfono'}</span>
                    </div>
                    <div class="card-horario">
                        <i class="bi bi-clock"></i>
                        <span>${cafeteria.horario_apertura || '--'} - ${cafeteria.horario_cierre || '--'}</span>
                    </div>
                </div>
            </div>
        `;
    });

    $('#div_cafeterias_propietario').html(html);
}

// Abrir offcanvas al hacer click en una cafetería
$(document).on('click', '.cafeteria-card-propietario', function() {
    const idCafeteria = $(this).data('id');
    abrirOffcanvasCafeteriaPropietario(idCafeteria);
});

function abrirOffcanvasCafeteriaPropietario(idCafeteria) {
    var datos = new FormData();
    datos.append("obtener_datos_cafeteria", true);
    datos.append("id", idCafeteria);

    $.ajax({
        url: url + 'views/ajax/ajax_cafeterias.php',
        method: 'POST',
        data: datos,
        cache: false,
        contentType: false,
        processData: false,
        beforeSend: cargaSistema(true),
        success: function (respuesta) {
            try {
                respuesta = JSON.parse(respuesta);
                if (respuesta.success && respuesta.data) {
                    mostrarInfoCafeteriaPropietario(respuesta.data);
                    
                    // Configurar botones de acción
                    $('#btn-editar-cafeteria').attr('href', url + 'cafeterias/' + idCafeteria + '/editar');
                    $('#btn-menu-cafeteria').attr('href', url + 'propietarios_menu/' + idCafeteria);
                    $('#btn-pedidos-cafeteria').attr('href', url + 'cafeteria_pedidos/' + idCafeteria);
                    $('#btn-qr-cafeteria').attr('href', url + 'propietarios_menu_qr/' + idCafeteria);
                    
                    // Configurar switch de estado
                    $('#switch-estado-cafeteria').prop('checked', respuesta.data.estado == 0);
                    $('#switch-estado-cafeteria').attr('idRegistro', idCafeteria);
                    $('#switch-estado-cafeteria').attr('tabla', 'cafeterias');
                    
                    // Configurar botón eliminar
                    $('#btn-eliminar-cafeteria').attr('idRegistro', idCafeteria);
                    $('#btn-eliminar-cafeteria').attr('tabla', 'cafeterias');
                    
                    // Configurar botón servicios
                    $('#btn-servicios-cafeteria').attr('idRegistro', idCafeteria);
                    
                    // Guardar ID en hidden
                    $('#id_cafeteria_propietario').val(idCafeteria);
                    
                    // Abrir offcanvas
                    const offcanvasEl = document.getElementById('offcanvasCafeteriaPropietario');
                    const offcanvas = new bootstrap.Offcanvas(offcanvasEl);
                    offcanvas.show();
                } else {
                    swal("¡Error!", "No se pudo cargar la información de la cafetería", "error");
                }
            } catch (e) {
                console.error('Error al parsear respuesta:', e);
                swal("¡Error!", "Error al cargar la información", "error");
            }
            cargaSistema(false);
        },
        error: function () {
            cargaSistema(false);
            swal("¡Error!", "Error al cargar la información", "error");
        }
    });
}

function mostrarInfoCafeteriaPropietario(cafeteria) {
    const imagen = cafeteria.imagen || url + 'views/assets/img/default-cafeteria.jpg';
    
    let html = `
        <div class="text-center mb-4">
            <img src="${url}${imagen}" alt="${cafeteria.nombre}" class="img-fluid rounded" style="max-height: 200px; width: auto;">
        </div>
        <div class="row">
            <div class="col-12">
                <div class="info-label">Nombre</div>
                <div class="info-value">${cafeteria.nombre || 'N/A'}</div>
            </div>
            <div class="col-12 mt-2">
                <div class="info-label">Dirección</div>
                <div class="info-value">${cafeteria.direccion || 'Sin dirección'}</div>
            </div>
            <div class="col-6 mt-2">
                <div class="info-label">Horario</div>
                <div class="info-value">${cafeteria.horario_apertura || '--'} - ${cafeteria.horario_cierre || '--'}</div>
            </div>
            <div class="col-6 mt-2">
                <div class="info-label">Teléfono</div>
                <div class="info-value">${cafeteria.telefono || 'Sin teléfono'}</div>
            </div>
            <div class="col-12 mt-2">
                <div class="info-label">Correo Electrónico</div>
                <div class="info-value">${cafeteria.correo_electronico || 'Sin correo'}</div>
            </div>
            <div class="col-6 mt-2">
                <div class="info-label">Ciudad</div>
                <div class="info-value">${cafeteria.ciudad || 'N/A'}</div>
            </div>
            <div class="col-6 mt-2">
                <div class="info-label">Estado</div>
                <div class="info-value">${cafeteria.entidad_federativa || 'N/A'}</div>
            </div>
        </div>
            `;
    
    if (cafeteria.descripcion) {
        html += `
            <div class="col-12 mt-2">
                <div class="info-label">Descripción</div>
                <div class="info-value">${cafeteria.descripcion}</div>
            </div>
        `;
    }
    
    $('#titulo_cafeteria_propietario').text(cafeteria.nombre);
    $('#info_cafeteria_propietario').html(html);
}

// Manejar eventos de botones
$(document).on('click', '#btn-servicios-cafeteria', function() {
    const idCafeteria = $(this).attr('idRegistro');
    $('#id_cafeteria').val(idCafeteria);
    
    // Cargar servicios usando la función existente de cafeterias.js
    if (typeof cargarServicios === 'function') {
        cargarServicios();
    } else {
        // Implementación alternativa si la función no está disponible
        var datos = new FormData();
        datos.append("cargar_servicios", true);
        datos.append("id_cafeteria", idCafeteria);
        
        $.ajax({
            url: url + 'views/ajax/ajax_cafeterias.php',
            method: 'POST',
            data: datos,
            cache: false,
            contentType: false,
            processData: false,
            beforeSend: cargaSistema(true),
            success: function (respuesta) {
                try {
                    respuesta = JSON.parse(respuesta);
                    $('#servicios').html(respuesta);
                } catch(e) {
                    $('#servicios').html(respuesta);
                }
                cargaSistema(false);
            }
        });
    }
    
    $('#modal_agregar_servicios').modal('show');
});

// Interceptar el evento de eliminación para evitar el reload completo
$(document).on('click', '#btn-eliminar-cafeteria.eliminarRegistro', function(e) {
    e.stopPropagation();
    const idCafeteria = $(this).attr('idRegistro');
    const tabla = $(this).attr('tabla');
    
    swal({
        title: '¡Cuidado!',
        text: '¿Estás seguro que deseas eliminar el registro seleccionado?',
        icon: 'warning',
        buttons: true,
        dangerMode: true,
    }).then((willDelete) => {
        if (willDelete) {
            let datos = new FormData();
            datos.append("tablaEliminar", tabla);
            datos.append("idEliminar", idCafeteria);
            
            $.ajax({
                url: url + "views/ajax/ajax_general.php",
                method: "POST",
                data: datos,
                cache: false,
                contentType: false,
                processData: false,
                success: function (respuesta) {
                    if (respuesta === "session_expired") {
                        sesionExpirada();
                    } else {
                        swal({
                            title: '¡Bien!',
                            text: '¡El registro se eliminó exitosamente!',
                            icon: 'success',
                            button: 'Aceptar',
                        }).then(function() {
                            $('#offcanvasCafeteriaPropietario').offcanvas('hide');
                            cargarCafeteriasPropietario();
                        });
                    }
                }
            });
            return false; // Prevenir el comportamiento por defecto
        }
    });
    return false;
});

// Escuchar cuando se cierra el offcanvas para recargar la lista
$('#offcanvasCafeteriaPropietario').on('hidden.bs.offcanvas', function() {
    // Solo recargar si no se está eliminando
    if (!$('#btn-eliminar-cafeteria').hasClass('eliminando')) {
        cargarCafeteriasPropietario();
    }
});

// La función eliminarCafeteria ya no es necesaria, se usa eliminarRegistro de general.js

// El cambio de estado se maneja automáticamente por el evento delegado de general.js
// No necesitamos manejar el evento aquí, solo asegurarnos de que los atributos estén correctos

