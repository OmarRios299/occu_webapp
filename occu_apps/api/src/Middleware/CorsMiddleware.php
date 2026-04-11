<?php

declare(strict_types=1);

namespace Occu\Api\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class CorsMiddleware implements MiddlewareInterface
{
    /** @var string[]|null */
    private ?array $allowedOrigins;

    public function __construct()
    {
        $raw = trim((string)(getenv('API_CORS_ORIGINS') ?: '*'));
        if ($raw === '*' || $raw === '') {
            $this->allowedOrigins = null; // wildcard
            return;
        }
        $this->allowedOrigins = array_values(array_filter(array_map('trim', explode(',', $raw))));
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $origin = $request->getHeaderLine('Origin');
        $allowOrigin = $this->resolveOrigin($origin);

        // Preflight
        if (strtoupper($request->getMethod()) === 'OPTIONS') {
            $response = new \Slim\Psr7\Response();
            return $this->withCorsHeaders($response, $allowOrigin);
        }

        $response = $handler->handle($request);
        return $this->withCorsHeaders($response, $allowOrigin);
    }

    private function resolveOrigin(string $origin): string
    {
        if ($this->allowedOrigins === null) {
            // wildcard: reflejar origin si viene, si no, *
            return $origin !== '' ? $origin : '*';
        }
        if ($origin !== '' && in_array($origin, $this->allowedOrigins, true)) {
            return $origin;
        }
        // default: no permitir origen no listado
        return $this->allowedOrigins[0] ?? '';
    }

    private function withCorsHeaders(ResponseInterface $response, string $allowOrigin): ResponseInterface
    {
        if ($allowOrigin === '') {
            return $response;
        }

        return $response
            ->withHeader('Access-Control-Allow-Origin', $allowOrigin)
            ->withHeader('Vary', 'Origin')
            ->withHeader('Access-Control-Allow-Credentials', 'true')
            ->withHeader('Access-Control-Allow-Methods', 'GET,POST,PUT,PATCH,DELETE,OPTIONS')
            ->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization');
    }
}

