$(document).ready(function () {
  actualizarContadorCarrito();
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
  const offcanvas = new bootstrap.Offcanvas(offcanvasEl);
  offcanvas.show();

  buscarCarrito();
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
