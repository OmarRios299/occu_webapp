<?php

declare(strict_types=1);

namespace Occu\Api\Features\AdminMenu\Actions;

use Occu\Api\Http\JsonResponder;
use Occu\Api\Middleware\RequireAuthMiddleware;
use Occu\Api\Repositories\MenuCatalogosRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Throwable;

final class CatalogosProductosActions
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
            $idSubcategoria = isset($queryParams['id_subcategoria']) ? (int)$queryParams['id_subcategoria'] : null;

            $filters = ['page' => $page, 'pageSize' => $pageSize];
            if ($q !== '') {
                $filters['q'] = $q;
            }
            if ($idSubcategoria !== null && $idSubcategoria > 0) {
                $filters['id_subcategoria'] = $idSubcategoria;
            }

            $result = $this->menuCatalogosRepo->findProductos($filters);
            $totalPages = (int)ceil($result['total'] / $pageSize);

            return $this->json->ok($response, [
                'data' => $result['items'],
                'page' => $page,
                'pageSize' => $pageSize,
                'totalItems' => $result['total'],
                'totalPages' => $totalPages,
            ]);
        } catch (Throwable $e) {
            error_log("Error en GET /admin/catalogos/menu/productos: " . $e->getMessage());
            return $this->json->error($response, 500, 'internal_error', 'Error al obtener productos: ' . $e->getMessage());
        }
    }

    public function create(Request $request, Response $response, array $args = []): Response
    {
        try {
            $userId = RequireAuthMiddleware::getUserId($request);
            $body = $request->getParsedBody();
            if (!is_array($body) || empty($body['nombre'] ?? '') || empty($body['id_subcategoria'] ?? '')) {
                return $this->json->error($response, 400, 'invalid_data', 'El nombre e id_subcategoria son requeridos');
            }

            $id = $this->menuCatalogosRepo->createProducto($body, $userId);
            return $this->json->created($response, ['id' => $id, 'message' => 'Producto creado exitosamente']);
        } catch (\RuntimeException $e) {
            return $this->json->error($response, 400, 'validation_error', $e->getMessage());
        } catch (Throwable $e) {
            error_log("Error en POST /admin/catalogos/menu/productos: " . $e->getMessage());
            return $this->json->error($response, 500, 'internal_error', 'Error al crear producto: ' . $e->getMessage());
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
            if (!is_array($body) || empty($body['nombre'] ?? '') || empty($body['id_subcategoria'] ?? '')) {
                return $this->json->error($response, 400, 'invalid_data', 'El nombre e id_subcategoria son requeridos');
            }

            $success = $this->menuCatalogosRepo->updateProducto($id, $body, $userId);
            if (!$success) {
                return $this->json->error($response, 404, 'not_found', 'Producto no encontrado');
            }
            return $this->json->ok($response, ['message' => 'Producto actualizado exitosamente']);
        } catch (\RuntimeException $e) {
            return $this->json->error($response, 400, 'validation_error', $e->getMessage());
        } catch (Throwable $e) {
            error_log("Error en PUT /admin/catalogos/menu/productos/{id}: " . $e->getMessage());
            return $this->json->error($response, 500, 'internal_error', 'Error al actualizar producto: ' . $e->getMessage());
        }
    }

    public function delete(Request $request, Response $response, array $args = []): Response
    {
        try {
            $id = (int)($args['id'] ?? 0);
            if ($id <= 0) {
                return $this->json->error($response, 400, 'invalid_id', 'ID inválido');
            }

            $success = $this->menuCatalogosRepo->deleteProducto($id);
            if (!$success) {
                return $this->json->error($response, 404, 'not_found', 'Producto no encontrado');
            }
            return $this->json->ok($response, ['message' => 'Producto eliminado exitosamente']);
        } catch (\RuntimeException $e) {
            return $this->json->error($response, 400, 'validation_error', $e->getMessage());
        } catch (Throwable $e) {
            error_log("Error en DELETE /admin/catalogos/menu/productos/{id}: " . $e->getMessage());
            return $this->json->error($response, 500, 'internal_error', 'Error al eliminar producto: ' . $e->getMessage());
        }
    }
}

