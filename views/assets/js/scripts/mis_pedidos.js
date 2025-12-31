// Variables globales para paginación
let paginaActual = 1;
const pedidosPorPagina = 10;

// Función para formatear folio a 6 dígitos
function formatearFolio(id) {
    return String(id).padStart(6, '0');
}

// Cargar pedidos al iniciar
$(document).ready(function() {
    if (moduloActual == "mis_pedidos") {
        cargarMisPedidos();
    }
});

// Función para cargar pedidos con paginación
function cargarMisPedidos(pagina = 1) {
    paginaActual = pagina;
    
    $.ajax({
        url: url + `views/ajax/ajax_mis_pedidos.php?obtenerMisPedidos=true&pagina=${pagina}&por_pagina=${pedidosPorPagina}`,
        method: 'GET',
        beforeSend: function() {
            cargaSistema(true);
        },
        success: function(response) {
            cargaSistema(false);
            try {
                const data = JSON.parse(response);
                
                // Cargar pedido actual
                if (data.pedido_actual) {
                    mostrarPedidoActual(data.pedido_actual);
                } else {
                    $('#pedido-actual-section').hide();
                }
                
                // Cargar pedidos anteriores
                if (data.pedidos_anteriores && data.pedidos_anteriores.length > 0) {
                    mostrarPedidosAnteriores(data.pedidos_anteriores, data.paginacion);
                } else {
                    $('#pedidos-anteriores-container').html(`
                        <div class="pedidos-vacio">
                            <i class="fas fa-inbox"></i>
                            <h4>No hay pedidos anteriores</h4>
                            <p>Aún no has realizado ningún pedido</p>
                        </div>
                    `);
                }
            } catch (e) {
                console.error('Error al procesar respuesta:', e);
                swal("Error", "Hubo un problema al cargar tus pedidos.", "error");
            }
        },
        error: function() {
            cargaSistema(false);
            swal("Error", "No se pudieron cargar tus pedidos.", "error");
        }
    });
}

// Mostrar pedido actual
function mostrarPedidoActual(pedido) {
    $('#pedido-actual-section').show();
    
    const estadoInfo = obtenerInfoEstado(pedido.estado_pedido);
    const fechaFormateada = formatearFecha(pedido.fecha_alta);
    const folioFormateado = formatearFolio(pedido.id);
    
    const html = `
        <div class="pedido-card">
            <div class="pedido-card-header">
                <img src="${url}${pedido.imagen_cafeteria || 'views/assets/img/cafeteria_default.png'}" 
                     alt="${pedido.nombre_cafeteria}" 
                     class="pedido-card-imagen"
                     onerror="this.src='${url}views/assets/img/cafeteria_default.png'">
                <div class="pedido-card-info">
                    <h5>${pedido.nombre_cafeteria}</h5>
                    <p><i class="fas fa-map-marker-alt me-1"></i>${pedido.direccion_cafeteria || 'Dirección no disponible'}</p>
                    <p class="mb-0"><strong>Folio: #${folioFormateado}</strong></p>
                </div>
            </div>
            <div class="pedido-card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="pedido-fecha"><i class="fas fa-calendar me-1"></i>${fechaFormateada}</span>
                    <span class="pedido-estado ${estadoInfo.clase}">
                        <i class="${estadoInfo.icono}"></i>
                        ${estadoInfo.texto}
                    </span>
                </div>
                ${pedido.minutos_transcurridos !== null ? `
                    <small class="text-muted d-block mb-2">
                        <i class="fas fa-clock me-1"></i>
                        ${pedido.minutos_transcurridos} minutos transcurridos
                    </small>
                ` : ''}
                ${pedido.codigo && (pedido.estado_pedido == 2 || pedido.estado_pedido == 6) ? `
                    <div class="alert alert-info mb-0 mt-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <strong><i class="fas fa-key me-2"></i>Código de verificación:</strong>
                                <p class="mb-0 mt-1" style="font-size: 1.5rem; font-weight: bold; letter-spacing: 0.3rem;">
                                    ${pedido.codigo}
                                </p>
                                <small class="text-muted">Muestre este código al recoger su pedido</small>
                            </div>
                        </div>
                    </div>
                ` : ''}
            </div>
            <div class="pedido-card-footer">
                <span class="pedido-monto">MX$${parseFloat(pedido.monto_total).toFixed(2)}</span>
                <div class="d-flex gap-2">
                    ${pedido.estado_pedido == 1 ? `
                        <button class="btn btn-sm btn-danger btn-cancelar-pedido" onclick="cancelarPedido(${pedido.id})">
                            <i class="fas fa-times me-1"></i> Cancelar
                        </button>
                    ` : ''}
                    <button class="btn btn-sm btn-outline-primary" onclick="verDetallePedido(${pedido.id})">
                        <i class="fas fa-eye me-1"></i> Ver detalle
                    </button>
                </div>
            </div>
        </div>
    `;
    
    $('#pedido-actual-container').html(html);
}

