<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

/**
 * Migración mínima para API (Slim): Refresh tokens independientes del sitio actual.
 *
 * - No toca tablas existentes del login web.
 * - Permite refresh token rotation y logout por token.
 */
final class ApiRefreshTokens extends AbstractMigration
{
    public function change(): void
    {
        if ($this->hasTable('api_refresh_tokens')) {
            return;
        }

        $table = $this->table('api_refresh_tokens', [
            'engine' => 'InnoDB',
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_general_ci',
        ]);

        $table
            ->addColumn('id_usuario', 'integer', [
                'null' => false,
                'signed' => true,
            ])
            ->addColumn('token_hash', 'string', [
                'limit' => 64, // sha256 hex
                'null' => false,
            ])
            ->addColumn('replaced_by_hash', 'string', [
                'limit' => 64,
                'null' => true,
                'default' => null,
            ])
            ->addColumn('ip_address', 'string', [
                'limit' => 45,
                'null' => true,
                'default' => null,
            ])
            ->addColumn('user_agent', 'text', [
                'null' => true,
                'default' => null,
            ])
            ->addColumn('created_at', 'datetime', [
                'null' => false,
            ])
            ->addColumn('expires_at', 'datetime', [
                'null' => false,
            ])
            ->addColumn('revoked_at', 'datetime', [
                'null' => true,
                'default' => null,
            ])
            ->addIndex(['token_hash'], ['unique' => true, 'name' => 'uniq_token_hash'])
            ->addIndex(['id_usuario'], ['name' => 'idx_usuario'])
            ->addIndex(['expires_at'], ['name' => 'idx_expires'])
            ->create();
    }
}

