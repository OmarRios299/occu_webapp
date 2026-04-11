## UI Specs — Overlays (Detalle y Menú sin romper la lista)

Fuente de verdad para el comportamiento tipo **offcanvas/drawer** que no debe afectar filtros/búsquedas de la lista.

---

## 1) Patrón profesional: Modal Routes (background location)

### Objetivo
- Mantener `/cafeterias` como pantalla base (con filtros/búsqueda intactos).
- Abrir detalle/menú como overlay **sin desmontar** la lista.
- Soportar:
  - Historial del navegador (Back/Forward)
  - Deep-link directo (abrir URL del detalle sin pasar por la lista)

### Regla
Cuando el usuario viene desde `/cafeterias?...`:
- Abrir overlay navegando a la ruta real (`/cafeterias/:id` o `/cafeterias/:id/menu`)
- Guardar `backgroundLocation` en `location.state`
- Renderizar overlay encima del background.
- Cerrar overlay: `navigate(-1)` (volver exactamente a la lista con sus filtros).

Cuando el usuario entra directo por URL (deep-link):
- Renderizar la pantalla como página completa (sin overlay), porque no hay background.

---

## 2) CF-002 — Detalle cafetería como Drawer (sin perder filtros)

### UI (fuente de verdad)
- `docs/UI_SPECS_CAFETERIA_DETAIL.md` (paridad con legacy `ver_cafeteria_contenido.php`)

### Desde Lista
- En `/cafeterias?...` al dar click en una card:
  - Navegar a `/cafeterias/:id` con `state.backgroundLocation = locationActual`
  - Renderizar un **Drawer desde la derecha** con el detalle.
  - La lista queda visible detrás (scroll del background bloqueado).

### Cerrar
- Botón “X” o “Cerrar” en el drawer:
  - `navigate(-1)` (NO navegar manualmente a `/cafeterias`)
  - No resetear filtros, paginación, ni texto de búsqueda.

### Deep-link
- Si el usuario abre `/cafeterias/1` directamente:
  - Renderizar como página completa (sin drawer), con botón “Volver” a `/cafeterias`.

---

## 3) MN-001/MN-002 — Menú como Drawer independiente (opcional)

Si se decide replicar legacy (detalle y menú como overlays separados):
- Desde Lista o Detalle, “Ver menú” abre `/cafeterias/:id/menu` como overlay con background.
- Esto permite cerrar menú y volver exactamente al estado anterior.

Nota: si el menú se maneja como página normal (sin overlay), también es válido, pero debe quedar decidido por ticket.

