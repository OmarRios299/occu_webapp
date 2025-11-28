// ========== VARIABLES GLOBALES ==========
let intervaloActualizacion = null;
const INTERVALO_SEGUNDOS = 15;

// ========== INICIALIZACIÓN ==========
$(document).ready(function () {
  if (moduloActual == "cafeteria_pedidos") {
    cargarPedidos();
    iniciarAutoActualizacion();
  }
});

// ========== AUTO-ACTUALIZACIÓN ==========
function iniciarAutoActualizacion() {
  if ($("#auto_refresh").is(":checked")) {
    intervaloActualizacion = setInterval(function () {
      cargarPedidos(true); // silent = true
    }, INTERVALO_SEGUNDOS * 1000);
  }
}

function detenerAutoActualizacion() {
  if (intervaloActualizacion) {
    clearInterval(intervaloActualizacion);
    intervaloActualizacion = null;
  }
}

$(document).on("change", "#auto_refresh", function () {
  if ($(this).is(":checked")) {
    iniciarAutoActualizacion();
  } else {
    detenerAutoActualizacion();
  }
});

// ========== CARGAR PEDIDOS ==========
$(document).on("click", "#btn_actualizar_pedidos", function () {
  cargarPedidos();
});

function cargarPedidos(silent = false) {
  let id_cafeteria = $("#id_cafeteria_pedidos").val();

  if (!silent) {
    // Mostrar loading en todas las listas
    $(".tab-pane .row").html(`
            <div class="col-12 text-center py-5">
                <div class="spinner-border text-secondary" role="status"></div>
                <p class="mt-2 text-muted">Cargando pedidos...</p>
            </div>
        `);
  }

  $.ajax({
    url: url + "views/ajax/ajax_cafeteria_pedidos.php",
    method: "GET",
    data: {
      obtenerPedidos: true,
      id_cafeteria: id_cafeteria,
    },
    success: function (response) {
      response = JSON.parse(response);

      // Actualizar contadores
      actualizarContadores(response.contadores);

      // Separar pedidos por estado
      let pendientes = response.pedidos.filter((p) => p.estado_pedido == 1);
      let preparando = response.pedidos.filter((p) => p.estado_pedido == 2);
      let entregados = response.pedidos.filter((p) => p.estado_pedido == 4);
      let rechazados = response.pedidos.filter((p) => p.estado_pedido == 3);

      // Renderizar cada lista
      renderizarPedidos("#lista_pendientes", pendientes, 1);
      renderizarPedidos("#lista_preparando", preparando, 2);
      renderizarPedidos("#lista_entregados", entregados, 4);
      renderizarPedidos("#lista_rechazados", rechazados, 3);
    },
    error: function () {
      if (!silent) {
        swal("Error", "No se pudieron cargar los pedidos", "error");
      }
    },
  });
}

function actualizarContadores(contadores) {
  $("#count_pendientes").text(contadores.pendientes);
  $("#count_preparando").text(contadores.preparando);
  $("#count_entregados").text(contadores.entregados_hoy);
  $("#count_rechazados").text(contadores.rechazados_hoy);

  $("#badge_pendientes").text(contadores.pendientes);
  $("#badge_preparando").text(contadores.preparando);
}

