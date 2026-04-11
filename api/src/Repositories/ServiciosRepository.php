<?php

declare(strict_types=1);

namespace Occu\Api\Repositories;

use Occu\Api\Infrastructure\Db;
use PDO;

final class ServiciosRepository
{
    private PDO $pdo;

    public function __construct(Db $db)
    {
        $this->pdo = $db->pdo();
    }

    /**
     * Obtiene todos los servicios activos
     * @return array<array{id: int, nombre: string}>
     */
    public function findAll(): array
    {
        $stmt = $this->pdo->prepare('SELECT id, nombre FROM servicios WHERE estado = 0 ORDER BY nombre ASC');
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
