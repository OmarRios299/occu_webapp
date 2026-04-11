<?php

declare(strict_types=1);

namespace Occu\Api\Middleware;

use Occu\Api\Http\JsonResponder;
use Occu\Api\Services\JwtService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class JwtAuthMiddleware implements MiddlewareInterface
{
    public const ATTR_PAYLOAD = 'auth.payload';

    public function __construct(
        private readonly JwtService $jwt,
        private readonly ?JsonResponder $json = null
    ) {}

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $auth = $request->getHeaderLine('Authorization');
        $token = $this->extractBearerToken($auth);
        if (!$token) {
            return $this->unauthorized();
        }

        $payload = $this->jwt->verifyAccessToken($token);
        if (!$payload) {
            return $this->unauthorized();
        }

        return $handler->handle($request->withAttribute(self::ATTR_PAYLOAD, $payload));
    }

    public static function getPayload(ServerRequestInterface $request): ?array
    {
        $p = $request->getAttribute(self::ATTR_PAYLOAD);
        return is_array($p) ? $p : null;
    }

    private function extractBearerToken(string $header): ?string
    {
        if (preg_match('/^Bearer\s+(.+)$/i', trim($header), $m)) {
            return trim($m[1]);
        }
        return null;
    }

    private function unauthorized(): ResponseInterface
    {
        $responder = $this->json ?? new JsonResponder();
        return $responder->error(new \Slim\Psr7\Response(), 401, 'unauthorized', 'No autorizado');
    }
}

