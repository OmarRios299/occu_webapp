## Cafeterías, ciudades, servicios y comentarios

Base URL (local): `http://127.0.0.1:8011/api`

---

## GET /cafeterias

**Roles**: público\n
**Query (opcional)**: depende del repo/implementación (filtros)\n
**OK**: `data` lista de cafeterías

---

## GET /cafeterias/{id}

**Roles**: público\n
**OK**: detalle cafetería

---

## GET /servicios

**Roles**: público\n
**OK**: catálogo de servicios

---

## GET /ciudades
**Roles**: público\n
**OK**: catálogo/paginado de ciudades (según implementación)

## GET /ciudades/{id}
**Roles**: público\n
**OK**: detalle ciudad

---

## GET /cafeterias/{id}/comentarios

**Roles**: público\n
**Query**:
- `page` number (default 1)
- `pageSize` number (default 5, max 50)

**OK**:
- `data.data` array de comentarios
- `data.page`, `data.pageSize`, `data.totalItems`, `data.totalPages`

---

## POST /cafeterias/{id}/comentarios

**Roles**: `Cliente` (auth requerido)\n
**Body**:
- `comentario` string (required, no vacío)

**201 OK**:
- `data.message` string

