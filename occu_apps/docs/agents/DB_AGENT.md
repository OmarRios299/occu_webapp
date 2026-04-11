## Guía de agente — DB (migraciones + baseline + seed)

### Objetivo del agente
Dejar una base de datos **100% reproducible** desde cero, con datos mínimos para probar el flujo completo (login, cafeterías, menú, carrito).

**Fuente de verdad**
- `api/bd/initial_vnext.sql` (DDL baseline)
- `api/bd/seeds/VnextMinimalSeed.php` (datos demo)
- `api/phinx.php` (config Phinx)

---

## Procedimiento (bootstrap recomendado)

### 1) Crear BD
- Crear base vacía, por defecto: `occu_v2` (o el nombre definido en `.env`).
- Confirmar que el root `.env` tiene:
  - `DB_HOST`
  - `DB_NAME`
  - `DB_USER`
  - `DB_PASS`
  - `DB_PORT` (default 3306)

### 2) Importar baseline SQL
- Importar `api/bd/initial_vnext.sql` en la BD creada.

### 3) Correr seed mínimo (demo)
Desde `api/`:

```bash
composer install
php vendor/bin/phinx seed:run -c phinx.php -e development -s VnextMinimalSeed
```

Credenciales demo (deben funcionar):
- `admin@occu.test` / `Admin123!`
- `owner@occu.test` / `Owner123!`
- `cliente@occu.test` / `Cliente123!`

---

## Validación mínima (checklist)

- **Integridad**:
  - Hay 1 país/estado/ciudad.
  - Hay 3 usuarios (`Administrador`, `Propietario`, `Cliente`) con `verificado='Si'`.
  - Hay 1 cafetería del owner.
  - Hay al menos 1 producto publicado en menú de cafetería.
- **API smoke** (lo valida el agente API o QA):
  - Login owner: `POST /api/auth/login` retorna access+refresh token.
  - `GET /api/cafeterias` retorna la cafetería demo.
  - `GET /api/cafeterias/1/menu` retorna al menos 1 producto.

---

## Reglas (no romper)

- No cambiar nombres de tablas.
- No introducir valores “random” en IDs demo. El seed usa IDs fijos para reproducibilidad.
- Mantener la convención `estado`: `0=activo`, `1=inactivo`, `2=eliminado`.

