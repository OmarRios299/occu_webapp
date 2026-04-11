<?php

declare(strict_types=1);

namespace Occu\Api\Features\Cafeterias\Actions;

use Occu\Api\Http\JsonResponder;
use Occu\Api\Middleware\RequireAuthMiddleware;
use Occu\Api\Repositories\CafeteriasRepository;
use Occu\Api\Support\Clock;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Throwable;

final class ComentariosActions
{
    public function __construct(
        private readonly JsonResponder $json,
        private readonly CafeteriasRepository $cafeteriasRepo,
    ) {
    }

    public function list(Request $request, Response $response, array $args = []): Response
    {
        try {
            $id = (int)($args['id'] ?? 0);
            if ($id <= 0) {
                return $this->json->error($response, 400, 'invalid_id', 'ID de cafetería inválido');
            }

            $queryParams = $request->getQueryParams();
            $page = isset($queryParams['page']) ? max(1, (int)$queryParams['page']) : 1;
            $pageSize = isset($queryParams['pageSize']) ? max(1, min(50, (int)$queryParams['pageSize'])) : 5;

            $result = $this->cafeteriasRepo->getComentarios($id, $page, $pageSize);
            $totalPages = (int)ceil($result['total'] / $pageSize);

            return $this->json->ok($response, [
                'data' => $result['items'],
                'page' => $page,
                'pageSize' => $pageSize,
                'totalItems' => $result['total'],
                'totalPages' => $totalPages,
            ]);
        } catch (Throwable $e) {
            error_log("Error en /cafeterias/{id}/comentarios: " . $e->getMessage());
            return $this->json->error($response, 500, 'internal_error', 'Error al obtener comentarios');
        }
    }

    public function create(Request $request, Response $response, array $args = []): Response
    {
        $usuarioId = RequireAuthMiddleware::getUserId($request);
        $id = (int)($args['id'] ?? 0);
        if ($id <= 0) {
            return $this->json->error($response, 400, 'invalid_id', 'ID de cafetería inválido');
        }
        $body = $request->getParsedBody();
        $comentario = $body['comentario'] ?? '';
        if (empty(trim($comentario))) {
            return $this->json->error($response, 400, 'invalid_comment', 'El comentario no puede estar vacío');
        }
        $zonaHoraria = $this->cafeteriasRepo->getZonaHoraria($id) ?: 'America/Tijuana';
        $fechaAlta = Clock::now($zonaHoraria);
        $success = $this->cafeteriasRepo->createComentario($id, $usuarioId, trim($comentario), $fechaAlta);
        if ($success) {
            return $this->json->created($response, ['message' => 'Comentario registrado exitosamente']);
        }
        return $this->json->error($response, 500, 'internal_error', 'Error al registrar comentario');
    }
}

