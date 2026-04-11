<?php

declare(strict_types=1);

namespace Occu\Api\Repositories;

use Occu\Api\Infrastructure\Db;
use PDO;

final class OwnerCafeteriasRepository
{
    private PDO $pdo;

    public function __construct(Db $db)
    {
        $this->pdo = $db->pdo();
    }

    /**
     * Obtiene todas las cafeterías de un usuario propietario
     * Si el usuario es Administrador, puede obtener todas las cafeterías
     * 
     * @param int $userId ID del usuario autenticado
     * @param string $userLevel Nivel del usuario ('Propietario' o 'Administrador')
     * @return array Lista de cafeterías
     */
    public function findByOwner(int $userId, string $userLevel): array
    {
        $whereClause = 'cafeterias.estado != 2'; // Excluir eliminadas (soft delete)
        
        // Si es Propietario, solo sus cafeterías. Si es Administrador, todas.
        if ($userLevel === 'Propietario') {
            $whereClause .= ' AND cafeterias.id_usuario = :userId';
        }

        $sql = "SELECT
            cafeterias.id,
            cafeterias.nombre,
            cafeterias.imagen,
            cafeterias.direccion,
            cafeterias.telefono,
            cafeterias.correo_electronico,
            cafeterias.horario_apertura,
            cafeterias.horario_cierre,
            cafeterias.estado,
            cafeterias.descripcion,
            cafeterias.id_ciudad,
            ciudades.nombre AS ciudad,
            entidades_federativas.nombre AS entidad_federativa
            FROM cafeterias
            INNER JOIN ciudades ON cafeterias.id_ciudad = ciudades.id
            INNER JOIN entidades_federativas ON ciudades.id_entidad_federativa = entidades_federativas.id
            WHERE {$whereClause}
            ORDER BY cafeterias.nombre ASC";

        $stmt = $this->pdo->prepare($sql);
        
        if ($userLevel === 'Propietario') {
            $stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
        }
        
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Obtiene los detalles de una cafetería específica
     * Verifica que el usuario sea el propietario o sea Administrador
     * 
     * @param int $cafeteriaId ID de la cafetería
     * @param int $userId ID del usuario autenticado
     * @param string $userLevel Nivel del usuario
     * @return array|null Datos de la cafetería o null si no existe/no autorizado
     */
    public function findById(int $cafeteriaId, int $userId, string $userLevel): ?array
    {
        $whereClause = 'cafeterias.id = :cafeteriaId AND cafeterias.estado != 2';
        
        // Si es Propietario, verificar que sea su cafetería
        if ($userLevel === 'Propietario') {
            $whereClause .= ' AND cafeterias.id_usuario = :userId';
        }

        $sql = "SELECT
            cafeterias.id,
            cafeterias.nombre,
            cafeterias.imagen,
            cafeterias.direccion,
            cafeterias.telefono,
            cafeterias.correo_electronico,
            cafeterias.horario_apertura,
            cafeterias.horario_cierre,
            cafeterias.horario_diferente,
            cafeterias.estado,
            cafeterias.descripcion,
            cafeterias.latitud,
            cafeterias.longitud,
            cafeterias.id_ciudad,
            ciudades.nombre AS ciudad,
            entidades_federativas.nombre AS entidad_federativa
            FROM cafeterias
            INNER JOIN ciudades ON cafeterias.id_ciudad = ciudades.id
            INNER JOIN entidades_federativas ON ciudades.id_entidad_federativa = entidades_federativas.id
            WHERE {$whereClause}
            LIMIT 1";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':cafeteriaId', $cafeteriaId, PDO::PARAM_INT);
        
        if ($userLevel === 'Propietario') {
            $stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
        }
        
        $stmt->execute();
        $result = $stmt->fetch();
        
        return $result ?: null;
    }

    /**
     * Actualiza el estado de una cafetería (0 = activa, 1 = inactiva)
     * Verifica autorización antes de actualizar
     * 
     * @param int $cafeteriaId ID de la cafetería
     * @param int $estado Nuevo estado (0 o 1)
     * @param int $userId ID del usuario autenticado
     * @param string $userLevel Nivel del usuario
     * @return bool True si se actualizó, false si no existe/no autorizado
     */
    public function updateEstado(int $cafeteriaId, int $estado, int $userId, string $userLevel): bool
    {
        // Verificar que existe y que el usuario tiene permisos
        $cafeteria = $this->findById($cafeteriaId, $userId, $userLevel);
        if (!$cafeteria) {
            return false;
        }

        // Validar que el estado sea 0 o 1
        if ($estado !== 0 && $estado !== 1) {
            return false;
        }

        $stmt = $this->pdo->prepare("UPDATE cafeterias SET estado = :estado WHERE id = :id");
        $stmt->bindValue(':estado', $estado, PDO::PARAM_INT);
        $stmt->bindValue(':id', $cafeteriaId, PDO::PARAM_INT);
        
        return $stmt->execute();
    }

    /**
     * Elimina una cafetería (soft delete: estado = 2)
     * Verifica autorización antes de eliminar
     * 
     * @param int $cafeteriaId ID de la cafetería
     * @param int $userId ID del usuario autenticado
     * @param string $userLevel Nivel del usuario
     * @return bool True si se eliminó, false si no existe/no autorizado
     */
    public function delete(int $cafeteriaId, int $userId, string $userLevel): bool
    {
        // Verificar que existe y que el usuario tiene permisos
        $cafeteria = $this->findById($cafeteriaId, $userId, $userLevel);
        if (!$cafeteria) {
            return false;
        }

        // Soft delete: estado = 2
        $stmt = $this->pdo->prepare("UPDATE cafeterias SET estado = 2 WHERE id = :id");
        $stmt->bindValue(':id', $cafeteriaId, PDO::PARAM_INT);
        
        return $stmt->execute();
    }

    /**
     * Obtiene todos los servicios con indicador de si están registrados para la cafetería
     * Similar al comportamiento legacy
     * 
     * @param int $cafeteriaId ID de la cafetería
     * @return array Lista de servicios con campo servicio_registrado (1 = sí, 0 = no)
     */
    public function getServiciosConEstado(int $cafeteriaId): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT 
                s.id AS id,
                s.nombre,
                s.imagen,
                CASE 
                    WHEN cs.id_servicio IS NOT NULL THEN 1
                    ELSE 0
                END AS servicio_registrado
            FROM servicios s
            LEFT JOIN (
                SELECT DISTINCT id_servicio 
                FROM cafeterias_servicios 
                WHERE id_cafeteria = :id AND estado = 0
            ) cs ON s.id = cs.id_servicio
            WHERE s.estado = 0
            ORDER BY s.nombre ASC"
        );
        $stmt->execute([':id' => $cafeteriaId]);
        return $stmt->fetchAll();
    }

    /**
     * Actualiza los servicios de una cafetería (reemplazo total)
     * Soft delete de todos los servicios actuales y luego inserta los nuevos
     * 
     * @param int $cafeteriaId ID de la cafetería
     * @param array<int> $servicioIds Array de IDs de servicios a asignar
     * @return bool True si se actualizó correctamente
     */
    public function updateServicios(int $cafeteriaId, array $servicioIds): bool
    {
        $this->pdo->beginTransaction();
        
        try {
            // Soft delete de todos los servicios actuales
            $stmtDelete = $this->pdo->prepare("UPDATE cafeterias_servicios SET estado = 2 WHERE id_cafeteria = :id");
            $stmtDelete->execute([':id' => $cafeteriaId]);

            // Insertar nuevos servicios
            if (!empty($servicioIds)) {
                $stmtInsert = $this->pdo->prepare(
                    "INSERT INTO cafeterias_servicios (id_servicio, id_cafeteria, estado) VALUES (:servicio, :cafeteria, 0)"
                );
                
                foreach ($servicioIds as $servicioId) {
                    $stmtInsert->execute([
                        ':servicio' => $servicioId,
                        ':cafeteria' => $cafeteriaId,
                    ]);
                }
            }

            $this->pdo->commit();
            return true;
        } catch (\Throwable $e) {
            $this->pdo->rollBack();
            error_log("Error actualizando servicios: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Crea una nueva cafetería
     * 
     * @param array $data Datos de la cafetería
     * @param int $userId ID del usuario que crea la cafetería
     * @return int ID de la cafetería creada
     * @throws \RuntimeException Si falla la creación
     */
    public function create(array $data, int $userId): int
    {
        $this->pdo->beginTransaction();

        try {
            // Validar nombre único
            $stmtCheck = $this->pdo->prepare("SELECT COUNT(*) FROM cafeterias WHERE nombre = :nombre AND estado != 2");
            $stmtCheck->execute([':nombre' => $data['nombre']]);
            if ($stmtCheck->fetchColumn() > 0) {
                throw new \RuntimeException('El nombre de cafetería ya está en uso');
            }

            // Validar email único si se proporciona
            if (!empty($data['correo_electronico'])) {
                $stmtCheck = $this->pdo->prepare("SELECT COUNT(*) FROM cafeterias WHERE correo_electronico = :correo AND estado != 2");
                $stmtCheck->execute([':correo' => $data['correo_electronico']]);
                if ($stmtCheck->fetchColumn() > 0) {
                    throw new \RuntimeException('El correo electrónico ya está en uso');
                }
            }

            $fechaAlta = \Occu\Api\Support\Clock::now('America/Tijuana');

            $stmt = $this->pdo->prepare("INSERT INTO cafeterias (
                nombre,
                imagen,
                id_ciudad,
                direccion,
                telefono,
                correo_electronico,
                latitud,
                longitud,
                horario_apertura,
                horario_cierre,
                horario_diferente,
                id_usuario,
                id_alta,
                fecha_alta,
                descripcion,
                estado
            ) VALUES (
                :nombre,
                :imagen,
                :id_ciudad,
                :direccion,
                :telefono,
                :correo_electronico,
                :latitud,
                :longitud,
                :horario_apertura,
                :horario_cierre,
                :horario_diferente,
                :id_usuario,
                :id_alta,
                :fecha_alta,
                :descripcion,
                0
            )");

            $stmt->execute([
                ':nombre' => $data['nombre'],
                ':imagen' => $data['imagen'] ?? 'views/assets/img/cafeteria_default.png',
                ':id_ciudad' => $data['id_ciudad'],
                ':direccion' => $data['direccion'],
                ':telefono' => $data['telefono'],
                ':correo_electronico' => $data['correo_electronico'] ?? '',
                ':latitud' => $data['latitud'] ?? '',
                ':longitud' => $data['longitud'] ?? '',
                ':horario_apertura' => $data['horario_apertura'] ?? null,
                ':horario_cierre' => $data['horario_cierre'] ?? null,
                ':horario_diferente' => $data['horario_diferente'] ?? 'NO',
                ':id_usuario' => $userId,
                ':id_alta' => $userId,
                ':fecha_alta' => $fechaAlta,
                ':descripcion' => $data['descripcion'] ?? '',
            ]);

            $cafeteriaId = (int)$this->pdo->lastInsertId();

            // Si hay horarios detallados, insertarlos
            if (isset($data['horarios_detallados']) && is_array($data['horarios_detallados'])) {
                $stmtHorario = $this->pdo->prepare(
                    "INSERT INTO cafeteria_horarios (id_cafeteria, dia, hora_apertura, hora_cierre, cerrado) 
                     VALUES (:id_cafeteria, :dia, :hora_apertura, :hora_cierre, :cerrado)"
                );

                foreach ($data['horarios_detallados'] as $dia => $horario) {
                    $stmtHorario->execute([
                        ':id_cafeteria' => $cafeteriaId,
                        ':dia' => $dia,
                        ':hora_apertura' => $horario['cerrado'] === 'SI' ? null : ($horario['apertura'] ?? null),
                        ':hora_cierre' => $horario['cerrado'] === 'SI' ? null : ($horario['cierre'] ?? null),
                        ':cerrado' => $horario['cerrado'] ?? 'NO',
                    ]);
                }
            }

            $this->pdo->commit();
            return $cafeteriaId;
        } catch (\Throwable $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    /**
     * Actualiza una cafetería existente
     * 
     * @param int $cafeteriaId ID de la cafetería
     * @param array $data Datos a actualizar
     * @param int $userId ID del usuario
     * @param string $userLevel Nivel del usuario
     * @return bool True si se actualizó correctamente
     */
    public function update(int $cafeteriaId, array $data, int $userId, string $userLevel): bool
    {
        // Verificar que existe y que el usuario tiene permisos
        $cafeteria = $this->findById($cafeteriaId, $userId, $userLevel);
        if (!$cafeteria) {
            return false;
        }

        $this->pdo->beginTransaction();

        try {
            // Validar nombre único (excepto la propia cafetería)
            $stmtCheck = $this->pdo->prepare("SELECT COUNT(*) FROM cafeterias WHERE nombre = :nombre AND id != :id AND estado != 2");
            $stmtCheck->execute([':nombre' => $data['nombre'], ':id' => $cafeteriaId]);
            if ($stmtCheck->fetchColumn() > 0) {
                throw new \RuntimeException('El nombre de cafetería ya está en uso');
            }

            // Validar email único si se proporciona (excepto la propia cafetería)
            if (!empty($data['correo_electronico'])) {
                $stmtCheck = $this->pdo->prepare("SELECT COUNT(*) FROM cafeterias WHERE correo_electronico = :correo AND id != :id AND estado != 2");
                $stmtCheck->execute([':correo' => $data['correo_electronico'], ':id' => $cafeteriaId]);
                if ($stmtCheck->fetchColumn() > 0) {
                    throw new \RuntimeException('El correo electrónico ya está en uso');
                }
            }

            // Si se proporciona imagen, actualizarla también
            $updateImagen = !empty($data['imagen']);
            $sqlUpdate = "UPDATE cafeterias SET
                nombre = :nombre,
                id_ciudad = :id_ciudad,
                direccion = :direccion,
                telefono = :telefono,
                correo_electronico = :correo_electronico,
                latitud = :latitud,
                longitud = :longitud,
                horario_apertura = :horario_apertura,
                horario_cierre = :horario_cierre,
                horario_diferente = :horario_diferente,
                descripcion = :descripcion";
            
            if ($updateImagen) {
                $sqlUpdate .= ", imagen = :imagen";
            }
            
            $sqlUpdate .= " WHERE id = :id";
            
            $stmt = $this->pdo->prepare($sqlUpdate);

            $params = [
                ':nombre' => $data['nombre'],
                ':id_ciudad' => $data['id_ciudad'],
                ':direccion' => $data['direccion'],
                ':telefono' => $data['telefono'],
                ':correo_electronico' => $data['correo_electronico'] ?? '',
                ':latitud' => $data['latitud'] ?? '',
                ':longitud' => $data['longitud'] ?? '',
                ':horario_apertura' => $data['horario_apertura'] ?? null,
                ':horario_cierre' => $data['horario_cierre'] ?? null,
                ':horario_diferente' => $data['horario_diferente'] ?? 'NO',
                ':descripcion' => $data['descripcion'] ?? '',
                ':id' => $cafeteriaId,
            ];
            
            if ($updateImagen) {
                // Eliminar imagen anterior si existe y no es la imagen por defecto
                $cafeteria = $this->findById($cafeteriaId, $userId, $userLevel);
                if ($cafeteria && !empty($cafeteria['imagen']) && $cafeteria['imagen'] !== 'views/assets/img/cafeteria_default.png') {
                    $rutaAnterior = __DIR__ . "/../../../{$cafeteria['imagen']}";
                    if (file_exists($rutaAnterior)) {
                        @unlink($rutaAnterior);
                    }
                }
                $params[':imagen'] = $data['imagen'];
            }
            
            $stmt->execute($params);

            // Eliminar horarios detallados antiguos
            $stmtDelete = $this->pdo->prepare("DELETE FROM cafeteria_horarios WHERE id_cafeteria = :id");
            $stmtDelete->execute([':id' => $cafeteriaId]);

            // Insertar nuevos horarios detallados si existen
            if (isset($data['horarios_detallados']) && is_array($data['horarios_detallados'])) {
                $stmtHorario = $this->pdo->prepare(
                    "INSERT INTO cafeteria_horarios (id_cafeteria, dia, hora_apertura, hora_cierre, cerrado) 
                     VALUES (:id_cafeteria, :dia, :hora_apertura, :hora_cierre, :cerrado)"
                );

                foreach ($data['horarios_detallados'] as $dia => $horario) {
                    $stmtHorario->execute([
                        ':id_cafeteria' => $cafeteriaId,
                        ':dia' => $dia,
                        ':hora_apertura' => $horario['cerrado'] === 'SI' ? null : ($horario['apertura'] ?? null),
                        ':hora_cierre' => $horario['cerrado'] === 'SI' ? null : ($horario['cierre'] ?? null),
                        ':cerrado' => $horario['cerrado'] ?? 'NO',
                    ]);
                }
            }

            $this->pdo->commit();
            return true;
        } catch (\Throwable $e) {
            $this->pdo->rollBack();
            error_log("Error actualizando cafetería: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Obtiene los horarios detallados de una cafetería
     * 
     * @param int $cafeteriaId ID de la cafetería
     * @return array Array asociativo con los días como claves y horarios como valores
     */
    public function getHorariosDetallados(int $cafeteriaId): array
    {
        $stmt = $this->pdo->prepare("
            SELECT dia, hora_apertura, hora_cierre, cerrado 
            FROM cafeteria_horarios 
            WHERE id_cafeteria = :id
        ");
        $stmt->execute([':id' => $cafeteriaId]);
        $horarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $resultado = [];
        foreach ($horarios as $horario) {
            $dia = $horario['dia'];
            $resultado[$dia] = [
                'apertura' => $horario['hora_apertura'] ?? '',
                'cierre' => $horario['hora_cierre'] ?? '',
                'cerrado' => $horario['cerrado'] ?? 'SI',
            ];
        }

        return $resultado;
    }

    /**
     * Actualiza solo la imagen de una cafetería
     * 
     * @param int $cafeteriaId ID de la cafetería
     * @param string $rutaImagen Ruta relativa de la nueva imagen
     * @param int $userId ID del usuario autenticado
     * @param string $userLevel Nivel del usuario
     * @return bool True si se actualizó, false si no existe/no autorizado
     */
    public function updateImagen(int $cafeteriaId, string $rutaImagen, int $userId, string $userLevel): bool
    {
        // Verificar que existe y que el usuario tiene permisos
        $cafeteria = $this->findById($cafeteriaId, $userId, $userLevel);
        if (!$cafeteria) {
            return false;
        }

        // Eliminar imagen anterior si existe y no es la imagen por defecto
        if (!empty($cafeteria['imagen']) && $cafeteria['imagen'] !== 'views/assets/img/cafeteria_default.png') {
            $rutaAnterior = __DIR__ . "/../../../{$cafeteria['imagen']}";
            if (file_exists($rutaAnterior)) {
                @unlink($rutaAnterior);
            }
        }

        $stmt = $this->pdo->prepare("UPDATE cafeterias SET imagen = :imagen WHERE id = :id");
        $stmt->bindValue(':imagen', $rutaImagen, PDO::PARAM_STR);
        $stmt->bindValue(':id', $cafeteriaId, PDO::PARAM_INT);
        
        return $stmt->execute();
    }
}
