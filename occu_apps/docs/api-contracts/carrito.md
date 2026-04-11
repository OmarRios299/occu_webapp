## Carrito (solo Cliente)

Base URL (local): `http://127.0.0.1:8011/api`

Headers:
- `Authorization: Bearer <access_token>`

---

## GET /carrito?cafeteria_id={id}

**Roles**: `Cliente`\n
**OK**:
- `data = null` si no hay carrito
- `data` objeto carrito si existe (incluye items)

---

## POST /carrito/items

**Roles**: `Cliente`\n
**Body**:
- `cafeteria_id` number (required)
- `producto_id` number (required)
- `tamano_id` number|null (optional)
- `cantidad` number (optional, default 1, min 1)
- `selecciones` object map `ingredienteId -> cantidad` (optional)
  - solo se envían ingredientes con cantidad > 0

**OK**:
- `data.item_id` number
- `data.cantidad_total` number

Errores:
- 400 `invalid_request` / `validation_error`

