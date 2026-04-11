<?php

declare(strict_types=1);

// API independiente (Slim) montada en /api

require_once __DIR__ . '/../config/env.php';
loadEnv(__DIR__ . '/../.env');

$autoload = __DIR__ . '/vendor/autoload.php';
if (!file_exists($autoload)) {
    http_response_code(500);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'success' => false,
        'errorCode' => 'vendor_missing',
        'message' => 'Dependencias no instaladas. Ejecuta composer install dentro de /api.',
    ]);
    exit;
}

require $autoload;

use Occu\Api\App;

App::run();

