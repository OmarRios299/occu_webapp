<?php
declare(strict_types=1);

namespace Occu\Api\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Stream;

/**
 * Fallback de parseo JSON.
 *
 * En algunos entornos el BodyParsingMiddleware puede no popular `getParsedBody()`
 * para `application/json`. Este middleware:
 * - Sólo actúa si el Content-Type es JSON
 * - Sólo si `getParsedBody()` viene vacío
 * - Intenta json_decode del body y hace `withParsedBody()` si es válido
 *
 * Importante: rebobina el stream para no romper middlewares posteriores.
 */
final class JsonBodyFallbackMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $apiDebug = filter_var(getenv('API_DEBUG') ?: '1', FILTER_VALIDATE_BOOLEAN);
        if ($apiDebug) {
            error_log('[JsonBodyFallbackMiddleware] hit method=' . $request->getMethod() . ' ct=' . $request->getHeaderLine('Content-Type'));
        }

        $method = strtoupper($request->getMethod());
        if (!in_array($method, ['POST', 'PUT', 'PATCH', 'DELETE'], true)) {
            return $handler->handle($request);
        }

        $contentType = strtolower($request->getHeaderLine('Content-Type'));
        if ($contentType === '' || !str_contains($contentType, 'application/json')) {
            return $handler->handle($request);
        }

        $parsed = $request->getParsedBody();
        if (is_array($parsed) && $parsed !== []) {
            return $handler->handle($request);
        }
        if (is_object($parsed) && (array)$parsed !== []) {
            return $handler->handle($request);
        }

        // Leer el stream (puede NO ser seekable; lo rehidratamos en php://temp)
        $raw = (string)$request->getBody();
        $rawLen = strlen($raw);
        $decodedOk = 0;
        $tmp = fopen('php://temp', 'r+');
        if (is_resource($tmp)) {
            fwrite($tmp, $raw);
            rewind($tmp);
            $request = $request->withBody(new Stream($tmp));
        }

        $rawTrim = trim($raw);

        if ($rawTrim !== '') {
            try {
                /** @var mixed $decoded */
                $decoded = json_decode($rawTrim, true, 512, JSON_THROW_ON_ERROR);
                if (is_array($decoded)) {
                    $decodedOk = 1;
                    $request = $request->withParsedBody($decoded);
                }
            } catch (\Throwable) {
                // noop
            }
        }

        $response = $handler->handle($request);
        if ($apiDebug) {
            return $response->withHeader('X-OCCU-JsonFallback', 'rawLen=' . $rawLen . ';decoded=' . $decodedOk);
        }
        return $response;
    }
}

