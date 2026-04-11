<?php

declare(strict_types=1);

namespace Occu\Api;

use Occu\Api\Features\AdminMenu\Routes as AdminMenuRoutes;
use Occu\Api\Features\Auth\Routes as AuthRoutes;
use Occu\Api\Features\Cafeterias\Routes as CafeteriasRoutes;
use Occu\Api\Features\Carrito\Routes as CarritoRoutes;
use Occu\Api\Features\Owner\Routes as OwnerRoutes;
use Occu\Api\Features\System\Routes as SystemRoutes;
use Occu\Api\Http\JsonResponder;
use Occu\Api\Infrastructure\Db;
use Occu\Api\Middleware\CorsMiddleware;
use Occu\Api\Middleware\RequireAuthMiddleware;
use Occu\Api\Middleware\RequireRoleMiddleware;
use Occu\Api\Repositories\CafeteriasMenuRepository;
use Occu\Api\Repositories\CafeteriasRepository;
use Occu\Api\Repositories\CarritoRepository;
use Occu\Api\Repositories\CiudadesRepository;
use Occu\Api\Repositories\MenuCatalogosRepository;
use Occu\Api\Repositories\MenuProductosRepository;
use Occu\Api\Repositories\OwnerCafeteriasRepository;
use Occu\Api\Repositories\PropietariosMenuGeneralRepository;
use Occu\Api\Repositories\PropietariosMenuPublicacionesRepository;
use Occu\Api\Repositories\RefreshTokenRepository;
use Occu\Api\Repositories\ServiciosRepository;
use Occu\Api\Repositories\UserRepository;
use Occu\Api\Services\AuthService;
use Occu\Api\Services\CafeteriasService;
use Occu\Api\Services\CarritoService;
use Occu\Api\Services\GoogleIdTokenService;
use Occu\Api\Services\JwtService;
use Occu\Api\Services\MenuReglasService;
use Occu\Api\Services\PinMailerService;
use Occu\Api\Services\PropietariosMenuPublicacionService;
use Occu\Api\Services\RefreshTokenService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Throwable;

