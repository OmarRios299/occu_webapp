// ============================================
// Sistema de Alertas - Frontend
// ============================================

// Variable global para almacenar alertas pendientes
var alertasPendientes = [];

// Función para inicializar alertas desde localStorage o desde respuesta de login
function inicializarAlertas() {
    // Intentar obtener alertas del localStorage (guardadas después del login)
    var alertasGuardadas = localStorage.getItem('alertasPendientes');
    
    if (alertasGuardadas) {
        try {
            alertasPendientes = JSON.parse(alertasGuardadas);
            localStorage.removeItem('alertasPendientes'); // Limpiar después de leer
        } catch (e) {
            console.error('Error al parsear alertas guardadas:', e);
        }
    }
    
    // Si hay alertas, mostrarlas
    if (alertasPendientes && alertasPendientes.length > 0) {
        renderizarAlertas(alertasPendientes);
    }
}

// Variable global para rastrear alertas ya mostradas (evitar duplicados)
var alertasMostradas = new Set();

// Variable global para rastrear alertas ya marcadas como vistas (evitar marcar dos veces)
var alertasMarcadasVista = new Set();

// Variable global para rastrear si hay una alerta personalizada siendo mostrada
var alertaPersonalizadaActiva = false;

// Función para obtener contador de ignorar de una alerta desde la base de datos (AJAX)
function obtenerContadorIgnorar(idAlerta, callback) {
    $.ajax({
        url: url + 'views/ajax/ajax_alertas.php',
        method: 'GET',
        data: {
            obtener_contador_ignorar: true,
            id_alerta: idAlerta
        },
        success: function(response) {
            try {
                var res = JSON.parse(response);
                if (res.success) {
                    callback(res.contador || 0);
                } else {
                    callback(0);
                }
            } catch (e) {
                console.error('Error al parsear respuesta del contador:', e);
                callback(0);
            }
        },
        error: function() {
            console.error('Error al obtener contador de ignorar');
            callback(0);
        }
    });
}

// Función para incrementar contador de ignorar de una alerta en la base de datos (AJAX)
function incrementarContadorIgnorar(idAlerta, callback) {
    $.ajax({
        url: url + 'views/ajax/ajax_alertas.php',
        method: 'POST',
        data: {
            incrementar_contador_ignorar: true,
            id_alerta: idAlerta
        },
        success: function(response) {
            try {
                var res = JSON.parse(response);
                if (res.success) {
                    callback(res.contador || 0);
                } else {
                    callback(0);
                }
            } catch (e) {
                console.error('Error al parsear respuesta del incremento:', e);
                callback(0);
            }
        },
        error: function() {
            console.error('Error al incrementar contador de ignorar');
            callback(0);
        }
    });
}

// Función para verificar si una alerta debe ser ignorada según el contador (asíncrona)
function debeIgnorarAlerta(alerta, callback) {
    var vecesIgnorar = parseInt(alerta.veces_ignorar || 0, 10);
    if (vecesIgnorar <= 0) {
        callback(false); // No hay veces a ignorar, mostrar normalmente
        return;
    }
    
    // Obtener contador actual desde la BD
    obtenerContadorIgnorar(alerta.id, function(contadorActual) {
        if (contadorActual < vecesIgnorar) {
            // Aún no se alcanza el límite, incrementar contador e ignorar
            incrementarContadorIgnorar(alerta.id, function(nuevoContador) {
                console.log('Alerta ' + alerta.id + ' ignorada. Contador: ' + nuevoContador + '/' + vecesIgnorar);
                callback(true); // Ignorar
            });
        } else {
            // Ya se alcanzó el límite, mostrar la alerta
            console.log('Alerta ' + alerta.id + ' se mostrará ahora (contador alcanzado: ' + contadorActual + '/' + vecesIgnorar + ')');
            callback(false); // No ignorar, mostrar
        }
    });
}

// Función para inicializar alertas del módulo actual
function inicializarAlertasModulo() {
    // Verificar si hay alertas del módulo actual definidas en el script
    if (typeof alertasModuloActual !== 'undefined' && alertasModuloActual && alertasModuloActual.length > 0) {
        // Filtrar alertas que ya se mostraron (evitar duplicados)
        var alertasNuevas = alertasModuloActual.filter(function(alerta) {
            return !alertasMostradas.has(alerta.id);
        });
        
        // Si hay alertas nuevas, mostrarlas
        if (alertasNuevas.length > 0) {
            renderizarAlertas(alertasNuevas);
        }
    }
}

