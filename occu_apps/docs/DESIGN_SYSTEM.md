## Design System (vNext) — Tailwind + shadcn/ui (sin modo oscuro)

Este documento es la **fuente de verdad** para el diseño. No se permiten “ajustes creativos” por agente.

> Origen: valores copiados de la app legacy (tipografía, colores, radius).

---

## 1) Tipografía

- Primaria: `Poppins` (300, 400, 600, 700, 900)
- Secundaria opcional: `Inconsolata` (solo para UI técnica)

Regla:
- `font-sans` = `Poppins`
- `font-mono` = `Inconsolata`

---

## 2) Tokens de color (usar variables, no hex en componentes)

Tokens (formato `R G B` para permitir alpha):

- Base:
  - `--occu-bg`: `235 238 246`
  - `--occu-surface`: `255 255 255`
  - `--occu-text`: `80 83 95`
  - `--occu-muted`: `237 241 243`
  - `--occu-muted-text`: `134 148 153`
  - `--occu-border`: `237 241 243`

- Brand:
  - `--occu-primary`: `241 108 91`
  - `--occu-primary-strong`: `240 85 64`
  - `--occu-accent`: `254 221 100`
  - `--occu-accent-ink`: `16 52 63`

- Estados:
  - `--occu-success`: `3 205 130`
  - `--occu-warning`: `255 192 15`
  - `--occu-danger`: `255 88 91`
  - `--occu-info`: `15 161 246`

Dónde declararlos:
- `apps/*/src/styles/tokens.css` (el mismo contenido en Cliente y Panel)

Regla:
- Los componentes deben consumir tokens vía Tailwind:
  - `bg-[rgb(var(--occu-surface))]` o mapeo en `tailwind.config.*`
- Prohibido hardcodear `#f16c5b` etc en componentes.

---

## 3) Radius (legacy)

Escala (CSS variables):
- `--radius-xs`: `6px`
- `--radius-sm`: `8px`
- `--radius-md`: `10px`
- `--radius-lg`: `12px` (default)
- `--radius-xl`: `16px`
- `--radius-2xl`: `20px`

Reglas:
- shadcn/ui debe mapear `--radius` base a `12px`.
- Bottom nav / offcanvas usan `16px–20px` en bordes superiores.

---

## 4) Layouts obligatorios

### Web Cliente
- Desktop: navbar superior
- Móvil: top bar + **bottom navigation fija**
- Siempre agregar `padding-bottom` para no tapar contenido con bottom nav.

### Web Panel
- Desktop: sidebar fijo (~240px) + topbar + contenido
- `md..lg`: sidebar colapsable (solo iconos)
- Móvil: drawer overlay
- Tablas dentro de contenedor `overflow-x-auto`

---

## 5) Patrones UI obligatorios

- Tablas: header (título + buscador + CTA), loading skeleton, empty state, error state, paginación.
- Formularios: labels + helper/error, 1 columna móvil / 2 columnas desktop si aplica.
- Drawers/Dialogs: header con gradiente primary→primary-strong, footer sticky.

