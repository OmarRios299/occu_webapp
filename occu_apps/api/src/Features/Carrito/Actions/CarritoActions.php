<?php

declare(strict_types=1);

namespace Occu\Api\Features\Carrito\Actions;

use Occu\Api\Http\JsonResponder;
use Occu\Api\Middleware\RequireAuthMiddleware;
use Occu\Api\Repositories\CarritoRepository;
use Occu\Api\Services\CarritoService;
use Occu\Api\Support\PublicUrl;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class CarritoActions
{
    public function __construct(
        private readonly JsonResponder $json,
        private readonly CarritoService $carritoService,
        private readonly CarritoRepository $carritoRepo,
    ) {
    }

    public function addItem(Request $request, Response $response, array $args = []): Response
    {
        $userId = RequireAuthMiddleware::getUserId($request);
        $body = $request->getParsedBody();
        if (!is_array($body)) {
            return $this->json->error($response, 400, 'invalid_request', 'Cuerpo de la petición inválido');
        }

        $cafeteriaId = isset($body['cafeteria_id']) ? (int)$body['cafeteria_id'] : 0;
        $productoId = isset($body['producto_id']) ? (int)$body['producto_id'] : 0;
        $tamanoId = isset($body['tamano_id']) && $body['tamano_id'] !== null ? (int)$body['tamano_id'] : null;
        $selecciones = isset($body['selecciones']) && is_array($body['selecciones']) ? $body['selecciones'] : [];
        $cantidad = isset($body['cantidad']) ? max(1, (int)$body['cantidad']) : 1;

        if ($cafeteriaId <= 0 || $productoId <= 0) {
            return $this->json->error($response, 400, 'invalid_request', 'ID de cafetería y producto son requeridos');
        }

        // Convertir selecciones a map de int => int
        $seleccionesMap = [];
        foreach ($selecciones as $ingId => $cant) {
            $ingIdInt = (int)$ingId;
            $cantInt = (int)$cant;
            if ($ingIdInt > 0 && $cantInt > 0) {
                $seleccionesMap[$ingIdInt] = $cantInt;
            }
        }

        $resultado = $this->carritoService->agregarItem(
            $userId,
            $cafeteriaId,
            $productoId,
            $tamanoId,
            $seleccionesMap,
            $cantidad
        );

        if (!$resultado['success']) {
            return $this->json->error($response, 400, 'validation_error', $resultado['message'] ?? 'Error al agregar al carrito');
        }

        return $this->json->ok($response, [
            'item_id' => $resultado['item_id'],
            'cantidad_total' => $resultado['cantidad_total'],
        ]);
    }

    public function get(Request $request, Response $response, array $args = []): Response
    {
        $userId = RequireAuthMiddleware::getUserId($request);
        $queryParams = $request->getQueryParams();
        $cafeteriaId = isset($queryParams['cafeteria_id']) ? (int)$queryParams['cafeteria_id'] : null;

        $carrito = $this->carritoRepo->obtenerCarrito($userId, $cafeteriaId);

        if (!$carrito) {
            return $this->json->ok($response, null);
        }
        if ($carrito['cafeteria_imagen'] ?? '') {
            $carrito['cafeteria_imagen'] = PublicUrl::toAbsolute($carrito['cafeteria_imagen']);
        }
        foreach ($carrito['items'] as &$item) {
            if ($item['producto_imagen'] ?? '') {
                $item['producto_imagen'] = PublicUrl::toAbsolute($item['producto_imagen']);
            }
        }
        return $this->json->ok($response, $carrito);
    }
}