// Renderizar todas las alertas pendientes
function renderizarAlertas(alertas) {
    if (!alertas || alertas.length === 0) return;
    
    // Filtrar alertas que ya se mostraron (evitar duplicados)
    var alertasFiltradas = alertas.filter(function(alerta) {
        return !alertasMostradas.has(alerta.id);
    });
    
    if (alertasFiltradas.length === 0) return;
    
    // Verificar contador de ignorar y filtrar alertas que deben ser ignoradas (asíncrono)
    var alertasParaMostrar = [];
    var alertasProcesadas = 0;
    var totalAlertas = alertasFiltradas.length;
    
    if (totalAlertas === 0) return;
    
    alertasFiltradas.forEach(function(alerta) {
        var vecesIgnorar = parseInt(alerta.veces_ignorar || 0, 10);
        
        if (vecesIgnorar <= 0) {
            // No tiene contador de ignorar, agregar directamente
            alertasParaMostrar.push(alerta);
            alertasProcesadas++;
            
            // Si ya procesamos todas, continuar
            if (alertasProcesadas === totalAlertas) {
                procesarAlertasParaMostrar(alertasParaMostrar);
            }
        } else {
            // Tiene contador de ignorar, verificar asíncronamente
            debeIgnorarAlerta(alerta, function(debeIgnorar) {
                alertasProcesadas++;
                
                if (!debeIgnorar) {
                    // No debe ignorarse, agregar a la lista
                    alertasParaMostrar.push(alerta);
                }
                
                // Si ya procesamos todas, continuar
                if (alertasProcesadas === totalAlertas) {
                    procesarAlertasParaMostrar(alertasParaMostrar);
                }
            });
        }
    });
}

// Función auxiliar para procesar las alertas que deben mostrarse
function procesarAlertasParaMostrar(alertasParaMostrar) {
    if (alertasParaMostrar.length === 0) return;
    
    // Verificar si ya se mostró una alerta personalizada (de cualquier fuente)
    // Revisar tanto en las alertas pasadas como en alertasModuloActual si existe
    var yaSeMostroPersonalizada = false;
    
    // Revisar en alertasParaMostrar
    alertasParaMostrar.forEach(function(alerta) {
        if (alerta.plantilla === 'personalizado' && alertasMostradas.has(alerta.id)) {
            yaSeMostroPersonalizada = true;
        }
    });
    
    // Revisar también en alertasModuloActual si existe
    if (!yaSeMostroPersonalizada && typeof alertasModuloActual !== 'undefined' && alertasModuloActual) {
        alertasModuloActual.forEach(function(alerta) {
            if (alerta.plantilla === 'personalizado' && alertasMostradas.has(alerta.id)) {
                yaSeMostroPersonalizada = true;
            }
        });
    }
    
    // Separar alertas personalizadas del resto
    var alertasPersonalizadas = alertasParaMostrar.filter(function(alerta) {
        return alerta.plantilla === 'personalizado';
    });
    
    var alertasNoPersonalizadas = alertasParaMostrar.filter(function(alerta) {
        return alerta.plantilla !== 'personalizado';
    });
    
    // Si ya se mostró una personalizada, ignorar todas las demás personalizadas
    var alertaPersonalizadaSeleccionada = null;
    if (!yaSeMostroPersonalizada && alertasPersonalizadas.length > 0) {
        // Ordenar personalizadas por prioridad (mayor primero)
        alertasPersonalizadas.sort(function(a, b) {
            return (b.prioridad || 0) - (a.prioridad || 0);
        });
        // Tomar solo la primera (mayor prioridad), ignorar las demás
        alertaPersonalizadaSeleccionada = alertasPersonalizadas[0];
        // Marcar INMEDIATAMENTE para evitar condiciones de carrera
        alertasMostradas.add(alertaPersonalizadaSeleccionada.id);
    }
    
    // Combinar la alerta personalizada seleccionada (si existe) con las demás alertas
    var alertasFinales = [];
    if (alertaPersonalizadaSeleccionada) {
        alertasFinales.push(alertaPersonalizadaSeleccionada);
    }
    alertasFinales = alertasFinales.concat(alertasNoPersonalizadas);
    
    if (alertasFinales.length === 0) return;
    
    // Ordenar por prioridad (mayor primero)
    alertasFinales.sort(function(a, b) {
        return (b.prioridad || 0) - (a.prioridad || 0);
    });
    
    // Para tours guiados, mostrar solo la primera alerta (la de mayor prioridad)
    // Las demás se mostrarán cuando se cierre la anterior
    var primeraAlerta = alertasFinales[0];
    
    // Solo agregar a alertasMostradas si no es una personalizada (ya se agregó arriba)
    if (primeraAlerta.plantilla !== 'personalizado') {
        alertasMostradas.add(primeraAlerta.id);
    }
    
    // Mostrar solo la primera alerta
    setTimeout(function() {
        mostrarAlerta(primeraAlerta);
    }, 200);
}

