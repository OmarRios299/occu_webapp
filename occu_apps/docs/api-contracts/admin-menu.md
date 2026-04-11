## Admin — Catálogos de menú + reglas OCCU (solo Administrador)

Base URL (local): `http://127.0.0.1:8011/api`

Headers:
- `Authorization: Bearer <access_token>`

---

## Plantillas/reglas por producto (OCCU)

### GET /admin/menu/productos
Query: `page`, `pageSize`, `q`

### GET /admin/menu/productos/{id}/reglas

### PUT /admin/menu/productos/{id}/reglas
**Body**:
- `categorias` array (opcional)
- `ingredientes` array (opcional)

---

## Catálogos

Todos siguen el patrón:
- `GET` list (con paginación)
- `POST` create (requiere `nombre` y llaves FK según entidad)
- `PUT` update
- `DELETE` delete (soft/hard según repo)

### Categorías
- `GET /admin/catalogos/menu/categorias`
- `GET /admin/catalogos/menu/categorias/{id}`
- `POST /admin/catalogos/menu/categorias` body: `nombre`, `imagen?`, `es_bebida?`
- `PUT /admin/catalogos/menu/categorias/{id}`
- `DELETE /admin/catalogos/menu/categorias/{id}`

### Subcategorías
- `GET /admin/catalogos/menu/subcategorias`
- `POST /admin/catalogos/menu/subcategorias` body mínimo: `nombre`, `id_categoria`
- `PUT /admin/catalogos/menu/subcategorias/{id}`
- `DELETE /admin/catalogos/menu/subcategorias/{id}`

### Productos
- `GET /admin/catalogos/menu/productos`
- `POST /admin/catalogos/menu/productos` body mínimo: `nombre`, `id_subcategoria`
- `PUT /admin/catalogos/menu/productos/{id}`
- `DELETE /admin/catalogos/menu/productos/{id}`

### Tamaños
- `GET /admin/catalogos/menu/tamanos`
- `POST /admin/catalogos/menu/tamanos` body mínimo: `nombre`
- `PUT /admin/catalogos/menu/tamanos/{id}`
- `DELETE /admin/catalogos/menu/tamanos/{id}`

### Categorías de ingredientes
- `GET /admin/catalogos/menu/ingredientes-categorias`
- `POST /admin/catalogos/menu/ingredientes-categorias` body mínimo: `nombre`
- `PUT /admin/catalogos/menu/ingredientes-categorias/{id}`
- `DELETE /admin/catalogos/menu/ingredientes-categorias/{id}`

### Ingredientes
- `GET /admin/catalogos/menu/ingredientes`
- `POST /admin/catalogos/menu/ingredientes` body mínimo: `nombre`, `id_ingrediente_categoria`
- `PUT /admin/catalogos/menu/ingredientes/{id}`
- `DELETE /admin/catalogos/menu/ingredientes/{id}`

