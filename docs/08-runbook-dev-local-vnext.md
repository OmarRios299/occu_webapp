## Runbook local (vNext) — Cómo correr API + apps

Este documento es para que puedas llevarlo al **nuevo proyecto en otra ruta** y que cualquier agente/desarrollador lo ejecute igual.

---

## 1) API (Slim PHP) en `http://127.0.0.1:8010/api`

### Requisitos
- PHP 8.0+ (recomendado 8.1+)
- Extensiones: `pdo_mysql`, `curl`
- Composer

### 1.1 Instalar dependencias

```bash
cd api
composer install
```

### 1.2 Variables de entorno (root `.env`)
Mínimo:
- `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS`
- `API_JWT_SECRET`
- `GOOGLE_CLIENT_ID`
- `MAIL`

Opcional:
- `API_DEBUG=1` (dev) / `0` (prod)
- `API_CORS_ORIGINS=http://localhost:5173,http://localhost:5174`

### 1.3 Correr API con PHP built-in server (sin XAMPP)

> Importante: para que funcione `/api/health` (y rutas dinámicas), usa el router `dev-router.php`.

Desde el **root** del repo:

```bash
php -S 127.0.0.1:8010 dev-router.php
```

Endpoints de prueba:
- `GET http://127.0.0.1:8010/api/health`
- `POST http://127.0.0.1:8010/api/auth/login`

---

## 2) Base de datos (Phinx)

### 2.1 Migrar
Desde el root:

```bash
vendor/bin/phinx migrate -e development
```

### 2.2 Seeds (si existen)

```bash
vendor/bin/phinx seed:run -e development
```

---

## 3) Web Cliente (React+Tailwind)

En vNext recomendado:
- `apps/web-cliente`

Comandos típicos:

```bash
npm install
npm run dev
```

Config:
- `VITE_API_BASE_URL=http://127.0.0.1:8010/api`

---

## 4) Web Panel (React+Tailwind)

En vNext recomendado:
- `apps/web-panel`

Comandos típicos:

```bash
npm install
npm run dev -- --port 5174
```

Config:
- `VITE_API_BASE_URL=http://127.0.0.1:8010/api`

