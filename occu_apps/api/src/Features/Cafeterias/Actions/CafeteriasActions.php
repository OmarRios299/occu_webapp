<?php

declare(strict_types=1);

namespace Occu\Api\Features\Cafeterias\Actions;

use Occu\Api\Http\JsonResponder;
use Occu\Api\Repositories\CafeteriasRepository;
use Occu\Api\Services\CafeteriasService;
use Occu\Api\Support\PublicUrl;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Throwable;

final class CafeteriasActions
{
    public function __construct(
        private readonly JsonResponder $json,
        private readonly CafeteriasRepository $cafeteriasRepo,
        private readonly CafeteriasService $cafeteriasService,
    ) {
    }

    public function list(Request $request, Response $response, array $args = []): Response
    {
        try {
            $queryParams = $request->getQueryParams();

            $page = isset($queryParams['page']) ? max(1, (int)$queryParams['page']) : 1;
            $pageSize = isset($queryParams['pageSize']) ? max(1, min(100, (int)$queryParams['pageSize'])) : 9;

            $filters = [
                'page' => $page,
                'pageSize' => $pageSize,
            ];

            if (isset($queryParams['q']) && $queryParams['q'] !== '') {
                $filters['q'] = $queryParams['q'];
            }

            if (isset($queryParams['ciudadId']) && $queryParams['ciudadId'] !== '') {
                $filters['ciudadId'] = (int)$queryParams['ciudadId'];
            }

            if (isset($queryParams['horario']) && in_array($queryParams['horario'], ['todos', 'abierto'])) {
                $filters['horario'] = $queryParams['horario'];
            }

            if (isset($queryParams['servicios']) && $queryParams['servicios'] !== '') {
                $filters['servicioIds'] = array_map('intval', explode(',', $queryParams['servicios']));
            }

            $result = $this->cafeteriasRepo->findPaginated($filters);
            $items = [];
            foreach ($result['items'] as $cafeteria) {
                try {
                    [$isOpen, $scheduleLabel] = $this->cafeteriasService->calculateIsOpen((int)$cafeteria['id']);
                    if (isset($filters['horario']) && $filters['horario'] === 'abierto' && !$isOpen) {
                        continue;
                    }
                    $imagenUrl = PublicUrl::toAbsolute($cafeteria['imagen'] ?? '');

                    $items[] = [
                        'id' => (int)$cafeteria['id'],
                        'nombre' => $cafeteria['nombre'] ?? '',
                        'imagen' => $imagenUrl,
                        'direccion' => $cafeteria['direccion'] ?? '',
                        'latitud' => isset($cafeteria['latitud']) && $cafeteria['latitud'] !== ''
                            ? (float)$cafeteria['latitud']
                            : null,
                        'longitud' => isset($cafeteria['longitud']) && $cafeteria['longitud'] !== ''
                            ? (float)$cafeteria['longitud']
                            : null,
                        'isOpenNow' => $isOpen,
                        'todayScheduleLabel' => $scheduleLabel,
                    ];
                } catch (Throwable $e) {
                    error_log("Error calculando horario para cafetería {$cafeteria['id']}: " . $e->getMessage());
                    $items[] = [
                        'id' => (int)$cafeteria['id'],
                        'nombre' => $cafeteria['nombre'] ?? '',
                        'imagen' => PublicUrl::toAbsolute($cafeteria['imagen'] ?? ''),
                        'direccion' => $cafeteria['direccion'] ?? '',
                        'latitud' => isset($cafeteria['latitud']) && $cafeteria['latitud'] !== ''
                            ? (float)$cafeteria['latitud']
                            : null,
                        'longitud' => isset($cafeteria['longitud']) && $cafeteria['longitud'] !== ''
                            ? (float)$cafeteria['longitud']
                            : null,
                        'isOpenNow' => false,
                        'todayScheduleLabel' => 'Horario no disponible',
                    ];
                }
            }

            $total = $result['total'];
            if (isset($filters['horario']) && $filters['horario'] === 'abierto') {
                $total = count($items);
            }

            $totalPages = (int)ceil($total / $pageSize);

            return $this->json->ok($response, [
                'data' => $items,
                'page' => $page,
                'pageSize' => $pageSize,
                'totalItems' => $total,
                'totalPages' => $totalPages,
            ]);
        } catch (Throwable $e) {
            error_log("Error en /cafeterias: " . $e->getMessage() . "\n" . $e->getTraceAsString());
            return $this->json->error($response, 500, 'internal_error', 'Error al obtener cafeterías: ' . $e->getMessage());
        }
    }

    public function detail(Request $request, Response $response, array $args = []): Response
    {
        try {
            $id = (int)($args['id'] ?? 0);
            if ($id <= 0) {
                return $this->json->error($response, 400, 'invalid_id', 'ID de cafetería inválido');
            }

            $cafeteria = $this->cafeteriasRepo->findById($id);
            if (!$cafeteria) {
                return $this->json->error($response, 404, 'not_found', 'Cafetería no encontrada');
            }

            [$isOpen, $scheduleLabel] = $this->cafeteriasService->calculateIsOpen($id);
            $imagenes = $this->cafeteriasRepo->getImagenes($id);
            $imagenesArray = array_map(fn ($img) => PublicUrl::toAbsolute($img['imagen'] ?? ''), $imagenes);
            $servicios = $this->cafeteriasRepo->getServicios($id);
            $serviciosArray = [];
            foreach ($servicios as $serv) {
                $serviciosArray[] = [
                    'id' => (int)$serv['id'],
                    'nombre' => $serv['nombre'],
                    'imagen' => PublicUrl::toAbsolute($serv['imagen'] ?? ''),
                ];
            }
            $imagenUrl = PublicUrl::toAbsolute($cafeteria['imagen'] ?? '');

            return $this->json->ok($response, [
                'id' => (int)$cafeteria['id'],
                'nombre' => $cafeteria['nombre'],
                'imagen' => $imagenUrl,
                'direccion' => $cafeteria['direccion'],
                'telefono' => $cafeteria['telefono'] ?? '',
                'correo' => $cafeteria['correo_electronico'] ?? '',
                'descripcion' => $cafeteria['descripcion'] ?? '',
                'ciudad' => $cafeteria['ciudad'],
                'entidad_federativa' => $cafeteria['entidad_federativa'],
                'pais' => $cafeteria['pais'],
                'latitud' => $cafeteria['latitud'] ?? '',
                'longitud' => $cafeteria['longitud'] ?? '',
                'isOpenNow' => $isOpen,
                'todayScheduleLabel' => $scheduleLabel,
                'imagenes' => $imagenesArray,
                'servicios' => $serviciosArray,
            ]);
        } catch (Throwable $e) {
            error_log("Error en /cafeterias/{id}: " . $e->getMessage() . "\n" . $e->getTraceAsString());
            return $this->json->error($response, 500, 'internal_error', 'Error al obtener detalles de cafetería: ' . $e->getMessage());
        }
    }
}

