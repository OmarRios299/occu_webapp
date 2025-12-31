// Evento para abrir el offcanvas de producto cuando se hace clic en un producto
// Usar delegación de eventos para que funcione con contenido cargado dinámicamente
$(document).on("click", ".addProducto", function () {
  const id_producto = $(this).attr("id-producto");
  
  if (!id_producto) {
    console.error("No se encontró el ID del producto");
    return;
  }
  
  // Obtener el offcanvas de producto (obtenerlo cada vez para asegurar que existe)
  const offcanvasEl = document.getElementById("offcanvasProducto");
  if (!offcanvasEl) {
    console.error("Offcanvas offcanvasProducto no encontrado");
    swal("¡Error!", "No se pudo abrir el producto. El componente no está disponible.", "error");
    return;
  }
  
  // Obtener o crear instancia del offcanvas
  let offcanvas = bootstrap.Offcanvas.getInstance(offcanvasEl);
  if (!offcanvas) {
    offcanvas = new bootstrap.Offcanvas(offcanvasEl);
  }
  
  // Establecer el ID del producto en el offcanvas
  $("#offcanvasProducto").attr("producto-id", id_producto);
  
  // Cargar los ingredientes del producto
  cargarIngredientesProducto(id_producto);
  
  // Abrir el offcanvas
  offcanvas.show();
});

let precioBase = 0;
let extrasTotal = 0;
let cantidadProducto = 1;

