$(document).ready(function () {
  if (nivelUsuario == 'Cliente' || nivelUsuario == 'Barista') {
    actualizarContadorCarrito();
  }
});

function actualizarContadorCarrito() {
  let cantidad;
  const badges = document.querySelectorAll(".contador-carrito");

  $.ajax({
    url: url + "views/ajax/ajax_carrito.php",
    method: "POST",
    data: { obtener_carrito: true },
    success: function (respuesta) {
      respuesta = JSON.parse(respuesta);
      cantidad = respuesta.cantidad;

      badges.forEach((badge) => {
        if (cantidad > 0) {
          badge.innerText = cantidad;
          badge.style.display = "block";
        } else {
          badge.style.display = "none";
        }
      });
    },
  });
}

$(document).on("click", "#ver-carrito", function () {
  const offcanvasEl = document.getElementById("offcanvasCarrito");
  
  // Cerrar cualquier otro offcanvas abierto (como el del menú) antes de abrir el carrito
  const offcanvasMenu = document.getElementById("offcanvasMenuCafeteria");
  if (offcanvasMenu) {
    const bsOffcanvasMenu = bootstrap.Offcanvas.getInstance(offcanvasMenu);
    if (bsOffcanvasMenu) {
      bsOffcanvasMenu.hide();
    }
  }
  
  // Asegurar que el z-index del carrito sea mayor
  $(offcanvasEl).css('z-index', '1075');
  
  const offcanvas = new bootstrap.Offcanvas(offcanvasEl);
  offcanvas.show();

  // Ocultar botón de filtros si estamos en el módulo de mapa
  if (moduloActual == 'cafeterias_mapa') {
    $("#btn_filtro_mapa").hide();
  }

  buscarCarrito();
  
  // Mostrar botón de filtros cuando se cierre el carrito (solo en módulo de mapa)
  if (moduloActual == 'cafeterias_mapa') {
    offcanvasEl.addEventListener('hidden.bs.offcanvas', function () {
      $("#btn_filtro_mapa").show();
    }, { once: true });
  }
});

function buscarCarrito() {
  let cafeteria = $("#id_carrito_cafeteria").val();

  $.ajax({
    url:
      url +
      "views/ajax/ajax_carrito.php?abrirCarrito=true&id_cafeteria=" +
      cafeteria,
    method: "GET",
    success: function (response) {
      response = JSON.parse(response);
      $("#lista_productos_carrito").html("");
      if (response.error) {
        $("#carrito_vacio").show();
        $("#cafe_imagen").hide();
      } else {
        $("#carrito_vacio").hide();
        $("#cafe_imagen").show();
      }

      // 1. Cargar imagen de la cafetería
      if (response.carrito.imagen_cafeteria) {
        $("#cafe_imagen").attr("src", url + response.carrito.imagen_cafeteria);
      }

      // 2. Renderizar productos
      renderProductosCarrito(response.productos);

      // 3. Opcional: total real
      actualizarTotalCarrito(response.productos);

      actualizarContadorCarrito();
    },
  });
}

