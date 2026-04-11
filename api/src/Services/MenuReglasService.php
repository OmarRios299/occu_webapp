<?php

declare(strict_types=1);

namespace Occu\Api\Services;

/**
 * Service para validar reglas de menú según las reglas de negocio
 */
final class MenuReglasService
{
    /**
     * Valida las reglas antes de guardar
     * 
     * @param array{
     *     categorias: array<int, array{
     *         id_ingrediente_categoria: int,
     *         tipo_seleccion: 'unica'|'multiple',
     *         seleccion_minima: int,
     *         seleccion_maxima: int|null,
     *         orden: int
     *     }>,
     *     ingredientes: array<int, array{
     *         id_ingrediente: int,
     *         permite_cantidad: bool,
     *         cantidad_minima: int,
     *         cantidad_maxima: int,
     *         paso_cantidad: int,
     *         cantidad_incluida: int,
     *         tipo_precio: 'por_porcion'|'fijo',
     *         precio_unitario: float,
     *         es_recomendado: bool
     *     }>
     * } $reglas
     * @throws \RuntimeException Si hay errores de validación
     */
    public function validateReglas(array $reglas): void
    {
        $errors = [];

        // Validar categorías
        foreach ($reglas['categorias'] ?? [] as $idx => $cat) {
            $prefix = "Categoría #{$idx}";

            // Validar tipo_seleccion
            if (!in_array($cat['tipo_seleccion'], ['unica', 'multiple'], true)) {
                $errors[] = "{$prefix}: tipo_seleccion debe ser 'unica' o 'multiple'";
            }

            // Si tipo_seleccion = unica
            if ($cat['tipo_seleccion'] === 'unica') {
                if ($cat['seleccion_maxima'] !== 1 && $cat['seleccion_maxima'] !== null) {
                    $errors[] = "{$prefix}: Si tipo_seleccion es 'unica', seleccion_maxima debe ser 1 o NULL";
                }
                if (!in_array($cat['seleccion_minima'], [0, 1], true)) {
                    $errors[] = "{$prefix}: Si tipo_seleccion es 'unica', seleccion_minima debe ser 0 o 1";
                }
            }

            // Validar seleccion_minima >= 0
            if ($cat['seleccion_minima'] < 0) {
                $errors[] = "{$prefix}: seleccion_minima debe ser >= 0";
            }

            // Validar seleccion_maxima si no es NULL
            if ($cat['seleccion_maxima'] !== null && $cat['seleccion_maxima'] < $cat['seleccion_minima']) {
                $errors[] = "{$prefix}: seleccion_maxima debe ser >= seleccion_minima";
            }
        }

        // Validar ingredientes
        foreach ($reglas['ingredientes'] ?? [] as $idx => $ing) {
            $prefix = "Ingrediente #{$idx}";

            // Validar tipo_precio
            if (!in_array($ing['tipo_precio'], ['por_porcion', 'fijo'], true)) {
                $errors[] = "{$prefix}: tipo_precio debe ser 'por_porcion' o 'fijo'";
            }

            // Si tipo_precio = fijo, forzar restricciones
            if ($ing['tipo_precio'] === 'fijo') {
                if ($ing['permite_cantidad'] !== false) {
                    $errors[] = "{$prefix}: Si tipo_precio es 'fijo', permite_cantidad debe ser false";
                }
                if ($ing['cantidad_maxima'] !== 1) {
                    $errors[] = "{$prefix}: Si tipo_precio es 'fijo', cantidad_maxima debe ser 1";
                }
                if ($ing['cantidad_minima'] > 1) {
                    $errors[] = "{$prefix}: Si tipo_precio es 'fijo', cantidad_minima no puede ser mayor a 1";
                }
            }

            // Si permite_cantidad = true
            if ($ing['permite_cantidad'] === true) {
                if ($ing['cantidad_maxima'] < $ing['cantidad_minima']) {
                    $errors[] = "{$prefix}: cantidad_maxima debe ser >= cantidad_minima";
                }
                if ($ing['paso_cantidad'] < 1) {
                    $errors[] = "{$prefix}: paso_cantidad debe ser >= 1";
                }
            }

            // Validar cantidad_incluida >= 0
            if ($ing['cantidad_incluida'] < 0) {
                $errors[] = "{$prefix}: cantidad_incluida debe ser >= 0";
            }

            // Validar precio_unitario >= 0
            if ($ing['precio_unitario'] < 0) {
                $errors[] = "{$prefix}: precio_unitario debe ser >= 0";
            }
        }

        if (!empty($errors)) {
            $mensaje = 'Errores de validación en las reglas:' . "\n" . implode("\n", array_map(fn($e) => "• {$e}", $errors));
            throw new \RuntimeException($mensaje);
        }
    }
}
