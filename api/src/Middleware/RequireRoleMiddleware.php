<?php

declare(strict_types=1);

namespace Occu\Api\Middleware;

use Occu\Api\Http\JsonResponder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Response;

/**
 * Valida que el usuario autenticado tenga uno de los roles permitidos.
 * Debe usarse después de RequireAuthMiddleware.
 *
 * @param string[] $allowedRoles Niveles permitidos (ej. ['Propietario','Administrador'])
 */
final class RequireRoleMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly array $allowedRoles,
        private readonly ?JsonResponder $json = null
    ) {}

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $user = RequireAuthMiddleware::getUser($request);
        if (!$user) {
            $responder = $this->json ?? new JsonResponder();
            return $responder->error(new Response(), 401, 'unauthorized', 'No autorizado');
        }

        $nivel = $user['nivel'] ?? '';
        if (!in_array($nivel, $this->allowedRoles, true)) {
            $responder = $this->json ?? new JsonResponder();
            return $responder->error(new Response(), 403, 'forbidden', 'No tienes permisos para acceder a esta ruta');
        }

        return $handler->handle($request);
    }
}