// Mostrar pedidos anteriores con paginación
function mostrarPedidosAnteriores(pedidos, paginacion) {
    if (pedidos.length === 0) {
        $('#pedidos-anteriores-container').html(`
            <div class="pedidos-vacio">
                <i class="fas fa-inbox"></i>
                <h4>No hay pedidos anteriores</h4>
                <p>Aún no has realizado ningún pedido</p>
            </div>
        `);
        return;
    }
    
    let html = '';
    pedidos.forEach(function(pedido) {
        const estadoInfo = obtenerInfoEstado(pedido.estado_pedido);
        const fechaFormateada = formatearFecha(pedido.fecha_alta);
        const folioFormateado = formatearFolio(pedido.id);
        
        html += `
            <div class="pedido-card" onclick="verDetallePedido(${pedido.id})">
                <div class="pedido-card-header">
                    <img src="${url}${pedido.imagen_cafeteria || 'views/assets/img/cafeteria_default.png'}" 
                         alt="${pedido.nombre_cafeteria}" 
                         class="pedido-card-imagen"
                         onerror="this.src='${url}views/assets/img/cafeteria_default.png'">
                    <div class="pedido-card-info">
                        <h5>${pedido.nombre_cafeteria}</h5>
                        <p><i class="fas fa-map-marker-alt me-1"></i>${pedido.direccion_cafeteria || 'Dirección no disponible'}</p>
                        <p class="mb-0"><strong>Folio: #${folioFormateado}</strong></p>
                    </div>
                </div>
                <div class="pedido-card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="pedido-fecha"><i class="fas fa-calendar me-1"></i>${fechaFormateada}</span>
                        <span class="pedido-estado ${estadoInfo.clase}">
                            <i class="${estadoInfo.icono}"></i>
                            ${estadoInfo.texto}
                        </span>
                    </div>
                </div>
                <div class="pedido-card-footer">
                    <span class="pedido-monto">MX$${parseFloat(pedido.monto_total).toFixed(2)}</span>
                    <i class="fas fa-chevron-right text-muted"></i>
                </div>
            </div>
        `;
    });
    
    // Agregar controles de paginación
    if (paginacion && paginacion.total_paginas > 1) {
        html += generarPaginacion(paginacion);
    }
    
    $('#pedidos-anteriores-container').html(html);
}

