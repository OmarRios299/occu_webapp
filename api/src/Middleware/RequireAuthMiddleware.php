<?php

declare(strict_types=1);

namespace Occu\Api\Middleware;

use Occu\Api\Http\JsonResponder;
use Occu\Api\Repositories\UserRepository;
use Occu\Api\Services\JwtService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Response;

/**
 * Valida JWT, carga el usuario y adjunta userId y user al request.
 * Atributos: auth.userId (int), auth.user (array).
 */
final class RequireAuthMiddleware implements MiddlewareInterface
{
    public const ATTR_USER_ID = 'auth.userId';
    public const ATTR_USER = 'auth.user';

    public function __construct(
        private readonly JwtService $jwt,
        private readonly UserRepository $users,
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
        if (!$payload || empty($payload['sub'])) {
            return $this->unauthorized();
        }

        $userId = (int)$payload['sub'];
        $user = $this->users->findById($userId);
        if (!$user) {
            return $this->unauthorized();
        }

        $request = $request
            ->withAttribute(JwtAuthMiddleware::ATTR_PAYLOAD, $payload)
            ->withAttribute(self::ATTR_USER_ID, $userId)
            ->withAttribute(self::ATTR_USER, $user);

        return $handler->handle($request);
    }

    public static function getUserId(ServerRequestInterface $request): int
    {
        return (int)$request->getAttribute(self::ATTR_USER_ID, 0);
    }

    /** @return array|null */
    public static function getUser(ServerRequestInterface $request): ?array
    {
        $u = $request->getAttribute(self::ATTR_USER);
        return is_array($u) ? $u : null;
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
        return $responder->error(new Response(), 401, 'unauthorized', 'No autorizado');
    }
}
