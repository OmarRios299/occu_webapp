<?php

declare(strict_types=1);

namespace Occu\Api\Features\Auth\Actions;

use Occu\Api\Http\JsonResponder;
use Occu\Api\Middleware\RequireAuthMiddleware;
use Occu\Api\Services\AuthService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class AuthActions
{
    public function __construct(
        private readonly JsonResponder $json,
        private readonly AuthService $auth,
    ) {
    }

    // Público (delegado a AuthService)
    public function register(Request $request, Response $response, array $args = []): Response
    {
        return $this->auth->register($request, $response);
    }

    public function verifyPin(Request $request, Response $response, array $args = []): Response
    {
        return $this->auth->verifyPin($request, $response);
    }

    public function resendPin(Request $request, Response $response, array $args = []): Response
    {
        return $this->auth->resendPin($request, $response);
    }

    public function login(Request $request, Response $response, array $args = []): Response
    {
        return $this->auth->login($request, $response);
    }

    public function refresh(Request $request, Response $response, array $args = []): Response
    {
        return $this->auth->refresh($request, $response);
    }

    public function google(Request $request, Response $response, array $args = []): Response
    {
        return $this->auth->googleLogin($request, $response);
    }

    public function googleComplete(Request $request, Response $response, array $args = []): Response
    {
        return $this->auth->googleComplete($request, $response);
    }

    public function logout(Request $request, Response $response, array $args = []): Response
    {
        return $this->auth->logout($request, $response);
    }

    // Privado
    public function me(Request $request, Response $response, array $args = []): Response
    {
        $user = RequireAuthMiddleware::getUser($request);
        return $this->json->ok($response, [
            'id' => (int)$user['id'],
            'nombre' => $user['nombre'] ?? null,
            'apellido' => $user['apellido'] ?? null,
            'correo' => $user['correo_electronico'] ?? null,
            'nivel' => $user['nivel'] ?? null,
            'verificado' => $user['verificado'] ?? null,
            'ciudad' => isset($user['id_ciudad']) ? (int)$user['id_ciudad'] : null,
            'imagen' => $user['imagen'] ?? null,
        ]);
    }
}

