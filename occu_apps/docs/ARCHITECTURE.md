## Arquitectura (OCCU vNext) — 2 Web Apps + 1 API + 1 BD

### Objetivo
Construir OCCU desde cero en **Frontend + Base de Datos**, reutilizando/refactorizando lo necesario del backend en `api/`, con:
- **Web Cliente**: público + rol `Cliente`.
- **Web Panel**: roles `Propietario` y `Administrador`.
- **Misma API** y **misma BD** para ambas.
- **BD nueva**, conservando **nombres de tablas** del dominio.
- Diseño consistente (ver [`DESIGN_SYSTEM.md`](./DESIGN_SYSTEM.md)).

---

## Estructura recomendada (monorepo)

```
api/                  # Slim PHP (backend)
apps/
  web-cliente/        # React + Tailwind (Vite)
  web-panel/          # React + Tailwind (Vite)
packages/
  api-client/         # cliente HTTP + auth/refresh (compartido)
  types/              # tipos/contratos compartidos (opcional)
  ui/                 # componentes base compartidos (opcional)
docs/                 # documentación fuente de verdad
```

---

## Roles y separación por app

### Web Cliente
- Público: cafeterías (lista/mapa/detalle/menú), login, registro, verificación PIN.
- Privado `Cliente`: carrito, pago, mis pedidos, reseñas.
- Si inicia sesión un `Propietario` o `Administrador`: **redirigir** a Web Panel.

### Web Panel
- Login panel.
- `Propietario`: mis cafeterías, servicios, menú, publicaciones, pedidos, QR.
- `Administrador`: catálogos, usuarios, supervisión.
- Si inicia sesión un `Cliente`: **redirigir** a Web Cliente.

> Nota: rol `Barista` **NO existe en vNext** por ahora.

---

## Backend (API)

- Slim PHP 8+ (ver `api/`).
- Features por módulo: `api/src/Features/<Feature>/Routes.php` + `Actions/`.
- Auth: access token JWT + refresh token.
- Respuesta estándar:
  - OK: `{ "success": true, "data": ... }`
  - Error: `{ "success": false, "errorCode": "...", "message": "...", "details"?: ... }`

---

## BD (desde cero, reproducible)

Fuente de verdad:
- Baseline SQL: `api/bd/initial_vnext.sql`
- Seed demo: `api/bd/seeds/VnextMinimalSeed.php`

Convención `estado` (soft delete):
- `0=activo`, `1=inactivo`, `2=eliminado`

---

## Local dev (recomendación)

- API: `http://127.0.0.1:8011/api` (evita chocar con legacy)
- Cliente: `http://localhost:5173`
- Panel: `http://localhost:5174`

Detalles en [`RUNBOOK.md`](./RUNBOOK.md).

