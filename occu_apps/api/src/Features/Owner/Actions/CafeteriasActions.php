<?php

declare(strict_types=1);

namespace Occu\Api\Features\Owner\Actions;

use Occu\Api\Http\JsonResponder;
use Occu\Api\Middleware\RequireAuthMiddleware;
use Occu\Api\Repositories\OwnerCafeteriasRepository;
use Occu\Api\Repositories\PropietariosMenuPublicacionesRepository;
use Occu\Api\Support\PublicUrl;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class CafeteriasActions
{
    public function __construct(
        private readonly JsonResponder $json,
        private readonly OwnerCafeteriasRepository $ownerCafeteriasRepo,
        private readonly PropietariosMenuPublicacionesRepository $propietariosMenuPublicacionesRepo,
    ) {
    }

    public function list(Request $request, Response $response, array $args = []): Response
    {
        $userId = RequireAuthMiddleware::getUserId($request);
        $user = RequireAuthMiddleware::getUser($request);
        $userLevel = $user['nivel'] ?? '';
        $queryParams = $request->getQueryParams();
        if ($userLevel === 'Administrador' && isset($queryParams['userId'])) {
            $userId = (int)$queryParams['userId'];
        }
        $cafeterias = $this->ownerCafeteriasRepo->findByOwner($userId, $userLevel);
        $items = [];
        foreach ($cafeterias as $cafeteria) {
            $imagenUrl = PublicUrl::toAbsolute($cafeteria['imagen'] ?? '');
            $items[] = [
                'id' => (int)$cafeteria['id'],
                'nombre' => $cafeteria['nombre'] ?? '',
                'imagen' => $imagenUrl,
                'direccion' => $cafeteria['direccion'] ?? '',
                'telefono' => $cafeteria['telefono'] ?? '',
                'correo_electronico' => $cafeteria['correo_electronico'] ?? null,
                'horario_apertura' => $cafeteria['horario_apertura'] ?? null,
                'horario_cierre' => $cafeteria['horario_cierre'] ?? null,
                'estado' => (int)$cafeteria['estado'],
                'id_ciudad' => isset($cafeteria['id_ciudad']) ? (int)$cafeteria['id_ciudad'] : null,
                'ciudad' => $cafeteria['ciudad'] ?? null,
                'entidad_federativa' => $cafeteria['entidad_federativa'] ?? null,
                'descripcion' => $cafeteria['descripcion'] ?? null,
            ];
        }
        return $this->json->ok($response, $items);
    }

    public function estadoMenu(Request $request, Response $response, array $args = []): Response
    {
        $userId = RequireAuthMiddleware::getUserId($request);
        $user = RequireAuthMiddleware::getUser($request);
        $userLevel = $user['nivel'] ?? '';
        $cafeterias = $this->ownerCafeteriasRepo->findByOwner($userId, $userLevel);
        $cafeteriaIds = array_map(fn ($c) => (int)$c['id'], $cafeterias);
        if (empty($cafeteriaIds)) {
            return $this->json->ok($response, []);
        }
        $estados = $this->propietariosMenuPublicacionesRepo->getEstadoActualizacion($cafeteriaIds);
        return $this->json->ok($response, $estados);
    }

    public function get(Request $request, Response $response, array $args = []): Response
    {
        $userId = RequireAuthMiddleware::getUserId($request);
        $user = RequireAuthMiddleware::getUser($request);
        $userLevel = $user['nivel'] ?? '';
        $cafeteriaId = (int)($args['id'] ?? 0);
        if ($cafeteriaId <= 0) {
            return $this->json->error($response, 400, 'invalid_id', 'ID de cafetería inválido');
        }
        $cafeteria = $this->ownerCafeteriasRepo->findById($cafeteriaId, $userId, $userLevel);
        if (!$cafeteria) {
            return $this->json->error($response, 404, 'not_found', 'Cafetería no encontrada o no autorizado');
        }
        $imagenUrl = PublicUrl::toAbsolute($cafeteria['imagen'] ?? '');
        return $this->json->ok($response, [
            'id' => (int)$cafeteria['id'],
            'nombre' => $cafeteria['nombre'] ?? '',
            'imagen' => $imagenUrl,
            'direccion' => $cafeteria['direccion'] ?? '',
            'telefono' => $cafeteria['telefono'] ?? '',
            'correo_electronico' => $cafeteria['correo_electronico'] ?? null,
            'horario_apertura' => $cafeteria['horario_apertura'] ?? null,
            'horario_cierre' => $cafeteria['horario_cierre'] ?? null,
            'estado' => (int)$cafeteria['estado'],
            'id_ciudad' => isset($cafeteria['id_ciudad']) ? (int)$cafeteria['id_ciudad'] : null,
            'ciudad' => $cafeteria['ciudad'] ?? null,
            'entidad_federativa' => $cafeteria['entidad_federativa'] ?? null,
            'latitud' => $cafeteria['latitud'] ?? '',
            'longitud' => $cafeteria['longitud'] ?? '',
            'descripcion' => $cafeteria['descripcion'] ?? null,
        ]);
    }

    public function patchEstado(Request $request, Response $response, array $args = []): Response
    {
        $userId = RequireAuthMiddleware::getUserId($request);
        $user = RequireAuthMiddleware::getUser($request);
        $userLevel = $user['nivel'] ?? '';
        $cafeteriaId = (int)($args['id'] ?? 0);
        if ($cafeteriaId <= 0) {
            return $this->json->error($response, 400, 'invalid_id', 'ID de cafetería inválido');
        }

        $body = $request->getParsedBody();
        $estado = isset($body['estado']) ? (int)$body['estado'] : null;

        if ($estado === null || ($estado !== 0 && $estado !== 1)) {
            return $this->json->error($response, 400, 'invalid_estado', 'El estado debe ser 0 (activa) o 1 (inactiva)');
        }

        $success = $this->ownerCafeteriasRepo->updateEstado($cafeteriaId, $estado, $userId, $userLevel);
        if (!$success) {
            return $this->json->error($response, 404, 'not_found', 'Cafetería no encontrada o no autorizado');
        }

        return $this->json->ok($response, ['message' => 'Estado actualizado exitosamente']);
    }

    public function delete(Request $request, Response $response, array $args = []): Response
    {
        $userId = RequireAuthMiddleware::getUserId($request);
        $user = RequireAuthMiddleware::getUser($request);
        $userLevel = $user['nivel'] ?? '';
        $cafeteriaId = (int)($args['id'] ?? 0);
        if ($cafeteriaId <= 0) {
            return $this->json->error($response, 400, 'invalid_id', 'ID de cafetería inválido');
        }

        $success = $this->ownerCafeteriasRepo->delete($cafeteriaId, $userId, $userLevel);
        if (!$success) {
            return $this->json->error($response, 404, 'not_found', 'Cafetería no encontrada o no autorizado');
        }

        return $this->json->ok($response, ['message' => 'Cafetería eliminada exitosamente']);
    }

    public function getServicios(Request $request, Response $response, array $args = []): Response
    {
        $userId = RequireAuthMiddleware::getUserId($request);
        $user = RequireAuthMiddleware::getUser($request);
        $userLevel = $user['nivel'] ?? '';
        $cafeteriaId = (int)($args['id'] ?? 0);
        if ($cafeteriaId <= 0) {
            return $this->json->error($response, 400, 'invalid_id', 'ID de cafetería inválido');
        }

        // Verificar que la cafetería existe y pertenece al usuario
        $cafeteria = $this->ownerCafeteriasRepo->findById($cafeteriaId, $userId, $userLevel);
        if (!$cafeteria) {
            return $this->json->error($response, 404, 'not_found', 'Cafetería no encontrada o no autorizado');
        }

        $servicios = $this->ownerCafeteriasRepo->getServiciosConEstado($cafeteriaId);
        $items = [];
        foreach ($servicios as $servicio) {
            $imagenUrl = PublicUrl::toAbsolute($servicio['imagen'] ?? '');
            $items[] = [
                'id' => (int)$servicio['id'],
                'nombre' => $servicio['nombre'] ?? '',
                'imagen' => $imagenUrl ?: null,
                'servicio_registrado' => (int)$servicio['servicio_registrado'],
            ];
        }

        return $this->json->ok($response, $items);
    }

    public function putServicios(Request $request, Response $response, array $args = []): Response
    {
        $userId = RequireAuthMiddleware::getUserId($request);
        $user = RequireAuthMiddleware::getUser($request);
        $userLevel = $user['nivel'] ?? '';
        $cafeteriaId = (int)($args['id'] ?? 0);
        if ($cafeteriaId <= 0) {
            return $this->json->error($response, 400, 'invalid_id', 'ID de cafetería inválido');
        }

        // Verificar que la cafetería existe y pertenece al usuario
        $cafeteria = $this->ownerCafeteriasRepo->findById($cafeteriaId, $userId, $userLevel);
        if (!$cafeteria) {
            return $this->json->error($response, 404, 'not_found', 'Cafetería no encontrada o no autorizado');
        }

        $body = $request->getParsedBody();
        $servicios = $body['servicios'] ?? [];

        if (!is_array($servicios)) {
            return $this->json->error($response, 400, 'invalid_servicios', 'Los servicios deben ser un array');
        }

        // Validar y convertir IDs
        $servicioIds = [];
        foreach ($servicios as $servicioId) {
            $id = (int)$servicioId;
            if ($id > 0) {
                $servicioIds[] = $id;
            }
        }

        $success = $this->ownerCafeteriasRepo->updateServicios($cafeteriaId, $servicioIds);
        if (!$success) {
            return $this->json->error($response, 500, 'internal_error', 'Error al actualizar servicios');
        }

        return $this->json->ok($response, ['message' => 'Servicios actualizados exitosamente']);
    }

    public function create(Request $request, Response $response, array $args = []): Response
    {
        $userId = RequireAuthMiddleware::getUserId($request);
        $body = $request->getParsedBody();

        // Validaciones básicas
        if (empty($body['nombre'] ?? '')) {
            return $this->json->error($response, 400, 'invalid_data', 'El nombre es requerido');
        }
        if (empty($body['telefono'] ?? '')) {
            return $this->json->error($response, 400, 'invalid_data', 'El teléfono es requerido');
        }
        if (empty($body['direccion'] ?? '')) {
            return $this->json->error($response, 400, 'invalid_data', 'La dirección es requerida');
        }
        if (empty($body['id_ciudad'] ?? '')) {
            return $this->json->error($response, 400, 'invalid_data', 'La ciudad es requerida');
        }

        $data = [
            'nombre' => trim($body['nombre']),
            'correo_electronico' => !empty($body['correo_electronico']) ? trim($body['correo_electronico']) : null,
            'telefono' => trim($body['telefono']),
            'direccion' => trim($body['direccion']),
            'id_ciudad' => (int)$body['id_ciudad'],
            'latitud' => $body['latitud'] ?? '',
            'longitud' => $body['longitud'] ?? '',
            'horario_apertura' => $body['horario_apertura'] ?? null,
            'horario_cierre' => $body['horario_cierre'] ?? null,
            'horario_diferente' => $body['horario_diferente'] ?? 'NO',
            'descripcion' => $body['descripcion'] ?? '',
        ];

        // Procesar horarios detallados si existen
        if (isset($body['horarios_detallados']) && is_array($body['horarios_detallados'])) {
            $data['horarios_detallados'] = $body['horarios_detallados'];
        }

        try {
            $cafeteriaId = $this->ownerCafeteriasRepo->create($data, $userId);
            return $this->json->created($response, ['id' => $cafeteriaId, 'message' => 'Cafetería creada exitosamente']);
        } catch (\RuntimeException $e) {
            return $this->json->error($response, 400, 'validation_error', $e->getMessage());
        }
    }

    public function update(Request $request, Response $response, array $args = []): Response
    {
        $userId = RequireAuthMiddleware::getUserId($request);
        $user = RequireAuthMiddleware::getUser($request);
        $userLevel = $user['nivel'] ?? '';
        $cafeteriaId = (int)($args['id'] ?? 0);
        if ($cafeteriaId <= 0) {
            return $this->json->error($response, 400, 'invalid_id', 'ID de cafetería inválido');
        }

        $body = $request->getParsedBody();

        // Validaciones básicas
        if (empty($body['nombre'] ?? '')) {
            return $this->json->error($response, 400, 'invalid_data', 'El nombre es requerido');
        }
        if (empty($body['telefono'] ?? '')) {
            return $this->json->error($response, 400, 'invalid_data', 'El teléfono es requerido');
        }
        if (empty($body['direccion'] ?? '')) {
            return $this->json->error($response, 400, 'invalid_data', 'La dirección es requerida');
        }
        if (empty($body['id_ciudad'] ?? '')) {
            return $this->json->error($response, 400, 'invalid_data', 'La ciudad es requerida');
        }

        $data = [
            'nombre' => trim($body['nombre']),
            'correo_electronico' => !empty($body['correo_electronico']) ? trim($body['correo_electronico']) : null,
            'telefono' => trim($body['telefono']),
            'direccion' => trim($body['direccion']),
            'id_ciudad' => (int)$body['id_ciudad'],
            'latitud' => $body['latitud'] ?? '',
            'longitud' => $body['longitud'] ?? '',
            'horario_apertura' => $body['horario_apertura'] ?? null,
            'horario_cierre' => $body['horario_cierre'] ?? null,
            'horario_diferente' => $body['horario_diferente'] ?? 'NO',
            'descripcion' => $body['descripcion'] ?? '',
        ];

        // Procesar horarios detallados si existen
        if (isset($body['horarios_detallados']) && is_array($body['horarios_detallados'])) {
            $data['horarios_detallados'] = $body['horarios_detallados'];
        }

        try {
            $success = $this->ownerCafeteriasRepo->update($cafeteriaId, $data, $userId, $userLevel);
            if (!$success) {
                return $this->json->error($response, 404, 'not_found', 'Cafetería no encontrada o no autorizado');
            }
            return $this->json->ok($response, ['message' => 'Cafetería actualizada exitosamente']);
        } catch (\RuntimeException $e) {
            return $this->json->error($response, 400, 'validation_error', $e->getMessage());
        }
    }
}

