<?php

declare(strict_types=1);

namespace Occu\Api\Services;

final class GoogleIdTokenService
{
    public function __construct(private readonly string $clientId) {}

    /**
     * @return array{sub:string,email:string,name:string,picture:string}|null
     */
    public function verify(string $idToken): ?array
    {
        $url = 'https://oauth2.googleapis.com/tokeninfo?id_token=' . urlencode($idToken);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        $raw = curl_exec($ch);
        $code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($code !== 200 || !$raw) {
            return null;
        }

        $data = json_decode($raw, true);
        if (!is_array($data)) return null;

        if (($data['aud'] ?? null) !== $this->clientId) {
            return null;
        }

        if (empty($data['sub'])) {
            return null;
        }

        return [
            'sub' => (string)$data['sub'],
            'email' => (string)($data['email'] ?? ''),
            'name' => (string)($data['name'] ?? ''),
            'picture' => (string)($data['picture'] ?? ''),
        ];
    }
}

