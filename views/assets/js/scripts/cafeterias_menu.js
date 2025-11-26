const offcanvasEl = document.getElementById("offcanvasProducto");
const offcanvas = new bootstrap.Offcanvas(offcanvasEl);
$(document).on("click", ".addProducto", function () {
  offcanvas.show();
  const id_producto = $(this).attr("id-producto");

  $("#offcanvasProducto").attr("producto-id", id_producto);

  cargarIngredientesProducto(id_producto);
});

let precioBase = 0;
let extrasTotal = 0;
let cantidadProducto = 1;

function cargarIngredientesProducto(id_producto) {
  let cafeteria = $("#id_cafeteria").val();
  let filtro = `?obtenerProducto=${true}&id_producto=${id_producto}&id_cafeteria=${cafeteria}`;

  $.ajax({
    url: url + "views/ajax/ajax_cafeterias_menu.php" + filtro,
    method: "GET",
    success: function (response) {
      response = JSON.parse(response);

      // ==============================
      // 1. LLENAR DATOS DEL PRODUCTO
      // ==============================
      $("#offcanvasProductoLabel").text(response.producto.nombre);
      $("#nombre").text(response.producto.nombre);
      $("#prod_descripcion").text(response.producto.descripcion || "");
      $("#prod_imagen").attr("src", url + response.producto.imagen);

      // ==============================
      // 2. GENERAR OPCIONES DEL MENÚ
      // ==============================
      let htmlOpciones = "";

      if (response.tamanos.length > 0) {
        // ===== TAMAÑOS =====
        htmlOpciones += `
              <div class="opcion-producto mb-4" <div class="opcion-producto mb-4" data-tipo="tamano" data-obligatorio="1">
                  <div class="d-flex justify-content-between align-items-center">
                      <h6 class="mb-1"><b>Elige el tamaño</b></h6>
                      <span class="badge bg-dark rounded-pill">Obligatorio</span>
                  </div>
                  <small class="text-muted mb-2 d-block">Selecciona 1</small>
              <div class="list-group">
              `;

        response.tamanos.forEach((item) => {
          htmlOpciones += `
            <label class="list-group-item d-flex justify-content-between align-items-center">
                <span>${item.nombre} ${item.unidad_medida} <span class="badge bg-warning text-dark ms-2">MX$${item.precio}</span></span>
                <input class="form-check-input radio-tamano" type="radio" name="tamano" data-precio="${item.precio}" data-id="${item.id}">
            </label>
            `;
        });

        htmlOpciones += `</div></div>`;
      }

      // ===== INGREDIENTES =====
      response.ingredientes.forEach((cat) => {
        let obligatorio =
          cat.obligatoria === "Si"
            ? ` <span class="badge bg-dark rounded-pill">Obligatorio</span>`
            : "";

        htmlOpciones += `
            <div class="opcion-producto mb-4" data-tipo="ingredientes" data-obligatorio="${
              cat.obligatoria === "Si" ? "1" : "0"
            }">
              <div class="d-flex justify-content-between align-items-center">
                <h6><b>${cat.nombre}</b></h6>
                ${obligatorio}
              </div>
              <small class="text-muted d-block mb-2">Selecciona 1</small>
            <div class="list-group">
          `;

        cat.ingredientes.forEach((item) => {
          let extra =
            item.costo_extra === "Si"
              ? `<span class="badge bg-warning text-dark ms-2">+MX$${item.precio}</span>`
              : "";

          if (item.costo_extra === "Si") {
            htmlOpciones += `
            <label class="list-group-item d-flex justify-content-between align-items-center">
                <span>${item.nombre} ${extra}</span>
                <div class="d-flex align-items-center gap-2">
                    <button class="btn btn-outline-secondary btn_extra_minus" data-id="${item.id}" data-precio="${item.precio}" type="button">-</button>
                    <span class="extra_cantidad" data-id="${item.id}" data-precio="${item.precio}">0</span>
                    <button class="btn btn-outline-secondary btn_extra_plus" data-id="${item.id}" data-precio="${item.precio}" type="button">+</button>
                </div>
            </label>`;
          } else {
            htmlOpciones += `
            <label class="list-group-item d-flex justify-content-between align-items-center">
                <span>${item.nombre}</span>
                <input class="form-check-input radio-ingrediente" type="radio" name="ingred_${cat.id}" data-id="${item.id}" data-precio="0">
            </label>`;
          }
        });

        htmlOpciones += `</div></div>`;
      });

      // Insertar todo
      $("#options").html(htmlOpciones);

      // ==============================
      // SI NO HAY TAMAÑOS, USAR PRECIO BASE DEL PRODUCTO
      // ==============================
      if (response.tamanos.length === 0) {
        precioBase = parseFloat(response.producto.precio);
        actualizarTotal();
      }
    },
  });
}

function actualizarTotal() {
  const total = (precioBase + extrasTotal) * cantidadProducto;
  $("#btn_agregar").text(`Agregar · MX$${total}`);
}