// Hacer la función global para que esté disponible desde otros archivos
window.cargarIngredientesProducto = function(id_producto) {
  // Obtener el ID de la cafetería desde diferentes fuentes
  let cafeteria = $("#id_cafeteria").val() || 
                  $("#id_cafeteria_menu_offcanvas").val() || 
                  $("#id_cafeteria_mapa").val() || 
                  $("#id_cafeteria_mapa_mobile").val();
  
  if (!cafeteria) {
    console.error("No se encontró el ID de la cafetería para cargar ingredientes.");
    swal("¡Error!", "No se pudo obtener la información de la cafetería para cargar el producto.", "error");
    return;
  }
  
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
              <div class="opcion-producto mb-4" data-tipo="tamano" data-obligatorio="1">
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

        // Verificar si hay ingredientes con cantidad_gratis en esta categoría
        const tieneIngredientesIncluidos = cat.ingredientes.some(item => parseInt(item.cantidad_gratis) > 0);
        
        htmlOpciones += `
            <div class="opcion-producto mb-4" data-tipo="ingredientes" data-obligatorio="${
              cat.obligatoria === "Si" ? "1" : "0"
            }" data-categoria-id="${cat.id}">
              <div class="d-flex justify-content-between align-items-center">
                <h6><b>${cat.nombre}</b></h6>
                ${obligatorio}
              </div>
              <small class="text-muted d-block mb-2">Selecciona 1 opción</small>
            <div class="list-group mb-3">
          `;

        // Primero mostrar los ingredientes como radio buttons (selección única)
        cat.ingredientes.forEach((item) => {
          const cantidadGratis = parseInt(item.cantidad_gratis) || 0;
          const precio = parseFloat(item.precio) || 0;
          const tieneIncluido = cantidadGratis > 0;
          
          let badgeIncluido = "";
          let badgePrecio = "";
          
          // if (tieneIncluido) {
          //   badgeIncluido = `<span class="badge bg-success ms-2">Incluido</span>`;
          // }
          
          // Si tiene precio y no tiene cantidad gratis, mostrar badge de precio
          if (precio > 0 && cantidadGratis === 0) {
            badgePrecio = `<span class="badge bg-warning text-dark ms-2">+MX$${precio.toFixed(2)}</span>`;
          }
          
          // Radio button para selección única
          // NUNCA marcar automáticamente - el cliente siempre debe seleccionar
          htmlOpciones += `
          <label class="list-group-item d-flex justify-content-between align-items-center">
              <span>${item.nombre} ${badgeIncluido} ${badgePrecio}</span>
              <input class="form-check-input radio-ingrediente-categoria" 
                  type="radio" 
                  name="ingred_${cat.id}" 
                  data-id="${item.id}" 
                  data-precio="${precio}"
                  data-cantidad-gratis="${cantidadGratis}"
                  data-categoria-id="${cat.id}">
          </label>
          `;
        });

        htmlOpciones += `</div>`;
        
        // Sección de extras (solo ingredientes que permiten múltiples porciones)
        // Un ingrediente permite múltiples porciones si:
        // 1. Tiene cantidad_gratis > 0 (viene incluido y puedes agregar más)
        // 2. O tiene costo_extra = 'Si' Y cantidad_gratis > 0 (puedes agregar porciones extra)
        // NO incluir ingredientes que solo tienen precio pero cantidad_gratis = 0 (selección única con costo)
        const ingredientesConExtras = cat.ingredientes.filter(item => {
          const cantidadGratis = parseInt(item.cantidad_gratis) || 0;
          const precio = parseFloat(item.precio) || 0;
          const costoExtra = item.costo_extra === "Si";
          
          // Solo mostrar si tiene precio Y (tiene cantidad_gratis > 0 O tiene costo_extra = 'Si' con cantidad_gratis > 0)
          return precio > 0 && cantidadGratis > 0;
        });
        
        if (ingredientesConExtras.length > 0) {
          htmlOpciones += `
            <div class="extras-categoria mb-2" data-categoria-id="${cat.id}">
              <small class="text-muted d-block mb-2"><b>Agregar extras (con costo adicional):</b></small>
              <div class="list-group">
          `;
          
          ingredientesConExtras.forEach((item) => {
            const precio = parseFloat(item.precio) || 0;
            const cantidadGratis = parseInt(item.cantidad_gratis) || 0;
            
            htmlOpciones += `
            <label class="list-group-item d-flex justify-content-between align-items-center">
                <span>${item.nombre} <span class="badge bg-warning text-dark ms-2">+MX$${precio.toFixed(2)}</span></span>
                <div class="d-flex align-items-center gap-2">
                    <button class="btn btn-sm btn-outline-secondary btn_extra_minus" 
                        data-id="${item.id}" 
                        data-precio="${precio}" 
                        data-cantidad-gratis="${cantidadGratis}"
                        data-categoria-id="${cat.id}"
                        type="button">-</button>
                    <span class="extra_cantidad" 
                        data-id="${item.id}" 
                        data-precio="${precio}"
                        data-cantidad-gratis="${cantidadGratis}"
                        data-categoria-id="${cat.id}">0</span>
                    <button class="btn btn-sm btn-outline-secondary btn_extra_plus" 
                        data-id="${item.id}" 
                        data-precio="${precio}"
                        data-cantidad-gratis="${cantidadGratis}"
                        data-categoria-id="${cat.id}"
                        type="button">+</button>
                </div>
            </label>
            `;
          });
          
          htmlOpciones += `</div></div>`;
        }
        
        htmlOpciones += `</div>`;
      });

      // Insertar todo
      $("#options").html(htmlOpciones);

      // ==============================
      // INICIALIZAR TOTAL
      // ==============================
      // NUNCA seleccionar automáticamente ingredientes - el cliente siempre debe elegir
      setTimeout(function() {
        actualizarTotal();
      }, 50);

      // ==============================
      // SI NO HAY TAMAÑOS, USAR PRECIO BASE DEL PRODUCTO
      // ==============================
      if (response.tamanos.length === 0) {
        precioBase = parseFloat(response.producto.precio) || 0;
      } else {
        precioBase = 0;
      }
      
      actualizarTotal();
    },
  });
};

