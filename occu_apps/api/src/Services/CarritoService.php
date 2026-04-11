<?php

declare(strict_types=1);

namespace Occu\Api\Services;

use Occu\Api\Repositories\CafeteriasMenuRepository;
use Occu\Api\Repositories\CarritoRepository;
use RuntimeException;

final class CarritoService
{
    public function __construct(
        private CarritoRepository $carritoRepo,
        private CafeteriasMenuRepository $menuRepo
    ) {
    }

    /**
     * Valida y agrega un item al carrito
     * 
     * @param int $userId
     * @param int $cafeteriaId
     * @param int $productoId
     * @param int|null $tamanoId
     * @param array<int, int> $selecciones Map de ingredienteId => cantidad
     * @param int $cantidad
     * @return array{success: bool, item_id?: int, cantidad_total?: int, message?: string}
     */
    public function agregarItem(
        int $userId,
        int $cafeteriaId,
        int $productoId,
        ?int $tamanoId,
        array $selecciones,
        int $cantidad
    ): array {
        // Validar cantidad
        if ($cantidad < 1) {
            return ['success' => false, 'message' => 'La cantidad debe ser al menos 1'];
        }

        // Obtener producto con reglas
        $producto = $this->menuRepo->getProducto($cafeteriaId, $productoId);
        if (!$producto) {
            return ['success' => false, 'message' => 'Producto no encontrado o no disponible'];
        }

        // Validar tamaño si hay tamaños disponibles
        if (count($producto['tamanos']) > 0) {
            if ($tamanoId === null) {
                return ['success' => false, 'message' => 'Debes seleccionar un tamaño'];
            }
            $tamanoValido = false;
            foreach ($producto['tamanos'] as $tamano) {
                if ($tamano['id'] === $tamanoId) {
                    $tamanoValido = true;
                    break;
                }
            }
            if (!$tamanoValido) {
                return ['success' => false, 'message' => 'Tamaño inválido'];
            }
        } else {
            // Si no hay tamaños, tamanoId debe ser null
            $tamanoId = null;
        }

        // Validar reglas por categoría
        $validacion = $this->validarReglas($producto['reglas_categorias'], $selecciones);
        if (!$validacion['valido']) {
            return ['success' => false, 'message' => $validacion['mensaje']];
        }

        // Calcular precios de personalizaciones
        $personalizaciones = $this->calcularPersonalizaciones($producto['reglas_categorias'], $selecciones);

        // Obtener o crear carrito
        $carritoId = $this->carritoRepo->obtenerOCrearCarrito($userId, $cafeteriaId);

        // Buscar item igual para merge
        $itemExistente = $this->carritoRepo->buscarItemIgual($carritoId, $productoId, $tamanoId, $selecciones);

        if ($itemExistente) {
            // Merge: incrementar cantidad
            $nuevaCantidad = (int)$itemExistente['cantidad'] + $cantidad;
            $this->carritoRepo->actualizarCantidadItem((int)$itemExistente['id'], $nuevaCantidad);
            return [
                'success' => true,
                'item_id' => (int)$itemExistente['id'],
                'cantidad_total' => $nuevaCantidad,
            ];
        }

        // Crear nuevo item
        $itemId = $this->carritoRepo->agregarItem($carritoId, $productoId, $tamanoId, $cantidad);

        // Guardar personalizaciones (snapshot)
        $this->carritoRepo->guardarPersonalizaciones($itemId, $personalizaciones);

        return [
            'success' => true,
            'item_id' => $itemId,
            'cantidad_total' => $cantidad,
        ];
    }

