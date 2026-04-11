<?php

declare(strict_types=1);

namespace Occu\Api\Features\AdminMenu;

use Occu\Api\Features\AdminMenu\Actions\CatalogosCategoriasActions;
use Occu\Api\Features\AdminMenu\Actions\CatalogosIngredientesActions;
use Occu\Api\Features\AdminMenu\Actions\CatalogosIngredientesCategoriasActions;
use Occu\Api\Features\AdminMenu\Actions\CatalogosProductosActions;
use Occu\Api\Features\AdminMenu\Actions\CatalogosSubcategoriasActions;
use Occu\Api\Features\AdminMenu\Actions\CatalogosTamanosActions;
use Occu\Api\Features\AdminMenu\Actions\MenuProductosActions;
use Occu\Api\Http\JsonResponder;
use Occu\Api\Middleware\RequireRoleMiddleware;
use Occu\Api\Repositories\MenuCatalogosRepository;
use Occu\Api\Repositories\MenuProductosRepository;
use Occu\Api\Services\MenuReglasService;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;

final class Routes
{
    /**
     * @param array{
     *   json: JsonResponder,
     *   requireAuth: RequireAuthMiddleware,
     *   requireAdminRole: RequireRoleMiddleware,
     *   menuProductosRepo: MenuProductosRepository,
     *   menuCatalogosRepo: MenuCatalogosRepository,
     *   menuReglasService: MenuReglasService
     * } $deps
     */
    public static function register(App $app, array $deps): void
    {
        $json = $deps['json'];
        $requireAuth = $deps['requireAuth'];
        $requireAdminRole = $deps['requireAdminRole'];
        $menuProductosRepo = $deps['menuProductosRepo'];
        $menuCatalogosRepo = $deps['menuCatalogosRepo'];
        $menuReglasService = $deps['menuReglasService'];

        $menuProductosActions = new MenuProductosActions($json, $menuProductosRepo, $menuReglasService);
        $categoriasActions = new CatalogosCategoriasActions($json, $menuCatalogosRepo);
        $subcategoriasActions = new CatalogosSubcategoriasActions($json, $menuCatalogosRepo);
        $productosActions = new CatalogosProductosActions($json, $menuCatalogosRepo);
        $tamanosActions = new CatalogosTamanosActions($json, $menuCatalogosRepo);
        $ingredientesCategoriasActions = new CatalogosIngredientesCategoriasActions($json, $menuCatalogosRepo);
        $ingredientesActions = new CatalogosIngredientesActions($json, $menuCatalogosRepo);

        // Admin (solo Administrador) - grupo con middleware
        $app->group('/admin', function (RouteCollectorProxy $adminGroup) use (
            $menuProductosActions,
            $categoriasActions,
            $subcategoriasActions,
            $productosActions,
            $tamanosActions,
            $ingredientesCategoriasActions,
            $ingredientesActions,
        ) {
            $adminGroup->get('/menu/productos', [$menuProductosActions, 'list']);
            $adminGroup->get('/menu/productos/{id}/reglas', [$menuProductosActions, 'getReglas']);
            $adminGroup->put('/menu/productos/{id}/reglas', [$menuProductosActions, 'putReglas']);

            $adminGroup->get('/catalogos/menu/categorias', [$categoriasActions, 'list']);
            $adminGroup->get('/catalogos/menu/categorias/{id}', [$categoriasActions, 'get']);
            $adminGroup->post('/catalogos/menu/categorias', [$categoriasActions, 'create']);
            $adminGroup->put('/catalogos/menu/categorias/{id}', [$categoriasActions, 'update']);
            $adminGroup->delete('/catalogos/menu/categorias/{id}', [$categoriasActions, 'delete']);

            $adminGroup->get('/catalogos/menu/subcategorias', [$subcategoriasActions, 'list']);
            $adminGroup->post('/catalogos/menu/subcategorias', [$subcategoriasActions, 'create']);
            $adminGroup->put('/catalogos/menu/subcategorias/{id}', [$subcategoriasActions, 'update']);
            $adminGroup->delete('/catalogos/menu/subcategorias/{id}', [$subcategoriasActions, 'delete']);

            $adminGroup->get('/catalogos/menu/productos', [$productosActions, 'list']);
            $adminGroup->post('/catalogos/menu/productos', [$productosActions, 'create']);
            $adminGroup->put('/catalogos/menu/productos/{id}', [$productosActions, 'update']);
            $adminGroup->delete('/catalogos/menu/productos/{id}', [$productosActions, 'delete']);

            $adminGroup->get('/catalogos/menu/tamanos', [$tamanosActions, 'list']);
            $adminGroup->post('/catalogos/menu/tamanos', [$tamanosActions, 'create']);
            $adminGroup->put('/catalogos/menu/tamanos/{id}', [$tamanosActions, 'update']);
            $adminGroup->delete('/catalogos/menu/tamanos/{id}', [$tamanosActions, 'delete']);

            $adminGroup->get('/catalogos/menu/ingredientes-categorias', [$ingredientesCategoriasActions, 'list']);
            $adminGroup->post('/catalogos/menu/ingredientes-categorias', [$ingredientesCategoriasActions, 'create']);
            $adminGroup->put('/catalogos/menu/ingredientes-categorias/{id}', [$ingredientesCategoriasActions, 'update']);
            $adminGroup->delete('/catalogos/menu/ingredientes-categorias/{id}', [$ingredientesCategoriasActions, 'delete']);

            $adminGroup->get('/catalogos/menu/ingredientes', [$ingredientesActions, 'list']);
            $adminGroup->post('/catalogos/menu/ingredientes', [$ingredientesActions, 'create']);
            $adminGroup->put('/catalogos/menu/ingredientes/{id}', [$ingredientesActions, 'update']);
            $adminGroup->delete('/catalogos/menu/ingredientes/{id}', [$ingredientesActions, 'delete']);
        })->add($requireAdminRole)->add($requireAuth);
    }
}