// Mostrar una alerta según su plantilla
function mostrarAlerta(alerta) {
    switch(alerta.plantilla) {
        case 'default':
            mostrarAlertaDefault(alerta);
            break;
        case 'modal':
            mostrarAlertaModal(alerta);
            break;
        case 'banner':
            mostrarAlertaBanner(alerta);
            break;
        case 'card':
            mostrarAlertaCard(alerta);
            break;
        case 'personalizado':
            mostrarAlertaPersonalizada(alerta);
            break;
        case 'tour_guido':
            mostrarAlertaTourGuiado(alerta);
            break;
        default:
            mostrarAlertaDefault(alerta);
    }
}

// Alerta simple (SweetAlert)
function mostrarAlertaDefault(alerta) {
    var botones = {};
    var tieneBotones = alerta.botones && alerta.botones.length > 0;
    
    if (tieneBotones) {
        alerta.botones.forEach(function(boton, index) {
            var key = boton.texto.toLowerCase().replace(/\s+/g, '_') || 'btn_' + index;
            botones[key] = {
                text: boton.texto,
                value: boton.url || 'cerrar'
            };
        });
    } else {
        botones = { confirm: 'Aceptar' };
    }
    
    var icono = alerta.icono ? alerta.icono : alerta.tipo;
    
    // Marcar como vista inmediatamente al aparecer
    marcarAlertaVista(alerta.id);
    
    swal({
        title: alerta.titulo,
        text: alerta.mensaje,
        icon: icono,
        buttons: botones,
        className: 'alerta-sistema'
    }).then(function(value) {
        if (value && value !== 'cerrar' && value !== true) {
            window.location.href = url + value;
        }
    });
}

// Alerta en Modal Bootstrap
function mostrarAlertaModal(alerta) {
    var botonesHTML = '';
    
    if (alerta.botones && alerta.botones.length > 0) {
        alerta.botones.forEach(function(boton) {
            var clase = 'btn-' + (boton.tipo || 'primary');
            var icono = boton.icono ? '<i class="' + boton.icono + '"></i> ' : '';
            var onclick = '';
            
            if (boton.cerrar) {
                onclick = 'data-bs-dismiss="modal"';
            } else if (boton.url) {
                onclick = 'onclick="window.location.href=\'' + url + boton.url + '\';"';
            } else {
                onclick = 'data-bs-dismiss="modal"';
            }
            
            botonesHTML += '<button type="button" class="btn ' + clase + '" ' + onclick + '>' + icono + boton.texto + '</button>';
        });
    } else {
        botonesHTML = '<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>';
    }
    
    var iconoHTML = alerta.icono ? '<i class="' + alerta.icono + '"></i> ' : '';
    var tipoClase = 'alert-' + (alerta.tipo || 'info');
    
    var modalHTML = '<div class="modal fade" id="alertaModal' + alerta.id + '" tabindex="-1" aria-labelledby="alertaModalLabel' + alerta.id + '" aria-hidden="true">' +
        '<div class="modal-dialog modal-dialog-centered">' +
        '<div class="modal-content">' +
        '<div class="modal-header ' + tipoClase + '">' +
        '<h5 class="modal-title" id="alertaModalLabel' + alerta.id + '">' + iconoHTML + alerta.titulo + '</h5>' +
        '<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>' +
        '</div>' +
        '<div class="modal-body">' +
        '<p>' + alerta.mensaje + '</p>' +
        '</div>' +
        '<div class="modal-footer">' +
        botonesHTML +
        '</div>' +
        '</div>' +
        '</div>' +
        '</div>';
    
    $('body').append(modalHTML);
    
    var modalElement = document.getElementById('alertaModal' + alerta.id);
    var modal = new bootstrap.Modal(modalElement);
    
    // Marcar como vista inmediatamente cuando se muestra el modal
    $(modalElement).on('shown.bs.modal', function() {
        marcarAlertaVista(alerta.id);
    });
    
    // Limpiar al cerrar
    $(modalElement).on('hidden.bs.modal', function() {
        $(this).remove();
    });
    
    modal.show();
}

