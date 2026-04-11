## Runbook local (vNext) — cómo correr API + BD + Apps

Este runbook asume que estás en el **root del repo vNext**.

---

## 1) Requisitos

- PHP 8.0+ (recomendado 8.1+)
- Extensiones PHP: `pdo_mysql`, `curl`
- Composer
- Node.js + npm (para las apps)
- MySQL/MariaDB

---

## 2) Variables de entorno (root `.env`)

Crear `.env` (no se commitea). Basarte en `.env.example`.

Mínimo:
- `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS`, `DB_PORT`
- `API_JWT_SECRET`

Opcional (dev):
- `API_DEBUG=1`
- `API_CORS_ORIGINS=http://localhost:5173,http://localhost:5174`

---

## 3) Base de datos (desde cero)

### Opción A (recomendada para bootstrap): baseline SQL
1) Crea una BD vacía (ej. `occu_v2`)
2) Importa:
   - `api/bd/initial_vnext.sql`
3) Corre seeds demo:

```bash
cd api
composer install
php vendor/bin/phinx seed:run -c phinx.php -e development -s VnextMinimalSeed
```

Credenciales demo:
- `admin@occu.test` / `Admin123!`
- `owner@occu.test` / `Owner123!`
- `cliente@occu.test` / `Cliente123!`

### Opción B: Phinx migrations (cambios futuros)
```bash
cd api
composer install
php vendor/bin/phinx migrate -c phinx.php -e development
```

---

## 4) API (Slim) en `http://127.0.0.1:8011/api`

Desde el root:
```bash
php -S 127.0.0.1:8011 dev-router.php
```

### Nota importante (Windows)
Si alguna vez hay confusión de rutas, usa el router con **ruta absoluta**:
```bash
php -S 127.0.0.1:8011 \"C:\\\\ruta\\\\al\\\\repo\\\\dev-router.php\"
```

Endpoints de prueba:
- `GET http://127.0.0.1:8011/api/health`
- `POST http://127.0.0.1:8011/api/auth/login`

---

## 5) Web Cliente y Web Panel

Instalar dependencias del monorepo:

```bash
npm install
```

Ver instrucciones en:
- `apps/web-cliente/README.md`
- `apps/web-panel/README.md`

Config común (Vite):
- `VITE_API_BASE_URL=http://127.0.0.1:8011/api`