function renderProductosCarrito(productos) {
  let html = "";

  productos.forEach((item) => {
    // Precio base del producto
    let precioBase = parseFloat(item.precio);

    // Suma del costo de ingredientes
    let precioIngredientes = 0;
    if (item.ingredientes && item.ingredientes.length > 0) {
      item.ingredientes.forEach((ing) => {
        precioIngredientes += parseFloat(ing.total || 0);
      });
    }

    // Precio final por una unidad del producto
    let precioUnitario = precioBase + precioIngredientes;

    // Precio total por cantidad
    let totalItem = precioUnitario * item.cantidad;

    let ingredientesText = item.ingredientes.map((x) => x.nombre).join(", ");

    let botonRestar = `
            <button class="btn btn-outline-secondary btn-restar" data-id="${
              item.id_item
            }" type="button">
                ${
                  item.cantidad == 1
                    ? '<i class="fas fa-trash text-dark" style="font-size:13px;"></i>'
                    : "-"
                }
            </button>
        `;

    let tamano =
      item.tamano !== null ? item.medida + " " + item.unidad_medida + ", " : "";

    html += `
            <div class="product-item row mb-2 pb-2 border-bottom">
                
                <div class="col-2 align-top">
                   <img src="${url + item.imagen}" class="product-image" alt="">
                </div>
                
                <div class="col-10 row">
                    <div class="col-12">
                        <span class="product-name fw-bold">${
                          item.nombre
                        }</span><br>
                    </div>
                     <div class="col-12">
                        <small>${tamano + ingredientesText}</small>
                    </div>


                    <div class="col-12 px-3 py-2 bg-white d-flex justify-content-between">

                        <!-- PRECIO DINÁMICO -->
                        <span class="fw-bold mt-2">MX$${totalItem}</span>

                        <!-- Controles -->
                        <div class="input-group" style="width: 150px;">
                           ${botonRestar}
                            
                            <input class="form-control text-center cantidad" 
                                data-id="${item.id_item}" 
                                value="${item.cantidad}" readonly>

                            <button class="btn btn-outline-secondary btn-sumar" data-id="${
                              item.id_item
                            }" type="button">+</button>
                        </div>
                    </div>
                </div>

            </div>
        `;
  });

  $("#lista_productos_carrito").html(html);
}

function actualizarTotalCarrito(productos) {
  let total = 0;

  productos.forEach((item) => {
    let precioBase = parseFloat(item.precio);
    let precioIngredientes = 0;

    if (item.ingredientes && item.ingredientes.length > 0) {
      item.ingredientes.forEach((ing) => {
        precioIngredientes += parseFloat(ing.total || 0);
      });
    }

    let precioUnitario = precioBase + precioIngredientes;

    total += precioUnitario * parseFloat(item.cantidad);
  });

  $("#btn_pagar").text(`Pagar $${total}`);
}

// RESTAR CANTIDAD
$(document).on("click", ".btn-restar", function () {
  let id = $(this).data("id");
  actualizarCantidadItem(id, -1);
});

// SUMAR CANTIDAD
$(document).on("click", ".btn-sumar", function () {
  let id = $(this).data("id");
  actualizarCantidadItem(id, +1);
});

function actualizarCantidadItem(id_item, cambio) {
  let input = $(`input.cantidad[data-id="${id_item}"]`);
  let cantidadActual = parseInt(input.val());
  let nuevaCantidad = cantidadActual + cambio;

  if (cantidadActual === 1 && cambio === -1) {
    $.ajax({
      url: url + "views/ajax/ajax_carrito.php",
      method: "POST",
      data: {
        eliminarItem: true,
        id_item: id_item,
      },
      success: function (resp) {
        if (resp === "success") {
          buscarCarrito();
          actualizarContadorCarrito();
        } else {
          swal("Error", "No se pudo eliminar el producto", "error");
        }
      },
    });

    return;
  }

  input.prop("disabled", true);
  input.css("opacity", "0.5");

  $.ajax({
    url: url + "views/ajax/ajax_carrito.php",
    method: "POST",
    data: {
      actualizarCantidad: true,
      id_item,
      cantidad: nuevaCantidad,
    },
    success: function (resp) {
      input.prop("disabled", false).css("opacity", "1");

      if (resp === "success") {
        buscarCarrito();
      } else {
        swal("Error", "No se pudo actualizar el carrito", "error");
      }
    },
  });
}

// ========== FUNCIONALIDAD BOTÓN PAGAR ==========