// Banner fijo en la parte superior
function mostrarAlertaBanner(alerta) {
    var botonesHTML = '';
    
    if (alerta.botones && alerta.botones.length > 0) {
        alerta.botones.forEach(function(boton) {
            var clase = 'btn btn-sm btn-' + (boton.tipo || 'primary');
            var icono = boton.icono ? '<i class="' + boton.icono + '"></i> ' : '';
            var onclick = '';
            
            if (boton.url) {
                onclick = 'onclick="window.location.href=\'' + url + boton.url + '\';"';
            } else {
                onclick = 'onclick="cerrarBanner(' + alerta.id + ');"';
            }
            
            botonesHTML += '<button type="button" class="' + clase + '" ' + onclick + '>' + icono + boton.texto + '</button>';
        });
    }
    
    var tipoClase = 'alert-' + (alerta.tipo || 'info');
    var iconoHTML = alerta.icono ? '<i class="' + alerta.icono + '"></i> ' : '';
    
    var bannerHTML = '<div class="alert ' + tipoClase + ' alert-dismissible fade show alerta-banner" ' +
        'id="alertaBanner' + alerta.id + '" ' +
        'role="alert" ' +
        'style="position: fixed; top: 0; left: 0; right: 0; z-index: 9999; margin: 0; border-radius: 0; box-shadow: 0 2px 4px rgba(0,0,0,0.2);">' +
        '<div class="container d-flex justify-content-between align-items-center flex-wrap">' +
        '<div class="flex-grow-1">' +
        iconoHTML +
        '<strong>' + alerta.titulo + '</strong> ' + alerta.mensaje +
        '</div>' +
        '<div class="d-flex gap-2 align-items-center ms-2">' +
        botonesHTML +
        '<button type="button" class="btn-close" onclick="cerrarBanner(' + alerta.id + ')" aria-label="Cerrar"></button>' +
        '</div>' +
        '</div>' +
        '</div>';
    
    $('body').prepend(bannerHTML);
    
    // Marcar como vista inmediatamente al aparecer
    marcarAlertaVista(alerta.id);
    
    // Agregar estilos personalizados si existen
    if (alerta.estilo_personalizado) {
        var styleId = 'estilo-alerta-' + alerta.id;
        if (!$('#' + styleId).length) {
            $('<style id="' + styleId + '">').text(alerta.estilo_personalizado).appendTo('head');
        }
    }
    
    // Ajustar padding del body para que el contenido no quede oculto
    $('body').css('padding-top', $('#alertaBanner' + alerta.id).outerHeight() + 'px');
}

