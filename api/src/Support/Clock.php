<?php

declare(strict_types=1);

namespace Occu\Api\Support;

use DateTimeImmutable;
use DateTimeZone;

/**
 * Utilidad para obtener fechas en timezone específico sin modificar timezone global.
 *
 * Regla: guardar en BD como Y-m-d H:i:s en el timezone de la entidad
 * (ej. cafetería) o UTC según contexto. Para comentarios y fechas de cafetería,
 * usar el timezone de la cafetería cuando esté disponible.
 *
 * @see api/README.md para documentación de timezone
 */
final class Clock
{
    /**
     * Obtiene "now" en el timezone indicado.
     *
     * @param string $timezone Ej: 'America/Tijuana', 'UTC'
     * @param string $format  Formato de salida (default Y-m-d H:i:s)
     */
    public static function now(string $timezone = 'UTC', string $format = 'Y-m-d H:i:s'): string
    {
        $dt = new DateTimeImmutable('now', new DateTimeZone($timezone));
        return $dt->format($format);
    }

    /**
     * Obtiene la instancia DateTimeImmutable para cálculos adicionales.
     */
    public static function nowImmutable(string $timezone = 'UTC'): DateTimeImmutable
    {
        return new DateTimeImmutable('now', new DateTimeZone($timezone));
    }
}
