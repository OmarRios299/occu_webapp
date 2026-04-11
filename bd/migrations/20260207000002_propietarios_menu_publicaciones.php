<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

/**
 * Migración: Auditoría de publicaciones de menú a cafeterías
 *
 * Registra las acciones de publicación del menú general del propietario a sus cafeterías.
 */
final class PropietariosMenuPublicaciones extends AbstractMigration
{
    public function change(): void
    {
        $tableOptions = [
            'engine' => 'InnoDB',
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_general_ci',
        ];

        // Verificar si la tabla ya existe
        if ($this->hasTable('propietarios_menu_publicaciones')) {
            return;
        }

        $tPublicaciones = $this->table('propietarios_menu_publicaciones', $tableOptions);
        $tPublicaciones
            ->addColumn('id_propietario', 'integer', ['null' => false, 'signed' => true])
            ->addColumn('tipo', 'string', [
                'limit' => 20,
                'null' => false,
                'default' => 'todo',
                'comment' => 'todo | solo_precios',
            ])
            ->addColumn('cafeterias_afectadas', 'text', [
                'null' => false,
                'comment' => 'JSON array de IDs de cafeterías',
            ])
            ->addColumn('productos_publicados', 'integer', [
                'null' => false,
                'signed' => true,
                'default' => 0,
            ])
            ->addColumn('fecha_publicacion', 'datetime', ['null' => false])
            ->addIndex(['id_propietario'], ['name' => 'idx_pmp_propietario'])
            ->addIndex(['fecha_publicacion'], ['name' => 'idx_pmp_fecha'])
            ->addForeignKey('id_propietario', 'admin_usuarios', 'id', [
                'constraint' => 'fk_pmp_propietario',
                'delete' => 'CASCADE',
                'update' => 'CASCADE',
            ])
            ->create();

        // Agregar columna de última publicación a cafeterias_menu_productos
        // Esto se puede hacer con una migración separada o aquí mismo
        // Por ahora, usaremos la tabla de publicaciones para consultar la última fecha
    }
}
