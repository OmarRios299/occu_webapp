<input type="hidden" id="id_carrito_cafeteria" value="<?= $action[1] ?? "" ?>">
<div class="offcanvas offcanvas-end offcanvas-unified slide-from-right" tabindex="-1" id="offcanvasCarrito" aria-labelledby="offcanvasCarritoLabel">
    <div class="offcanvas-header offcanvas-unified-header">
        <div class="offcanvas-unified-header-content">
            <h5 id="offcanvasCarritoLabel" class="offcanvas-unified-title">
                <i class="fa-solid fa-cart-shopping"></i>
                <span>Mi carrito</span>
            </h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Cerrar"></button>
        </div>
    </div>
    <div class="offcanvas-body offcanvas-unified-body">
        <div class="h-100 d-flex flex-column overflow-hidden">
            <!-- Contenido scrollable -->
            <div class="offcanvas-unified-scrollable">
                <div class="offcanvas-unified-content">
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
            </div>

            <!-- Footer fijo -->
            <div class="offcanvas-unified-footer">
                <button class="btn btn-warning text-white rounded-pill px-4 fw-bold" id="btn_pagar">Pagar $0 </button>
            </div>
        </div>
    </div>
</div>