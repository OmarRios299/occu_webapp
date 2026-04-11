## Backlog MVP (vNext) — tickets para agentes

Regla: cada ticket debe:
- referenciar el contrato en `docs/api-contracts/*`
- respetar rutas de `docs/FUNCTIONAL_MAP.md`
- respetar diseño de `docs/DESIGN_SYSTEM.md`

---

## 0) Infra / Base (FR)

### FR-001 — Setup Tailwind + tokens (Cliente)
- **Scope**: `apps/web-cliente`
- **Hecho cuando**:
  - Existe `src/styles/tokens.css` con variables `--occu-*` y radius
  - Tailwind configurado para consumir tokens (no hex hardcoded)
  - Sin overflow horizontal en 320px

### FR-002 — Setup Tailwind + tokens (Panel)
- Igual que FR-001 pero en `apps/web-panel`

### FR-003 — Paquete `@occu/api-client` usable desde apps
- **Scope**: `packages/api-client`
- **Hecho cuando**:
  - Hay funciones para login/refresh y un fetch wrapper con `Authorization`
  - Documentado cómo se usa desde apps

### FR-004 — AppShellCliente (TopBar + BottomNav)
- **Scope**: `apps/web-cliente`
- **UI (fuente de verdad)**: `docs/UI_SPECS_NAVIGATION.md`
- **Hecho cuando**:
  - Desktop: top navbar con Logo + links + acciones según specs
  - Móvil: top bar con Logo+Carrito y bottom nav con 3 tabs (Cafeterías/Mapa/Cuenta)
  - Existe la ruta `/cuenta` con opciones de sesión (mis pedidos/cerrar sesión)
  - Sin overflow horizontal en 320px y bottom nav no tapa contenido

### FR-005 — AppShellPanel (Sidebar + Topbar + Drawer móvil)
- **Scope**: `apps/web-panel`
- **UI (fuente de verdad)**: `docs/UI_SPECS_NAVIGATION.md`
- **Hecho cuando**:
  - Desktop: sidebar con header Logo OCCU y items por rol (Owner/Admin) según specs
  - Móvil: sidebar en drawer (hamburguesa)
  - Cerrar sesión accesible en 1 click desde sidebar

---

## 1) Auth (AU)

### AU-001 — Login Cliente (UI + integración)
- **App**: Web Cliente\n
- **Rutas**: `/login`
- **API**: `POST /auth/login`, `GET /auth/me`
- **Hecho cuando**:
  - Login funciona con credenciales del seed
  - Si `nivel` != `Cliente`, redirige fuera (Panel)
  - Estados: loading / error message (de API) / success

### AU-002 — Login Panel (UI + integración)
- **App**: Web Panel\n
- **Rutas**: `/panel/login`
- **API**: `POST /auth/login`, `GET /auth/me`
- **Hecho cuando**:
  - Login funciona con credenciales del seed
  - Si `nivel` == `Cliente`, redirige fuera (Cliente)

### AU-003 — Refresh token automático
- **Apps**: Cliente + Panel\n
- **API**: `POST /auth/refresh`
- **Hecho cuando**:
  - Al recibir 401 en endpoint privado, se intenta refresh 1 vez y se reintenta request
  - Si falla refresh, se limpia sesión y se redirige a login

---

## 2) Cafeterías (CF)

### CF-001 — Lista de cafeterías (Cliente)
- **Ruta**: `/cafeterias`
- **API**: `GET /cafeterias`
- **Hecho cuando**:
  - Renderiza lista desde API
  - Estado vacío (sin cafeterías) muestra CTA
  - Click en cafetería navega a detalle o menú

### CF-002 — Detalle cafetería (Cliente)
- **Ruta**: `/cafeterias/:id`
- **API**: `GET /cafeterias/{id}`, `GET /cafeterias/{id}/comentarios`
- **Hecho cuando**:
  - Implementa patrón **Modal Routes** (background location) según `docs/UI_SPECS_OVERLAYS.md`
  - UI del drawer respeta `docs/UI_SPECS_CAFETERIA_DETAIL.md` (paridad con legacy)
  - Desde `/cafeterias?...` abre **Drawer** sin perder filtros/búsquedas
  - Al cerrar, vuelve con `navigate(-1)` y conserva estado de lista
  - Deep-link directo `/cafeterias/:id` se renderiza como página completa (sin drawer)
  - Muestra datos principales + comentarios paginados