function renderizarPedidos(contenedor, pedidos, estado) {
  if (pedidos.length === 0) {
    let mensaje = obtenerMensajeVacio(estado);
    $(contenedor).html(`
            <div class="col-12 text-center py-5">
                <i class="fas ${mensaje.icono} fa-3x text-muted mb-3"></i>
                <p class="text-muted">${mensaje.texto}</p>
            </div>
        `);
    return;
  }

  let html = "";

  pedidos.forEach((pedido) => {
    let tiempoClase =
      pedido.minutos_transcurridos > 15 && estado == 1 ? "urgente" : "";
    let cardClase = estado == 1 && pedido.minutos_transcurridos < 2 ? "pedido-nuevo" : "";

    let itemsHtml = pedido.items_resumen
      .map(
        (item) =>
          `<span class="badge bg-light text-dark me-1 mb-1">${item.cantidad}x ${item.nombre}${item.tamano ? " (" + item.tamano + ")" : ""}</span>`
      )
      .join("");

    let botonesHtml = obtenerBotonesPedido(pedido, estado);

    html += `
            <div class="col-12 col-md-6 col-lg-4 mb-3">
                <div class="card pedido-card estado-${estado} ${cardClase}">
                    <div class="card-header d-flex justify-content-between align-items-center py-2">
                        <div>
                            <strong class="text-primary">#${pedido.id}</strong>
                            <span class="ms-2 text-muted tiempo-pedido ${tiempoClase}">
                                <i class="fas fa-clock"></i> ${formatearTiempo(pedido.minutos_transcurridos)}
                            </span>
                        </div>
                        <span class="fw-bold text-success">$${parseFloat(pedido.monto_total).toFixed(2)}</span>
                    </div>
                    <div class="card-body py-2">
                        <div class="mb-2">
                            <i class="fas fa-user text-muted me-1"></i>
                            <strong>${pedido.nombre_cliente}</strong>
                            ${pedido.telefono_cliente ? `<br><small class="text-muted"><i class="fas fa-phone me-1"></i>${pedido.telefono_cliente}</small>` : ""}
                        </div>
                        <div class="mb-2">
                            <small class="text-muted d-block mb-1">Productos (${pedido.total_items}):</small>
                            ${itemsHtml}
                        </div>
                        ${estado == 3 && pedido.motivo_rechazo ? `
                            <div class="alert alert-danger py-1 px-2 mb-0 mt-2">
                                <small><i class="fas fa-times-circle me-1"></i>${pedido.motivo_rechazo}</small>
                            </div>
                        ` : ""}
                    </div>
                    <div class="card-footer py-2 d-flex gap-2">
                        <button class="btn btn-sm btn-outline-secondary flex-grow-1 btn_ver_detalle" data-id="${pedido.id}">
                            <i class="fas fa-eye"></i> Ver
                        </button>
                        ${botonesHtml}
                    </div>
                </div>
            </div>
        `;
  });

  $(contenedor).html(html);
}

function obtenerMensajeVacio(estado) {
  switch (estado) {
    case 1:
      return { icono: "fa-inbox", texto: "No hay pedidos pendientes" };
    case 2:
      return { icono: "fa-fire", texto: "No hay pedidos en preparación" };
    case 3:
      return { icono: "fa-times-circle", texto: "No hay pedidos rechazados" };
    case 4:
      return { icono: "fa-check-circle", texto: "No hay pedidos entregados hoy" };
    default:
      return { icono: "fa-box", texto: "No hay pedidos" };
  }
}

function obtenerBotonesPedido(pedido, estado) {
  let btnImprimir = `
        <button class="btn btn-sm btn-outline-dark btn_imprimir_rapido" data-id="${pedido.id}" title="Imprimir comanda">
            <i class="fas fa-print"></i>
        </button>
    `;

  switch (estado) {
    case 1: // Pendiente
      return `
                ${btnImprimir}
                <button class="btn btn-sm btn-danger btn_rechazar_pedido" data-id="${pedido.id}">
                    <i class="fas fa-times"></i>
                </button>
                <button class="btn btn-sm btn-success btn_aceptar_pedido" data-id="${pedido.id}">
                    <i class="fas fa-check"></i> Aceptar
                </button>
            `;
    case 2: // En preparación
      return `
                ${btnImprimir}
                <button class="btn btn-sm btn-success btn_entregar_pedido flex-grow-1" data-id="${pedido.id}">
                    <i class="fas fa-check-double"></i> Entregar
                </button>
            `;
    case 3: // Rechazado
    case 4: // Entregado
      return btnImprimir;
    default:
      return "";
  }
}

function formatearTiempo(minutos) {
  if (minutos < 1) return "Ahora";
  if (minutos < 60) return `${minutos} min`;
  let horas = Math.floor(minutos / 60);
  let mins = minutos % 60;
  return `${horas}h ${mins}m`;
}

// ========== VER DETALLE DEL PEDIDO ==========
$(document).on("click", ".btn_ver_detalle", function () {
  let id_pedido = $(this).data("id");
  cargarDetallePedido(id_pedido);
});