function actualizarTotal() {
  // Recalcular extrasTotal
  extrasTotal = 0;
  
  // Calcular costo del ingrediente seleccionado (si tiene precio y no está incluido)
  $(".radio-ingrediente-categoria:checked").each(function() {
    const precio = parseFloat($(this).data("precio")) || 0;
    const cantidadGratis = parseInt($(this).data("cantidad-gratis")) || 0;
    const categoriaId = $(this).data("categoria-id");
    const idIngrediente = $(this).data("id");
    
    // Si tiene precio y no tiene cantidad gratis, se cobra desde el inicio
    if (precio > 0 && cantidadGratis === 0) {
      extrasTotal += precio;
    }
    
    // Si tiene cantidad gratis y precio, verificar si hay extras en la sección de extras
    if (cantidadGratis > 0 && precio > 0) {
      const extraCantidadSpan = $(`.extras-categoria[data-categoria-id='${categoriaId}'] .extra_cantidad[data-id='${idIngrediente}']`);
      if (extraCantidadSpan.length > 0) {
        const cantidadExtras = parseInt(extraCantidadSpan.text()) || 0;
        // El extra_cantidad solo cuenta los EXTRAS (adicionales), no la cantidad gratis
        // Cobrar solo los extras
        if (cantidadExtras > 0) {
          extrasTotal += cantidadExtras * precio;
        }
      }
    }
  });
  
  // Calcular extras de ingredientes DIFERENTES al seleccionado en el radio
  $(".extras-categoria .extra_cantidad").each(function() {
    const cantidad = parseInt($(this).text()) || 0;
    const precio = parseFloat($(this).data("precio")) || 0;
    const categoriaId = $(this).data("categoria-id");
    const idExtra = $(this).data("id");
    
    if (cantidad > 0 && precio > 0) {
      // Verificar si este ingrediente está seleccionado en el radio
      const radioSeleccionado = $(`.radio-ingrediente-categoria[data-categoria-id='${categoriaId}']:checked`);
      const idRadio = radioSeleccionado.length > 0 ? radioSeleccionado.data("id") : null;
      
      // Solo procesar si NO es el mismo ingrediente seleccionado (ese ya se procesó arriba)
      if (idRadio !== idExtra) {
        // Es un ingrediente diferente, cobrar toda la cantidad
        extrasTotal += cantidad * precio;
      }
    }
  });
  
  const total = (precioBase + extrasTotal) * cantidadProducto;
  $("#btn_agregar_producto_carrito").text(`Agregar · MX$${total.toFixed(2)}`);
}

$(document).on("change", ".radio-tamano", function () {
  precioBase = parseFloat($(this).data("precio"));
  actualizarTotal();
});

// Manejar cambio de radio para ingredientes (selección única)
$(document).on("change", ".radio-ingrediente-categoria", function () {
  // No resetear extras al cambiar de ingrediente
  // Los extras con costo adicional son independientes y deben mantenerse
  actualizarTotal();
});


$(document).on("click", ".btn_extra_plus", function () {
  const id = $(this).data("id");
  const categoriaId = $(this).data("categoria-id");
  let span = $(`.extras-categoria[data-categoria-id='${categoriaId}'] .extra_cantidad[data-id='${id}']`);
  let cant = parseInt(span.text()) || 0;
  
  // Simplemente incrementar - el extra_cantidad solo cuenta los EXTRAS (adicionales)
  // La cantidad gratis del radio se maneja por separado
  cant++;
  span.text(cant);
  actualizarTotal();
});

