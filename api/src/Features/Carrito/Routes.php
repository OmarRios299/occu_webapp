<?php

declare(strict_types=1);

namespace Occu\Api\Features\Carrito;

use Occu\Api\Features\Carrito\Actions\CarritoActions;
use Occu\Api\Http\JsonResponder;
use Occu\Api\Middleware\RequireRoleMiddleware;
use Occu\Api\Repositories\CarritoRepository;
use Occu\Api\Services\CarritoService;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;

final class Routes
{
    /**
     * @param array{
     *   json: JsonResponder,
     *   requireAuth: RequireAuthMiddleware,
     *   requireClienteRole: RequireRoleMiddleware,
     *   carritoService: CarritoService,
     *   carritoRepo: CarritoRepository
     * } $deps
     */
    public static function register(App $app, array $deps): void
    {
        $json = $deps['json'];
        $requireAuth = $deps['requireAuth'];
        $requireClienteRole = $deps['requireClienteRole'];
        $carritoService = $deps['carritoService'];
        $carritoRepo = $deps['carritoRepo'];

        $actions = new CarritoActions($json, $carritoService, $carritoRepo);

        // Carrito (solo Cliente) - grupo con middleware
        $app->group('/carrito', function (RouteCollectorProxy $carritoGroup) use ($actions) {
            $carritoGroup->post('/items', [$actions, 'addItem']);
            $carritoGroup->get('', [$actions, 'get']);
        })->add($requireClienteRole)->add($requireAuth);
    }
}

