<?php

declare(strict_types=1);

namespace Occu\Api\Infrastructure;

use PDO;
use PDOException;

final class Db
{
    private ?PDO $pdo = null;

    public function pdo(): PDO
    {
        if ($this->pdo) {
            return $this->pdo;
        }

        $host = (string)getenv('DB_HOST');
        $name = (string)getenv('DB_NAME');
        $user = (string)getenv('DB_USER');
        $pass = (string)getenv('DB_PASS');

        if ($host === '' || $name === '' || $user === '') {
            throw new \RuntimeException('DB_* no configuradas (DB_HOST, DB_NAME, DB_USER, DB_PASS)');
        }

        try {
            $pdo = new PDO(
                "mysql:host={$host};dbname={$name};charset=utf8mb4",
                $user,
                $pass,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]
            );
        } catch (PDOException $e) {
            throw new \RuntimeException('No se pudo conectar a la BD');
        }

        $this->pdo = $pdo;
        return $this->pdo;
    }
}

