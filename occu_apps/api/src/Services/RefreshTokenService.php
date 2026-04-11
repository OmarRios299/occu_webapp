<?php

declare(strict_types=1);

namespace Occu\Api\Services;

use DateInterval;
use DateTimeImmutable;
use Occu\Api\Repositories\RefreshTokenRepository;

final class RefreshTokenService
{
    public function __construct(private readonly RefreshTokenRepository $repo) {}

    /**
     * @return array{refresh_token:string, refresh_token_expires_at:string}
     */
    public function createForUser(int $userId, ?string $ip, ?string $userAgent): array
    {
        $ttlDays = (int)(getenv('API_REFRESH_TTL_DAYS') ?: 30);
        if ($ttlDays < 1) $ttlDays = 30;

        $token = $this->generateToken();
        $hash = $this->hashToken($token);

        $expiresAt = (new DateTimeImmutable('now'))->add(new DateInterval('P' . $ttlDays . 'D'));
        $this->repo->create($userId, $hash, $expiresAt, $ip, $userAgent);

        return [
            'refresh_token' => $token,
            'refresh_token_expires_at' => $expiresAt->format(DATE_ATOM),
        ];
    }

    /**
     * @return array{user_id:int, new_refresh_token:string, new_refresh_token_expires_at:string}|null
     */
    public function rotate(string $refreshToken, ?string $ip, ?string $userAgent): ?array
    {
        $oldHash = $this->hashToken($refreshToken);
        $row = $this->repo->findByHash($oldHash);
        if (!$row) return null;

        if (!empty($row['revoked_at'])) return null;
        if (!empty($row['expires_at']) && strtotime((string)$row['expires_at']) < time()) return null;

        $userId = (int)$row['id_usuario'];

        $new = $this->createForUser($userId, $ip, $userAgent);
        $newHash = $this->hashToken($new['refresh_token']);
        $this->repo->rotate($oldHash, $newHash);

        return [
            'user_id' => $userId,
            'new_refresh_token' => $new['refresh_token'],
            'new_refresh_token_expires_at' => $new['refresh_token_expires_at'],
        ];
    }

    public function revoke(string $refreshToken): void
    {
        $hash = $this->hashToken($refreshToken);
        $this->repo->revoke($hash);
    }

    private function generateToken(): string
    {
        $bytes = random_bytes(32);
        return rtrim(strtr(base64_encode($bytes), '+/', '-_'), '=');
    }

    private function hashToken(string $token): string
    {
        return hash('sha256', $token);
    }
}