$(document).on("change", ".radio-tamano", function () {
  precioBase = parseFloat($(this).data("precio"));
  actualizarTotal();
});

$(document).on("click", ".btn_extra_plus", function () {
  const id = $(this).data("id");
  const precio = parseFloat($(this).data("precio"));

  let span = $(`.extra_cantidad[data-id='${id}']`);
  let cant = parseInt(span.text()) + 1;
  span.text(cant);

  extrasTotal += precio;
  actualizarTotal();
});

$(document).on("click", ".btn_extra_minus", function () {
  const id = $(this).data("id");
  const precio = parseFloat($(this).data("precio"));

  let span = $(`.extra_cantidad[data-id='${id}']`);
  let cant = parseInt(span.text());

  if (cant > 0) {
    cant--;
    span.text(cant);
    extrasTotal -= precio;
    actualizarTotal();
  }
});

$("#btn_sumar").click(function () {
  cantidadProducto++;
  $(".form-control.text-center").val(cantidadProducto);
  actualizarTotal();
});

$("#btn_restar").click(function () {
  if (cantidadProducto > 1) {
    cantidadProducto--;
    $(".form-control.text-center").val(cantidadProducto);
    actualizarTotal();
  }
});

$(document).on("click", "#btn_agregar", function () {
  agregarProductoCarrito();
});

function agregarProductoCarrito(eliminarOtroCarrito = "No") {
  const v = validarOpciones();
  if (!v.valido) {
    swal("¡Atención!", v.mensaje, "warning");
    return;
  }

  let idProducto = $("#offcanvasProducto").attr("producto-id");
  let idCafeteria = $("#id_cafeteria").val();
  let ingredientes = [];

  let tamano = $(".radio-tamano:checked").data("id");

  $(".radio-ingrediente:checked").each(function () {
    ingredientes.push({
      id: $(this).data("id"),
      cantidad: 1,
    });
  });

  $(".extra_cantidad").each(function () {
    const cant = parseInt($(this).text());
    if (cant > 0) {
      ingredientes.push({
        id: $(this).data("id"),
        cantidad: cant,
      });
    }
  });

  var datos = new FormData();
  datos.append("agregar_producto", true);
  datos.append("id_cafeteria", idCafeteria);
  datos.append("id_producto", idProducto);
  datos.append("cantidad", cantidadProducto);
  datos.append("tamano", tamano);
  datos.append("ingredientes", JSON.stringify(ingredientes));
  datos.append("eliminarOtroCarrito", eliminarOtroCarrito);

  $.ajax({
    url: url + "views/ajax/ajax_cafeterias_menu.php",
    method: "POST",
    data: datos,
    cache: false,
    contentType: false,
    processData: false,
    beforeSend: function () {
      cargaSistema(true);
    },
    success: function (respuesta) {
      cargaSistema(false);

      try {
        respuesta = JSON.parse(respuesta);
      } catch (e) {}

      if (respuesta.error) {
        if (respuesta.error === "otroCarrito") {
          swal({
            title: "¡Confirmación!",
            text: respuesta.message,
            icon: "warning",
            buttons: {
              cancel: "Cancelar",
              confirm: "Sí, eliminar",
            },
            dangerMode: true,
          }).then((willDelete) => {
            if (willDelete) {
              agregarProductoCarrito("Si");
            } else {
            }
          });
          return;
        }
        swal("Error", respuesta.message, "error");
        return;
      }

      swal("Perfecto", "Producto agregado al carrito", "success");
      offcanvas.hide();
      $("#btn_agregar").text(`Agregar · MX$ 0`);
      actualizarContadorCarrito();
    },
  });
}

function validarOpciones() {
  let valido = true;
  let mensaje = "";

  $(".opcion-producto[data-obligatorio='1']").each(function () {
    const tipo = $(this).data("tipo");
    const titulo = $(this).find("h6 b").text();

    // ==============================
    // VALIDAR TAMAÑO
    // ==============================
    if (tipo === "tamano") {
      if ($(this).find("input[type='radio']:checked").length === 0) {
        valido = false;
        mensaje = `Debes elegir un tamaño.`;
        return false;
      }
    }

    // ==============================
    // VALIDAR INGREDIENTES
    // ==============================
    if (tipo === "ingredientes") {
      const tieneRadios = $(this).find("input[type='radio']").length > 0;
      const tieneCantidad = $(this).find(".extra_cantidad").length > 0;

      // ---- Caso: categoría con radios ----
      if (tieneRadios) {
        if ($(this).find("input[type='radio']:checked").length === 0) {
          valido = false;
          mensaje = `Debes seleccionar una opción de "${titulo}".`;
          return false;
        }
      }

      // ---- Caso: categoría con cantidades ----
      if (tieneCantidad) {
        let suma = 0;
        $(this)
          .find(".extra_cantidad")
          .each(function () {
            suma += parseInt($(this).text());
          });

        if (suma === 0) {
          valido = false;
          mensaje = `Debes seleccionar al menos una unidad en "${titulo}".`;
          return false;
        }
      }
    }
  });

  return { valido, mensaje };
}
