<?php

declare(strict_types=1);

use Phinx\Seed\AbstractSeed;

/**
 * Seed: ejemplos de reglas (plantillas OCCU) para validar escenarios canónicos.
 *
 * Asume IDs del initial_db.sql (pueden ajustarse si cambian):
 * - Producto 3: Latte
 * - Producto 2: Americano
 * - Categoría ingredientes 2: Leche
 * - Categoría ingredientes 4: Saborizantes /Jarabes
 * - Ingredientes leche: 4..10 (Leche Entera, Light, Deslactosada, Almendra, Coco, Soya, Avena)
 * - Jarabes: 16..23 (Vainilla, Avellana, Caramelo, Chocolate Blanco, Menta, Chai, Matcha, Almendra Dulce)
 */
final class MenuReglasEjemploSeed extends AbstractSeed
{
    public function run(): void
    {
        $now = date('Y-m-d H:i:s');
        $idAlta = 1;

        // ----------------------------
        // Producto 3 (Latte): Leche obligatoria + Jarabes con cantidad 0..3
        // ----------------------------
        $this->execute("
            INSERT INTO menu_productos_reglas_ingredientes_categorias
                (id_producto, id_ingrediente_categoria, tipo_seleccion, seleccion_minima, seleccion_maxima, orden, estado, id_alta, fecha_alta)
            VALUES
                (3, 2, 'unica', 1, 1, 10, 0, {$idAlta}, '{$now}'),
                (3, 4, 'multiple', 0, NULL, 20, 0, {$idAlta}, '{$now}')
            ON DUPLICATE KEY UPDATE
                tipo_seleccion = VALUES(tipo_seleccion),
                seleccion_minima = VALUES(seleccion_minima),
                seleccion_maxima = VALUES(seleccion_maxima),
                orden = VALUES(orden),
                estado = VALUES(estado);
        ");

        // Leches (sin cantidad, precio fijo 0 por defecto)
        $leches = [4, 5, 6, 7, 8, 9, 10];
        foreach ($leches as $idIngrediente) {
            $this->execute("
                INSERT INTO menu_productos_reglas_ingredientes
                    (id_producto, id_ingrediente, permite_cantidad, cantidad_minima, cantidad_maxima, paso_cantidad, cantidad_incluida, tipo_precio, precio_unitario, es_recomendado, estado, id_alta, fecha_alta)
                VALUES
                    (3, {$idIngrediente}, 0, 0, 1, 1, 0, 'fijo', 0.00, 0, 0, {$idAlta}, '{$now}')
                ON DUPLICATE KEY UPDATE
                    permite_cantidad = VALUES(permite_cantidad),
                    cantidad_minima = VALUES(cantidad_minima),
                    cantidad_maxima = VALUES(cantidad_maxima),
                    paso_cantidad = VALUES(paso_cantidad),
                    cantidad_incluida = VALUES(cantidad_incluida),
                    tipo_precio = VALUES(tipo_precio),
                    precio_unitario = VALUES(precio_unitario),
                    es_recomendado = VALUES(es_recomendado),
                    estado = VALUES(estado);
            ");
        }

        // Jarabes (con cantidad 0..3, precio por porción)
        $jarabes = [16, 17, 18, 19, 20, 21, 22, 23];
        foreach ($jarabes as $idIngrediente) {
            $recomendado = ($idIngrediente === 16) ? 1 : 0; // Vainilla recomendado
            $this->execute("
                INSERT INTO menu_productos_reglas_ingredientes
                    (id_producto, id_ingrediente, permite_cantidad, cantidad_minima, cantidad_maxima, paso_cantidad, cantidad_incluida, tipo_precio, precio_unitario, es_recomendado, estado, id_alta, fecha_alta)
                VALUES
                    (3, {$idIngrediente}, 1, 0, 3, 1, 0, 'por_porcion', 3.00, {$recomendado}, 0, {$idAlta}, '{$now}')
                ON DUPLICATE KEY UPDATE
                    permite_cantidad = VALUES(permite_cantidad),
                    cantidad_minima = VALUES(cantidad_minima),
                    cantidad_maxima = VALUES(cantidad_maxima),
                    paso_cantidad = VALUES(paso_cantidad),
                    cantidad_incluida = VALUES(cantidad_incluida),
                    tipo_precio = VALUES(tipo_precio),
                    precio_unitario = VALUES(precio_unitario),
                    es_recomendado = VALUES(es_recomendado),
                    estado = VALUES(estado);
            ");
        }

        // ----------------------------
        // Producto 2 (Americano): Jarabes con cantidad 0..3 (sin leche)
        // ----------------------------
        $this->execute("
            INSERT INTO menu_productos_reglas_ingredientes_categorias
                (id_producto, id_ingrediente_categoria, tipo_seleccion, seleccion_minima, seleccion_maxima, orden, estado, id_alta, fecha_alta)
            VALUES
                (2, 4, 'multiple', 0, NULL, 10, 0, {$idAlta}, '{$now}')
            ON DUPLICATE KEY UPDATE
                tipo_seleccion = VALUES(tipo_seleccion),
                seleccion_minima = VALUES(seleccion_minima),
                seleccion_maxima = VALUES(seleccion_maxima),
                orden = VALUES(orden),
                estado = VALUES(estado);
        ");

        foreach ($jarabes as $idIngrediente) {
            $recomendado = ($idIngrediente === 16) ? 1 : 0;
            $this->execute("
                INSERT INTO menu_productos_reglas_ingredientes
                    (id_producto, id_ingrediente, permite_cantidad, cantidad_minima, cantidad_maxima, paso_cantidad, cantidad_incluida, tipo_precio, precio_unitario, es_recomendado, estado, id_alta, fecha_alta)
                VALUES
                    (2, {$idIngrediente}, 1, 0, 3, 1, 0, 'por_porcion', 3.00, {$recomendado}, 0, {$idAlta}, '{$now}')
            ON DUPLICATE KEY UPDATE
                    permite_cantidad = VALUES(permite_cantidad),
                    cantidad_minima = VALUES(cantidad_minima),
                    cantidad_maxima = VALUES(cantidad_maxima),
                    paso_cantidad = VALUES(paso_cantidad),
                    cantidad_incluida = VALUES(cantidad_incluida),
                    tipo_precio = VALUES(tipo_precio),
                    precio_unitario = VALUES(precio_unitario),
                    es_recomendado = VALUES(es_recomendado),
                    estado = VALUES(estado);
            ");
        }
    }
}