final class App
{
    public static function run(): void
    {
        $apiDebug = filter_var(getenv('API_DEBUG') ?: '1', FILTER_VALIDATE_BOOLEAN);

        $app = AppFactory::create();
        $app->setBasePath('/api');

        $app->addBodyParsingMiddleware();
        $app->addRoutingMiddleware();

        $json = new JsonResponder();

        $errorMiddleware = $app->addErrorMiddleware($apiDebug, true, $apiDebug);
        $errorMiddleware->setDefaultErrorHandler(self::jsonErrorHandler($json, $apiDebug));

        // IMPORTANTE:
        // En Slim, el último middleware agregado corre primero.
        // Necesitamos CORS "por fuera" para que el preflight OPTIONS no falle en routing.
        $app->add(new CorsMiddleware());

        // Dependencias (mínimas) sin contenedor por ahora
        $db = new Db();
        $users = new UserRepository($db);
        $refreshRepo = new RefreshTokenRepository($db);
        $cafeteriasRepo = new CafeteriasRepository($db);
        $cafeteriasMenuRepo = new CafeteriasMenuRepository($db);
        $carritoRepo = new CarritoRepository($db);
        $carritoService = new CarritoService($carritoRepo, $cafeteriasMenuRepo);
        $ownerCafeteriasRepo = new OwnerCafeteriasRepository($db);
        $serviciosRepo = new ServiciosRepository($db);
        $ciudadesRepo = new CiudadesRepository($db);
        $menuProductosRepo = new MenuProductosRepository($db);
        $menuCatalogosRepo = new MenuCatalogosRepository($db);
        $propietariosMenuGeneralRepo = new PropietariosMenuGeneralRepository($db);
        $propietariosMenuPublicacionesRepo = new PropietariosMenuPublicacionesRepository($db);
        $propietariosMenuPublicacionService = new PropietariosMenuPublicacionService(
            $propietariosMenuGeneralRepo,
            $propietariosMenuPublicacionesRepo,
            $ownerCafeteriasRepo
        );
        $cafeteriasService = new CafeteriasService($cafeteriasRepo);
        $menuReglasService = new MenuReglasService();

        $jwt = new JwtService(
            secret: self::requireEnv('API_JWT_SECRET'),
            issuer: rtrim((string)getenv('API_JWT_ISSUER') ?: 'occu.app', '/'),
            audience: rtrim((string)getenv('API_JWT_AUDIENCE') ?: 'occu', '/'),
        );
        $refresh = new RefreshTokenService($refreshRepo);
        $mailer = new PinMailerService(
            apiKey: self::requireEnv('MAIL'),
            fromEmail: (string)(getenv('MAIL_FROM_EMAIL') ?: 'noreply@occu.app'),
            fromName: (string)(getenv('MAIL_FROM_NAME') ?: 'OCCU'),
        );
        $google = new GoogleIdTokenService(clientId: self::requireEnv('GOOGLE_CLIENT_ID'));

        $auth = new AuthService($users, $jwt, $refresh, $mailer, $google);

        // Middlewares reutilizables (para uso en Features/*/Routes.php)
        $requireAuth = new RequireAuthMiddleware($jwt, $users, $json);
        $requireOwnerRole = new RequireRoleMiddleware(['Propietario', 'Administrador'], $json);
        $requireAdminRole = new RequireRoleMiddleware(['Administrador'], $json);
        $requireClienteRole = new RequireRoleMiddleware(['Cliente'], $json);

        // Registro de rutas por feature (App.php no debe seguir creciendo)
        $deps = [
            'json' => $json,
            'auth' => $auth,
            'requireAuth' => $requireAuth,
            'requireOwnerRole' => $requireOwnerRole,
            'requireAdminRole' => $requireAdminRole,
            'requireClienteRole' => $requireClienteRole,

            // Repos/Services
            'ownerCafeteriasRepo' => $ownerCafeteriasRepo,
            'propietariosMenuPublicacionesRepo' => $propietariosMenuPublicacionesRepo,
            'propietariosMenuGeneralRepo' => $propietariosMenuGeneralRepo,
            'propietariosMenuPublicacionService' => $propietariosMenuPublicacionService,
            'menuReglasService' => $menuReglasService,
            'cafeteriasRepo' => $cafeteriasRepo,
            'cafeteriasService' => $cafeteriasService,
            'serviciosRepo' => $serviciosRepo,
            'ciudadesRepo' => $ciudadesRepo,
            'cafeteriasMenuRepo' => $cafeteriasMenuRepo,
            'menuProductosRepo' => $menuProductosRepo,
            'menuCatalogosRepo' => $menuCatalogosRepo,
            'carritoService' => $carritoService,
            'carritoRepo' => $carritoRepo,
        ];

        SystemRoutes::register($app, $deps);
        AuthRoutes::register($app, $deps);
        OwnerRoutes::register($app, $deps);
        CafeteriasRoutes::register($app, $deps);
        AdminMenuRoutes::register($app, $deps);
        CarritoRoutes::register($app, $deps);

        $app->run();
    }

    /**
     * Slim acepta un callable como error handler por defecto.
     * Cuando API_DEBUG=0 (producción), NO expone mensaje interno ni trace al cliente.
     *
     * @return callable(Request, Throwable, bool, bool, bool): Response
     */
    private static function jsonErrorHandler(JsonResponder $json, bool $apiDebug): callable
    {
        return function (
            Request $request,
            Throwable $exception,
            bool $displayErrorDetails,
            bool $logErrors,
            bool $logErrorDetails
        ) use ($json, $apiDebug): Response {
            $response = new \Slim\Psr7\Response();

            if ($logErrors) {
                error_log(sprintf(
                    '[API Error] %s: %s in %s:%d',
                    get_class($exception),
                    $exception->getMessage(),
                    $exception->getFile(),
                    $exception->getLine()
                ));
                if ($logErrorDetails && $exception->getTraceAsString()) {
                    error_log('[API Error Trace] ' . $exception->getTraceAsString());
                }
            }

            $message = 'Error interno del servidor';
            $details = [];

            if ($apiDebug) {
                $message = $exception->getMessage() ?: $message;
                $details = [
                    'type' => get_class($exception),
                    'file' => $exception->getFile(),
                    'line' => $exception->getLine(),
                ];
                if ($logErrorDetails) {
                    $details['trace'] = explode("\n", $exception->getTraceAsString());
                }
            }

            return $json->error(
                $response,
                500,
                'internal_error',
                $message,
                $details
            );
        };
    }

    private static function requireEnv(string $key): string
    {
        $v = getenv($key);
        if ($v === false || trim((string)$v) === '') {
            throw new \RuntimeException("Falta variable de entorno requerida: {$key}");
        }
        return (string)$v;
    }
}
