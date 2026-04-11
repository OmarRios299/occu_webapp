<?php

declare(strict_types=1);

namespace Occu\Api\Services;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

final class JwtService
{
    public function __construct(
        private readonly string $secret,
        private readonly string $issuer,
        private readonly string $audience,
    ) {}

    public function issueAccessToken(int $userId, string $nivel, int $ttlSeconds = 900): string
    {
        $now = time();
        $payload = [
            'iss' => $this->issuer,
            'aud' => $this->audience,
            'iat' => $now,
            'exp' => $now + $ttlSeconds,
            'sub' => (string)$userId,
            'nivel' => $nivel,
            'typ' => 'access',
        ];
        return JWT::encode($payload, $this->secret, 'HS256');
    }

    public function issueGooglePendingToken(int $userId, int $ttlSeconds = 600): string
    {
        $now = time();
        $payload = [
            'iss' => $this->issuer,
            'aud' => $this->audience,
            'iat' => $now,
            'exp' => $now + $ttlSeconds,
            'sub' => (string)$userId,
            'typ' => 'google_pending',
        ];
        return JWT::encode($payload, $this->secret, 'HS256');
    }

    public function verifyAccessToken(string $jwt): ?array
    {
        $payload = $this->decode($jwt);
        if (!$payload) return null;
        if (($payload['typ'] ?? null) !== 'access') return null;
        return $payload;
    }

    public function verifyGooglePendingToken(string $jwt): ?array
    {
        $payload = $this->decode($jwt);
        if (!$payload) return null;
        if (($payload['typ'] ?? null) !== 'google_pending') return null;
        return $payload;
    }

    private function decode(string $jwt): ?array
    {
        try {
            $decoded = JWT::decode($jwt, new Key($this->secret, 'HS256'));
            /** @var array $arr */
            $arr = json_decode(json_encode($decoded), true);
            return is_array($arr) ? $arr : null;
        } catch (\Throwable $e) {
            return null;
        }
    }
}

