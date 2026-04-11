<?php

declare(strict_types=1);

namespace Occu\Api\Features\Owner\Actions;

use Occu\Api\Http\JsonResponder;
use Occu\Api\Middleware\RequireAuthMiddleware;
use Occu\Api\Repositories\PropietariosMenuGeneralRepository;
use Occu\Api\Services\MenuReglasService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class MenuGeneralProductosActions
{
    public function __construct(
        private readonly JsonResponder $json,
        private readonly PropietariosMenuGeneralRepository $propietariosMenuGeneralRepo,
        private readonly MenuReglasService $menuReglasService,
    ) {
    }

    public function list(Request $request, Response $response, array $args = []): Response
    {
        $userId = RequireAuthMiddleware::getUserId($request);
        $user = RequireAuthMiddleware::getUser($request);
        $userLevel = $user['nivel'] ?? '';
        $queryParams = $request->getQueryParams();
        $propietarioId = $userId;
        if ($userLevel === 'Administrador' && isset($queryParams['propietarioId'])) {
            $propietarioId = (int)$queryParams['propietarioId'];
        }

        $page = isset($queryParams['page']) ? max(1, (int)$queryParams['page']) : 1;
        $pageSize = isset($queryParams['pageSize']) ? max(1, min(100, (int)$queryParams['pageSize'])) : 20;
        $q = isset($queryParams['q']) ? trim($queryParams['q']) : '';
        $idSubcategoria = isset($queryParams['id_subcategoria']) ? (int)$queryParams['id_subcategoria'] : null;
        $estado = isset($queryParams['estado']) ? (int)$queryParams['estado'] : null;

        $filters = ['page' => $page, 'pageSize' => $pageSize];
        if ($q !== '') {
            $filters['q'] = $q;
        }
        if ($idSubcategoria !== null) {
            $filters['id_subcategoria'] = $idSubcategoria;
        }
        if ($estado !== null) {
            $filters['estado'] = $estado;
        }

        $result = $this->propietariosMenuGeneralRepo->findProductos($filters, $propietarioId);
        $totalPages = (int)ceil($result['total'] / $pageSize);

        return $this->json->ok($response, [
            'data' => $result['items'],
            'page' => $page,
            'pageSize' => $pageSize,
            'totalItems' => $result['total'],
            'totalPages' => $totalPages,
        ]);
    }

    public function bulkUpdate(Request $request, Response $response, array $args = []): Response
    {
        $userId = RequireAuthMiddleware::getUserId($request);
        $body = $request->getParsedBody();
        if (!is_array($body) || !isset($body['productos']) || !is_array($body['productos'])) {
            return $this->json->error($response, 400, 'invalid_data', 'El body debe contener un array de productos');
        }

        // Validar estructura básica
        foreach ($body['productos'] as $prod) {
            if (empty($prod['id_producto'] ?? '')) {
                return $this->json->error($response, 400, 'invalid_data', 'Cada producto debe tener id_producto');
            }
        }

        $result = $this->propietariosMenuGeneralRepo->updateProductos($body['productos'], $userId);

        return $this->json->ok($response, [
            'message' => 'Productos actualizados exitosamente',
            'productos_actualizados' => $result['actualizados'],
        ]);
    }

    public function get(Request $request, Response $response, array $args = []): Response
    {
        $userId = RequireAuthMiddleware::getUserId($request);
        $productoId = (int)($args['id'] ?? 0);
        if ($productoId <= 0) {
            return $this->json->error($response, 400, 'invalid_id', 'ID de producto inválido');
        }

        if (!$this->propietariosMenuGeneralRepo->verificarAcceso($productoId, $userId)) {
            return $this->json->error($response, 404, 'not_found', 'Producto no encontrado o no disponible');
        }

        $producto = $this->propietariosMenuGeneralRepo->getProducto($productoId, $userId);
        if (!$producto) {
            return $this->json->error($response, 404, 'not_found', 'Producto no encontrado');
        }

        return $this->json->ok($response, $producto);
    }

    public function getReglas(Request $request, Response $response, array $args = []): Response
    {
        $userId = RequireAuthMiddleware::getUserId($request);
        $productoId = (int)($args['id'] ?? 0);
        if ($productoId <= 0) {
            return $this->json->error($response, 400, 'invalid_id', 'ID de producto inválido');
        }

        if (!$this->propietariosMenuGeneralRepo->verificarAcceso($productoId, $userId)) {
            return $this->json->error($response, 404, 'not_found', 'Producto no encontrado o no disponible');
        }

        $reglas = $this->propietariosMenuGeneralRepo->getReglas($productoId, $userId);
        return $this->json->ok($response, $reglas);
    }

    public function putReglas(Request $request, Response $response, array $args = []): Response
    {
        $userId = RequireAuthMiddleware::getUserId($request);
        $productoId = (int)($args['id'] ?? 0);
        if ($productoId <= 0) {
            return $this->json->error($response, 400, 'invalid_id', 'ID de producto inválido');
        }

        // Verificar acceso
        if (!$this->propietariosMenuGeneralRepo->verificarAcceso($productoId, $userId)) {
            return $this->json->error($response, 404, 'not_found', 'Producto no encontrado o no disponible');
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
        $result = $this->propietariosMenuGeneralRepo->saveReglas($productoId, $reglas, $userId);

        return $this->json->ok($response, [
            'message' => 'Reglas guardadas exitosamente',
            'categorias_guardadas' => $result['categorias_guardadas'],
            'ingredientes_guardados' => $result['ingredientes_guardados'],
        ]);
    }
}

