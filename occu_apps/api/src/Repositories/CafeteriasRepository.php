<?php

declare(strict_types=1);

namespace Occu\Api\Repositories;

use Occu\Api\Infrastructure\Db;
use PDO;

final class CafeteriasRepository
{
    private PDO $pdo;

    public function __construct(Db $db)
    {
        $this->pdo = $db->pdo();
    }

    /**
     * Obtiene cafeterías paginadas con filtros
     * 
     * @param array{
     *     page: int,
     *     pageSize: int,
     *     q?: string,
     *     ciudadId?: int,
     *     horario?: 'todos'|'abierto',
     *     servicioIds?: int[]
     * } $filters
     * @return array{items: array, total: int}
     */
    public function findPaginated(array $filters): array
    {
        $page = $filters['page'];
        $pageSize = $filters['pageSize'];
        $offset = ($page - 1) * $pageSize;

        $busqueda = isset($filters['q']) && $filters['q'] !== '' 
            ? '%' . $filters['q'] . '%' 
            : null;

        // Construir WHERE
        $where = [
            'cafeterias.estado = 0',
            'paises.estado = 0',
            'ciudades.estado = 0',
        ];

        $params = [];

        if ($busqueda) {
            $where[] = '(cafeterias.nombre LIKE :busqueda OR ciudades.nombre LIKE :busqueda)';
            $params[':busqueda'] = $busqueda;
        }

        if (isset($filters['ciudadId']) && $filters['ciudadId'] > 0) {
            $where[] = 'cafeterias.id_ciudad = :ciudadId';
            $params[':ciudadId'] = $filters['ciudadId'];
        }

        // Filtro de servicios
        if (isset($filters['servicioIds']) && !empty($filters['servicioIds'])) {
            $servicioIds = array_map('intval', $filters['servicioIds']);
            $servicioIds = array_filter($servicioIds, fn($id) => $id > 0); // Filtrar IDs inválidos
            
            if (!empty($servicioIds)) {
                $placeholders = [];
                foreach ($servicioIds as $idx => $id) {
                    $key = ':servicio' . $idx;
                    $placeholders[] = $key;
                    $params[$key] = $id;
                }
                $where[] = 'cafeterias.id IN (
                    SELECT DISTINCT id_cafeteria 
                    FROM cafeterias_servicios 
                    WHERE estado = 0 AND id_servicio IN (' . implode(',', $placeholders) . ')
                )';
            }
        }

        $whereClause = implode(' AND ', $where);

        // Query para obtener items
        $sql = "SELECT
            cafeterias.id,
            cafeterias.nombre,
            cafeterias.imagen,
            cafeterias.direccion,
            cafeterias.latitud,
            cafeterias.longitud,
            ciudades.nombre AS ciudad,
            entidades_federativas.nombre AS entidad_federativa,
            paises.nombre AS pais
            FROM cafeterias
            INNER JOIN ciudades ON cafeterias.id_ciudad = ciudades.id
            INNER JOIN entidades_federativas ON ciudades.id_entidad_federativa = entidades_federativas.id
            INNER JOIN paises ON entidades_federativas.id_pais = paises.id
            WHERE {$whereClause}
            ORDER BY cafeterias.nombre ASC
            LIMIT :offset, :limit";