// Generar controles de paginación
function generarPaginacion(paginacion) {
    let html = '<nav aria-label="Paginación de pedidos" class="mt-4"><ul class="pagination justify-content-center">';
    
    // Botón anterior
    if (paginacion.pagina_actual > 1) {
        html += `<li class="page-item"><a class="page-link" href="#" onclick="cargarMisPedidos(${paginacion.pagina_actual - 1}); return false;"><i class="fas fa-chevron-left"></i></a></li>`;
    } else {
        html += `<li class="page-item disabled"><span class="page-link"><i class="fas fa-chevron-left"></i></span></li>`;
    }
    
    // Números de página
    const inicio = Math.max(1, paginacion.pagina_actual - 2);
    const fin = Math.min(paginacion.total_paginas, paginacion.pagina_actual + 2);
    
    if (inicio > 1) {
        html += `<li class="page-item"><a class="page-link" href="#" onclick="cargarMisPedidos(1); return false;">1</a></li>`;
        if (inicio > 2) {
            html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
        }
    }
    
    for (let i = inicio; i <= fin; i++) {
        if (i === paginacion.pagina_actual) {
            html += `<li class="page-item active"><span class="page-link">${i}</span></li>`;
        } else {
            html += `<li class="page-item"><a class="page-link" href="#" onclick="cargarMisPedidos(${i}); return false;">${i}</a></li>`;
        }
    }
    
    if (fin < paginacion.total_paginas) {
        if (fin < paginacion.total_paginas - 1) {
            html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
        }
        html += `<li class="page-item"><a class="page-link" href="#" onclick="cargarMisPedidos(${paginacion.total_paginas}); return false;">${paginacion.total_paginas}</a></li>`;
    }
    
    // Botón siguiente
    if (paginacion.pagina_actual < paginacion.total_paginas) {
        html += `<li class="page-item"><a class="page-link" href="#" onclick="cargarMisPedidos(${paginacion.pagina_actual + 1}); return false;"><i class="fas fa-chevron-right"></i></a></li>`;
    } else {
        html += `<li class="page-item disabled"><span class="page-link"><i class="fas fa-chevron-right"></i></span></li>`;
    }
    
    html += '</ul></nav>';
    
    // Información de paginación
    html += `<div class="text-center mt-3"><small class="text-muted">Mostrando ${((paginacion.pagina_actual - 1) * paginacion.por_pagina) + 1} - ${Math.min(paginacion.pagina_actual * paginacion.por_pagina, paginacion.total_pedidos)} de ${paginacion.total_pedidos} pedidos</small></div>`;
    
    return html;
}

// Ver detalle de un pedido
function verDetallePedido(idPedido) {
    $.ajax({
        url: url + 'views/ajax/ajax_mis_pedidos.php?obtenerDetallePedido=true&id_pedido=' + idPedido,
        method: 'GET',
        beforeSend: function() {
            cargaSistema(true);
        },
        success: function(response) {
            cargaSistema(false);
            try {
                const data = JSON.parse(response);
                
                if (data.error) {
                    swal("Error", data.message, "error");
                    return;
                }
                
                mostrarDetallePedido(data.pedido, data.items);
                
                // Abrir offcanvas
                const offcanvasEl = document.getElementById('offcanvasDetallePedido');
                const offcanvas = new bootstrap.Offcanvas(offcanvasEl);
                offcanvas.show();
            } catch (e) {
                console.error('Error al procesar respuesta:', e);
                swal("Error", "Hubo un problema al cargar el detalle del pedido.", "error");
            }
        },
        error: function() {
            cargaSistema(false);
            swal("Error", "No se pudo cargar el detalle del pedido.", "error");
        }
    });
}

// Mostrar detalle del pedido en el offcanvas
function mostrarDetallePedido(pedido, items) {
    const estadoInfo = obtenerInfoEstado(pedido.estado_pedido);
    const fechaFormateada = formatearFecha(pedido.fecha_alta);
    
    let html = `
        <!-- Header con imagen de cafetería -->
        <div class="position-relative">
            <img src="${url}${pedido.imagen_cafeteria || 'views/assets/img/cafeteria_default.png'}" 
                 alt="${pedido.nombre_cafeteria}" 
                 class="w-100 object-fit-cover"
                 style="max-height: 200px;"
                 onerror="this.src='${url}views/assets/img/cafeteria_default.png'">
        </div>
        
        <!-- Información general -->
        <div class="p-3">
            <h4 class="mb-2">${pedido.nombre_cafeteria}</h4>
            <p class="text-muted mb-2">
                <i class="fas fa-map-marker-alt me-2"></i>${pedido.direccion_cafeteria || 'Dirección no disponible'}
            </p>
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="pedido-estado ${estadoInfo.clase}">
                    <i class="${estadoInfo.icono}"></i>
                    ${estadoInfo.texto}
                </span>
                <span class="pedido-monto">MX$${parseFloat(pedido.monto_total).toFixed(2)}</span>
            </div>
            <div class="mb-3">
                <small class="text-muted">
                    <i class="fas fa-calendar me-1"></i>Fecha: ${fechaFormateada}
                </small>
            </div>
        </div>
        
        <!-- Items del pedido -->
        <div class="px-3 pb-3">
            <h5 class="mb-3">Productos</h5>
            <div class="list-group">
    `;
    
    items.forEach(function(item) {
        html += `
            <div class="list-group-item">
                <div class="d-flex align-items-start gap-3">
                    <img src="${url}${item.imagen_producto || 'views/assets/img/cafeteria_default.png'}" 
                         alt="${item.nombre_producto}" 
                         class="rounded"
                         style="width: 60px; height: 60px; object-fit: cover;"
                         onerror="this.src='${url}views/assets/img/cafeteria_default.png'">
                    <div class="flex-grow-1">
                        <h6 class="mb-1">${item.nombre_producto}</h6>
                        ${item.nombre_tamano ? `<small class="text-muted">${item.nombre_tamano} ${item.medida || ''} ${item.unidad_medida || ''}</small><br>` : ''}
                        <small class="text-muted">Cantidad: ${item.cantidad}</small>
                        ${item.ingredientes && item.ingredientes.length > 0 ? `
                            <div class="mt-2">
                                <small class="text-muted d-block mb-1"><strong>Ingredientes:</strong></small>
                                ${item.ingredientes.map(ing => `
                                    <small class="d-block text-muted ms-3">
                                        • ${ing.nombre_ingrediente} 
                                        ${ing.cantidad > ing.cantidad_gratis ? `(${ing.cantidad - ing.cantidad_gratis} extra)` : ''}
                                    </small>
                                `).join('')}
                            </div>
                        ` : ''}
                        <div class="mt-2">
                            <strong class="text-primary">MX$${parseFloat(item.monto_total).toFixed(2)}</strong>
                        </div>
                    </div>
                </div>
            </div>
        `;
    });
    
    html += `
            </div>
        </div>
    `;
    
    $('#detalle-pedido-content').html(html);
    $('#offcanvasDetallePedidoLabel').text('Detalle del Pedido #' + formatearFolio(pedido.id));
}

