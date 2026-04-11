<?php

declare(strict_types=1);

// Config Phinx (vNext)
// - No hardcodear credenciales aquí.
// - Tomar DB_* desde variables de entorno (cargadas desde el .env del root vía `api/index.php` / `config/env.php`).

// Cargar .env del root para que Phinx use la MISMA BD que la API.
$envLoader = __DIR__ . '/../config/env.php';
$envFile = __DIR__ . '/../.env';
if (file_exists($envLoader) && file_exists($envFile)) {
    require_once $envLoader;
    if (function_exists('loadEnv')) {
        loadEnv($envFile);
    }
}

return [
    'paths' => [
        'migrations' => '%%PHINX_CONFIG_DIR%%/bd/migrations',
        'seeds' => '%%PHINX_CONFIG_DIR%%/bd/seeds',
    ],
    'environments' => [
        'default_migration_table' => 'phinxlog',
        'default_environment' => 'development',
        'production' => [
            'adapter' => 'mysql',
            'host' => getenv('DB_HOST') ?: 'localhost',
            'name' => getenv('DB_NAME') ?: 'occu_v2',
            'user' => getenv('DB_USER') ?: 'root',
            'pass' => getenv('DB_PASS') ?: '',
            'port' => getenv('DB_PORT') ?: '3306',
            'charset' => 'utf8mb4',
        ],
        'development' => [
            'adapter' => 'mysql',
            'host' => getenv('DB_HOST') ?: 'localhost',
            'name' => getenv('DB_NAME') ?: 'occu_v2',
            'user' => getenv('DB_USER') ?: 'root',
            'pass' => getenv('DB_PASS') ?: '',
            'port' => getenv('DB_PORT') ?: '3306',
            'charset' => 'utf8mb4',
        ],
        'testing' => [
            'adapter' => 'mysql',
            'host' => getenv('DB_HOST') ?: 'localhost',
            'name' => (getenv('DB_NAME') ?: 'occu_v2') . '_test',
            'user' => getenv('DB_USER') ?: 'root',
            'pass' => getenv('DB_PASS') ?: '',
            'port' => getenv('DB_PORT') ?: '3306',
            'charset' => 'utf8mb4',
        ],
    ],
    'version_order' => 'creation',
];