// Card flotante
function mostrarAlertaCard(alerta) {
    var tipoClase = 'alert-' + (alerta.tipo || 'info');
    var iconoHTML = alerta.icono ? '<i class="' + alerta.icono + '"></i> ' : '';
    
    var cardHTML = '<div class="card alerta-card shadow-lg" id="alertaCard' + alerta.id + '" ' +
        'style="position: fixed; top: 20px; right: 20px; z-index: 10000; max-width: 400px; min-width: 300px;">' +
        '<div class="card-header ' + tipoClase + ' d-flex justify-content-between align-items-center">' +
        '<h6 class="mb-0">' + iconoHTML + alerta.titulo + '</h6>' +
        '<button type="button" class="btn-close btn-close-white" onclick="cerrarCard(' + alerta.id + ')"></button>' +
        '</div>' +
        '<div class="card-body">' +
        '<p class="card-text">' + alerta.mensaje + '</p>';
    
    if (alerta.botones && alerta.botones.length > 0) {
        cardHTML += '<div class="d-flex gap-2">';
        alerta.botones.forEach(function(boton) {
            var clase = 'btn btn-sm btn-' + (boton.tipo || 'primary');
            var icono = boton.icono ? '<i class="' + boton.icono + '"></i> ' : '';
            var onclick = '';
            
            if (boton.url) {
                onclick = 'onclick="window.location.href=\'' + url + boton.url + '\';"';
            } else {
                onclick = 'onclick="cerrarCard(' + alerta.id + ');"';
            }
            
            cardHTML += '<button type="button" class="' + clase + '" ' + onclick + '>' + icono + boton.texto + '</button>';
        });
        cardHTML += '</div>';
    }
    
    cardHTML += '</div></div>';
    
    $('body').append(cardHTML);
    
    // Marcar como vista inmediatamente al aparecer
    marcarAlertaVista(alerta.id);
    
    // Agregar estilos personalizados si existen
    if (alerta.estilo_personalizado) {
        var styleId = 'estilo-alerta-' + alerta.id;
        if (!$('#' + styleId).length) {
            $('<style id="' + styleId + '">').text(alerta.estilo_personalizado).appendTo('head');
        }
    }
    
    // Auto-cerrar después de 10 segundos si no tiene botones
    if (!alerta.botones || alerta.botones.length === 0) {
        setTimeout(function() {
            cerrarCard(alerta.id);
        }, 10000);
    }
}

// HTML completamente personalizado
function mostrarAlertaPersonalizada(alerta) {
    if (!alerta.html_personalizado) return;
    
    // CRÍTICO: Verificar si ya hay una alerta personalizada activa
    // Si hay alguna, NO mostrar esta nueva (solo se permite UNA a la vez)
    if (alertaPersonalizadaActiva || $('.alerta-personalizada').length > 0 || $('.alerta-overlay').length > 0) {
        console.log('Ya hay una alerta personalizada activa, ignorando alerta ID:', alerta.id);
        return;
    }
    
    // Marcar que hay una alerta personalizada activa INMEDIATAMENTE
    alertaPersonalizadaActiva = true;
    
    // Crear contenedor para la alerta personalizada
    var contenedor = $('<div>')
        .attr('id', 'alertaPersonalizada' + alerta.id)
        .addClass('alerta-personalizada')
        .html(alerta.html_personalizado)
        .css({
            'position': 'fixed',
            'top': '50%',
            'left': '50%',
            'transform': 'translate(-50%, -50%)',
            'z-index': '10000',
            'max-width': '90%',
            'max-height': '90vh',
            'overflow': 'auto'
        });
    
    // Agregar overlay
    var overlay = $('<div>')
        .addClass('alerta-overlay')
        .css({
            'position': 'fixed',
            'top': 0,
            'left': 0,
            'right': 0,
            'bottom': 0,
            'background': 'rgba(0,0,0,0.5)',
            'z-index': '9999'
        })
        .on('click', function() {
            cerrarAlertaPersonalizada(alerta.id);
        });
    
    $('body').append(overlay).append(contenedor);
    
    // Marcar como vista inmediatamente al aparecer
    marcarAlertaVista(alerta.id);
    
    // Agregar estilos personalizados
    if (alerta.estilo_personalizado) {
        var styleId = 'estilo-alerta-' + alerta.id;
        if (!$('#' + styleId).length) {
            $('<style id="' + styleId + '">').text(alerta.estilo_personalizado).appendTo('head');
        }
    }
}

// Cerrar banner
function cerrarBanner(idAlerta) {
    $('#alertaBanner' + idAlerta).fadeOut(300, function() {
        $(this).remove();
        
        // Restaurar padding del body
        $('body').css('padding-top', '');
    });
}

// Cerrar card
function cerrarCard(idAlerta) {
    $('#alertaCard' + idAlerta).fadeOut(300, function() {
        $(this).remove();
    });
}

