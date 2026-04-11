## Design System (vNext) — Tailwind + shadcn/ui (sin modo oscuro)

### Objetivo
Definir **reglas exactas** para que agentes implementen UI consistente en **Web Cliente** y **Web Panel**, reutilizando:
- **Tipografía** de la app legacy.
- **Colores** (variables `:root`) de la app legacy.
- **Radius** (escala) de la app legacy.

Reglas no negociables:
- **No** hardcodear colores en componentes.
- Colores centralizados en **tokens CSS** (variables).
- Layouts 100% responsivos, sin overflow horizontal.
- Sin modo oscuro en MVP.

---

## 1) Tipografía (legacy)

Fuente principal (legacy):
- Google Fonts: `Poppins` (300, 400, 600, 700, 900)
- Fallback: `sans-serif`

Fuente secundaria (legacy / opcional):
- `Inconsolata` (solo para `code`, logs o UI técnica)

Regla vNext:
- `font-sans` = `Poppins`
- `font-mono` = `Inconsolata`

---

## 2) Paleta base (legacy → tokens vNext)

### 2.1 Variables legacy detectadas en `views/assets/css/css/style.css`
Principales:
- `--principal`: `#f16c5b` (rgb `241 108 91`)
- `--color-sidebar`: `#f05540` (rgb `240 85 64`) (usado en `.bg-plantilla`)
- `--secundario`: `#fedd64` (rgb `254 221 100`)
- `--tercero`: `#3E51A0` (rgb `62 81 160`)
- `--cuarto`: `#10343f` (rgb `16 52 63`)
- `--bg-body`: `#ebeef6` (rgb `235 238 246`)
- `--plantilla-claro`: `#fcece6` (rgb `252 236 230`)
- `--color-texto`: `#50535f` (rgb `80 83 95`)
- `--color-gris`: `#869499` (rgb `134 148 153`)
- `--color-gris-claro`: `#edf1f3` (rgb `237 241 243`)
- `--color-blanco`: `#fff` (rgb `255 255 255`)
- `--color-negro`: `#000` (rgb `0 0 0`)

Estados:
- `--color-verde`: `#03cd82` (rgb `3 205 130`)
- `--color-amarillo`: `#ffc00f` (rgb `255 192 15`)
- `--color-rojo`: `#ff585b` (rgb `255 88 91`)
- `--color-azul-cielo`: `#0fa1f6` (rgb `15 161 246`)

> Nota: el repo no contiene los logos (`logo_blanco.png`, `logo_1.png`) referenciados en `views/`. En vNext deberán copiarse/exportarse explícitamente como assets.

### 2.2 Tokens vNext (CSS variables recomendadas)
Formato: `R G B` para poder usar alpha en Tailwind (`rgb(var(--token) / <alpha-value>)`).

Definir en `apps/*/src/styles/tokens.css` (mismo archivo para ambas apps):
- `--occu-bg`: `235 238 246`
- `--occu-surface`: `255 255 255`
- `--occu-text`: `80 83 95`
- `--occu-muted`: `237 241 243`
- `--occu-muted-text`: `134 148 153`
- `--occu-border`: `237 241 243`

Brand:
- `--occu-primary`: `241 108 91`
- `--occu-primary-strong`: `240 85 64` (equivalente legacy `--color-sidebar`)
- `--occu-accent`: `254 221 100`
- `--occu-accent-ink`: `16 52 63`

Estados:
- `--occu-success`: `3 205 130`
- `--occu-warning`: `255 192 15`
- `--occu-danger`: `255 88 91`
- `--occu-info`: `15 161 246`

### 2.3 Mapeo Tailwind (en `tailwind.config.ts`)
Configurar `theme.extend.colors` con tokens, ejemplo conceptual:
- `bg` → `rgb(var(--occu-bg) / <alpha-value>)`
- `surface` → `rgb(var(--occu-surface) / <alpha-value>)`
- `text` → `rgb(var(--occu-text) / <alpha-value>)`
- `primary` → `rgb(var(--occu-primary) / <alpha-value>)`
- `accent` → `rgb(var(--occu-accent) / <alpha-value>)`
- `border` → `rgb(var(--occu-border) / <alpha-value>)`