// Click en botón pagar - redirige a página de método de pago
$(document).on("click", "#btn_pagar", function () {
  // Verificar que el carrito no esté vacío
  let totalText = $(this).text();
  let total = parseFloat(totalText.replace(/[^0-9.]/g, ""));

  if (total <= 0) {
    swal("Carrito vacío", "Agrega productos antes de continuar", "warning");
    return;
  }

  // Cerrar el offcanvas del carrito
  const offcanvasEl = document.getElementById("offcanvasCarrito");
  const offcanvas = bootstrap.Offcanvas.getInstance(offcanvasEl);
  if (offcanvas) {
    offcanvas.hide();
  }

  // Redirigir a página de método de pago
  window.location.href = url + "metodo_pago";
});

// ========== FUNCIONES PARA PÁGINA DE MÉTODO DE PAGO ==========

// Cargar resumen cuando se está en la página de método de pago
$(document).ready(function () {
  if (moduloActual === "metodo_pago") {
    cargarResumenPago();
  }
});

function cargarResumenPago() {
  $.ajax({
    url: url + "views/ajax/ajax_carrito.php?resumenPago=true",
    method: "GET",
    success: function (response) {
      response = JSON.parse(response);

      if (response.error) {
        $("#resumen_pedido").html(`
          <div class="text-center py-3">
            <i class="fas fa-exclamation-circle fa-2x text-warning"></i>
            <p class="mb-0 mt-2">${response.message}</p>
          </div>
        `);
        $("#btn_confirmar_pago").prop("disabled", true);
        return;
      }

      // Renderizar resumen
      let html = `
        <div class="mb-2">
          <strong><i class="fas fa-store me-2"></i>${response.carrito.nombre_cafeteria}</strong>
        </div>
        <hr class="my-2">
      `;

      response.productos.forEach((producto) => {
        html += `
          <div class="d-flex justify-content-between mb-1">
            <span>${producto.cantidad}x ${producto.nombre}</span>
            <span>$${producto.subtotal.toFixed(2)}</span>
          </div>
        `;
      });

      $("#resumen_pedido").html(html);
      $("#total_pagar").text("$" + response.total.toFixed(2));
    },
    error: function () {
      $("#resumen_pedido").html(`
        <div class="text-center py-3 text-danger">
          <i class="fas fa-times-circle fa-2x"></i>
          <p class="mb-0 mt-2">Error al cargar el resumen</p>
        </div>
      `);
    },
  });
}

// Habilitar botón cuando se selecciona método de pago
$(document).on("change", 'input[name="metodo_pago"]', function () {
  $("#btn_confirmar_pago").prop("disabled", false);
});

// Procesar pago al hacer click en Aceptar
$(document).on("click", "#btn_confirmar_pago", function () {
  let metodoPago = $('input[name="metodo_pago"]:checked').val();

  if (!metodoPago) {
    swal("Error", "Selecciona un método de pago", "warning");
    return;
  }

  // Deshabilitar botón mientras procesa
  let btn = $(this);
  btn.prop("disabled", true);
  btn.html('<i class="fas fa-spinner fa-spin me-2"></i>Procesando...');

  $.ajax({
    url: url + "views/ajax/ajax_carrito.php",
    method: "POST",
    data: {
      procesarPago: true,
      metodo_pago: metodoPago,
    },
    success: function (response) {
      response = JSON.parse(response);

      if (response.error) {
        swal("Error", response.message, "error");
        btn.prop("disabled", false);
        btn.html('<i class="fas fa-check me-2"></i>Aceptar');
        return;
      }

      // Éxito
      swal({
        title: "¡Pedido realizado!",
        text: `Tu pedido #${response.id_venta} ha sido registrado por $${response.total.toFixed(2)}`,
        icon: "success",
        button: "Aceptar",
      }).then(() => {
        // Redirigir a lista de cafeterías o dashboard
        window.location.href = url + "mis_pedidos";
      });
    },
    error: function () {
      swal("Error", "Ocurrió un error al procesar el pago", "error");
      btn.prop("disabled", false);
      btn.html('<i class="fas fa-check me-2"></i>Aceptar');
    },
  });
});