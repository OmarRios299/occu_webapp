<?php

declare(strict_types=1);

namespace Occu\Api\Features\Cafeterias\Actions;

use Occu\Api\Http\JsonResponder;
use Occu\Api\Repositories\CiudadesRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Throwable;

final class CiudadesActions
{
    public function __construct(
        private readonly JsonResponder $json,
        private readonly CiudadesRepository $ciudadesRepo,
    ) {
    }

    public function list(Request $request, Response $response, array $args = []): Response
    {
        try {
            $ciudades = $this->ciudadesRepo->findAll();
            return $this->json->ok($response, $ciudades);
        } catch (Throwable $e) {
            error_log("Error en /ciudades: " . $e->getMessage() . "\n" . $e->getTraceAsString());
            return $this->json->error($response, 500, 'internal_error', 'Error al obtener ciudades: ' . $e->getMessage());
        }
    }

    public function get(Request $request, Response $response, array $args = []): Response
    {
        try {
            $id = (int)($args['id'] ?? 0);
            if ($id <= 0) {
                return $this->json->error($response, 400, 'invalid_id', 'ID de ciudad inválido');
            }

            $ciudad = $this->ciudadesRepo->findById($id);
            if (!$ciudad) {
                return $this->json->error($response, 404, 'not_found', 'Ciudad no encontrada');
            }

            $coordenadas = null;
            if (!empty($ciudad['coordenadas']) && $ciudad['coordenadas'] !== 'null') {
                try {
                    $coordenadas = json_decode($ciudad['coordenadas'], true);
                    if (!is_array($coordenadas) || count($coordenadas) < 3) {
                        $coordenadas = null;
                    }
                } catch (Throwable $e) {
                    error_log("Error parseando coordenadas de ciudad {$id}: " . $e->getMessage());
                    $coordenadas = null;
                }
            }

            return $this->json->ok($response, [
                'id' => (int)$ciudad['id'],
                'nombre' => $ciudad['nombre'],
                'coordenadas' => $coordenadas,
            ]);
        } catch (Throwable $e) {
            error_log("Error en /ciudades/{id}: " . $e->getMessage() . "\n" . $e->getTraceAsString());
            return $this->json->error($response, 500, 'internal_error', 'Error al obtener ciudad: ' . $e->getMessage());
        }
    }
}

