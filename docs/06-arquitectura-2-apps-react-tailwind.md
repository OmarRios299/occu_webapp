## Arquitectura recomendada (OCCU vNext) — 2 Web Apps + 1 API + 1 BD

### Objetivo
Construir OCCU desde cero en **Frontend + Base de Datos**, reutilizando y/o refactorizando lo que ya existe en `api/`, con estos requisitos:
- **Dos web apps separadas**:
  - **Web Cliente** (rol `Cliente` + público).
  - **Web Panel** (roles `Propietario` y `Administrador`).
- **Mismo Backend y misma BD** para ambas.
- **BD nueva**, pero manteniendo **nombres de tablas** del dominio existente (ej. `admin_usuarios`, `cafeterias_imagenes`, etc.).
- Sistema **escalable** y **fácil de entender**.
- El proyecto vNext vivirá en **otra ruta/repositorio** (separado de la web vieja y del frontend MUI actual). Estos documentos son el “paquete de contexto” para llevártelos.

---

## Alternativas (y recomendación)

### Alternativa A (recomendada): Monorepo + 2 SPAs (Vite) + paquetes compartidos
**Qué es**
- Un solo repositorio con:
  - `api/` (Slim PHP existente)
  - `apps/web-cliente/` (React + Tailwind)
  - `apps/web-panel/` (React + Tailwind)
  - `packages/` (código compartido: cliente HTTP, tipos, UI base)
  - `bd/` (migraciones/seed)

**Pros**
- Separación real de producto (cliente vs panel) sin mezclar rutas/UX.
- Reutilización controlada (misma capa de API client y componentes base).
- Deploy independiente por app (dominios distintos) sin duplicar backend.
- Evita “ensuciar” el frontend con guards/menus de 3 roles en un solo shell.
- Preparado para futuro: agregar `apps/web-xxx/` es “copia patrón” (mismo stack, mismos paquetes).

**Contras**
- Requiere un “workspace” (pnpm/npm workspaces) y disciplina de estructura.

### Alternativa B: Una sola SPA con RBAC (NO recomendada aquí)
**Pros**: más simple para empezar.  
**Contras**: contradice el requerimiento de 2 apps, crece el routing/menú, y sube el riesgo de mezclar permisos y UX.

### Alternativa C: Reescribir API en otro stack (NO recomendada ahora)
**Por qué no**: ya existe `api/` con Slim, JWT, features por rutas, repos, y migraciones que ya modelan el menú v2. Reescribir ahorita ralentiza y aumenta riesgo.

**Recomendación**: **Alternativa A**.

---

## Decisiones técnicas (vNext)

### Backend
- Mantener `api/` (Slim PHP 8+).
- Rutas por feature (según `api/AGENTS.md`): `api/src/Features/<Feature>/Routes.php` + `Actions/`.
- Auth: JWT access + refresh tokens (ya existe base).
- Respuestas JSON consistentes:
  - Éxito: `{ "success": true, "data": ... }`
  - Error: `{ "success": false, "message": "...", "errorCode": "..." , "details"?: ... }`

### Base de datos
- BD nueva (vacía) creada desde migraciones `bd/migrations/`.
- Mantener nombres de tablas del dominio.
- Reglas obligatorias:
  - Tipos correctos (precios `DECIMAL(10,2)`; no `TEXT`).
  - Índices en consultas frecuentes.
  - Campos de auditoría mínimos (cuando aplique): `fecha_alta`, `id_alta`, opcional `fecha_modificacion`, `id_modificacion`.
  - Soft delete por `estado` (convención): `0=activo, 1=inactivo, 2=eliminado`.
- Menú/personalización:
  - Adoptar el modelo de `bd/migrations/20260207000000_menu_personalizacion_v2.php` (plantillas OCCU + menú por cafetería + snapshots).

### Frontend
- React + TypeScript estricto + Tailwind.
- Router: `react-router-dom`.
- Datos: `@tanstack/react-query` (recomendado) + fetch client.
- Estado de sesión: contexto + storage (localStorage) + refresh.
- Validación de forms: `zod` + `react-hook-form`.
- Accesibilidad: componentes headless (Radix UI) o headless propio.

### Usuarios / roles (pensando en futuro)
- En vNext, el rol `Barista` **se elimina por el momento** (no existe en el producto).
- El sistema debe permitir agregar roles a futuro sin reescribir todo:
  - **API**: autorización por middleware de rol(es) por Feature (ya existe `RequireRoleMiddleware`).
  - **Frontends**: cada app define qué roles son válidos; si entra un rol no permitido, redirige al dominio correcto o muestra “No autorizado”.

---

## Estructura de repo propuesta (objetivo)

```
occu_webApp/
  api/                       # Slim API (existente)
  bd/
    migrations/
    seeds/
  apps/
    web-cliente/             # React+Tailwind (nuevo)
    web-panel/               # React+Tailwind (nuevo)
  packages/
    api-client/              # fetch client, auth refresh, helpers
    types/                   # tipos compartidos (contratos API)
    ui/                      # UI base (Button, Input, Modal, etc.)
  docs/
    api-contracts/           # contratos por endpoint (opcional)
```

> Nota: el `frontend/` actual (MUI) se considera **legacy de la app nueva**. Se puede:
> - mantener temporalmente como referencia, o
> - reemplazar gradualmente migrando features a `apps/*`.

---

## Separación por apps (reglas de permisos)

### Web Cliente (público + Cliente)
- Público: cafeterías lista/mapa/detalle/menú, login, registro, verificación PIN.
- Privado Cliente: carrito, método de pago, mis pedidos, reseñas.
- Si inicia sesión un `Propietario` o `Administrador`: redirigir a **Web Panel**.

### Web Panel (Propietario + Administrador)
- Login (para `Propietario` y `Administrador`).
- Propietario: mis cafeterías, edición, servicios, menú, publicaciones, pedidos, QR.
- Administrador: catálogos (servicios, menú global), plantillas/reglas, usuarios, pedidos (solo lectura).
- Si inicia sesión un `Cliente`: redirigir a **Web Cliente**.

---

## Dominios / Deploy (sugerido)
- `cliente.occu.test` → `apps/web-cliente`
- `panel.occu.test` → `apps/web-panel`
- `api.occu.test/api` → `api/`

En local:
- Cliente: `http://localhost:5173`
- Panel: `http://localhost:5174`
- API (servidor embebido PHP): `http://127.0.0.1:8010/api`

> Nota: actualmente la API se levanta desde consola con PHP built-in server (no XAMPP).

---

## “No tirar la API” — qué se reutiliza
Se reutiliza y se refuerza:
- Estructura por features (Auth, Owner, Cafeterias, AdminMenu, Carrito).
- JWT + refresh.
- Repos existentes (`MenuCatalogosRepository`, `MenuProductosRepository`, etc.).
- Migraciones del menú v2 (plantillas + publicación + snapshots).

Se evita reutilizar:
- App legacy PHP (`controllers/`, `views/`, `models/` viejos) para UI/UX.

