<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

/**
 * Migración: Sistema de Alertas y Registro de Sesiones
 * 
 * Crea las tablas necesarias para:
 * 1. Registrar sesiones de usuarios (detectar primera vez)
 * 2. Sistema de alertas configurable
 * 3. Registro de alertas vistas por usuarios
 */
final class SistemaAlertasYSesiones extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     */
    public function change(): void
    {
        // 1. Tabla de sesiones de usuarios
        $table_sesiones = $this->table('usuarios_sesiones', [
            'engine' => 'InnoDB',
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_general_ci'
        ]);
        
        $table_sesiones->addColumn('id_usuario', 'integer', [
            'signed' => true,
            'null' => false
        ])
        ->addColumn('fecha_login', 'datetime', [
            'null' => false
        ])
        ->addColumn('ip_address', 'string', [
            'limit' => 45,
            'null' => true,
            'default' => null
        ])
        ->addColumn('user_agent', 'text', [
            'null' => true,
            'default' => null
        ])
        ->addColumn('token_sesion', 'string', [
            'limit' => 255,
            'null' => true,
            'default' => null
        ])
        ->addColumn('tipo_login', 'string', [
            'limit' => 20,
            'null' => false,
            'default' => 'normal',
            'comment' => 'normal, google, token'
        ])
        ->addIndex(['id_usuario', 'fecha_login'], ['name' => 'idx_usuario_fecha'])
        ->addIndex(['id_usuario'], ['name' => 'idx_usuario'])
        ->addIndex(['fecha_login'], ['name' => 'idx_fecha_login'])
        ->create();

        // 2. Tabla de alertas del sistema
        $table_alertas = $this->table('sistema_alertas', [
            'engine' => 'InnoDB',
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_general_ci'
        ]);
        
        $table_alertas->addColumn('codigo', 'string', [
            'limit' => 100,
            'null' => false,
            'comment' => 'Identificador único: primera_vez, onboarding_paso_1, etc.'
        ])
        ->addColumn('titulo', 'text', [
            'null' => false
        ])
        ->addColumn('mensaje', 'text', [
            'null' => false
        ])
        ->addColumn('tipo', 'string', [
            'limit' => 50,
            'null' => false,
            'default' => 'info',
            'comment' => 'info, warning, success, error'
        ])
        ->addColumn('icono', 'string', [
            'limit' => 100,
            'null' => true,
            'default' => null
        ])
        ->addColumn('tipo_mostrar', 'string', [
            'limit' => 20,
            'null' => false,
            'default' => 'una_vez',
            'comment' => 'una_vez, n_veces, mientras_activa'
        ])
        ->addColumn('veces_mostrar', 'integer', [
            'signed' => true,
            'null' => false,
            'default' => 1,
            'comment' => 'Solo si tipo_mostrar = n_veces'
        ])
        ->addColumn('fecha_inicio', 'datetime', [
            'null' => true,
            'default' => null,
            'comment' => 'NULL = siempre activa'
        ])
        ->addColumn('fecha_fin', 'datetime', [
            'null' => true,
            'default' => null,
            'comment' => 'NULL = sin fecha de fin'
        ])
        ->addColumn('activa', 'boolean', [
            'null' => false,
            'default' => true
        ])
        ->addColumn('id_modulo', 'integer', [
            'signed' => true,
            'null' => true,
            'default' => null,
            'comment' => 'NULL = global, o id de permisos_modulos'
        ])
        ->addColumn('criterios_rol', 'text', [
            'null' => true,
            'default' => null,
            'comment' => 'JSON: ["Administrador", "Cliente"] o NULL = todos'
        ])
        ->addColumn('criterios_version', 'string', [
            'limit' => 50,
            'null' => true,
            'default' => null,
            'comment' => 'Versión mínima requerida'
        ])
        ->addColumn('criterios_permisos', 'text', [
            'null' => true,
            'default' => null,
            'comment' => 'JSON con condiciones adicionales'
        ])
        ->addColumn('prioridad', 'integer', [
            'signed' => true,
            'null' => false,
            'default' => 0,
            'comment' => 'Mayor = más importante'
        ])
        ->addColumn('plantilla', 'string', [
            'limit' => 50,
            'null' => false,
            'default' => 'default',
            'comment' => 'default, modal, banner, card, personalizado'
        ])
        ->addColumn('html_personalizado', 'text', [
            'limit' => \Phinx\Db\Adapter\MysqlAdapter::TEXT_LONG,
            'null' => true,
            'default' => null,
            'comment' => 'HTML completo personalizado (si plantilla=personalizado)'
        ])
        ->addColumn('botones', 'text', [
            'null' => true,
            'default' => null,
            'comment' => 'JSON: [{"texto":"Ir a...", "url":"/ruta", "tipo":"primary"}, ...]'
        ])
        ->addColumn('estilo_personalizado', 'text', [
            'null' => true,
            'default' => null,
            'comment' => 'CSS personalizado adicional'
        ])
        ->addColumn('estado', 'integer', [
            'signed' => true,
            'null' => false,
            'default' => 0
        ])
        ->addColumn('id_alta', 'integer', [
            'signed' => true,
            'null' => false
        ])
        ->addColumn('fecha_alta', 'datetime', [
            'null' => false
        ])
        ->addIndex(['codigo'], ['unique' => true, 'name' => 'codigo'])
        ->addIndex(['activa', 'estado'], ['name' => 'idx_activa'])
        ->addIndex(['id_modulo'], ['name' => 'idx_modulo'])
        ->addIndex(['fecha_inicio', 'fecha_fin'], ['name' => 'idx_fechas'])
        ->create();

        // 3. Tabla de alertas vistas por usuarios
        $table_vistas = $this->table('usuarios_alertas_vistas', [
            'engine' => 'InnoDB',
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_general_ci'
        ]);
        
        $table_vistas->addColumn('id_usuario', 'integer', [
            'signed' => true,
            'null' => false,
            'limit' => 11
        ])
        ->addColumn('id_alerta', 'integer', [
            'signed' => true,
            'null' => false,
            'limit' => 11
        ])
        ->addColumn('fecha_vista', 'datetime', [
            'null' => false
        ])
        ->addColumn('veces_vista', 'integer', [
            'signed' => true,
            'null' => false,
            'default' => 1,
            'limit' => 11
        ])
        ->addIndex(['id_usuario'], ['name' => 'idx_usuario'])
        ->addIndex(['id_alerta'], ['name' => 'idx_alerta'])
        ->addIndex(['id_usuario', 'id_alerta'], ['unique' => true, 'name' => 'idx_usuario_alerta'])
        ->create();

        // 4. Insertar alerta de ejemplo: Primera vez
        $this->execute("
            INSERT INTO `sistema_alertas` 
            (`codigo`, `titulo`, `mensaje`, `tipo`, `tipo_mostrar`, `veces_mostrar`, `criterios_rol`, `activa`, `estado`, `id_alta`, `fecha_alta`)
            VALUES 
            ('primera_vez_bienvenida', 
             '¡Bienvenido a OCCU!', 
             'Esta es tu primera vez aquí. Explora nuestras cafeterías y disfruta del mejor café.',
             'success',
             'una_vez',
             1,
             '[\"Cliente\", \"Propietario\", \"Barista\"]',
             1,
             0,
             1,
             NOW())
            ON DUPLICATE KEY UPDATE codigo = codigo;
        ");
    }
}

