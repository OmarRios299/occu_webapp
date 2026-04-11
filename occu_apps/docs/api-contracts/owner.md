## Owner (Propietario/Administrador)

Base URL (local): `http://127.0.0.1:8011/api`

Headers:
- `Authorization: Bearer <access_token>`

---

## Cafeterías

### GET /owner/cafeterias
**Roles**: `Propietario` o `Administrador`\n
**Query (admin)**:
- `userId` number (opcional) para listar cafeterías de otro owner

### GET /owner/cafeterias/{id}
**OK**: detalle cafetería (incluye lat/long, descripcion, etc.)

### POST /owner/cafeterias
**Body (requeridos)**:
- `nombre` string
- `telefono` string
- `direccion` string
- `id_ciudad` number
\n**Body (opcionales)**:
- `correo_electronico` string|null
- `latitud` string
- `longitud` string
- `horario_apertura` string|null (`HH:MM:SS`)
- `horario_cierre` string|null (`HH:MM:SS`)
- `horario_diferente` string (`SI|NO`)
- `descripcion` string
- `horarios_detallados` array (opcional)

### PUT /owner/cafeterias/{id}
Mismo body que create.

### PATCH /owner/cafeterias/{id}/estado
**Body**:
- `estado` number (0 activa, 1 inactiva)

### DELETE /owner/cafeterias/{id}

### GET /owner/cafeterias/{id}/servicios
Retorna catálogo + flag `servicio_registrado`.

### PUT /owner/cafeterias/{id}/servicios
**Body**:
- `servicios` number[] (ids)

---

## Menú general del propietario

### GET /owner/menu-general/productos
**Query** (opcionales):
- `page`, `pageSize`, `q`, `id_subcategoria`, `estado`\n
**Admin** puede pasar `propietarioId`.

### PUT /owner/menu-general/productos
Bulk update.\n
**Body**:
- `productos`: array de objetos con al menos `id_producto`

### GET /owner/menu-general/productos/{id}

### GET /owner/menu-general/productos/{id}/reglas

### PUT /owner/menu-general/productos/{id}/reglas
**Body**:
- `categorias` array (opcional)
- `ingredientes` array (opcional)
\n
Debe pasar validación de `MenuReglasService`.

---

## Publicación

### POST /owner/menu-publicar
**Body**:
- `cafeteria_ids`: number[] (no vacío)
- `modo`: `todo|solo_precios`