$(document).on("click", ".btn_extra_minus", function () {
  const id = $(this).data("id");
  const categoriaId = $(this).data("categoria-id");
  let span = $(`.extras-categoria[data-categoria-id='${categoriaId}'] .extra_cantidad[data-id='${id}']`);
  let cant = parseInt(span.text()) || 0;

  if (cant > 0) {
    // Simplemente decrementar - el extra_cantidad solo cuenta los EXTRAS (adicionales)
    cant--;
    span.text(cant);
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

$(document).on("click", "#btn_agregar_producto_carrito", function () {
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

  // Ingredientes seleccionados (radio buttons - selección única por categoría)
  $(".radio-ingrediente-categoria:checked").each(function () {
    const id = $(this).data("id");
    const cantidadGratis = parseInt($(this).data("cantidad-gratis")) || 0;
    const categoriaId = $(this).data("categoria-id");
    
    // Obtener cantidad de extras para este ingrediente (si existe en la sección de extras)
    const extraCantidad = parseInt($(`.extras-categoria[data-categoria-id='${categoriaId}'] .extra_cantidad[data-id='${id}']`).text()) || 0;
    
    // Calcular cantidad total: cantidad gratis (mínimo 1) + extras
    const cantidadBase = cantidadGratis > 0 ? cantidadGratis : 1;
    const cantidadTotal = cantidadBase + extraCantidad;
    
    // Sumar con cantidad existente si el ingrediente ya está en el array
    const index = ingredientes.findIndex(ing => ing.id === id);
    if (index >= 0) {
      ingredientes[index].cantidad += cantidadTotal;
    } else {
      ingredientes.push({
        id: id,
        cantidad: cantidadTotal,
      });
    }
  });

  // Ingredientes con radio normal (sin categoría - para compatibilidad)
  $(".radio-ingrediente:checked").each(function () {
    ingredientes.push({
      id: $(this).data("id"),
      cantidad: 1,
    });
  });

  // Extras de ingredientes (solo los que NO están seleccionados en el radio)
  $(".extras-categoria .extra_cantidad").each(function () {
    const cant = parseInt($(this).text()) || 0;
    const id = $(this).data("id");
    const categoriaId = $(this).data("categoria-id");
    
    if (cant > 0) {
      // Verificar si este ingrediente está seleccionado en el radio de su categoría
      const radioSeleccionado = $(`.radio-ingrediente-categoria[data-categoria-id='${categoriaId}']:checked`);
      const idRadio = radioSeleccionado.length > 0 ? radioSeleccionado.data("id") : null;
      
      // Solo agregar si NO es el mismo ingrediente seleccionado (ya se procesó arriba)
      if (idRadio !== id) {
        // Verificar si ya existe en el array
        const index = ingredientes.findIndex(ing => ing.id === id);
        if (index >= 0) {
          ingredientes[index].cantidad += cant;
        } else {
          ingredientes.push({
            id: id,
            cantidad: cant,
          });
        }
      }
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
        } else if (respuesta.error === 'error_sesion') {
          swal({
            title: "¡OK!",
            text: respuesta.message,
            icon: "warning",
            button: "Aceptar",
          }).then(function () {
            window.location = url+"login";
          });
          return;
        }
        swal("Error", respuesta.message, "error");
        return;
      }

      swal("Perfecto", "Producto agregado al carrito", "success");
      
      // Cerrar el offcanvas de producto
      const offcanvasProductoEl = document.getElementById("offcanvasProducto");
      if (offcanvasProductoEl) {
        const offcanvasProducto = bootstrap.Offcanvas.getInstance(offcanvasProductoEl);
        if (offcanvasProducto) {
          offcanvasProducto.hide();
        }
      }
      
      $("#btn_agregar_producto_carrito").text(`Agregar · MX$ 0`);
      actualizarContadorCarrito();
      
      // Actualizar el total del carrito en el menú y en el offcanvas de producto
      const idCafeteria = $("#id_cafeteria").val() || 
                          $("#id_cafeteria_menu_offcanvas").val() || 
                          $("#id_cafeteria_mapa").val() || 
                          $("#id_cafeteria_mapa_mobile").val();
      if (idCafeteria) {
        actualizarTotalCarritoMenu(idCafeteria);
      }
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
        // Verificar si hay un radio seleccionado
        const tieneRadioSeleccionado = $(this).find(".radio-ingrediente-categoria:checked").length > 0;
        
        if (!tieneRadioSeleccionado) {
          valido = false;
          mensaje = `Debes seleccionar una opción de "${titulo}".`;
          return false;
        }
      }
    }
  });

  return { valido, mensaje };
}

// Función para actualizar el total del carrito en el menú
function actualizarTotalCarritoMenu(idCafeteria) {
  $.ajax({
    url: url + "views/ajax/ajax_carrito.php",
    method: "POST",
    data: {
      obtener_resumen_cafeteria: true,
      id_cafeteria: idCafeteria
    },
    success: function (respuesta) {
      respuesta = JSON.parse(respuesta);
      const $menuCarrito = $("#offcanvasMenuCafeteria .menu-carrito-resumen");
      
      if (respuesta.success && respuesta.carrito && respuesta.carrito.total_productos > 0) {
        const totalHtml = `
          <div class="menu-carrito-resumen" style="position: sticky; bottom: 0; background: var(--principal, #ffc107); color: white; padding: 1rem; margin-top: 1rem; border-radius: 12px 12px 0 0; box-shadow: 0 -2px 10px rgba(0,0,0,0.1); z-index: 10;">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <small style="display: block; opacity: 0.9;">Total en carrito</small>
                <strong style="font-size: 1.1rem;">${respuesta.carrito.total_productos} producto${respuesta.carrito.total_productos > 1 ? 's' : ''} · MX$${parseFloat(respuesta.carrito.total).toFixed(2)}</strong>
              </div>
              <button type="button" class="btn btn-light btn-sm" onclick="document.getElementById('ver-carrito').click();" style="border-radius: 20px;">
                <i class="fas fa-shopping-cart"></i> Ver carrito
              </button>
            </div>
          </div>
        `;
        
        if ($menuCarrito.length) {
          $menuCarrito.replaceWith(totalHtml);
        } else {
          $("#offcanvasMenuCafeteria .menu-cafeterias").append(totalHtml);
        }
      } else {
        // Ocultar si no hay carrito
        $menuCarrito.remove();
      }
    },
    error: function () {
      console.error("Error al actualizar el total del carrito en el menú");
    }
  });
}