function cargarDetallePedido(id_pedido) {
  $("#detalle_numero_pedido").text(id_pedido);
  $("#detalle_pedido_contenido").html(`
        <div class="text-center py-4">
            <div class="spinner-border text-primary" role="status"></div>
            <p class="mt-2 text-muted">Cargando detalle...</p>
        </div>
    `);
  $("#detalle_pedido_acciones").html("");
  $("#modal_detalle_pedido").modal("show");

  $.ajax({
    url: url + "views/ajax/ajax_cafeteria_pedidos.php",
    method: "GET",
    data: {
      obtenerDetallePedido: true,
      id_pedido: id_pedido,
    },
    success: function (response) {
      response = JSON.parse(response);

      if (response.error) {
        $("#detalle_pedido_contenido").html(
          `<div class="alert alert-danger">${response.message}</div>`
        );
        return;
      }

      renderizarDetallePedido(response.pedido, response.items);
    },
  });
}

function renderizarDetallePedido(pedido, items) {
  let estadoBadge = obtenerBadgeEstado(pedido.estado_pedido);

  let html = `
        <div class="row mb-3">
            <div class="col-md-6">
                <p class="mb-1"><strong>Cliente:</strong> ${pedido.nombre_cliente}</p>
                ${pedido.telefono_cliente ? `<p class="mb-1"><strong>Teléfono:</strong> ${pedido.telefono_cliente}</p>` : ""}
                ${pedido.correo_cliente ? `<p class="mb-1"><strong>Correo:</strong> ${pedido.correo_cliente}</p>` : ""}
            </div>
            <div class="col-md-6 text-md-end">
                <p class="mb-1"><strong>Fecha:</strong> ${pedido.fecha_alta}</p>
                <p class="mb-1"><strong>Estado:</strong> ${estadoBadge}</p>
                <p class="mb-1"><strong>Tiempo:</strong> ${formatearTiempo(pedido.minutos_transcurridos)}</p>
            </div>
        </div>

        <hr>

        <h6 class="mb-3"><i class="fas fa-shopping-bag me-2"></i>Productos del pedido</h6>
    `;

  items.forEach((item) => {
    let tamanoText = item.nombre_tamano
      ? `<span class="badge bg-secondary">${item.medida} ${item.unidad_medida}</span>`
      : "";

    let ingredientesHtml = "";
    if (item.ingredientes && item.ingredientes.length > 0) {
      ingredientesHtml = '<div class="mt-2"><small class="text-muted">Ingredientes:</small><br>';
      item.ingredientes.forEach((ing) => {
        let extraClass = ing.costo_extra == 1 ? "ingrediente-extra" : "";
        let extraText = ing.monto_total > 0 ? ` (+$${parseFloat(ing.monto_total).toFixed(2)})` : "";
        ingredientesHtml += `<span class="ingrediente-tag ${extraClass}">${ing.cantidad}x ${ing.nombre_ingrediente}${extraText}</span>`;
      });
      ingredientesHtml += "</div>";
    }

    html += `
            <div class="producto-item">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <strong>${item.cantidad}x ${item.nombre_producto}</strong> ${tamanoText}
                        ${ingredientesHtml}
                    </div>
                    <div class="text-end">
                        <span class="fw-bold">$${parseFloat(item.monto_total).toFixed(2)}</span>
                    </div>
                </div>
            </div>
        `;
  });

  html += `
        <hr>
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Total:</h5>
            <h4 class="mb-0 text-success">$${parseFloat(pedido.monto_total).toFixed(2)}</h4>
        </div>
    `;

  if (pedido.estado_pedido == 3 && pedido.motivo_rechazo) {
    html += `
            <div class="alert alert-danger mt-3 mb-0">
                <strong><i class="fas fa-times-circle me-2"></i>Motivo de rechazo:</strong><br>
                ${pedido.motivo_rechazo}
            </div>
        `;
  }

  $("#detalle_pedido_contenido").html(html);

  // Guardar datos para impresión
  window.pedidoActual = { pedido, items };

  // Botones de acción según estado
  let accionesHtml = `
        <button class="btn btn-outline-secondary btn_imprimir_ticket" data-id="${pedido.id}">
            <i class="fas fa-print me-1"></i> Imprimir
        </button>
    `;
  
  if (pedido.estado_pedido == 1) {
    accionesHtml += `
            <button class="btn btn-danger btn_rechazar_pedido" data-id="${pedido.id}">
                <i class="fas fa-times me-1"></i> Rechazar
            </button>
            <button class="btn btn-success btn_aceptar_pedido" data-id="${pedido.id}">
                <i class="fas fa-check me-1"></i> Aceptar pedido
            </button>
        `;
  } else if (pedido.estado_pedido == 2) {
    accionesHtml += `
            <button class="btn btn-success btn_entregar_pedido" data-id="${pedido.id}">
                <i class="fas fa-check-double me-1"></i> Marcar como entregado
            </button>
        `;
  }

  $("#detalle_pedido_acciones").html(accionesHtml);
}

