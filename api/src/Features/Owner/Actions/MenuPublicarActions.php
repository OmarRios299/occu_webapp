<?php

declare(strict_types=1);

namespace Occu\Api\Features\Owner\Actions;

use Occu\Api\Http\JsonResponder;
use Occu\Api\Middleware\RequireAuthMiddleware;
use Occu\Api\Services\PropietariosMenuPublicacionService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class MenuPublicarActions
{
    public function __construct(
        private readonly JsonResponder $json,
        private readonly PropietariosMenuPublicacionService $propietariosMenuPublicacionService,
    ) {
    }

    public function publicar(Request $request, Response $response, array $args = []): Response
    {
        try {
            $userId = RequireAuthMiddleware::getUserId($request);
            $body = $request->getParsedBody();
            if (!is_array($body) || !isset($body['cafeteria_ids']) || !is_array($body['cafeteria_ids']) || empty($body['cafeteria_ids'])) {
                return $this->json->error($response, 400, 'invalid_data', 'El body debe contener un array no vacío de cafeteria_ids');
            }
            if (!isset($body['modo']) || !in_array($body['modo'], ['todo', 'solo_precios'])) {
                return $this->json->error($response, 400, 'invalid_data', 'El modo debe ser "todo" o "solo_precios"');
            }
            $cafeteriaIds = array_map('intval', $body['cafeteria_ids']);
            $modo = $body['modo'];
            $result = $this->propietariosMenuPublicacionService->publicarMenu($userId, $cafeteriaIds, $modo);
            return $this->json->ok($response, [
                'message' => 'Menú publicado exitosamente',
                'cafeterias_publicadas' => $result['publicadas'],
                'productos_publicados' => $result['productos_publicados'],
            ]);
        } catch (\RuntimeException $e) {
            return $this->json->error($response, 400, 'validation_error', $e->getMessage());
        }
    }
}