// Cerrar alerta personalizada
function cerrarAlertaPersonalizada(idAlerta) {
    $('.alerta-overlay').remove();
    $('#alertaPersonalizada' + idAlerta).fadeOut(300, function() {
        $(this).remove();
        // Resetear flag de alerta personalizada activa
        alertaPersonalizadaActiva = false;
    });
}

// Marcar alerta como vista
function marcarAlertaVista(idAlerta) {
    if (!idAlerta) return;
    
    // Evitar marcar la misma alerta dos veces
    if (alertasMarcadasVista.has(idAlerta)) {
        console.log('Alerta ' + idAlerta + ' ya fue marcada como vista, omitiendo...');
        return;
    }
    
    // Marcar como procesada ANTES de hacer la petición
    alertasMarcadasVista.add(idAlerta);
    
    $.ajax({
        url: url + 'views/ajax/ajax_alertas.php',
        method: 'POST',
        data: {
            marcarVista: true,
            id_alerta: idAlerta
        },
        success: function(response) {
            // Opcional: log para debugging
            console.log('Alerta ' + idAlerta + ' marcada como vista');
        },
        error: function(xhr, status, error) {
            console.error('Error al marcar alerta como vista:', error);
            // Si hay error, quitar del Set para permitir reintento
            alertasMarcadasVista.delete(idAlerta);
        }
    });
}

