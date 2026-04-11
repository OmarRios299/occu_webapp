<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

/**
 * Migración: Agregar campos de contador de ignorar a sistema_alertas
 * 
 * Agrega campos para controlar cuántas veces ignorar una alerta
 * antes de mostrarla cuando aparece un elemento específico en pantalla
 */
final class AlertasContadorIgnorar extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change(): void
    {
        // Agregar campo veces_ignorar a sistema_alertas
        $table_alertas = $this->table('sistema_alertas');
        
        $table_alertas->addColumn('veces_ignorar', 'integer', [
            'signed' => true,
            'null' => false,
            'default' => 0,
            'comment' => 'Número de veces a ignorar antes de mostrar la alerta',
            'after' => 'veces_mostrar'
        ])
        ->update();
        
        // Agregar campo veces_ignorada a usuarios_alertas_vistas para guardar el contador por usuario
        $table_vistas = $this->table('usuarios_alertas_vistas');
        
        $table_vistas->addColumn('veces_ignorada', 'integer', [
            'signed' => true,
            'null' => false,
            'default' => 0,
            'comment' => 'Número de veces que el usuario ha ignorado esta alerta',
            'after' => 'veces_vista'
        ])
        ->update();
    }
}