function obtenerBadgeEstado(estado) {
  switch (parseInt(estado)) {
    case 1:
      return '<span class="badge bg-warning text-dark">Pendiente</span>';
    case 2:
      return '<span class="badge bg-primary">En preparación</span>';
    case 3:
      return '<span class="badge bg-danger">Rechazado</span>';
    case 4:
      return '<span class="badge bg-success">Entregado</span>';
    case 5:
      return '<span class="badge bg-secondary">Cancelado</span>';
    default:
      return '<span class="badge bg-secondary">Desconocido</span>';
  }
}

// ========== ACCIONES SOBRE PEDIDOS ==========

// Aceptar pedido
$(document).on("click", ".btn_aceptar_pedido", function () {
  let id_pedido = $(this).data("id");
  let btn = $(this);

  btn.prop("disabled", true).html('<i class="fas fa-spinner fa-spin"></i>');

  $.ajax({
    url: url + "views/ajax/ajax_cafeteria_pedidos.php",
    method: "POST",
    data: {
      aceptarPedido: true,
      id_pedido: id_pedido,
    },
    success: function (response) {
      response = JSON.parse(response);

      if (response.success) {
        swal("¡Aceptado!", response.message, "success");
        $("#modal_detalle_pedido").modal("hide");
        cargarPedidos();
      } else {
        swal("Error", response.message, "error");
        btn.prop("disabled", false).html('<i class="fas fa-check"></i> Aceptar');
      }
    },
  });
});

// Abrir modal de rechazo
$(document).on("click", ".btn_rechazar_pedido", function () {
  let id_pedido = $(this).data("id");
  $("#rechazar_id_pedido").val(id_pedido);
  $("#motivo_rechazo").val("");
  $("#modal_detalle_pedido").modal("hide");
  $("#modal_rechazar_pedido").modal("show");
});

// Confirmar rechazo
$(document).on("click", "#btn_confirmar_rechazo", function () {
  let id_pedido = $("#rechazar_id_pedido").val();
  let motivo = $("#motivo_rechazo").val().trim();

  if (!motivo) {
    swal("Error", "Debe proporcionar un motivo de rechazo", "warning");
    return;
  }

  let btn = $(this);
  btn.prop("disabled", true).html('<i class="fas fa-spinner fa-spin"></i> Procesando...');

  $.ajax({
    url: url + "views/ajax/ajax_cafeteria_pedidos.php",
    method: "POST",
    data: {
      rechazarPedido: true,
      id_pedido: id_pedido,
      motivo: motivo,
    },
    success: function (response) {
      response = JSON.parse(response);

      if (response.success) {
        swal("Rechazado", response.message, "info");
        $("#modal_rechazar_pedido").modal("hide");
        cargarPedidos();
      } else {
        swal("Error", response.message, "error");
      }

      btn.prop("disabled", false).html('<i class="fas fa-times me-1"></i> Confirmar rechazo');
    },
  });
});

