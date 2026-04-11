## Guía de agente — Web Cliente (React + Tailwind)

### Objetivo del agente
Implementar la **Web Cliente** (público + `Cliente`) sin inventar rutas ni diseño.

**Fuente de verdad**
- Rutas/pantallas: `docs/FUNCTIONAL_MAP.md`
- Design system: `docs/DESIGN_SYSTEM.md`
- UI exacta de navegación: `docs/UI_SPECS_NAVIGATION.md`
- Contratos API: `docs/api-contracts/*`

---

## Reglas duras (no improvisar)

- Usar exactamente las rutas listadas en `FUNCTIONAL_MAP.md`.
- Layout obligatorio:
  - Desktop: top navbar
  - Móvil: top bar + **bottom nav fija**
- No hardcodear colores/radius (usar tokens).
- Estados UI obligatorios: loading / empty / error.

---

## Pantallas mínimas (orden recomendado)

1) `/cafeterias` (lista + filtros básicos)\n
2) `/cafeterias/:id/menu` (categorías → productos)\n
3) Producto: drawer/modal de personalización v2\n
4) `/login` + guardar sesión (access + refresh)\n
5) `/carrito` + `POST /carrito/items`\n

---

## Personalización v2 (UX exacta)

Basado en payload de `GET /cafeterias/{id}/menu/productos/{productoId}`:
- Categoría `tipo_seleccion=unica` → radio
- Categoría `tipo_seleccion=multiple`:\n
  - `permite_cantidad=0` → checkbox\n
  - `permite_cantidad=1` → stepper 0..max\n

Reglas:
- No permitir “Agregar” si faltan mínimos obligatorios.
- Mostrar total estimado en tiempo real.
- `es_recomendado`: solo resaltar visualmente.

---

## Sesión/Auth

- Guardar `access_token` y `refresh_token` (storage definido por el equipo, recomendado: localStorage + refresh).
- En cada request privado: `Authorization: Bearer <access_token>`.
- Si 401: intentar `POST /auth/refresh` y reintentar una vez.
- Si el rol no es `Cliente`: redirigir fuera (Web Panel).