// Mostrar tour guiado
function mostrarAlertaTourGuiado(alerta) {
    // Verificar que Driver esté disponible
    if (typeof Driver === 'undefined') {
        console.error('Sistema de tours no está disponible. Verifica que tour-guide.js esté cargado.');
        return;
    }
    
    if (!alerta.html_personalizado) {
        console.error('Tour guiado sin configuración de pasos');
        return;
    }

    try {
        var pasos = JSON.parse(alerta.html_personalizado);
        if (!Array.isArray(pasos) || pasos.length === 0) {
            console.error('Tour guiado sin pasos válidos');
            return;
        }

        // Obtener módulo actual
        var moduloActual = $('.moduloActual').val() || '';
        
        // Buscar el primer paso que corresponde al módulo actual
        var pasoActual = null;
        var indicePasoActual = -1;

        for (var i = 0; i < pasos.length; i++) {
            if (pasos[i].modulo === moduloActual) {
                pasoActual = pasos[i];
                indicePasoActual = i;
                break;
            }
        }

        // Si no hay paso para el módulo actual, buscar el primero que debe redirigir
        if (!pasoActual) {
            for (var j = 0; j < pasos.length; j++) {
                if (pasos[j].redirigir && pasos[j].modulo !== moduloActual) {
                    // Redirigir a ese módulo
                    window.location.href = url + pasos[j].modulo;
                    return;
                }
            }
            // Si no hay pasos para este módulo ni redirecciones, no mostrar tour
            return;
        }

        // Construir pasos de Driver.js solo para el módulo actual
        var driverSteps = [];
        
        // Agregar el paso actual y los siguientes del mismo módulo
        for (var k = indicePasoActual; k < pasos.length; k++) {
            if (pasos[k].modulo === moduloActual && pasos[k].selector) {
                var step = {
                    element: pasos[k].selector,
                    popover: {
                        title: pasos[k].titulo || alerta.titulo,
                        description: pasos[k].descripcion || alerta.mensaje,
                        position: pasos[k].posicion || 'top'
                    }
                };
                
                // Si el siguiente paso es de otro módulo y debe redirigir, agregar acción
                if (k < pasos.length - 1 && pasos[k + 1].modulo !== moduloActual && pasos[k + 1].redirigir) {
                    step.popover.onNextClick = function() {
                        window.location.href = url + pasos[k + 1].modulo;
                    };
                }
                
                driverSteps.push(step);
            } else if (pasos[k].modulo !== moduloActual) {
                break;
            }
        }

        if (driverSteps.length === 0) {
            console.error('No hay pasos válidos para el módulo actual');
            return;
        }

        // Esperar a que el DOM esté completamente cargado (más tiempo para elementos dinámicos)
        var intentosEspera = 0;
        var maxIntentosEspera = 20; // 10 segundos máximo (20 * 500ms)
        
        function verificarYMostrarTour() {
            // Verificar que los elementos existan
            var elementosExistentes = [];
            driverSteps.forEach(function(step) {
                // Intentar múltiples selectores si el primero no funciona
                var elemento = null;
                if ($(step.element).length > 0) {
                    elemento = $(step.element)[0];
                } else {
                    // Si es un selector de switch, intentar variaciones
                    if (step.element.includes('check_subcategoria')) {
                        var alternativas = [
                            '.check_subcategoria:first',
                            '.check_subcategoria:eq(0)',
                            '.form-check-input.check_subcategoria:first',
                            'input.check_subcategoria:first'
                        ];
                        for (var i = 0; i < alternativas.length; i++) {
                            if ($(alternativas[i]).length > 0) {
                                elemento = $(alternativas[i])[0];
                                step.element = alternativas[i]; // Actualizar selector
                                break;
                            }
                        }
                    }
                    // Si es un selector de botón de productos, intentar variaciones
                    if (step.element.includes('propietarios_menu_productos') || step.element.includes('Seleccionar productos')) {
                        var alternativas = [
                            'a.products-button-preview[href*="propietarios_menu_productos"]',
                            'a[href*="propietarios_menu_productos"]',
                            '.products-button-preview:contains("Seleccionar productos")',
                            'a:contains("Seleccionar productos")'
                        ];
                        for (var i = 0; i < alternativas.length; i++) {
                            if ($(alternativas[i]).length > 0) {
                                elemento = $(alternativas[i])[0];
                                step.element = alternativas[i]; // Actualizar selector
                                break;
                            }
                        }
                    }
                    // Si es un selector de check_productos, intentar variaciones
                    if (step.element.includes('check_productos')) {
                        var alternativas = [
                            '.check_productos:first',
                            '.check_productos:eq(0)',
                            '.form-check-input.check_productos:first',
                            'input.check_productos:first'
                        ];
                        for (var i = 0; i < alternativas.length; i++) {
                            if ($(alternativas[i]).length > 0) {
                                elemento = $(alternativas[i])[0];
                                step.element = alternativas[i]; // Actualizar selector
                                break;
                            }
                        }
                    }
                    // Si es un selector de botones de edición de productos, intentar variaciones
                    if (step.element.includes('btn_editar_ingre')) {
                        var alternativas = [
                            '.btn_editar_ingre:first',
                            '.btn-ingredientes.btn_editar_ingre:first',
                            'button.btn_editar_ingre:first'
                        ];
                        for (var i = 0; i < alternativas.length; i++) {
                            if ($(alternativas[i]).length > 0) {
                                elemento = $(alternativas[i])[0];
                                step.element = alternativas[i]; // Actualizar selector
                                break;
                            }
                        }
                    }
                    if (step.element.includes('btn_editar_tamanos')) {
                        var alternativas = [
                            '.btn_editar_tamanos:first',
                            '.btn-categorias.btn_editar_tamanos:first',
                            'button.btn_editar_tamanos:first'
                        ];
                        for (var i = 0; i < alternativas.length; i++) {
                            if ($(alternativas[i]).length > 0) {
                                elemento = $(alternativas[i])[0];
                                step.element = alternativas[i]; // Actualizar selector
                                break;
                            }
                        }
                    }
                    if (step.element.includes('btn_editar_precio_alimento')) {
                        var alternativas = [
                            '.btn_editar_precio_alimento:first',
                            '.btn-precio.btn_editar_precio_alimento:first',
                            'button.btn_editar_precio_alimento:first'
                        ];
                        for (var i = 0; i < alternativas.length; i++) {
                            if ($(alternativas[i]).length > 0) {
                                elemento = $(alternativas[i])[0];
                                step.element = alternativas[i]; // Actualizar selector
                                break;
                            }
                        }
                    }
                }
                
                if (elemento) {
                    elementosExistentes.push({
                        element: elemento,
                        popover: step.popover
                    });
                }
            });

            if (elementosExistentes.length === 0) {
                intentosEspera++;
                if (intentosEspera < maxIntentosEspera) {
                    // Esperar un poco más y reintentar
                    setTimeout(verificarYMostrarTour, 300);
                    return;
                } else {
                    console.warn('No se encontraron elementos para el tour en este módulo después de varios intentos');
                    // Marcar como vista para que no bloquee otras alertas
                    marcarAlertaVista(alerta.id);
                    // Mostrar siguiente alerta si existe
                    setTimeout(function() {
                        mostrarSiguienteAlertaDelModulo();
                    }, 100);
                    return;
                }
            }
            
            // Si encontramos elementos, continuar con el tour
            var driver = new Driver({
                allowClose: true,
                overlayClickNext: false,
                showButtons: ['next', 'previous', 'close'],
                keyboardControl: true,
                onHighlightStarted: function(element) {
                    // Marcar alerta como vista cuando se inicia el tour (primer paso)
                    if (!alertasMarcadasVista.has(alerta.id)) {
                        marcarAlertaVista(alerta.id);
                    }
                    // Scroll al elemento si es necesario
                    element.scrollIntoView({ behavior: 'smooth', block: 'center' });
                },
                onDestroyed: function() {
                    // Después de cerrar, verificar si hay otra alerta pendiente del mismo módulo
                    setTimeout(function() {
                        mostrarSiguienteAlertaDelModulo();
                    }, 200);
                }
            });

            // Iniciar el tour
            driver.drive(elementosExistentes);
        }
        
        // Iniciar verificación después de un delay inicial
        setTimeout(verificarYMostrarTour, 1000);

    } catch (e) {
        console.error('Error al parsear configuración del tour:', e);
    }
}

