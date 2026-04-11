<?php

declare(strict_types=1);

namespace Occu\Api\Features\Cafeterias\Actions;

use Occu\Api\Http\JsonResponder;
use Occu\Api\Repositories\ServiciosRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Throwable;

final class ServiciosActions
{
    public function __construct(
        private readonly JsonResponder $json,
        private readonly ServiciosRepository $serviciosRepo,
    ) {
    }

    public function list(Request $request, Response $response, array $args = []): Response
    {
        try {
            $servicios = $this->serviciosRepo->findAll();
            return $this->json->ok($response, $servicios);
        } catch (Throwable $e) {
            error_log("Error en /servicios: " . $e->getMessage() . "\n" . $e->getTraceAsString());
            return $this->json->error($response, 500, 'internal_error', 'Error al obtener servicios: ' . $e->getMessage());
        }
    }
}

