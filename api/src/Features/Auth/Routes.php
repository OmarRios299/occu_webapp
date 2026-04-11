<?php

declare(strict_types=1);

namespace Occu\Api\Features\Auth;

use Occu\Api\Features\Auth\Actions\AuthActions;
use Occu\Api\Http\JsonResponder;
use Occu\Api\Middleware\RequireAuthMiddleware;
use Occu\Api\Services\AuthService;
use Slim\App;

final class Routes
{
    /**
     * @param array{json: JsonResponder, auth: AuthService, requireAuth: RequireAuthMiddleware} $deps
     */
    public static function register(App $app, array $deps): void
    {
        $auth = $deps['auth'];
        $requireAuth = $deps['requireAuth'];
        $actions = new AuthActions($deps['json'], $auth);

        // Auth (público)
        $app->post('/auth/register', [$actions, 'register']);
        $app->post('/auth/verify-pin', [$actions, 'verifyPin']);
        $app->post('/auth/resend-pin', [$actions, 'resendPin']);
        $app->post('/auth/login', [$actions, 'login']);
        $app->post('/auth/refresh', [$actions, 'refresh']);
        $app->post('/auth/google', [$actions, 'google']);
        $app->post('/auth/google/complete', [$actions, 'googleComplete']);
        $app->post('/auth/logout', [$actions, 'logout']);

        // Auth (privado)
        $app->get('/auth/me', [$actions, 'me'])->add($requireAuth);
    }
}

