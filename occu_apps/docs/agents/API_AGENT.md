## Guía de agente — API (contratos, roles, endpoints MVP)

### Objetivo del agente
Mantener y/o ajustar la API para que ambas apps (Cliente y Panel) puedan implementar el MVP **sin improvisar payloads**.

**Fuente de verdad**
- Rutas: `api/src/Features/*/Routes.php`
- Acciones: `api/src/Features/*/Actions/*`
- Contratos: `docs/api-contracts/*`
- Guía de implementación API (patrones y ubicación de cambios): `api/AGENTS.md`

---

## Reglas duras

- No cambiar nombres de tablas.
- No cambiar llaves de request/response sin actualizar `docs/api-contracts/*`.
- Todas las respuestas deben mantener el wrapper:
  - OK: `{ success:true, data: ... }`
  - Error: `{ success:false, errorCode, message, details? }`
- Auth:
  - Authorization header: `Bearer <access_token>`
  - Roles: `Cliente`, `Propietario`, `Administrador`

---

## Endpoints (MVP) por feature

### System
- `GET /api/health`

### Auth (público)
Definidos en `api/src/Features/Auth/Routes.php`:
- `POST /api/auth/register`
- `POST /api/auth/verify-pin`
- `POST /api/auth/resend-pin`
- `POST /api/auth/login`
- `POST /api/auth/refresh`
- `POST /api/auth/google`
- `POST /api/auth/google/complete`
- `POST /api/auth/logout`

### Auth (privado)
- `GET /api/auth/me`

### Cafeterías (público)
- `GET /api/cafeterias`
- `GET /api/cafeterias/{id}`
- `GET /api/servicios`
- `GET /api/ciudades`
- `GET /api/ciudades/{id}`
- `GET /api/cafeterias/{id}/comentarios`
- `POST /api/cafeterias/{id}/comentarios` (auth)
- `GET /api/cafeterias/{id}/menu`
- `GET /api/cafeterias/{id}/menu/productos/{productoId}`

### Carrito (solo Cliente)
Grupo `api/src/Features/Carrito/Routes.php`:
- `POST /api/carrito/items`
- `GET /api/carrito?cafeteria_id=...`

### Owner (Propietario/Administrador)
Grupo `api/src/Features/Owner/Routes.php`:
- Cafeterías
  - `GET /api/owner/cafeterias`
  - `GET /api/owner/cafeterias/{id}`
  - `POST /api/owner/cafeterias`
  - `PUT /api/owner/cafeterias/{id}`
  - `PATCH /api/owner/cafeterias/{id}/estado`
  - `DELETE /api/owner/cafeterias/{id}`
  - `GET /api/owner/cafeterias/{id}/servicios`
  - `PUT /api/owner/cafeterias/{id}/servicios`
  - `GET /api/owner/cafeterias/estado-menu`
- Menú general
  - `GET /api/owner/menu-general/productos`
  - `PUT /api/owner/menu-general/productos` (bulk)
  - `GET /api/owner/menu-general/productos/{id}`
  - `GET /api/owner/menu-general/productos/{id}/reglas`
  - `PUT /api/owner/menu-general/productos/{id}/reglas`
- Publicación
  - `POST /api/owner/menu-publicar`

### Admin (solo Administrador)
Grupo `api/src/Features/AdminMenu/Routes.php`:
- `GET /api/admin/menu/productos`
- `GET /api/admin/menu/productos/{id}/reglas`
- `PUT /api/admin/menu/productos/{id}/reglas`
- Catálogos: `categorias`, `subcategorias`, `productos`, `tamanos`, `ingredientes-categorias`, `ingredientes`

---

## Formato de request (importante)

La API consume `getParsedBody()`. Por consistencia en SPAs:
- Preferido: `Content-Type: application/json`
- Alternativa compatible: `application/x-www-form-urlencoded`

Si se cambia el parseo, hay que actualizar contratos y frontends.

