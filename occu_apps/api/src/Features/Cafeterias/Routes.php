<?php

declare(strict_types=1);

namespace Occu\Api\Features\Cafeterias;

use Occu\Api\Features\Cafeterias\Actions\CafeteriasActions;
use Occu\Api\Features\Cafeterias\Actions\CiudadesActions;
use Occu\Api\Features\Cafeterias\Actions\ComentariosActions;
use Occu\Api\Features\Cafeterias\Actions\MenuActions;
use Occu\Api\Features\Cafeterias\Actions\ServiciosActions;
use Occu\Api\Http\JsonResponder;
use Occu\Api\Middleware\RequireAuthMiddleware;
use Occu\Api\Repositories\CafeteriasMenuRepository;
use Occu\Api\Repositories\CafeteriasRepository;
use Occu\Api\Repositories\CiudadesRepository;
use Occu\Api\Repositories\ServiciosRepository;
use Occu\Api\Services\CafeteriasService;
use Slim\App;

final class Routes
{
    /**
     * @param array{
     *   json: JsonResponder,
     *   requireAuth: RequireAuthMiddleware,
     *   cafeteriasRepo: CafeteriasRepository,
     *   cafeteriasService: CafeteriasService,
     *   serviciosRepo: ServiciosRepository,
     *   ciudadesRepo: CiudadesRepository,
     *   cafeteriasMenuRepo: CafeteriasMenuRepository
     * } $deps
     */
    public static function register(App $app, array $deps): void
    {
        $json = $deps['json'];
        $requireAuth = $deps['requireAuth'];
        $cafeteriasRepo = $deps['cafeteriasRepo'];
        $cafeteriasService = $deps['cafeteriasService'];
        $serviciosRepo = $deps['serviciosRepo'];
        $ciudadesRepo = $deps['ciudadesRepo'];
        $cafeteriasMenuRepo = $deps['cafeteriasMenuRepo'];

        $cafeteriasActions = new CafeteriasActions($json, $cafeteriasRepo, $cafeteriasService);
        $serviciosActions = new ServiciosActions($json, $serviciosRepo);
        $ciudadesActions = new CiudadesActions($json, $ciudadesRepo);
        $comentariosActions = new ComentariosActions($json, $cafeteriasRepo);
        $menuActions = new MenuActions($json, $cafeteriasMenuRepo);

        // Cafeterías (público)
        $app->get('/cafeterias', [$cafeteriasActions, 'list']);
        $app->get('/servicios', [$serviciosActions, 'list']);
        $app->get('/ciudades', [$ciudadesActions, 'list']);
        $app->get('/ciudades/{id}', [$ciudadesActions, 'get']);

        $app->get('/cafeterias/{id}/comentarios', [$comentariosActions, 'list']);
        $app->post('/cafeterias/{id}/comentarios', [$comentariosActions, 'create'])->add($requireAuth);

        // Menú de cafetería (público) - DEBE IR ANTES de /cafeterias/{id} para que Slim lo reconozca
        $app->get('/cafeterias/{id}/menu', [$menuActions, 'getMenu']);
        $app->get('/cafeterias/{id}/menu/productos/{productoId}', [$menuActions, 'getProducto']);

        // Detalle de cafetería (público)
        $app->get('/cafeterias/{id}', [$cafeteriasActions, 'detail']);
    }
}

