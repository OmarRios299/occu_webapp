/**
 * Sistema de Tours Guiados Simple (sin dependencias externas)
 * Reemplazo de Driver.js para tours guiados
 */

(function(window) {
    'use strict';

    function TourGuide(options) {
        this.options = options || {};
        this.steps = [];
        this.currentStep = 0;
        this.overlay = null;
        this.popover = null;
        this.isActive = false;
        
        // Configuración por defecto
        this.config = {
            allowClose: true,
            overlayClickNext: false,
            showButtons: ['next', 'previous', 'close'],
            keyboardControl: true,
            onHighlightStarted: null,
            onDestroyed: null
        };
        
        // Merge con opciones personalizadas
        for (var key in this.config) {
            if (this.options.hasOwnProperty(key)) {
                this.config[key] = this.options[key];
            }
        }
    }

    TourGuide.prototype.drive = function(steps) {
        if (!steps || steps.length === 0) {
            console.error('TourGuide: No hay pasos para mostrar');
            return;
        }
        
        this.steps = steps;
        this.currentStep = 0;
        this.isActive = true;
        this.showStep(0);
    };

    TourGuide.prototype.showStep = function(index) {
        if (index < 0 || index >= this.steps.length) {
            this.destroy();
            return;
        }
        
        this.currentStep = index;
        var step = this.steps[index];
        
        // Obtener elemento
        var element = null;
        if (typeof step.element === 'string') {
            element = document.querySelector(step.element);
        } else {
            element = step.element;
        }
        
        if (!element) {
            console.warn('TourGuide: Elemento no encontrado:', step.element);
            this.next();
            return;
        }
        
        // Callback onHighlightStarted
        if (this.config.onHighlightStarted) {
            this.config.onHighlightStarted(element);
        } else {
            // Scroll al elemento por defecto
            element.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
        
        // Crear overlay y popover
        this.createOverlay();
        this.createPopover(element, step);
    };

    TourGuide.prototype.createOverlay = function() {
        // Remover overlay existente
        if (this.overlay) {
            document.body.removeChild(this.overlay);
        }
        
        // Crear overlay
        this.overlay = document.createElement('div');
        this.overlay.className = 'tour-guide-overlay';
        this.overlay.style.cssText = 'position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); z-index: 9998; transition: opacity 0.3s;';
        
        // Click en overlay
        if (this.config.overlayClickNext) {
            this.overlay.addEventListener('click', function() {
                this.next();
            }.bind(this));
        }
        
        document.body.appendChild(this.overlay);
    };

    TourGuide.prototype.createPopover = function(element, step) {
        // Remover popover existente
        if (this.popover) {
            document.body.removeChild(this.popover);
        }
        
        // Obtener posición del elemento
        var rect = element.getBoundingClientRect();
        var position = step.popover && step.popover.position ? step.popover.position : 'bottom';
        var title = step.popover && step.popover.title ? step.popover.title : '';
        var description = step.popover && step.popover.description ? step.popover.description : '';
        
        // Crear popover
        this.popover = document.createElement('div');
        this.popover.className = 'tour-guide-popover';
        
        // Crear contenido
        var html = '<div class="tour-guide-popover-content">';
        if (title) {
            html += '<div class="tour-guide-popover-title">' + escapeHtml(title) + '</div>';
        }
        if (description) {
            html += '<div class="tour-guide-popover-description">' + escapeHtml(description) + '</div>';
        }
        html += '<div class="tour-guide-popover-buttons">';
        
        // Botones
        if (this.config.showButtons.indexOf('previous') !== -1 && this.currentStep > 0) {
            html += '<button class="tour-guide-btn tour-guide-btn-prev">Anterior</button>';
        }
        if (this.config.showButtons.indexOf('next') !== -1) {
            var nextText = this.currentStep === this.steps.length - 1 ? 'Finalizar' : 'Siguiente';
            html += '<button class="tour-guide-btn tour-guide-btn-next">' + nextText + '</button>';
        }
        if (this.config.showButtons.indexOf('close') !== -1) {
            html += '<button class="tour-guide-btn tour-guide-btn-close" title="Cerrar">×</button>';
        }
        
        html += '</div></div>';
        html += '<div class="tour-guide-arrow"></div>';
        
        this.popover.innerHTML = html;
        document.body.appendChild(this.popover);
        
        // Calcular posición inteligente
        setTimeout(function() {
            positionPopover(this.popover, element, position, rect);
        }.bind(this), 0);
        
        // Event listeners
        var prevBtn = this.popover.querySelector('.tour-guide-btn-prev');
        if (prevBtn) {
            prevBtn.addEventListener('click', this.previous.bind(this));
        }
        
        var nextBtn = this.popover.querySelector('.tour-guide-btn-next');
        if (nextBtn) {
            nextBtn.addEventListener('click', this.next.bind(this));
        }
        
        var closeBtn = this.popover.querySelector('.tour-guide-btn-close');
        if (closeBtn) {
            closeBtn.addEventListener('click', this.destroy.bind(this));
        }
        
        // Resaltar elemento
        element.style.position = 'relative';
        element.style.zIndex = '10000';
    };
    
    function positionPopover(popover, element, preferredPosition, elementRect) {
        // Obtener dimensiones del popover después de agregarlo al DOM
        var popoverRect = popover.getBoundingClientRect();
        var windowWidth = window.innerWidth;
        var windowHeight = window.innerHeight;
        var padding = 20;
        var arrowSize = 16;
        var spacing = 15;
        
        var popoverWidth = popoverRect.width || 320; // Fallback si aún no está renderizado
        var popoverHeight = popoverRect.height || 150;
        var elementWidth = elementRect.width;
        var elementHeight = elementRect.height;
        var elementTop = elementRect.top + window.pageYOffset;
        var elementLeft = elementRect.left + window.pageXOffset;
        var elementBottom = elementRect.bottom + window.pageYOffset;
        var elementRight = elementRect.right + window.pageXOffset;
        var elementCenterX = elementLeft + (elementWidth / 2);
        var elementCenterY = elementTop + (elementHeight / 2);
        
        // Usar getBoundingClientRect para posición relativa a viewport
        var viewportElementTop = elementRect.top;
        var viewportElementLeft = elementRect.left;
        var viewportElementBottom = elementRect.bottom;
        var viewportElementRight = elementRect.right;
        var viewportElementCenterX = viewportElementLeft + (elementWidth / 2);
        var viewportElementCenterY = viewportElementTop + (elementHeight / 2);
        
        var finalPosition = preferredPosition;
        var top = 0;
        var left = 0;
        var arrowClass = '';
        var transform = '';
        var arrowLeftPos = 0;
        var arrowTopPos = 0;
        
        // Determinar mejor posición basado en espacio disponible (usando viewport)
        var spaceTop = viewportElementTop;
        var spaceBottom = windowHeight - viewportElementBottom;
        var spaceLeft = viewportElementLeft;
        var spaceRight = windowWidth - viewportElementRight;
        
        // Estimar espacio necesario (usar dimensiones mínimas)
        var minPopoverHeight = 150;
        var minPopoverWidth = 250;
        
        // Si la posición preferida no tiene espacio, elegir la mejor alternativa
        if (preferredPosition === 'top' && spaceTop < minPopoverHeight + spacing + arrowSize) {
            if (spaceBottom > spaceTop) finalPosition = 'bottom';
            else if (spaceRight > spaceLeft && spaceRight > minPopoverWidth + spacing + arrowSize) finalPosition = 'right';
            else if (spaceLeft > minPopoverWidth + spacing + arrowSize) finalPosition = 'left';
            else finalPosition = 'bottom'; // Forzar bottom si no hay espacio
        } else if (preferredPosition === 'bottom' && spaceBottom < minPopoverHeight + spacing + arrowSize) {
            if (spaceTop > spaceBottom && spaceTop > minPopoverHeight + spacing + arrowSize) finalPosition = 'top';
            else if (spaceRight > spaceLeft && spaceRight > minPopoverWidth + spacing + arrowSize) finalPosition = 'right';
            else if (spaceLeft > minPopoverWidth + spacing + arrowSize) finalPosition = 'left';
            else finalPosition = 'top'; // Forzar top si no hay espacio
        } else if (preferredPosition === 'left' && spaceLeft < minPopoverWidth + spacing + arrowSize) {
            if (spaceRight > spaceLeft && spaceRight > minPopoverWidth + spacing + arrowSize) finalPosition = 'right';
            else if (spaceBottom > spaceTop && spaceBottom > minPopoverHeight + spacing + arrowSize) finalPosition = 'bottom';
            else if (spaceTop > minPopoverHeight + spacing + arrowSize) finalPosition = 'top';
            else finalPosition = 'right'; // Forzar right si no hay espacio
        } else if (preferredPosition === 'right' && spaceRight < minPopoverWidth + spacing + arrowSize) {
            if (spaceLeft > spaceRight && spaceLeft > minPopoverWidth + spacing + arrowSize) finalPosition = 'left';
            else if (spaceBottom > spaceTop && spaceBottom > minPopoverHeight + spacing + arrowSize) finalPosition = 'bottom';
            else if (spaceTop > minPopoverHeight + spacing + arrowSize) finalPosition = 'top';
            else finalPosition = 'left'; // Forzar left si no hay espacio
        }
        
        // Calcular posición basado en posición final (usar viewport)
        switch(finalPosition) {
            case 'top':
                top = viewportElementTop - popoverHeight - spacing;
                left = viewportElementCenterX;
                arrowClass = 'tour-guide-arrow-bottom';
                transform = 'translate(-50%, 0)';
                break;
            case 'bottom':
                top = viewportElementBottom + spacing;
                left = viewportElementCenterX;
                arrowClass = 'tour-guide-arrow-top';
                transform = 'translate(-50%, 0)';
                break;
            case 'left':
                top = viewportElementCenterY;
                left = viewportElementLeft - popoverWidth - spacing;
                arrowClass = 'tour-guide-arrow-right';
                transform = 'translate(0, -50%)';
                break;
            case 'right':
                top = viewportElementCenterY;
                left = viewportElementRight + spacing;
                arrowClass = 'tour-guide-arrow-left';
                transform = 'translate(0, -50%)';
                break;
        }
        
        // Recalcular dimensiones reales del popover
        var actualPopoverWidth = popover.offsetWidth || popoverWidth;
        var actualPopoverHeight = popover.offsetHeight || popoverHeight;
        
        // Asegurar que el popover no se salga de la pantalla
        var minLeft = padding;
        var maxLeft = windowWidth - actualPopoverWidth - padding;
        var minTop = padding;
        var maxTop = windowHeight - actualPopoverHeight - padding;
        
        left = Math.max(minLeft, Math.min(left, maxLeft));
        top = Math.max(minTop, Math.min(top, maxTop));
        
        // En móviles, centrar horizontalmente y posicionar mejor
        if (windowWidth < 768) {
            left = windowWidth / 2;
            if (finalPosition === 'top') {
                top = Math.max(padding, viewportElementTop - actualPopoverHeight - spacing);
                transform = 'translate(-50%, 0)';
            } else if (finalPosition === 'bottom') {
                top = Math.min(maxTop, viewportElementBottom + spacing);
                transform = 'translate(-50%, 0)';
            } else {
                transform = 'translate(-50%, -50%)';
            }
        }
        
        // Aplicar estilos
        popover.style.top = top + 'px';
        popover.style.left = left + 'px';
        popover.style.transform = transform;
        popover.style.zIndex = '9999';
        popover.classList.add('tour-guide-popover-' + finalPosition);
        
        // Recalcular posición real después de aplicar estilos
        var finalPopoverRect = popover.getBoundingClientRect();
        var finalLeft = finalPopoverRect.left;
        var finalTop = finalPopoverRect.top;
        var finalWidth = finalPopoverRect.width;
        var finalHeight = finalPopoverRect.height;
        
        // Configurar flecha
        var arrow = popover.querySelector('.tour-guide-arrow');
        if (arrow) {
            arrow.className = 'tour-guide-arrow ' + arrowClass;
            
            if (finalPosition === 'top' || finalPosition === 'bottom') {
                // Posicionar flecha horizontalmente
                arrowLeftPos = viewportElementCenterX - finalLeft;
                arrowLeftPos = Math.min(Math.max(arrowLeftPos, 20), finalWidth - 20);
                arrow.style.left = arrowLeftPos + 'px';
                arrow.style.top = '';
                arrow.style.right = '';
                arrow.style.bottom = '';
            } else {
                // Posicionar flecha verticalmente
                arrowTopPos = viewportElementCenterY - finalTop;
                arrowTopPos = Math.min(Math.max(arrowTopPos, 20), finalHeight - 20);
                arrow.style.top = arrowTopPos + 'px';
                arrow.style.left = '';
                arrow.style.right = '';
                arrow.style.bottom = '';
            }
        }
    }

    TourGuide.prototype.next = function() {
        if (this.currentStep < this.steps.length - 1) {
            // Si hay callback onNextClick, ejecutarlo
            var currentStep = this.steps[this.currentStep];
            if (currentStep.popover && currentStep.popover.onNextClick) {
                currentStep.popover.onNextClick();
                return;
            }
            this.showStep(this.currentStep + 1);
        } else {
            this.destroy();
        }
    };

    TourGuide.prototype.previous = function() {
        if (this.currentStep > 0) {
            this.showStep(this.currentStep - 1);
        }
    };

    TourGuide.prototype.destroy = function() {
        this.isActive = false;
        
        // Remover overlay
        if (this.overlay) {
            document.body.removeChild(this.overlay);
            this.overlay = null;
        }
        
        // Remover popover
        if (this.popover) {
            document.body.removeChild(this.popover);
            this.popover = null;
        }
        
        // Restaurar elementos
        this.steps.forEach(function(step) {
            var element = null;
            if (typeof step.element === 'string') {
                element = document.querySelector(step.element);
            } else {
                element = step.element;
            }
            if (element) {
                element.style.position = '';
                element.style.zIndex = '';
            }
        });
        
        // Callback onDestroyed
        if (this.config.onDestroyed) {
            this.config.onDestroyed();
        }
    };

    // Funciones auxiliares
    function escapeHtml(text) {
        var div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Función eliminada - ahora se usa positionPopover que maneja todo el posicionamiento

    // Control de teclado
    document.addEventListener('keydown', function(e) {
        if (window.currentTourGuide && window.currentTourGuide.isActive) {
            if (e.key === 'Escape') {
                window.currentTourGuide.destroy();
            } else if (e.key === 'ArrowRight' || e.key === 'ArrowDown') {
                window.currentTourGuide.next();
            } else if (e.key === 'ArrowLeft' || e.key === 'ArrowUp') {
                window.currentTourGuide.previous();
            }
        }
    });

    // Exponer globalmente
    window.Driver = function(options) {
        var tour = new TourGuide(options);
        window.currentTourGuide = tour;
        return tour;
    };

})(window);

