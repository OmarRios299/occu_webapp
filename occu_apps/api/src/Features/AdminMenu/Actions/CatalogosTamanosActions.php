<?php

declare(strict_types=1);

namespace Occu\Api\Features\AdminMenu\Actions;

use Occu\Api\Http\JsonResponder;
use Occu\Api\Middleware\RequireAuthMiddleware;
use Occu\Api\Repositories\MenuCatalogosRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Throwable;

final class CatalogosTamanosActions
{
    public function __construct(
        private readonly JsonResponder $json,
        private readonly MenuCatalogosRepository $menuCatalogosRepo,
    ) {
    }

    public function list(Request $request, Response $response, array $args = []): Response
    {
        try {
            $queryParams = $request->getQueryParams();
            $page = isset($queryParams['page']) ? max(1, (int)$queryParams['page']) : 1;
            $pageSize = isset($queryParams['pageSize']) ? max(1, min(100, (int)$queryParams['pageSize'])) : 20;
            $q = isset($queryParams['q']) ? trim($queryParams['q']) : '';

            $filters = ['page' => $page, 'pageSize' => $pageSize];
            if ($q !== '') {
                $filters['q'] = $q;
            }

            $result = $this->menuCatalogosRepo->findTamanos($filters);
            $totalPages = (int)ceil($result['total'] / $pageSize);

            return $this->json->ok($response, [
                'data' => $result['items'],
                'page' => $page,
                'pageSize' => $pageSize,
                'totalItems' => $result['total'],
                'totalPages' => $totalPages,
            ]);
        } catch (Throwable $e) {
            error_log("Error en GET /admin/catalogos/menu/tamanos: " . $e->getMessage());
            return $this->json->error($response, 500, 'internal_error', 'Error al obtener tamaños: ' . $e->getMessage());
        }
    }

    public function create(Request $request, Response $response, array $args = []): Response
    {
        try {
            $userId = RequireAuthMiddleware::getUserId($request);
            $body = $request->getParsedBody();
            if (!is_array($body) || empty($body['nombre'] ?? '')) {
                return $this->json->error($response, 400, 'invalid_data', 'El nombre es requerido');
            }

            $id = $this->menuCatalogosRepo->createTamano($body, $userId);
            return $this->json->created($response, ['id' => $id, 'message' => 'Tamaño creado exitosamente']);
        } catch (\RuntimeException $e) {
            return $this->json->error($response, 400, 'validation_error', $e->getMessage());
        } catch (Throwable $e) {
            error_log("Error en POST /admin/catalogos/menu/tamanos: " . $e->getMessage());
            return $this->json->error($response, 500, 'internal_error', 'Error al crear tamaño: ' . $e->getMessage());
        }
    }

    public function update(Request $request, Response $response, array $args = []): Response
    {
        try {
            $userId = RequireAuthMiddleware::getUserId($request);
            $id = (int)($args['id'] ?? 0);
            if ($id <= 0) {
                return $this->json->error($response, 400, 'invalid_id', 'ID inválido');
            }

            $body = $request->getParsedBody();
            if (!is_array($body) || empty($body['nombre'] ?? '')) {
                return $this->json->error($response, 400, 'invalid_data', 'El nombre es requerido');
            }

            $success = $this->menuCatalogosRepo->updateTamano($id, $body, $userId);
            if (!$success) {
                return $this->json->error($response, 404, 'not_found', 'Tamaño no encontrado');
            }
            return $this->json->ok($response, ['message' => 'Tamaño actualizado exitosamente']);
        } catch (\RuntimeException $e) {
            return $this->json->error($response, 400, 'validation_error', $e->getMessage());
        } catch (Throwable $e) {
            error_log("Error en PUT /admin/catalogos/menu/tamanos/{id}: " . $e->getMessage());
            return $this->json->error($response, 500, 'internal_error', 'Error al actualizar tamaño: ' . $e->getMessage());
        }
    }

    public function delete(Request $request, Response $response, array $args = []): Response
    {
        try {
            $id = (int)($args['id'] ?? 0);
            if ($id <= 0) {
                return $this->json->error($response, 400, 'invalid_id', 'ID inválido');
            }

            $success = $this->menuCatalogosRepo->deleteTamano($id);
            if (!$success) {
                return $this->json->error($response, 404, 'not_found', 'Tamaño no encontrado');
            }
            return $this->json->ok($response, ['message' => 'Tamaño eliminado exitosamente']);
        } catch (\RuntimeException $e) {
            return $this->json->error($response, 400, 'validation_error', $e->getMessage());
        } catch (Throwable $e) {
            error_log("Error en DELETE /admin/catalogos/menu/tamanos/{id}: " . $e->getMessage());
            return $this->json->error($response, 500, 'internal_error', 'Error al eliminar tamaño: ' . $e->getMessage());
        }
    }
}