        $stmt = $this->pdo->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
        }
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $pageSize, PDO::PARAM_INT);
        $stmt->execute();
        $items = $stmt->fetchAll();

        // Query para contar total (con los mismos filtros)
        // Si hay filtro de servicios, necesitamos usar DISTINCT para evitar duplicados
        $countSelect = isset($filters['servicioIds']) && !empty($filters['servicioIds']) 
            ? "SELECT COUNT(DISTINCT cafeterias.id) as total"
            : "SELECT COUNT(*) as total";
            
        $countSql = "{$countSelect}
            FROM cafeterias
            INNER JOIN ciudades ON cafeterias.id_ciudad = ciudades.id
            INNER JOIN entidades_federativas ON ciudades.id_entidad_federativa = entidades_federativas.id
            INNER JOIN paises ON entidades_federativas.id_pais = paises.id
            WHERE {$whereClause}";

        $countStmt = $this->pdo->prepare($countSql);
        foreach ($params as $key => $value) {
            $countStmt->bindValue($key, $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
        }
        $countStmt->execute();
        $total = (int)$countStmt->fetchColumn();

        return [
            'items' => $items,
            'total' => $total,
        ];
    }

    /**
     * Obtiene los detalles completos de una cafetería
     */
    public function findById(int $id): ?array
    {
        $stmt = $this->pdo->prepare("SELECT
            cafeterias.*,
            ciudades.nombre AS ciudad,
            entidades_federativas.nombre AS entidad_federativa,
            paises.nombre AS pais
            FROM cafeterias
            INNER JOIN ciudades ON cafeterias.id_ciudad = ciudades.id
            INNER JOIN entidades_federativas ON ciudades.id_entidad_federativa = entidades_federativas.id
            INNER JOIN paises ON entidades_federativas.id_pais = paises.id
            WHERE cafeterias.id = :id AND cafeterias.estado = 0
            LIMIT 1");
        
        $stmt->execute([':id' => $id]);
        return $stmt->fetch() ?: null;
    }

    /**
     * Obtiene las imágenes de una cafetería
     */
    public function getImagenes(int $cafeteriaId): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT imagen, descripcion 
             FROM cafeterias_imagenes 
             WHERE id_cafeteria = :id AND estado = 0 
             ORDER BY id ASC"
        );
        $stmt->execute([':id' => $cafeteriaId]);
        return $stmt->fetchAll();
    }

    /**
     * Obtiene los servicios de una cafetería
     */
    public function getServicios(int $cafeteriaId): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT servicios.id, servicios.nombre, servicios.imagen
             FROM servicios
             INNER JOIN cafeterias_servicios ON cafeterias_servicios.id_servicio = servicios.id
             WHERE cafeterias_servicios.id_cafeteria = :id 
             AND cafeterias_servicios.estado = 0 
             AND servicios.estado = 0
             ORDER BY servicios.nombre ASC"
        );
        $stmt->execute([':id' => $cafeteriaId]);
        return $stmt->fetchAll();
    }

    /**
     * Obtiene horarios simples de una cafetería
     * Los horarios simples están en la tabla cafeterias directamente
     */
    public function getHorariosSimples(int $cafeteriaId): ?array
    {
        $stmt = $this->pdo->prepare(
            "SELECT horario_apertura, horario_cierre 
             FROM cafeterias 
             WHERE id = :id AND estado = 0 
             LIMIT 1"
        );
        $stmt->execute([':id' => $cafeteriaId]);
        $result = $stmt->fetch();
        
        // Solo retornar si tiene valores válidos
        if ($result && (!empty($result['horario_apertura']) || !empty($result['horario_cierre']))) {
            return $result;
        }
        return null;
    }

    /**
     * Obtiene horarios detallados de una cafetería
     * La tabla es cafeteria_horarios (singular) con campos: dia, hora_apertura, hora_cierre, cerrado
     */
    public function getHorariosDetallados(int $cafeteriaId): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT dia, hora_apertura, hora_cierre, cerrado 
             FROM cafeteria_horarios 
             WHERE id_cafeteria = :id 
             ORDER BY dia"
        );
        $stmt->execute([':id' => $cafeteriaId]);
        $rows = $stmt->fetchAll();

        $result = [];
        foreach ($rows as $row) {
            $result[$row['dia']] = [
                'apertura' => $row['hora_apertura'],
                'cierre' => $row['hora_cierre'],
                'cerrado' => $row['cerrado'],
            ];
        }
        return $result;
    }

    /**
     * Obtiene comentarios paginados de una cafetería
     */
    public function getComentarios(int $cafeteriaId, int $page, int $pageSize): array
    {
        $offset = ($page - 1) * $pageSize;

        // Contar total
        $countStmt = $this->pdo->prepare(
            "SELECT COUNT(*) as total 
             FROM cafeterias_comentarios 
             WHERE id_cafeteria = :id AND estado = 0"
        );
        $countStmt->execute([':id' => $cafeteriaId]);
        $total = (int)$countStmt->fetchColumn();

        // Obtener comentarios
        $stmt = $this->pdo->prepare(
            "SELECT 
                cafeterias_comentarios.*,
                CONCAT(admin_usuarios.nombre, ' ', admin_usuarios.apellido) AS nombre_usuario
             FROM cafeterias_comentarios
             INNER JOIN admin_usuarios ON cafeterias_comentarios.id_usuario = admin_usuarios.id
             WHERE cafeterias_comentarios.id_cafeteria = :id 
             AND cafeterias_comentarios.estado = 0
             ORDER BY cafeterias_comentarios.fecha_alta DESC
             LIMIT :offset, :limit"
        );
        $stmt->bindValue(':id', $cafeteriaId, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $pageSize, PDO::PARAM_INT);
        $stmt->execute();
        $comentarios = $stmt->fetchAll();

        return [
            'items' => $comentarios,
            'total' => $total,
        ];
    }

    /**
     * Registra un comentario (requiere autenticación)
     */
    public function createComentario(int $cafeteriaId, int $usuarioId, string $comentario, string $fechaAlta): bool
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO cafeterias_comentarios 
             (id_cafeteria, id_usuario, comentario, fecha_alta, estado) 
             VALUES (:id_cafeteria, :id_usuario, :comentario, :fecha_alta, 0)"
        );
        return $stmt->execute([
            ':id_cafeteria' => $cafeteriaId,
            ':id_usuario' => $usuarioId,
            ':comentario' => $comentario,
            ':fecha_alta' => $fechaAlta,
        ]);
    }

    /**
     * Obtiene la zona horaria de la ciudad de una cafetería
     */
    public function getZonaHoraria(int $cafeteriaId): ?string
    {
        $stmt = $this->pdo->prepare(
            "SELECT ciudades.zona_horaria 
             FROM cafeterias
             INNER JOIN ciudades ON cafeterias.id_ciudad = ciudades.id
             WHERE cafeterias.id = :id 
             LIMIT 1"
        );
        $stmt->execute([':id' => $cafeteriaId]);
        $result = $stmt->fetchColumn();
        return $result ? (string)$result : null;
    }
}
