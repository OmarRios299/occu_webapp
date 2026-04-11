<?php

declare(strict_types=1);

namespace Occu\Api\Features\Cafeterias\Actions;

use Occu\Api\Http\JsonResponder;
use Occu\Api\Repositories\CafeteriasMenuRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Throwable;

final class MenuActions
{
    public function __construct(
        private readonly JsonResponder $json,
        private readonly CafeteriasMenuRepository $cafeteriasMenuRepo,
    ) {
    }

    public function getMenu(Request $request, Response $response, array $args = []): Response
    {
        try {
            $id = (int)($args['id'] ?? 0);
            if ($id <= 0) {
                return $this->json->error($response, 400, 'invalid_id', 'ID de cafetería inválido');
            }

            $menu = $this->cafeteriasMenuRepo->getMenu($id);

            if (empty($menu)) {
                // Verificar si la cafetería existe
                $pdo = $this->cafeteriasMenuRepo->getPdo();
                $stmtCheck = $pdo->prepare("SELECT id FROM cafeterias WHERE id = :id");
                $stmtCheck->bindValue(':id', $id, \PDO::PARAM_INT);
                $stmtCheck->execute();
                if (!$stmtCheck->fetch()) {
                    return $this->json->error($response, 404, 'not_found', 'Cafetería no encontrada');
                }
                // Cafetería existe pero no tiene menú publicado
                return $this->json->ok($response, []);
            }

            return $this->json->ok($response, $menu);
        } catch (Throwable $e) {
            error_log("Error en /cafeterias/{id}/menu: " . $e->getMessage() . "\n" . $e->getTraceAsString());
            return $this->json->error($response, 500, 'internal_error', 'Error al obtener menú: ' . $e->getMessage());
        }
    }

    public function getProducto(Request $request, Response $response, array $args = []): Response
    {
        try {
            $cafeteriaId = (int)($args['id'] ?? 0);
            $productoId = (int)($args['productoId'] ?? 0);

            if ($cafeteriaId <= 0 || $productoId <= 0) {
                return $this->json->error($response, 400, 'invalid_id', 'ID inválido');
            }

            $producto = $this->cafeteriasMenuRepo->getProducto($cafeteriaId, $productoId);

            if (!$producto) {
                return $this->json->error($response, 404, 'not_found', 'Producto no encontrado o no disponible en esta cafetería');
            }

            return $this->json->ok($response, $producto);
        } catch (Throwable $e) {
            error_log("Error en /cafeterias/{id}/menu/productos/{productoId}: " . $e->getMessage() . "\n" . $e->getTraceAsString());
            return $this->json->error($response, 500, 'internal_error', 'Error al obtener producto: ' . $e->getMessage());
        }
    }
}

