## OCCU API (Slim) — Auth

Esta carpeta contiene una API **independiente** del sitio actual, montada en `https://occu.app/api`.

La web existente puede vivir en `https://occu.app/administracion` sin depender de esta API.

### Estructura y escalabilidad (Features)

- `api/src/App.php` es el **composition root** (bootstrap + dependencias + montar módulos). **No se agregan rutas aquí**.
- Las rutas viven por feature en `api/src/Features/*/Routes.php`:
  - `Features/System` (health)
  - `Features/Auth`
  - `Features/Owner`
  - `Features/AdminMenu`
  - `Features/Cafeterias`
  - `Features/Carrito`
- Para agregar un endpoint nuevo, colócalo dentro del feature correspondiente (o crea uno nuevo).
- Guía para agentes/contribuidores: ver `api/AGENTS.md`.

### Requisitos

- PHP 8.0+ (recomendado 8.1+)
- Extensión `pdo_mysql`
- Extensión `curl` (para validar Google ID Token)
- Apache con `AllowOverride All` (para que funcione `.htaccess`)

### Instalación local

1) Instalar dependencias de la API:

```bash
cd api
composer install
```

2) Definir variables de entorno (en tu `.env` del root o variables del servidor):

- `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS`
- `API_JWT_SECRET` (obligatoria)
- `GOOGLE_CLIENT_ID` (obligatoria para Google login)
- `MAIL` (API key de MailerSend, obligatoria para registro/PIN)
- Opcionales:
  - `API_CORS_ORIGINS` (por defecto `*`, o lista separada por comas)
  - `API_REFRESH_TTL_DAYS` (por defecto `30`)
  - `API_JWT_ISSUER` (por defecto `occu.app`)
  - `API_JWT_AUDIENCE` (por defecto `occu`)
  - `MAIL_FROM_EMAIL`, `MAIL_FROM_NAME`
  - **`API_DEBUG`**: `1` (default) muestra detalles de errores 500 al cliente; `0` en producción oculta mensajes internos y trace (solo "Error interno del servidor"). El logging server-side (error_log) mantiene detalle completo.
  - **`APP_URL`**: Base para URLs absolutas (imágenes, etc.). Usado por `PublicUrl::toAbsolute()`.

### API_DEBUG y errores en producción

Cuando `API_DEBUG=0`, las respuestas 500 **no** exponen `$exception->getMessage()` ni trace al cliente. Se responde con mensaje genérico "Error interno del servidor". El detalle se sigue registrando en `error_log` para debugging server-side.

### Timezone (multi-zona)

No se modifica `date_default_timezone_set` global. Se usa `Occu\Api\Support\Clock`:
- `Clock::now(string $timezone = 'UTC', string $format = 'Y-m-d H:i:s')`: devuelve la fecha/hora actual en el timezone indicado.
- `Clock::nowImmutable(string $timezone)`: devuelve `DateTimeImmutable` para cálculos.
- Regla: fechas en BD se guardan en `Y-m-d H:i:s` del timezone de la entidad (ej. cafetería). Para comentarios y horarios de cafetería se usa la zona horaria de la ciudad de la cafetería (por defecto `America/Tijuana`).

### Migración BD (mínima)

La API usa una tabla **nueva** para refresh tokens: `api_refresh_tokens`.

Se agregó una migración Phinx en `bd/migrations/20260206000000_api_refresh_tokens.php`.

Ejecuta migraciones con Phinx (desde el root del proyecto):

```bash
vendor/bin/phinx migrate -e development
```

> En producción usa `-e production` (según tu `phinx.php`).

### Endpoints de Auth

- `GET  /api/health`
- `POST /api/auth/register`
- `POST /api/auth/verify-pin`
- `POST /api/auth/resend-pin`
- `POST /api/auth/login`
- `POST /api/auth/refresh`
- `POST /api/auth/google`
- `POST /api/auth/google/complete`
- `POST /api/auth/logout`
- `GET  /api/auth/me` (requiere header `Authorization: Bearer <access_token>`)

