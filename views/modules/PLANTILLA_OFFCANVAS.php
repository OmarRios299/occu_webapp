<!-- 
============================================
PLANTILLA PARA NUEVO OFFCANVAS UNIFICADO
============================================

Esta es la plantilla base para crear un nuevo offcanvas usando las clases unificadas.
Copia esta estructura y personaliza según tus necesidades.

DIRECCIONES DISPONIBLES:
- slide-from-right: Desde la derecha (desktop)
- slide-from-left: Desde la izquierda
- slide-from-bottom: Desde abajo (móvil)

CLASES BOOTSTRAP:
- offcanvas-end: Desde la derecha
- offcanvas-start: Desde la izquierda  
- offcanvas-bottom: Desde abajo

RESPONSIVE:
- En desktop (≥992px): Aparece desde la derecha por defecto
- En móvil (<992px): Puede aparecer desde abajo usando offcanvas-bottom
-->

<!-- Offcanvas Unificado -->
<div class="offcanvas offcanvas-end offcanvas-unified slide-from-right" tabindex="-1" id="offcanvasEjemplo" aria-labelledby="offcanvasEjemploLabel">
    
    <!-- HEADER UNIFICADO -->
    <div class="offcanvas-header offcanvas-unified-header">
        <div class="offcanvas-unified-header-content">
            <h5 id="offcanvasEjemploLabel" class="offcanvas-unified-title">
                <i class="bi bi-icono-aqui"></i>
                <span>Título del Offcanvas</span>
            </h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Cerrar"></button>
        </div>
    </div>
    
    <!-- BODY UNIFICADO -->
    <div class="offcanvas-body offcanvas-unified-body">
        <!-- Contenedor principal con estructura flex -->
        <div class="h-100 d-flex flex-column overflow-hidden">
            
            <!-- CONTENIDO SCROLLABLE -->
            <div class="offcanvas-unified-scrollable">
                <div class="offcanvas-unified-content">
                    
                    <!-- ============================================
                         AQUÍ VA TU CONTENIDO
                         ============================================ -->
                    
                    <div class="mb-3">
                        <h6>Contenido Principal</h6>
                        <p>Este es el área donde va el contenido principal del offcanvas.</p>
                        <p>Este contenido será scrollable si excede la altura disponible.</p>
                    </div>
                    
                    <!-- Ejemplo: Formulario -->
                    <form id="formEjemplo" class="mb-3">
                        <div class="mb-3">
                            <label for="inputEjemplo" class="form-label">Campo de Ejemplo</label>
                            <input type="text" class="form-control" id="inputEjemplo" placeholder="Escribe algo...">
                        </div>
                        
                        <!-- Ejemplo: Select2 (si lo necesitas) -->
                        <div class="mb-3">
                            <label for="selectEjemplo" class="form-label">Select de Ejemplo</label>
                            <select class="form-control select2" id="selectEjemplo">
                                <option value="">Selecciona una opción</option>
                                <option value="1">Opción 1</option>
                                <option value="2">Opción 2</option>
                            </select>
                        </div>
                    </form>
                    
                    <!-- Más contenido... -->
                    <div class="mb-3">
                        <p>Puedes agregar más contenido aquí.</p>
                        <p>El scroll funcionará automáticamente si el contenido es muy largo.</p>
                    </div>
                    
                    <!-- ============================================ -->
                    
                </div>
            </div>
            
            <!-- FOOTER FIJO (OPCIONAL) -->
            <div class="offcanvas-unified-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="offcanvas">
                    <i class="bi bi-x-circle"></i>
                    <span>Cancelar</span>
                </button>
                <button type="button" class="btn btn-primary" onclick="guardarEjemplo();">
                    <i class="bi bi-check-circle"></i>
                    <span>Guardar</span>
                </button>
            </div>
            
        </div>
    </div>
</div>

<!-- 
============================================
VARIACIONES DE LA PLANTILLA
============================================

1. OFFCANVAS DESDE ABAJO (MÓVIL):
   Cambia las clases del div principal:
   <div class="offcanvas offcanvas-bottom offcanvas-unified slide-from-bottom" ...>

2. OFFCANVAS DESDE LA IZQUIERDA:
   <div class="offcanvas offcanvas-start offcanvas-unified slide-from-left" ...>

3. OFFCANVAS SIN FOOTER:
   Simplemente elimina el div con clase "offcanvas-unified-footer"

4. OFFCANVAS CON ANCHO PERSONALIZADO:
   Agrega clases de ancho:
   - width-small: 350px
   - width-medium: 500px  
   - width-large: 600px
   Ejemplo: <div class="offcanvas ... width-medium" ...>

5. OFFCANVAS CON ALTURA PERSONALIZADA (solo para slide-from-bottom):
   Agrega clases de altura:
   - height-small: 50%
   - height-large: 90%
   Ejemplo: <div class="offcanvas ... slide-from-bottom height-large" ...>
-->

<!-- 
============================================
JAVASCRIPT PARA ABRIR EL OFFCANVAS
============================================

// Abrir offcanvas
const offcanvasEl = document.getElementById('offcanvasEjemplo');
const offcanvas = new bootstrap.Offcanvas(offcanvasEl);
offcanvas.show();

// O usando jQuery
$('#offcanvasEjemplo').offcanvas('show');

// Si usas Select2 dentro del offcanvas, re-inicialízalo:
setTimeout(function() {
    $('#offcanvasEjemplo .select2').each(function() {
        if ($(this).data('select2')) {
            $(this).select2('destroy');
        }
        $(this).select2({
            dropdownParent: $('#offcanvasEjemplo')
        });
    });
}, 100);
-->

