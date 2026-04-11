<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

/**
 * Migración: Menú general del propietario (borrador)
 *
 * Crea tablas para el menú base del propietario que NO afecta cafeterías hasta publicar.
 * Estructura similar a las plantillas OCCU pero específica por propietario.
 */
final class PropietariosMenuGeneral extends AbstractMigration
{
    public function change(): void
    {
        $tableOptions = [
            'engine' => 'InnoDB',
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_general_ci',
        ];

        // ==========================================================
        // A) Productos del menú general del propietario
        // ==========================================================
        if (!$this->hasTable('propietarios_menu_productos')) {
        $tMenuProd = $this->table('propietarios_menu_productos', $tableOptions);
        $tMenuProd
            ->addColumn('id_propietario', 'integer', ['null' => false, 'signed' => true])
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
            ->addIndex(['id_propietario', 'estado'], ['name' => 'idx_pmp_propietario_estado'])
            ->addIndex(['id_producto'], ['name' => 'idx_pmp_producto'])
            ->addIndex(['id_propietario', 'id_producto'], [
                'unique' => true,
                'name' => 'uniq_pmp_propietario_producto',
            ])
            ->addForeignKey('id_propietario', 'admin_usuarios', 'id', [
                'constraint' => 'fk_pmp_propietario',
                'delete' => 'CASCADE',
                'update' => 'CASCADE',
            ])
            ->addForeignKey('id_producto', 'menu_productos', 'id', [
                'constraint' => 'fk_pmp_producto',
                'delete' => 'CASCADE',
                'update' => 'CASCADE',
            ])
            ->create();
        }

        // ==========================================================
        // B) Tamaños y precios por tamaño
        // ==========================================================
        if (!$this->hasTable('propietarios_menu_productos_tamanos')) {
        $tMenuProdTamanos = $this->table('propietarios_menu_productos_tamanos', $tableOptions);
        $tMenuProdTamanos
            ->addColumn('id_propietario_menu_producto', 'integer', ['null' => false, 'signed' => false])
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
            ->addIndex(['id_propietario_menu_producto', 'estado'], ['name' => 'idx_pmpt_producto_estado'])
            ->addIndex(['id_tamano'], ['name' => 'idx_pmpt_tamano'])
            ->addIndex(['id_propietario_menu_producto', 'id_tamano'], [
                'unique' => true,
                'name' => 'uniq_pmpt_producto_tamano',
            ])
            ->addForeignKey('id_propietario_menu_producto', 'propietarios_menu_productos', 'id', [
                'constraint' => 'fk_pmpt_producto',
                'delete' => 'CASCADE',
                'update' => 'CASCADE',
            ])
            ->addForeignKey('id_tamano', 'menu_productos_tamanos', 'id', [
                'constraint' => 'fk_pmpt_tamano',
                'delete' => 'CASCADE',
                'update' => 'CASCADE',
            ])
            ->create();
        }

        // ==========================================================
        // C) Reglas por categoría de ingredientes
        // ==========================================================
        if (!$this->hasTable('propietarios_menu_productos_reglas_ingredientes_categorias')) {
        $tReglasCategorias = $this->table('propietarios_menu_productos_reglas_ingredientes_categorias', $tableOptions);
        $tReglasCategorias
            ->addColumn('id_propietario_menu_producto', 'integer', ['null' => false, 'signed' => false])
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
            ->addColumn('id_alta', 'integer', ['null' => false, 'signed' => true])
            ->addColumn('fecha_alta', 'datetime', ['null' => false])
            ->addIndex(['id_propietario_menu_producto'], ['name' => 'idx_pmpric_producto'])
            ->addIndex(['id_ingrediente_categoria'], ['name' => 'idx_pmpric_categoria'])
            ->addIndex(['id_propietario_menu_producto', 'id_ingrediente_categoria'], [
                'unique' => true,
                'name' => 'uniq_pmpric_producto_categoria',
            ])
            ->addForeignKey('id_propietario_menu_producto', 'propietarios_menu_productos', 'id', [
                'constraint' => 'fk_pmpric_producto',
                'delete' => 'CASCADE',
                'update' => 'CASCADE',
            ])
            ->addForeignKey('id_ingrediente_categoria', 'menu_ingredientes_categorias', 'id', [
                'constraint' => 'fk_pmpric_categoria',
                'delete' => 'CASCADE',
                'update' => 'CASCADE',
            ])
            ->create();
        }

        // ==========================================================
        // D) Reglas por ingrediente
        // ==========================================================
        if (!$this->hasTable('propietarios_menu_productos_reglas_ingredientes')) {
        $tReglasIngredientes = $this->table('propietarios_menu_productos_reglas_ingredientes', $tableOptions);
        $tReglasIngredientes
            ->addColumn('id_propietario_menu_producto', 'integer', ['null' => false, 'signed' => false])
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
            ->addColumn('id_alta', 'integer', ['null' => false, 'signed' => true])
            ->addColumn('fecha_alta', 'datetime', ['null' => false])
            ->addIndex(['id_propietario_menu_producto'], ['name' => 'idx_pmpri_producto'])
            ->addIndex(['id_ingrediente'], ['name' => 'idx_pmpri_ingrediente'])
            ->addIndex(['id_propietario_menu_producto', 'id_ingrediente'], [
                'unique' => true,
                'name' => 'uniq_pmpri_producto_ingrediente',
            ])
            ->addForeignKey('id_propietario_menu_producto', 'propietarios_menu_productos', 'id', [
                'constraint' => 'fk_pmpri_producto',
                'delete' => 'CASCADE',
                'update' => 'CASCADE',
            ])
            ->addForeignKey('id_ingrediente', 'menu_ingredientes', 'id', [
                'constraint' => 'fk_pmpri_ingrediente',
                'delete' => 'CASCADE',
                'update' => 'CASCADE',
            ])
            ->create();
        }
    }
}
