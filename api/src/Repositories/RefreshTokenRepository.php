<?php

declare(strict_types=1);

namespace Occu\Api\Repositories;

use DateTimeImmutable;
use Occu\Api\Infrastructure\Db;
use PDO;

final class RefreshTokenRepository
{
    private PDO $pdo;

    public function __construct(Db $db)
    {
        $this->pdo = $db->pdo();
    }

    public function create(int $userId, string $tokenHash, DateTimeImmutable $expiresAt, ?string $ip, ?string $userAgent): void
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO api_refresh_tokens
            (id_usuario, token_hash, created_at, expires_at, ip_address, user_agent)
            VALUES
            (:id_usuario, :token_hash, NOW(), :expires_at, :ip, :ua)'
        );

        $stmt->execute([
            ':id_usuario' => $userId,
            ':token_hash' => $tokenHash,
            ':expires_at' => $expiresAt->format('Y-m-d H:i:s'),
            ':ip' => $ip,
            ':ua' => $userAgent,
        ]);
    }

    public function findByHash(string $tokenHash): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM api_refresh_tokens WHERE token_hash = :h LIMIT 1');
        $stmt->execute([':h' => $tokenHash]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function revoke(string $tokenHash): void
    {
        $stmt = $this->pdo->prepare('UPDATE api_refresh_tokens SET revoked_at = NOW() WHERE token_hash = :h AND revoked_at IS NULL');
        $stmt->execute([':h' => $tokenHash]);
    }

    public function rotate(string $oldHash, string $newHash): void
    {
        $stmt = $this->pdo->prepare(
            'UPDATE api_refresh_tokens
             SET revoked_at = NOW(), replaced_by_hash = :new
             WHERE token_hash = :old AND revoked_at IS NULL'
        );
        $stmt->execute([':old' => $oldHash, ':new' => $newHash]);
    }
}