### CF-003 — Crear comentario (Cliente)
- **Ruta**: `/cafeterias/:id` (form)\n
- **API**: `POST /cafeterias/{id}/comentarios`
- **Hecho cuando**:
  - Solo con sesión de Cliente
  - Valida comentario no vacío
  - Al crear, refresca lista

---

## 3) Menú público (MN)

### MN-001 — Menú por cafetería (Cliente)
- **Ruta**: `/cafeterias/:id/menu`
- **API**: `GET /cafeterias/{id}/menu`
- **Hecho cuando**:
  - Renderiza categorías/subcategorías/productos
  - Estado vacío: “sin menú publicado”

### MN-002 — Producto + personalización v2 (Cliente)
- **Ruta**: `/cafeterias/:id/menu` (modal/drawer)
- **API**: `GET /cafeterias/{id}/menu/productos/{productoId}`
- **Hecho cuando**:
  - Renderiza controles según reglas (radio/checkbox/stepper)
  - No permite agregar si faltan mínimos obligatorios
  - Total estimado en tiempo real

---

## 4) Carrito (CR)

### CR-001 — Ver carrito (Cliente)
- **Ruta**: `/carrito`
- **API**: `GET /carrito?cafeteria_id=...`
- **Hecho cuando**:
  - Si no hay carrito: empty state
  - Si hay: lista items + totales (según response)

### CR-002 — Agregar item al carrito (Cliente)
- **Desde**: MN-002\n
- **API**: `POST /carrito/items`
- **Hecho cuando**:
  - Envía body según `docs/api-contracts/carrito.md`
  - Maneja errores de validación del backend

---

## 5) Panel Owner (OW)

### OW-001 — Mis cafeterías (Propietario)
- **Ruta**: `/panel/cafeterias`
- **API**: `GET /owner/cafeterias`
- **Hecho cuando**:
  - Lista cafeterías del owner
  - Botones: editar, activar/desactivar, servicios

### OW-002 — Crear/editar cafetería (Propietario)
- **Rutas**: `/panel/cafeterias/crear`, `/panel/cafeterias/:id/editar`
- **API**: `POST/PUT /owner/cafeterias`
- **Hecho cuando**:
  - Validaciones básicas (nombre/telefono/direccion/id_ciudad)
  - Maneja error `validation_error` con mensaje de API

### OW-003 — Servicios por cafetería (Propietario)
- **Ruta**: `/panel/cafeterias/:id/servicios`
- **API**: `GET/PUT /owner/cafeterias/{id}/servicios`
- **Hecho cuando**:
  - UI selecciona servicios y guarda

### OW-004 — Menú general: habilitar productos (Propietario)
- **Ruta**: `/panel/menu-general` (a definir, pero debe mapear a `FUNCTIONAL_MAP`)
- **API**: `GET/PUT /owner/menu-general/productos`
- **Hecho cuando**:
  - Bulk update funciona con body `productos`

### OW-005 — Reglas por producto (Propietario)
- **API**: `GET/PUT /owner/menu-general/productos/{id}/reglas`
- **Hecho cuando**:
  - Editor guarda `categorias` y `ingredientes`
  - Maneja errores de validación (`validation_error`)

### OW-006 — Publicar menú (Propietario)
- **API**: `POST /owner/menu-publicar`
- **Hecho cuando**:
  - Permite seleccionar 1..N cafeterías y modo (`todo|solo_precios`)

---

## 6) Panel Admin (AD)

### AD-001 — Catálogos (Administrador)
- **Rutas**: según `FUNCTIONAL_MAP.md`
- **API**: `/admin/catalogos/menu/*`
- **Hecho cuando**:
  - CRUD básico por entidad (mínimo: crear/editar/listar/eliminar)

### AD-002 — Plantillas/reglas OCCU por producto (Administrador)
- **API**: `GET/PUT /admin/menu/productos/{id}/reglas`
- **Hecho cuando**:
  - Editor guarda reglas y respeta `MenuReglasService`

