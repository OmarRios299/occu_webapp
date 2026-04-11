<?php

declare(strict_types=1);

namespace Occu\Api\Features\AdminMenu\Actions;

use Occu\Api\Http\JsonResponder;
use Occu\Api\Middleware\RequireAuthMiddleware;
use Occu\Api\Repositories\MenuProductosRepository;
use Occu\Api\Services\MenuReglasService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class MenuProductosActions
{
    public function __construct(
        private readonly JsonResponder $json,
        private readonly MenuProductosRepository $menuProductosRepo,
        private readonly MenuReglasService $menuReglasService,
    ) {
    }

    public function list(Request $request, Response $response, array $args = []): Response
    {
        $queryParams = $request->getQueryParams();
        $page = isset($queryParams['page']) ? max(1, (int)$queryParams['page']) : 1;
        $pageSize = isset($queryParams['pageSize']) ? max(1, min(100, (int)$queryParams['pageSize'])) : 20;
        $q = isset($queryParams['q']) ? trim($queryParams['q']) : '';

        $filters = [
            'page' => $page,
            'pageSize' => $pageSize,
        ];
        if ($q !== '') {
            $filters['q'] = $q;
        }

        $result = $this->menuProductosRepo->findPaginated($filters);
        $totalPages = (int)ceil($result['total'] / $pageSize);

        return $this->json->ok($response, [
            'data' => $result['items'],
            'page' => $page,
            'pageSize' => $pageSize,
            'totalItems' => $result['total'],
            'totalPages' => $totalPages,
        ]);
    }

    public function getReglas(Request $request, Response $response, array $args = []): Response
    {
        $productoId = (int)($args['id'] ?? 0);
        if ($productoId <= 0) {
            return $this->json->error($response, 400, 'invalid_id', 'ID de producto inválido');
        }

        if (!$this->menuProductosRepo->exists($productoId)) {
            return $this->json->error($response, 404, 'not_found', 'Producto no encontrado');
        }

        $reglas = $this->menuProductosRepo->getReglas($productoId);

        return $this->json->ok($response, $reglas);
    }

    public function putReglas(Request $request, Response $response, array $args = []): Response
    {
        $userId = RequireAuthMiddleware::getUserId($request);
        $productoId = (int)($args['id'] ?? 0);
        if ($productoId <= 0) {
            return $this->json->error($response, 400, 'invalid_id', 'ID de producto inválido');
        }

        if (!$this->menuProductosRepo->exists($productoId)) {
            return $this->json->error($response, 404, 'not_found', 'Producto no encontrado');
        }

        $body = $request->getParsedBody();
        if (!is_array($body) || (!isset($body['categorias']) && !isset($body['ingredientes']))) {
            return $this->json->error($response, 400, 'invalid_data', 'El body debe contener categorias y/o ingredientes');
        }

        $reglas = [
            'categorias' => $body['categorias'] ?? [],
            'ingredientes' => $body['ingredientes'] ?? [],
        ];

        // Validar reglas
        try {
            $this->menuReglasService->validateReglas($reglas);
        } catch (\RuntimeException $e) {
            return $this->json->error($response, 400, 'validation_error', $e->getMessage());
        }

        // Guardar en transacción
        $result = $this->menuProductosRepo->saveReglas($productoId, $reglas, $userId);

        return $this->json->ok($response, [
            'message' => 'Reglas guardadas exitosamente',
            'categorias_guardadas' => $result['categorias_guardadas'],
            'ingredientes_guardados' => $result['ingredientes_guardados'],
        ]);
    }
}

