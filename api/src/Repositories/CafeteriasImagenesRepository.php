<?php

declare(strict_types=1);

namespace Occu\Api\Repositories;

use Occu\Api\Infrastructure\Db;
use PDO;

final class CafeteriasImagenesRepository
{
    private PDO $pdo;

    public function __construct(Db $db)
    {
        $this->pdo = $db->pdo();
    }

    /**
     * Obtiene todas las imágenes de una cafetería
     * 
     * @param int $cafeteriaId ID de la cafetería
     * @param int $userId ID del usuario autenticado (para verificación)
     * @param string $userLevel Nivel del usuario
     * @return array Lista de imágenes
     */
    public function findByCafeteria(int $cafeteriaId, int $userId, string $userLevel): array
    {
        // Verificar que la cafetería existe y pertenece al usuario (si es Propietario)
        $sqlCheck = "SELECT id_usuario FROM cafeterias WHERE id = :id AND estado != 2";
        $stmtCheck = $this->pdo->prepare($sqlCheck);
        $stmtCheck->execute([':id' => $cafeteriaId]);
        $cafeteria = $stmtCheck->fetch();

        if (!$cafeteria) {
            return [];
        }

        // Si es Propietario, verificar que sea su cafetería
        if ($userLevel === 'Propietario' && (int)$cafeteria['id_usuario'] !== $userId) {
            return [];
        }

        $sql = "SELECT 
            id,
            id_cafeteria,
            imagen,
            descripcion,
            estado
            FROM cafeterias_imagenes
            WHERE id_cafeteria = :id_cafeteria AND estado != 2
            ORDER BY id DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id_cafeteria' => $cafeteriaId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Agrega una nueva imagen a la galería de una cafetería
     * 
     * @param int $cafeteriaId ID de la cafetería
     * @param string $rutaImagen Ruta relativa de la imagen
     * @param int $userId ID del usuario autenticado (para verificación)
     * @param string $userLevel Nivel del usuario
     * @return int ID de la imagen creada
     * @throws \RuntimeException Si no tiene permisos
     */
    public function create(int $cafeteriaId, string $rutaImagen, int $userId, string $userLevel): int
    {
        // Verificar permisos
        $sqlCheck = "SELECT id_usuario FROM cafeterias WHERE id = :id AND estado != 2";
        $stmtCheck = $this->pdo->prepare($sqlCheck);
        $stmtCheck->execute([':id' => $cafeteriaId]);
        $cafeteria = $stmtCheck->fetch();

        if (!$cafeteria) {
            throw new \RuntimeException('Cafetería no encontrada');
        }

        if ($userLevel === 'Propietario' && (int)$cafeteria['id_usuario'] !== $userId) {
            throw new \RuntimeException('No autorizado');
        }

        $sql = "INSERT INTO cafeterias_imagenes (id_cafeteria, imagen, descripcion, estado) 
                VALUES (:id_cafeteria, :imagen, '', 0)";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':id_cafeteria' => $cafeteriaId,
            ':imagen' => $rutaImagen,
        ]);

        return (int)$this->pdo->lastInsertId();
    }

    /**
     * Elimina una imagen (soft delete: estado = 2)
     * 
     * @param int $imagenId ID de la imagen
     * @param int $cafeteriaId ID de la cafetería (para verificación)
     * @param int $userId ID del usuario autenticado
     * @param string $userLevel Nivel del usuario
     * @return bool True si se eliminó correctamente
     */
    public function delete(int $imagenId, int $cafeteriaId, int $userId, string $userLevel): bool
    {
        // Verificar permisos
        $sqlCheck = "SELECT ci.id, c.id_usuario 
                     FROM cafeterias_imagenes ci
                     INNER JOIN cafeterias c ON ci.id_cafeteria = c.id
                     WHERE ci.id = :imagen_id AND ci.id_cafeteria = :cafeteria_id AND ci.estado != 2";
        $stmtCheck = $this->pdo->prepare($sqlCheck);
        $stmtCheck->execute([
            ':imagen_id' => $imagenId,
            ':cafeteria_id' => $cafeteriaId,
        ]);
        $imagen = $stmtCheck->fetch();

        if (!$imagen) {
            return false;
        }

        if ($userLevel === 'Propietario' && (int)$imagen['id_usuario'] !== $userId) {
            return false;
        }

        $stmt = $this->pdo->prepare("UPDATE cafeterias_imagenes SET estado = 2 WHERE id = :id");
        $stmt->execute([':id' => $imagenId]);

        return $stmt->rowCount() > 0;
    }

    /**
     * Actualiza el estado de una imagen (0 = activa, 1 = inactiva)
     * 
     * @param int $imagenId ID de la imagen
     * @param int $cafeteriaId ID de la cafetería (para verificación)
     * @param int $estado Nuevo estado (0 o 1)
     * @param int $userId ID del usuario autenticado
     * @param string $userLevel Nivel del usuario
     * @return bool True si se actualizó correctamente
     */
    public function updateEstado(int $imagenId, int $cafeteriaId, int $estado, int $userId, string $userLevel): bool
    {
        // Validar estado
        if ($estado !== 0 && $estado !== 1) {
            return false;
        }

        // Verificar permisos
        $sqlCheck = "SELECT ci.id, c.id_usuario 
                     FROM cafeterias_imagenes ci
                     INNER JOIN cafeterias c ON ci.id_cafeteria = c.id
                     WHERE ci.id = :imagen_id AND ci.id_cafeteria = :cafeteria_id AND ci.estado != 2";
        $stmtCheck = $this->pdo->prepare($sqlCheck);
        $stmtCheck->execute([
            ':imagen_id' => $imagenId,
            ':cafeteria_id' => $cafeteriaId,
        ]);
        $imagen = $stmtCheck->fetch();

        if (!$imagen) {
            return false;
        }

        if ($userLevel === 'Propietario' && (int)$imagen['id_usuario'] !== $userId) {
            return false;
        }

        $stmt = $this->pdo->prepare("UPDATE cafeterias_imagenes SET estado = :estado WHERE id = :id");
        $stmt->execute([
            ':estado' => $estado,
            ':id' => $imagenId,
        ]);

        return $stmt->rowCount() > 0;
    }
}
