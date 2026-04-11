<?php

declare(strict_types=1);

namespace Occu\Api\Features\Owner;

use Occu\Api\Features\Owner\Actions\CafeteriasActions;
use Occu\Api\Features\Owner\Actions\MenuGeneralProductosActions;
use Occu\Api\Features\Owner\Actions\MenuPublicarActions;
use Occu\Api\Http\JsonResponder;
use Occu\Api\Middleware\RequireAuthMiddleware;
use Occu\Api\Middleware\RequireRoleMiddleware;
use Occu\Api\Repositories\OwnerCafeteriasRepository;
use Occu\Api\Repositories\PropietariosMenuGeneralRepository;
use Occu\Api\Repositories\PropietariosMenuPublicacionesRepository;
use Occu\Api\Services\MenuReglasService;
use Occu\Api\Services\PropietariosMenuPublicacionService;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;

final class Routes
{
    /**
     * @param array{
     *   json: JsonResponder,
     *   requireAuth: RequireAuthMiddleware,
     *   requireOwnerRole: RequireRoleMiddleware,
     *   ownerCafeteriasRepo: OwnerCafeteriasRepository,
     *   propietariosMenuPublicacionesRepo: PropietariosMenuPublicacionesRepository,
     *   propietariosMenuGeneralRepo: PropietariosMenuGeneralRepository,
     *   propietariosMenuPublicacionService: PropietariosMenuPublicacionService,
     *   menuReglasService: MenuReglasService
     * } $deps
     */
    public static function register(App $app, array $deps): void
    {
        $json = $deps['json'];
        $requireAuth = $deps['requireAuth'];
        $requireOwnerRole = $deps['requireOwnerRole'];
        $ownerCafeteriasRepo = $deps['ownerCafeteriasRepo'];
        $propietariosMenuPublicacionesRepo = $deps['propietariosMenuPublicacionesRepo'];
        $propietariosMenuGeneralRepo = $deps['propietariosMenuGeneralRepo'];
        $propietariosMenuPublicacionService = $deps['propietariosMenuPublicacionService'];
        $menuReglasService = $deps['menuReglasService'];

        $cafeteriasActions = new CafeteriasActions($json, $ownerCafeteriasRepo, $propietariosMenuPublicacionesRepo);
        $menuGeneralActions = new MenuGeneralProductosActions($json, $propietariosMenuGeneralRepo, $menuReglasService);
        $menuPublicarActions = new MenuPublicarActions($json, $propietariosMenuPublicacionService);

        // Owner (Propietario/Administrador) - grupo con middleware
        $app->group('/owner', function (RouteCollectorProxy $group) use ($cafeteriasActions, $menuGeneralActions, $menuPublicarActions) {
            $group->get('/cafeterias', [$cafeteriasActions, 'list']);
            $group->get('/cafeterias/estado-menu', [$cafeteriasActions, 'estadoMenu']);
            $group->get('/cafeterias/{id}', [$cafeteriasActions, 'get']);
            $group->patch('/cafeterias/{id}/estado', [$cafeteriasActions, 'patchEstado']);
            $group->delete('/cafeterias/{id}', [$cafeteriasActions, 'delete']);
            $group->get('/cafeterias/{id}/servicios', [$cafeteriasActions, 'getServicios']);
            $group->put('/cafeterias/{id}/servicios', [$cafeteriasActions, 'putServicios']);
            $group->post('/cafeterias', [$cafeteriasActions, 'create']);
            $group->put('/cafeterias/{id}', [$cafeteriasActions, 'update']);

            $group->get('/menu-general/productos', [$menuGeneralActions, 'list']);
            $group->put('/menu-general/productos', [$menuGeneralActions, 'bulkUpdate']);
            $group->get('/menu-general/productos/{id}', [$menuGeneralActions, 'get']);
            $group->get('/menu-general/productos/{id}/reglas', [$menuGeneralActions, 'getReglas']);
            $group->put('/menu-general/productos/{id}/reglas', [$menuGeneralActions, 'putReglas']);

            $group->post('/menu-publicar', [$menuPublicarActions, 'publicar']);
        })->add($requireOwnerRole)->add($requireAuth);
    }
}