    /**
     * Valida que las selecciones cumplan con las reglas
     * 
     * @param array $reglasCategorias
     * @param array<int, int> $selecciones
     * @return array{valido: bool, mensaje?: string}
     */
    private function validarReglas(array $reglasCategorias, array $selecciones): array
    {
        foreach ($reglasCategorias as $reglaCat) {
            $ingredientesEnCategoria = array_filter(
                $reglaCat['ingredientes'],
                fn($ing) => isset($selecciones[$ing['id']]) && $selecciones[$ing['id']] > 0
            );

            $cantidadSeleccionada = count($ingredientesEnCategoria);

            // Validar mínimo
            if ($cantidadSeleccionada < $reglaCat['seleccion_minima']) {
                return [
                    'valido' => false,
                    'mensaje' => "Debes seleccionar al menos {$reglaCat['seleccion_minima']} " .
                        ($reglaCat['seleccion_minima'] === 1 ? 'opción' : 'opciones') .
                        " en {$reglaCat['nombre']}",
                ];
            }

            // Validar máximo
            if ($reglaCat['seleccion_maxima'] !== null && $cantidadSeleccionada > $reglaCat['seleccion_maxima']) {
                return [
                    'valido' => false,
                    'mensaje' => "Puedes seleccionar máximo {$reglaCat['seleccion_maxima']} " .
                        ($reglaCat['seleccion_maxima'] === 1 ? 'opción' : 'opciones') .
                        " en {$reglaCat['nombre']}",
                ];
            }

            // Validar ingredientes seleccionados
            foreach ($ingredientesEnCategoria as $ing) {
                $cantidad = $selecciones[$ing['id']];

                // Validar agotado
                if ($ing['agotado']) {
                    return [
                        'valido' => false,
                        'mensaje' => "El ingrediente {$ing['nombre']} está agotado",
                    ];
                }

                // Validar límites de cantidad
                if ($cantidad < $ing['cantidad_minima']) {
                    return [
                        'valido' => false,
                        'mensaje' => "La cantidad mínima para {$ing['nombre']} es {$ing['cantidad_minima']}",
                    ];
                }

                if ($cantidad > $ing['cantidad_maxima']) {
                    return [
                        'valido' => false,
                        'mensaje' => "La cantidad máxima para {$ing['nombre']} es {$ing['cantidad_maxima']}",
                    ];
                }

                // Validar paso de cantidad
                if ($ing['permite_cantidad'] && ($cantidad - $ing['cantidad_minima']) % $ing['paso_cantidad'] !== 0) {
                    return [
                        'valido' => false,
                        'mensaje' => "La cantidad para {$ing['nombre']} debe ser múltiplo de {$ing['paso_cantidad']}",
                    ];
                }
            }
        }

        return ['valido' => true];
    }

    /**
     * Calcula los precios de las personalizaciones aplicando cantidad_incluida
     * 
     * @param array $reglasCategorias
     * @param array<int, int> $selecciones
     * @return array<array{ingrediente_id: int, categoria_id: int|null, nombre: string, cantidad: int, precio_unitario: float, monto_total: float}>
     */
    private function calcularPersonalizaciones(array $reglasCategorias, array $selecciones): array
    {
        $personalizaciones = [];

        foreach ($reglasCategorias as $reglaCat) {
            foreach ($reglaCat['ingredientes'] as $ing) {
                $cantidad = $selecciones[$ing['id']] ?? 0;
                if ($cantidad <= 0) {
                    continue;
                }

                // Calcular monto según tipo de precio y cantidad_incluida
                $montoTotal = 0.0;

                if ($ing['tipo_precio'] === 'fijo') {
                    // Precio fijo: se cobra una vez independientemente de cantidad
                    $montoTotal = $ing['precio_unitario'];
                } else {
                    // Por porción: cobrar solo por porciones adicionales
                    $porcionesAdicionales = max(0, $cantidad - $ing['cantidad_incluida']);
                    $montoTotal = $porcionesAdicionales * $ing['precio_unitario'];
                }

                $personalizaciones[] = [
                    'ingrediente_id' => $ing['id'],
                    'categoria_id' => $reglaCat['id'],
                    'nombre' => $ing['nombre'],
                    'cantidad' => $cantidad,
                    'precio_unitario' => $ing['precio_unitario'],
                    'monto_total' => $montoTotal,
                ];
            }
        }

        return $personalizaciones;
    }
}