// Entregar pedido
$(document).on("click", ".btn_entregar_pedido", function () {
  let id_pedido = $(this).data("id");
  let btn = $(this);

  swal({
    title: "¿Entregar pedido?",
    text: "Se marcará como entregado al cliente",
    icon: "info",
    buttons: ["Cancelar", "Sí, entregar"],
  }).then((confirmar) => {
    if (confirmar) {
      btn.prop("disabled", true).html('<i class="fas fa-spinner fa-spin"></i>');

      $.ajax({
        url: url + "views/ajax/ajax_cafeteria_pedidos.php",
        method: "POST",
        data: {
          entregarPedido: true,
          id_pedido: id_pedido,
        },
        success: function (response) {
          response = JSON.parse(response);

          if (response.success) {
            swal("¡Entregado!", response.message, "success");
            $("#modal_detalle_pedido").modal("hide");
            cargarPedidos();
          } else {
            swal("Error", response.message, "error");
            btn.prop("disabled", false).html('<i class="fas fa-check-double"></i> Entregar');
          }
        },
      });
    }
  });
});

// ========== IMPRESIÓN DE TICKET/COMANDA ==========

// Imprimir desde el modal de detalle
$(document).on("click", ".btn_imprimir_ticket", function () {
  if (!window.pedidoActual) {
    swal("Error", "No hay datos del pedido para imprimir", "error");
    return;
  }
  imprimirTicket(window.pedidoActual.pedido, window.pedidoActual.items);
});

// Impresión rápida desde la tarjeta
$(document).on("click", ".btn_imprimir_rapido", function (e) {
  e.stopPropagation(); // Evitar que se abra el modal
  let id_pedido = $(this).data("id");
  let btn = $(this);

  btn.prop("disabled", true).html('<i class="fas fa-spinner fa-spin"></i>');

  // Cargar datos del pedido y luego imprimir
  $.ajax({
    url: url + "views/ajax/ajax_cafeteria_pedidos.php",
    method: "GET",
    data: {
      obtenerDetallePedido: true,
      id_pedido: id_pedido,
    },
    success: function (response) {
      response = JSON.parse(response);

      if (response.error) {
        swal("Error", response.message, "error");
      } else {
        imprimirTicket(response.pedido, response.items);
      }

      btn.prop("disabled", false).html('<i class="fas fa-print"></i>');
    },
    error: function () {
      swal("Error", "No se pudo cargar el pedido", "error");
      btn.prop("disabled", false).html('<i class="fas fa-print"></i>');
    },
  });
});

function imprimirTicket(pedido, items) {
  // Generar contenido del ticket
  let ticketHtml = generarTicketHtml(pedido, items);

  // Abrir ventana de impresión
  let ventanaImpresion = window.open("", "_blank", "width=350,height=600");
  
  ventanaImpresion.document.write(`
    <!DOCTYPE html>
    <html>
    <head>
      <title>Ticket #${pedido.id}</title>
      <style>
        @page {
          size: 80mm auto;
          margin: 0;
        }
        * {
          margin: 0;
          padding: 0;
          box-sizing: border-box;
        }
        body {
          font-family: 'Courier New', monospace;
          font-size: 12px;
          width: 80mm;
          padding: 5mm;
          background: white;
        }
        .ticket-header {
          text-align: center;
          border-bottom: 2px dashed #000;
          padding-bottom: 10px;
          margin-bottom: 10px;
        }
        .ticket-header h1 {
          font-size: 18px;
          margin-bottom: 5px;
        }
        .ticket-header .pedido-numero {
          font-size: 28px;
          font-weight: bold;
          background: #000;
          color: #fff;
          padding: 5px 15px;
          display: inline-block;
          margin: 10px 0;
        }
        .info-cliente {
          border-bottom: 1px dashed #000;
          padding-bottom: 8px;
          margin-bottom: 8px;
        }
        .info-cliente p {
          margin: 3px 0;
        }
        .productos {
          margin: 10px 0;
        }
        .producto {
          border-bottom: 1px dotted #ccc;
          padding: 8px 0;
        }
        .producto:last-child {
          border-bottom: none;
        }
        .producto-nombre {
          font-weight: bold;
          font-size: 14px;
        }
        .producto-cantidad {
          font-size: 16px;
          font-weight: bold;
        }
        .producto-tamano {
          background: #eee;
          padding: 2px 6px;
          font-size: 11px;
          display: inline-block;
          margin-left: 5px;
        }
        .ingredientes {
          margin-top: 5px;
          padding-left: 15px;
          font-size: 11px;
        }
        .ingrediente {
          margin: 2px 0;
        }
        .ingrediente-extra {
          font-weight: bold;
        }
        .total-section {
          border-top: 2px dashed #000;
          margin-top: 10px;
          padding-top: 10px;
          text-align: right;
        }
        .total {
          font-size: 18px;
          font-weight: bold;
        }
        .footer {
          text-align: center;
          margin-top: 15px;
          padding-top: 10px;
          border-top: 1px dashed #000;
          font-size: 10px;
        }
        .fecha-hora {
          margin-top: 5px;
        }
        .linea-corte {
          border-top: 1px dashed #999;
          margin: 15px 0;
          position: relative;
        }
        .linea-corte::before {
          content: "✂";
          position: absolute;
          top: -8px;
          left: 0;
          background: white;
          padding-right: 5px;
        }
        @media print {
          body {
            width: 80mm;
          }
          .no-print {
            display: none !important;
          }
        }
        .btn-imprimir {
          display: block;
          width: 100%;
          padding: 10px;
          margin-top: 15px;
          background: #28a745;
          color: white;
          border: none;
          cursor: pointer;
          font-size: 14px;
        }
        .btn-imprimir:hover {
          background: #218838;
        }
      </style>
    </head>
    <body>
      ${ticketHtml}
      <button class="btn-imprimir no-print" onclick="window.print();">
        🖨️ IMPRIMIR TICKET
      </button>
    </body>
    </html>
  `);

  ventanaImpresion.document.close();
}

