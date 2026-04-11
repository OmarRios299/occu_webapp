<?php
declare(strict_types=1);

/**
 * Router para PHP built-in server.
 *
 * Uso:
 *   php -S 127.0.0.1:8010 dev-router.php
 *
 * Permite que rutas como /api/health funcionen sin Apache/.htaccess.
 */

$uriPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';

// Si existe un archivo real (assets), servirlo tal cual.
$fullPath = __DIR__ . $uriPath;
if ($uriPath !== '/' && is_file($fullPath)) {
    return false;
}

// Enrutar todo lo que comience con /api a la API Slim.
if ($uriPath === '/api' || str_starts_with($uriPath, '/api/')) {
    $_SERVER['SCRIPT_NAME'] = '/api/index.php';
    $_SERVER['SCRIPT_FILENAME'] = __DIR__ . '/api/index.php';
    require __DIR__ . '/api/index.php';
    return true;
}

// Si alguien abre el root, devolver una pista simple (no afecta vNext real).
header('Content-Type: text/plain; charset=utf-8');
http_response_code(200);
echo "Servidor dev activo (occu_apps).\n";
echo "API: http://127.0.0.1:8010/api/health\n";
echo "Nota: este router es solo para desarrollo local.\n";

