<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

/**
 * Migración: Menú escalable (personalización por producto)
 *
 * Crea tablas para:
 * - Plantillas OCCU por producto (reglas por categoría e ingrediente).
 * - Menú publicado por cafetería (copiando reglas de plantilla y permitiendo ajustes locales).
 * - Snapshot de personalizaciones en carrito y venta (consistencia histórica).
 *
 * Convenciones:
 * - Nombres en español y estilo actual: menu_*, cafeterias_*, ventas_*.
 * - `seleccion_maxima` permite NULL = sin límite (evita números mágicos como 999).
 * - Incluye `agotado` por ingrediente a nivel cafetería.
 */
final class MenuPersonalizacionV2 extends AbstractMigration
{
    public function change(): void
    {
        $tableOptions = [
            'engine' => 'InnoDB',
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_general_ci',
        ];

        // ==========================================================
        // B) Plantillas OCCU por producto (reglas)
        // ==========================================================
        $tReglasCategorias = $this->table('menu_productos_reglas_ingredientes_categorias', $tableOptions);
        $tReglasCategorias
            ->addColumn('id_producto', 'integer', ['null' => false, 'signed' => true])
            ->addColumn('id_ingrediente_categoria', 'integer', ['null' => false, 'signed' => true])
            ->addColumn('tipo_seleccion', 'string', [
                'limit' => 15,
                'null' => false,
                'default' => 'unica', // unica | multiple
                'comment' => 'unica | multiple',
            ])
            ->addColumn('seleccion_minima', 'integer', [
                'null' => false,
                'signed' => true,
                'default' => 0,
            ])
            ->addColumn('seleccion_maxima', 'integer', [
                'null' => true,
                'signed' => true,
                'default' => null,
                'comment' => 'NULL = sin límite',
            ])
            ->addColumn('orden', 'integer', [
                'null' => false,
                'signed' => true,
                'default' => 0,
            ])
            ->addColumn('estado', 'integer', [
                'null' => false,
                'signed' => true,
                'default' => 0,
                'comment' => '0=activo, 1=inactivo, 2=eliminado',
            ])
            ->addColumn('id_alta', 'integer', ['null' => false, 'signed' => true])
            ->addColumn('fecha_alta', 'datetime', ['null' => false])
            ->addIndex(['id_producto'], ['name' => 'idx_mpric_producto'])
            ->addIndex(['id_ingrediente_categoria'], ['name' => 'idx_mpric_categoria'])
            ->addIndex(['id_producto', 'id_ingrediente_categoria'], [
                'unique' => true,
                'name' => 'uniq_mpric_producto_categoria',
            ])
            ->create();

        $tReglasIngredientes = $this->table('menu_productos_reglas_ingredientes', $tableOptions);
        $tReglasIngredientes
            ->addColumn('id_producto', 'integer', ['null' => false, 'signed' => true])
            ->addColumn('id_ingrediente', 'integer', ['null' => false, 'signed' => true])
            ->addColumn('permite_cantidad', 'boolean', [
                'null' => false,
                'default' => false,
            ])
            ->addColumn('cantidad_minima', 'integer', [
                'null' => false,
                'signed' => true,
                'default' => 0,
            ])
            ->addColumn('cantidad_maxima', 'integer', [
                'null' => false,
                'signed' => true,
                'default' => 1,
            ])
            ->addColumn('paso_cantidad', 'integer', [
                'null' => false,
                'signed' => true,
                'default' => 1,
            ])
            ->addColumn('cantidad_incluida', 'integer', [
                'null' => false,
                'signed' => true,
                'default' => 0,
            ])
            ->addColumn('tipo_precio', 'string', [
                'limit' => 20,
                'null' => false,
                'default' => 'por_porcion', // por_porcion | fijo
                'comment' => 'por_porcion | fijo',
            ])
            ->addColumn('precio_unitario', 'decimal', [
                'precision' => 10,
                'scale' => 2,
                'null' => false,
                'default' => 0,
            ])
            ->addColumn('es_recomendado', 'boolean', [
                'null' => false,
                'default' => false,
            ])
            ->addColumn('estado', 'integer', [
                'null' => false,
                'signed' => true,
                'default' => 0,
                'comment' => '0=activo, 1=inactivo, 2=eliminado',
            ])
            ->addColumn('id_alta', 'integer', ['null' => false, 'signed' => true])
            ->addColumn('fecha_alta', 'datetime', ['null' => false])
            ->addIndex(['id_producto'], ['name' => 'idx_mpri_producto'])
            ->addIndex(['id_ingrediente'], ['name' => 'idx_mpri_ingrediente'])
            ->addIndex(['id_producto', 'id_ingrediente'], [
                'unique' => true,
                'name' => 'uniq_mpri_producto_ingrediente',
            ])
            ->create();

        // ==========================================================
        // C) Menú por cafetería (publicación real)
        // ==========================================================
        $tMenuCafProd = $this->table('cafeterias_menu_productos', $tableOptions);
        $tMenuCafProd
            ->addColumn('id_cafeteria', 'integer', ['null' => false, 'signed' => true])
            ->addColumn('id_producto', 'integer', ['null' => false, 'signed' => true])
            ->addColumn('precio_base', 'decimal', [
                'precision' => 10,
                'scale' => 2,
                'null' => false,
                'default' => 0,
            ])
            ->addColumn('estado', 'integer', [
                'null' => false,
                'signed' => true,
                'default' => 0,
                'comment' => '0=activo, 1=inactivo, 2=eliminado',
            ])
            ->addColumn('id_alta', 'integer', ['null' => false, 'signed' => true])
            ->addColumn('fecha_alta', 'datetime', ['null' => false])
            ->addIndex(['id_cafeteria', 'estado'], ['name' => 'idx_cmp_cafeteria_estado'])
            ->addIndex(['id_producto'], ['name' => 'idx_cmp_producto'])
            ->addIndex(['id_cafeteria', 'id_producto'], [
                'unique' => true,
                'name' => 'uniq_cmp_cafeteria_producto',
            ])
            ->create();

        $tMenuCafProdTamanos = $this->table('cafeterias_menu_productos_tamanos', $tableOptions);
        $tMenuCafProdTamanos
            ->addColumn('id_cafeteria_menu_producto', 'integer', ['null' => false, 'signed' => true])
            ->addColumn('id_tamano', 'integer', ['null' => false, 'signed' => true])
            ->addColumn('precio', 'decimal', [
                'precision' => 10,
                'scale' => 2,
                'null' => false,
                'default' => 0,
            ])
            ->addColumn('estado', 'integer', [
                'null' => false,
                'signed' => true,
                'default' => 0,
                'comment' => '0=activo, 1=inactivo, 2=eliminado',
            ])
            ->addIndex(['id_cafeteria_menu_producto', 'estado'], ['name' => 'idx_cmpt_producto_estado'])
            ->addIndex(['id_tamano'], ['name' => 'idx_cmpt_tamano'])
            ->addIndex(['id_cafeteria_menu_producto', 'id_tamano'], [
                'unique' => true,
                'name' => 'uniq_cmpt_producto_tamano',
            ])
            ->create();

        $tCafReglasCategorias = $this->table('cafeterias_menu_productos_reglas_ingredientes_categorias', $tableOptions);
        $tCafReglasCategorias
            ->addColumn('id_cafeteria_menu_producto', 'integer', ['null' => false, 'signed' => true])
            ->addColumn('id_ingrediente_categoria', 'integer', ['null' => false, 'signed' => true])
            ->addColumn('tipo_seleccion', 'string', [
                'limit' => 15,
                'null' => false,
                'default' => 'unica',
                'comment' => 'unica | multiple',
            ])
            ->addColumn('seleccion_minima', 'integer', [
                'null' => false,
                'signed' => true,
                'default' => 0,
            ])
            ->addColumn('seleccion_maxima', 'integer', [
                'null' => true,
                'signed' => true,
                'default' => null,
                'comment' => 'NULL = sin límite',
            ])
            ->addColumn('orden', 'integer', [
                'null' => false,
                'signed' => true,
                'default' => 0,
            ])
            ->addColumn('estado', 'integer', [
                'null' => false,
                'signed' => true,
                'default' => 0,
                'comment' => '0=activo, 1=inactivo, 2=eliminado',
            ])
            ->addIndex(['id_cafeteria_menu_producto', 'estado'], ['name' => 'idx_cmp_ric_producto_estado'])
            ->addIndex(['id_ingrediente_categoria'], ['name' => 'idx_cmp_ric_categoria'])
            ->addIndex(['id_cafeteria_menu_producto', 'id_ingrediente_categoria'], [
                'unique' => true,
                'name' => 'uniq_cmp_ric_producto_categoria',
            ])
            ->create();

        $tCafReglasIngredientes = $this->table('cafeterias_menu_productos_reglas_ingredientes', $tableOptions);
        $tCafReglasIngredientes
            ->addColumn('id_cafeteria_menu_producto', 'integer', ['null' => false, 'signed' => true])
            ->addColumn('id_ingrediente', 'integer', ['null' => false, 'signed' => true])
            ->addColumn('agotado', 'boolean', [
                'null' => false,
                'default' => false,
                'comment' => 'true = no disponible en esta cafetería',
            ])
            ->addColumn('permite_cantidad', 'boolean', [
                'null' => false,
                'default' => false,
            ])
            ->addColumn('cantidad_minima', 'integer', [
                'null' => false,
                'signed' => true,
                'default' => 0,
            ])
            ->addColumn('cantidad_maxima', 'integer', [
                'null' => false,
                'signed' => true,
                'default' => 1,
            ])
            ->addColumn('paso_cantidad', 'integer', [
                'null' => false,
                'signed' => true,
                'default' => 1,
            ])
            ->addColumn('cantidad_incluida', 'integer', [
                'null' => false,
                'signed' => true,
                'default' => 0,
            ])
            ->addColumn('tipo_precio', 'string', [
                'limit' => 20,
                'null' => false,
                'default' => 'por_porcion',
                'comment' => 'por_porcion | fijo',
            ])
            ->addColumn('precio_unitario', 'decimal', [
                'precision' => 10,
                'scale' => 2,
                'null' => false,
                'default' => 0,
            ])
            ->addColumn('es_recomendado', 'boolean', [
                'null' => false,
                'default' => false,
            ])
            ->addColumn('estado', 'integer', [
                'null' => false,
                'signed' => true,
                'default' => 0,
                'comment' => '0=activo, 1=inactivo, 2=eliminado',
            ])
            ->addIndex(['id_cafeteria_menu_producto', 'estado'], ['name' => 'idx_cmp_ri_producto_estado'])
            ->addIndex(['id_ingrediente'], ['name' => 'idx_cmp_ri_ingrediente'])
            ->addIndex(['id_cafeteria_menu_producto', 'id_ingrediente'], [
                'unique' => true,
                'name' => 'uniq_cmp_ri_producto_ingrediente',
            ])
            ->create();

        // ==========================================================
        // D) Snapshot carrito / venta (personalizaciones seleccionadas)
        // ==========================================================
        $tCarritoPers = $this->table('ventas_carrito_items_personalizaciones', $tableOptions);
        $tCarritoPers
            ->addColumn('id_carrito_item', 'integer', ['null' => false, 'signed' => true])
            ->addColumn('id_ingrediente', 'integer', ['null' => false, 'signed' => true])
            ->addColumn('id_ingrediente_categoria_snapshot', 'integer', [
                'null' => true,
                'signed' => true,
                'default' => null,
            ])
            ->addColumn('nombre_ingrediente_snapshot', 'string', [
                'limit' => 255,
                'null' => false,
                'default' => '',
            ])
            ->addColumn('cantidad', 'integer', ['null' => false, 'signed' => true, 'default' => 0])
            ->addColumn('precio_unitario_snapshot', 'decimal', [
                'precision' => 10,
                'scale' => 2,
                'null' => false,
                'default' => 0,
            ])
            ->addColumn('monto_total_snapshot', 'decimal', [
                'precision' => 10,
                'scale' => 2,
                'null' => false,
                'default' => 0,
            ])
            ->addColumn('estado', 'integer', [
                'null' => false,
                'signed' => true,
                'default' => 0,
                'comment' => '0=activo, 2=eliminado',
            ])
            ->addColumn('fecha_alta', 'datetime', ['null' => false])
            ->addIndex(['id_carrito_item'], ['name' => 'idx_vcip_item'])
            ->addIndex(['id_ingrediente'], ['name' => 'idx_vcip_ingrediente'])
            ->addIndex(['id_carrito_item', 'id_ingrediente'], [
                'unique' => true,
                'name' => 'uniq_vcip_item_ingrediente',
            ])
            ->create();

        $tVentaPers = $this->table('ventas_items_personalizaciones', $tableOptions);
        $tVentaPers
            ->addColumn('id_venta_item', 'integer', ['null' => false, 'signed' => true])
            ->addColumn('id_ingrediente', 'integer', ['null' => false, 'signed' => true])
            ->addColumn('id_ingrediente_categoria_snapshot', 'integer', [
                'null' => true,
                'signed' => true,
                'default' => null,
            ])
            ->addColumn('nombre_ingrediente_snapshot', 'string', [
                'limit' => 255,
                'null' => false,
                'default' => '',
            ])
            ->addColumn('cantidad', 'integer', ['null' => false, 'signed' => true, 'default' => 0])
            ->addColumn('precio_unitario_snapshot', 'decimal', [
                'precision' => 10,
                'scale' => 2,
                'null' => false,
                'default' => 0,
            ])
            ->addColumn('monto_total_snapshot', 'decimal', [
                'precision' => 10,
                'scale' => 2,
                'null' => false,
                'default' => 0,
            ])
            ->addColumn('estado', 'integer', [
                'null' => false,
                'signed' => true,
                'default' => 0,
                'comment' => '0=activo, 2=eliminado',
            ])
            ->addColumn('fecha_alta', 'datetime', ['null' => false])
            ->addIndex(['id_venta_item'], ['name' => 'idx_vip_venta_item'])
            ->addIndex(['id_ingrediente'], ['name' => 'idx_vip_ingrediente'])
            ->addIndex(['id_venta_item', 'id_ingrediente'], [
                'unique' => true,
                'name' => 'uniq_vip_item_ingrediente',
            ])
            ->create();
    }
}

