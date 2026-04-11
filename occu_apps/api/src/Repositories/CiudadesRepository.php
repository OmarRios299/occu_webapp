<?php

declare(strict_types=1);

namespace Occu\Api\Repositories;

use Occu\Api\Infrastructure\Db;
use PDO;

final class CiudadesRepository
{
    private PDO $pdo;

    public function __construct(Db $db)
    {
        $this->pdo = $db->pdo();
    }

    /**
     * Obtiene todas las ciudades activas
     * @return array<array{id: int, nombre: string}>
     */
    public function findAll(): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT id, nombre 
             FROM ciudades 
             WHERE estado = 0 
             ORDER BY nombre ASC'
        );
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Obtiene una ciudad por ID con sus coordenadas
     * @return array{id: int, nombre: string, coordenadas: string}|null
     */
    public function findById(int $id): ?array
    {
        $stmt = $this->pdo->prepare(
            'SELECT id, nombre, coordenadas 
             FROM ciudades 
             WHERE id = :id AND estado = 0 
             LIMIT 1'
        );
        $stmt->execute([':id' => $id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }
}