// Obtener información del estado del pedido
function obtenerInfoEstado(estado_pedido) {
    switch(parseInt(estado_pedido)) {
        case 1:
            return {
                texto: 'Pendiente',
                clase: 'pendiente',
                icono: 'fas fa-clock'
            };
        case 2:
            return {
                texto: 'Preparando',
                clase: 'preparando',
                icono: 'fas fa-utensils'
            };
        case 3:
            return {
                texto: 'Rechazado',
                clase: 'rechazado',
                icono: 'fas fa-times-circle'
            };
        case 4:
            return {
                texto: 'Entregado',
                clase: 'entregado',
                icono: 'fas fa-check-circle'
            };
        case 5:
            return {
                texto: 'Cancelado',
                clase: 'rechazado',
                icono: 'fas fa-ban'
            };
        case 6:
            return {
                texto: 'Pedido listo',
                clase: 'terminado',
                icono: 'fas fa-check-double'
            };
        default:
            return {
                texto: 'Desconocido',
                clase: 'pendiente',
                icono: 'fas fa-question'
            };
    }
}

// Formatear fecha
function formatearFecha(fecha) {
    if (!fecha) return 'Fecha no disponible';
    
    const fechaObj = new Date(fecha);
    const opciones = { 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    };
    
    return fechaObj.toLocaleDateString('es-MX', opciones);
}

// Cancelar pedido
function cancelarPedido(idPedido) {
    swal({
        title: "¿Cancelar pedido?",
        text: "Esta acción cancelará tu pedido. ¿Estás seguro?",
        icon: "warning",
        buttons: ["No, mantener pedido", "Sí, cancelar"],
        dangerMode: true
    }).then((confirmar) => {
        if (confirmar) {
            $.ajax({
                url: url + 'views/ajax/ajax_mis_pedidos.php',
                method: 'POST',
                data: {
                    cancelarPedido: true,
                    id_pedido: idPedido
                },
                beforeSend: function() {
                    cargaSistema(true);
                },
                success: function(response) {
                    cargaSistema(false);
                    try {
                        const data = JSON.parse(response);
                        
                        if (data.success) {
                            swal("¡Cancelado!", data.message, "success");
                            cargarMisPedidos();
                        } else {
                            swal("Error", data.message, "error");
                        }
                    } catch (e) {
                        console.error('Error al procesar respuesta:', e);
                        swal("Error", "Hubo un problema al cancelar el pedido.", "error");
                    }
                },
                error: function() {
                    cargaSistema(false);
                    swal("Error", "No se pudo cancelar el pedido.", "error");
                }
            });
        }
    });
}