// Guardar alertas en localStorage (para usar después del login)
function guardarAlertasParaMostrar(alertas) {
    if (alertas && alertas.length > 0) {
        localStorage.setItem('alertasPendientes', JSON.stringify(alertas));
    }
}

// Función auxiliar para copiar código (ejemplo de uso en alertas personalizadas)
function copiarCodigoPrueba() {
    var codigo = 'PRUEBA2025';
    navigator.clipboard.writeText(codigo).then(function() {
        swal('¡Copiado!', 'Código ' + codigo + ' copiado al portapapeles', 'success');
    }).catch(function() {
        // Fallback para navegadores antiguos
        var textArea = document.createElement('textarea');
        textArea.value = codigo;
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand('copy');
        document.body.removeChild(textArea);
        swal('¡Copiado!', 'Código ' + codigo + ' copiado al portapapeles', 'success');
    });
}

// Función para mostrar la siguiente alerta del módulo (después de cerrar una)
function mostrarSiguienteAlertaDelModulo() {
    if (typeof alertasModuloActual === 'undefined' || !alertasModuloActual || alertasModuloActual.length === 0) {
        return;
    }
    
    // Filtrar alertas que aún no se han mostrado
    var alertasPendientes = alertasModuloActual.filter(function(alerta) {
        return !alertasMostradas.has(alerta.id);
    });
    
    // Verificar si ya se mostró una alerta personalizada
    var yaSeMostroPersonalizada = false;
    if (alertasModuloActual) {
        alertasModuloActual.forEach(function(alerta) {
            if (alerta.plantilla === 'personalizado' && alertasMostradas.has(alerta.id)) {
                yaSeMostroPersonalizada = true;
            }
        });
    }
    
    // Si ya se mostró una personalizada, filtrar todas las demás personalizadas
    if (yaSeMostroPersonalizada) {
        alertasPendientes = alertasPendientes.filter(function(alerta) {
            return alerta.plantilla !== 'personalizado';
        });
    }
    
    // Ordenar por prioridad (mayor primero)
    alertasPendientes.sort(function(a, b) {
        return (b.prioridad || 0) - (a.prioridad || 0);
    });
    
    // Mostrar la primera alerta pendiente
    if (alertasPendientes.length > 0) {
        var siguienteAlerta = alertasPendientes[0];
        alertasMostradas.add(siguienteAlerta.id);
        setTimeout(function() {
            mostrarAlerta(siguienteAlerta);
        }, 100);
    }
}

// Inicializar cuando el DOM esté listo
$(document).ready(function() {
    // Esperar un momento para que todo esté cargado
    setTimeout(function() {
        inicializarAlertas(); // Alertas del login
        inicializarAlertasModulo(); // Alertas del módulo actual
    }, 300);
});

