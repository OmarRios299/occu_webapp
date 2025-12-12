<input type="hidden" id="id_carrito_cafeteria" value="<?= $action[1] ?? "" ?>">
<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasCarrito" aria-labelledby="offcanvasCarritoLabel">
    
<div class="offcanvas-header filtros-offcanvas-header">
        <div class="filtros-header-content">
            <h5 id="offcanvasFiltrosLabel" class="offcanvas-title-filtros">
            <i class="fa-solid fa-cart-shopping fa-3x"></i>
                <span>Mi carrito</span>
            </h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Cerrar"></button>
               
            </button>
        </div>
    </div>
    <div class="offcanvas-body p-0">
        <div id="" class="h-100 d-flex flex-column overflow-hidden">

            <!-- Todo el contenido scrollable -->
            <div class="flex-grow-1 overflow-auto">

                <div class="">
                    <img src="" id="cafe_imagen" alt="Cafetería" class="w-100 object-fit-cover" style="max-height: 120px;">
                </div>
                <!-- Mensaje carrito vacío -->
                <div id="carrito_vacio" class="text-center py-5" style="display: none;">
                    <i class="fa-solid fa-cart-shopping fa-3x text-secondary"></i>
                    <h6 class="mt-3 text-muted">Tu carrito está vacío</h6>
                    <p class="text-muted" style="font-size: 14px;">Agrega productos para comenzar tu orden.</p>
                </div>


                <!-- Info general -->
                <div class="p-3" id="lista_productos_carrito">

                </div>

            </div>

            <!-- Footer fijo -->
            <div class="border-top px-3 py-2 bg-white d-flex justify-content-between align-items-right">
                <button class="btn btn-warning text-white rounded-pill px-4 fw-bold" id="btn_pagar">Pagar $0 </button>
            </div>

        </div>
    </div>
</div>