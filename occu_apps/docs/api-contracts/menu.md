## Menú público por cafetería

Base URL (local): `http://127.0.0.1:8011/api`

---

## GET /cafeterias/{id}/menu

**Roles**: público\n
**OK**:
- `data` array (estructura por categorías/subcategorías/productos según repositorio)
- Si la cafetería existe pero no tiene menú publicado: `data = []`

Errores:
- 404 `not_found`: cafetería no encontrada

---

## GET /cafeterias/{id}/menu/productos/{productoId}

**Roles**: público\n
**OK**: producto con reglas v2 y tamaños si aplica (estructura depende del repositorio)

Notas:
- Este endpoint debe exponer suficiente info para renderizar personalización v2:
  - categorías de ingredientes con `tipo_seleccion`, min/max, orden
  - ingredientes con `permite_cantidad`, min/max/paso, `cantidad_incluida`, pricing, `agotado`, `es_recomendado`

