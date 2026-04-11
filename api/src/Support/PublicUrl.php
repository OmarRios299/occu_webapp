<?php

declare(strict_types=1);

namespace Occu\Api\Support;

/**
 * Helper para generar URLs absolutas desde paths relativos.
 * Usa APP_URL como base; si el path ya es una URL absoluta válida, se devuelve tal cual.
 */
final class PublicUrl
{
    private static ?string $baseUrl = null;

    public static function toAbsolute(string $pathOrUrl): string
    {
        if ($pathOrUrl === '') {
            return '';
        }
        if (filter_var($pathOrUrl, FILTER_VALIDATE_URL)) {
            return $pathOrUrl;
        }
        return self::getBaseUrl() . '/' . ltrim($pathOrUrl, '/');
    }

    public static function getBaseUrl(): string
    {
        if (self::$baseUrl === null) {
            self::$baseUrl = rtrim(
                (string)(getenv('APP_URL') ?: 'http://localhost/OCCU/occu_webApp/'),
                '/'
            );
        }
        return self::$baseUrl;
    }

    /** Permite inyectar base para tests */
    public static function setBaseUrl(?string $url): void
    {
        self::$baseUrl = $url;
    }
}
