# Guía para Agentes (OCCU API Slim)

Este documento define **reglas y pasos** para modificar o agregar endpoints sin romper la estructura ni cambiar el comportamiento de la API accidentalmente.

## Principios (no negociables)

### `App.php` NO crece
- **Prohibido** definir rutas en `api/src/App.php`.
- `api/src/App.php` es solo **composition root**: bootstrap, dependencias, middlewares globales y `Features/*/Routes::register()`.

### Rutas por Feature + Actions
- Las rutas viven en `api/src/Features/<Feature>/Routes.php`.
- La lógica vive en `api/src/Features/<Feature>/Actions/*.php`.
- `Routes.php` debe ser **solo wiring**:
  - definir rutas
  - aplicar middlewares
  - apuntar a `[$action, 'metodo']`

### No romper contratos
Al tocar un endpoint existente, **no cambies**:
- paths (`/cafeterias/...`)
- métodos HTTP
- forma del JSON (keys/estructura)
- códigos HTTP

Si necesitas romper compatibilidad, debe ser **API v2** (tema separado).

## Dónde agregar un endpoint nuevo

### 1) Decide el Feature
Usa uno existente o crea uno nuevo:
- `System`, `Auth`, `Owner`, `AdminMenu`, `Cafeterias`, `Carrito`, etc.

Si creas un feature nuevo:
- crea `api/src/Features/<Feature>/Routes.php`
- crea `api/src/Features/<Feature>/Actions/`
- registra el feature en `api/src/App.php` (solo el `::register()` y deps necesarias)

### 2) Crea un Action
Regla: un Action debe ser **clase pequeña** con dependencias explícitas por constructor.

Ejemplo:

```php
<?php
declare(strict_types=1);

namespace Occu\Api\Features\Cafeterias\Actions;

use Occu\Api\Http\JsonResponder;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class ExampleAction
{
    public function __construct(private readonly JsonResponder $json) {}

    public function __invoke(Request $request, Response $response, array $args = []): Response
    {
        return $this->json->ok($response, ['ok' => true]);
    }
}
```

> Nota: en este repo se usa tanto `__invoke()` como métodos nombrados; ambos son válidos. Preferencia: **métodos nombrados** cuando un mismo Action agrupa varias operaciones del mismo recurso.

### 3) Registra la ruta en `Routes.php`
`Routes.php` debe:
- instanciar Actions
- mapear rutas a métodos
- aplicar middlewares

Ejemplo:

```php
$example = new ExampleAction($deps['json']);
$app->get('/example', [$example, '__invoke']);
```

## Dependencias (reglas)

### `deps` es el contrato entre `App.php` y Features
Las Features reciben un `array $deps` creado en `api/src/App.php`.

Reglas:
- Solo agrega algo a `$deps` si de verdad lo necesita un Feature.
- No construyas repos/servicios dentro de `Routes.php` (excepto Actions del feature).
- Si agregas una dependencia nueva, actualiza:
  - `api/src/App.php` (wiring)
  - el docblock `@param array{...} $deps` en `Routes.php`

## Middlewares y auth/roles

### Autenticación
- Para endpoints privados usa `RequireAuthMiddleware`.
- Obtén el usuario desde el request:
  - `RequireAuthMiddleware::getUserId($request)`
  - `RequireAuthMiddleware::getUser($request)`

### Roles
Usa los middlewares ya existentes:
- Owner: `requireOwnerRole`
- Admin: `requireAdminRole`
- Cliente: `requireClienteRole`

Regla: aplica middlewares **a nivel de group** cuando sea posible.

## Respuestas y errores

### Responder siempre con `JsonResponder`
- Éxito: `ok()` / `created()`
- Error: `error($res, $status, $code, $message, $details = [])`

### Producción vs desarrollo
- `API_DEBUG=0` debe **ocultar** mensajes internos/trace en 500.
- Evita `return ... $e->getMessage()` en producción. Si ya existe en endpoints legacy, no lo “empeores”.

### try/catch
- No envuelvas todo en `try/catch` “por costumbre”.
- Usa `try/catch` solo cuando el error sea **esperado** (ej. `RuntimeException` de validación) y debas responder 400/422.

## Timezones
- No uses `date_default_timezone_set()` dentro de endpoints.
- Para timestamps dependientes de zona, usa `Occu\Api\Support\Clock`.
- La zona horaria se resuelve por ciudad/cafetería (ver repositorio/servicio correspondiente).

## URLs públicas (imágenes)
- Para rutas/paths de imágenes o assets que pueden venir como path relativo, usa:
  - `Occu\Api\Support\PublicUrl::toAbsolute($pathOrUrl)`

## Orden de rutas (Slim)
En Slim, rutas más específicas deben registrarse antes que variables.
Ejemplo importante (ya aplicado):
- `/cafeterias/{id}/menu` debe ir **antes** de `/cafeterias/{id}`

Si agregas nuevas rutas bajo `/cafeterias/{id}/...`, colócalas **antes** del detalle `/cafeterias/{id}`.

## Checklist antes de terminar

### Sintaxis
Ejecuta:

```bash
cd api
php -l "src/Features/<Feature>/Routes.php"
php -l "src/Features/<Feature>/Actions/<TuAction>.php"
```

### Compatibilidad
- Verifica que no cambiaste paths/métodos.
- Verifica que el JSON no cambió (keys/estructura).
- Verifica que los middlewares correctos siguen aplicados.

### Documentación
Si agregas:
- una variable de entorno → documenta en `api/README.md`
- un feature nuevo → documenta su existencia (recomendado)