Regla:
- Los componentes shadcn/ui deben consumir estas variables vía clases Tailwind (no hex directos).

---

## 3) Radius (copiado del legacy)

Valores observados en legacy:
- Inputs y botones modernos: `8px`, `10px`, `12px`
- Cards/login: `16px`
- Contenedores grandes: `20px`

Escala vNext (CSS variables):
- `--radius-xs`: `6px`
- `--radius-sm`: `8px`
- `--radius-md`: `10px`
- `--radius-lg`: `12px` (**default**)
- `--radius-xl`: `16px`
- `--radius-2xl`: `20px`

Regla:
- shadcn/ui `--radius` base debe mapearse a `12px` (equivalente a `--radius-lg`).
- Los componentes de navegación móvil (bottom nav) y offcanvas usan `16px–20px` en bordes superiores (como legacy).

---

## 4) Breakpoints y layouts (regla única para ambas apps)

Se usarán los breakpoints default de Tailwind:
- `sm 640`, `md 768`, `lg 1024`, `xl 1280`, `2xl 1536`

### 4.1 Web Cliente (layout)
- **Desktop (`lg+`)**: navbar superior.
- **Móvil/Tablet (`<lg`)**:
  - top bar pequeña con logo (sticky, opcional hide-on-scroll)
  - **bottom navigation** fija.

Regla importante:
- Todas las páginas en móvil deben tener `padding-bottom` suficiente para que el bottom nav no tape contenido.

Bottom nav (legacy equivalente):
- Tabs mínimo (MVP):
  - Cafeterías (lista)
  - Ubicaciones (mapa)
  - Cuenta (login / menú cuenta si hay sesión)
  - (Carrito se muestra como icono en top bar móvil cuando hay sesión)

### 4.2 Web Panel (layout)
- **Desktop (`lg+`)**: sidebar fijo (ancho ~240px) + contenido.
- **Pantallas medianas (`md..lg`)**: sidebar **colapsable** a modo “mini” (solo iconos).
- **Móvil (`<md`)**: sidebar en modo **drawer** (overlay) con botón hamburguesa.

Regla:
- El contenido principal nunca debe hacer overflow horizontal; tablas usan contenedor `overflow-x-auto`.

---

## 5) Patrones UI obligatorios (para no improvisar)

### 5.1 Tablas (Panel/Admin)
Patrón único:
- Header con:
  - Título
  - Buscador (debounced)
  - Botón primario “Agregar” (si aplica)
- Cuerpo:
  - Tabla con acciones por fila (Ver/Editar/Activar-Desactivar/Eliminar soft)
  - Paginación abajo
- Estados:
  - Loading skeleton
  - Empty state (mensaje + CTA)
  - Error state (mensaje + “Reintentar”)

### 5.2 Formularios
- Layout: 1 columna (móvil), 2 columnas (desktop) cuando haya > 4 campos.
- Inputs con label + helper/error.
- Botones:
  - Primario: `primary`
  - Secundario: “ghost/outline” con `border`
  - Peligro: `danger`

### 5.3 Offcanvas/Dialogs/Drawers
Se replica el patrón legacy:
- Header con gradiente `primary → primary-strong`.
- Body scrollable.
- Footer sticky (acciones).

---

## 6) Checklist de implementación (para agentes)

En cada app (Cliente y Panel):
- Agregar `tokens.css` con variables `--occu-*` y `--radius-*`.
- Configurar Tailwind para consumir tokens (sin hex en componentes).
- Instalar y configurar `shadcn/ui` + Radix.
- Definir componentes base en `packages/ui`:
  - `AppShellCliente` (top+bottom nav)
  - `AppShellPanel` (sidebar responsivo)
  - `Button`, `Input`, `Select`, `Dialog`, `Drawer`, `Table`, `Toast`
- Verificar:
  - 0 overflows horizontales en `320px`, `768px`, `1024px`
  - bottom nav no tapa contenido
  - sidebar colapsa y drawer funciona