function generarTicketHtml(pedido, items) {
  let fechaHora = new Date(pedido.fecha_alta).toLocaleString("es-MX", {
    day: "2-digit",
    month: "2-digit",
    year: "numeric",
    hour: "2-digit",
    minute: "2-digit",
  });

  let productosHtml = "";

  items.forEach((item) => {
    let tamanoHtml = item.nombre_tamano
      ? `<span class="producto-tamano">${item.medida} ${item.unidad_medida}</span>`
      : "";

    let ingredientesHtml = "";
    if (item.ingredientes && item.ingredientes.length > 0) {
      ingredientesHtml = '<div class="ingredientes">';
      item.ingredientes.forEach((ing) => {
        let extraClass = ing.costo_extra == 1 ? "ingrediente-extra" : "";
        let extraText = ing.monto_total > 0 ? ` (+$${parseFloat(ing.monto_total).toFixed(2)})` : "";
        ingredientesHtml += `<div class="ingrediente ${extraClass}">• ${ing.cantidad}x ${ing.nombre_ingrediente}${extraText}</div>`;
      });
      ingredientesHtml += "</div>";
    }

    productosHtml += `
      <div class="producto">
        <div>
          <span class="producto-cantidad">${item.cantidad}x</span>
          <span class="producto-nombre">${item.nombre_producto}</span>
          ${tamanoHtml}
        </div>
        ${ingredientesHtml}
        <div style="text-align: right; margin-top: 3px;">
          <strong>$${parseFloat(item.monto_total).toFixed(2)}</strong>
        </div>
      </div>
    `;
  });

  return `
    <div class="ticket-header">
      <h1>${pedido.nombre_cafeteria || "CAFETERÍA"}</h1>
      <div>*** COMANDA ***</div>
      <div class="pedido-numero">#${pedido.id}</div>
    </div>

    <div class="info-cliente">
      <p><strong>Cliente:</strong> ${pedido.nombre_cliente}</p>
      ${pedido.telefono_cliente ? `<p><strong>Tel:</strong> ${pedido.telefono_cliente}</p>` : ""}
      <p class="fecha-hora"><strong>Fecha:</strong> ${fechaHora}</p>
    </div>

    <div class="productos">
      <div style="text-align: center; margin-bottom: 5px; font-weight: bold;">
        ═══════ PRODUCTOS ═══════
      </div>
      ${productosHtml}
    </div>

    <div class="total-section">
      <span>TOTAL: </span>
      <span class="total">$${parseFloat(pedido.monto_total).toFixed(2)}</span>
    </div>

    <div class="linea-corte"></div>

    <div class="footer">
      <div>¡Gracias por su preferencia!</div>
      <div style="margin-top: 5px; font-size: 9px;">
        Impreso: ${new Date().toLocaleString("es-MX")}
      </div>
    </div>
  `;
}

