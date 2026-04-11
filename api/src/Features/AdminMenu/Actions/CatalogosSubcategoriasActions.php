<?php

declare(strict_types=1);

namespace Occu\Api\Features\AdminMenu\Actions;

use Occu\Api\Http\JsonResponder;
use Occu\Api\Middleware\RequireAuthMiddleware;
use Occu\Api\Repositories\MenuCatalogosRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Throwable;

final class CatalogosSubcategoriasActions
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
            $idCategoria = isset($queryParams['id_categoria']) ? (int)$queryParams['id_categoria'] : null;

            $filters = ['page' => $page, 'pageSize' => $pageSize];
            if ($q !== '') {
                $filters['q'] = $q;
            }
            if ($idCategoria !== null && $idCategoria > 0) {
                $filters['id_categoria'] = $idCategoria;
            }

            $result = $this->menuCatalogosRepo->findSubcategorias($filters);
            $totalPages = (int)ceil($result['total'] / $pageSize);

            return $this->json->ok($response, [
                'data' => $result['items'],
                'page' => $page,
                'pageSize' => $pageSize,
                'totalItems' => $result['total'],
                'totalPages' => $totalPages,
            ]);
        } catch (Throwable $e) {
            error_log("Error en GET /admin/catalogos/menu/subcategorias: " . $e->getMessage());
            return $this->json->error($response, 500, 'internal_error', 'Error al obtener subcategorías: ' . $e->getMessage());
        }
    }

    public function create(Request $request, Response $response, array $args = []): Response
    {
        try {
            $userId = RequireAuthMiddleware::getUserId($request);
            $body = $request->getParsedBody();
            if (!is_array($body) || empty($body['nombre'] ?? '') || empty($body['id_categoria'] ?? '')) {
                return $this->json->error($response, 400, 'invalid_data', 'El nombre e id_categoria son requeridos');
            }

            $id = $this->menuCatalogosRepo->createSubcategoria($body, $userId);
            return $this->json->created($response, ['id' => $id, 'message' => 'Subcategoría creada exitosamente']);
        } catch (\RuntimeException $e) {
            return $this->json->error($response, 400, 'validation_error', $e->getMessage());
        } catch (Throwable $e) {
            error_log("Error en POST /admin/catalogos/menu/subcategorias: " . $e->getMessage());
            return $this->json->error($response, 500, 'internal_error', 'Error al crear subcategoría: ' . $e->getMessage());
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
            if (!is_array($body) || empty($body['nombre'] ?? '') || empty($body['id_categoria'] ?? '')) {
                return $this->json->error($response, 400, 'invalid_data', 'El nombre e id_categoria son requeridos');
            }

            $success = $this->menuCatalogosRepo->updateSubcategoria($id, $body, $userId);
            if (!$success) {
                return $this->json->error($response, 404, 'not_found', 'Subcategoría no encontrada');
            }
            return $this->json->ok($response, ['message' => 'Subcategoría actualizada exitosamente']);
        } catch (\RuntimeException $e) {
            return $this->json->error($response, 400, 'validation_error', $e->getMessage());
        } catch (Throwable $e) {
            error_log("Error en PUT /admin/catalogos/menu/subcategorias/{id}: " . $e->getMessage());
            return $this->json->error($response, 500, 'internal_error', 'Error al actualizar subcategoría: ' . $e->getMessage());
        }
    }

    public function delete(Request $request, Response $response, array $args = []): Response
    {
        try {
            $id = (int)($args['id'] ?? 0);
            if ($id <= 0) {
                return $this->json->error($response, 400, 'invalid_id', 'ID inválido');
            }

            $success = $this->menuCatalogosRepo->deleteSubcategoria($id);
            if (!$success) {
                return $this->json->error($response, 404, 'not_found', 'Subcategoría no encontrada');
            }
            return $this->json->ok($response, ['message' => 'Subcategoría eliminada exitosamente']);
        } catch (\RuntimeException $e) {
            return $this->json->error($response, 400, 'validation_error', $e->getMessage());
        } catch (Throwable $e) {
            error_log("Error en DELETE /admin/catalogos/menu/subcategorias/{id}: " . $e->getMessage());
            return $this->json->error($response, 500, 'internal_error', 'Error al eliminar subcategoría: ' . $e->getMessage());
        }
    }
}

