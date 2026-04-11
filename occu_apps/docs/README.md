## OCCU vNext — Documentación (fuente de verdad)

Este repo contiene **API + BD + 2 Web Apps** (Cliente y Panel) usando **la misma API** y **la misma base de datos**.

Regla principal: **los agentes NO improvisan**. Si algo no está aquí, se agrega a estos docs antes de implementarlo.

---

## 1) Quickstart (dev local)

Sigue el runbook:
- [`RUNBOOK.md`](./RUNBOOK.md)

Smoke tests obligatorios:
- `GET /api/health`
- `POST /api/auth/login` (credenciales demo del seed)

---

## 2) Docs “fuente de verdad”

- **Arquitectura y estructura del repo**: [`ARCHITECTURE.md`](./ARCHITECTURE.md)
- **Mapa funcional por app (rutas/pantallas/reglas UX)**: [`FUNCTIONAL_MAP.md`](./FUNCTIONAL_MAP.md)
- **Runbook reproducible (Windows-friendly)**: [`RUNBOOK.md`](./RUNBOOK.md)
- **Design system (tokens, tipografía, radius, patrones UI)**: [`DESIGN_SYSTEM.md`](./DESIGN_SYSTEM.md)
- **UI Specs (Navegación exacta)**: [`UI_SPECS_NAVIGATION.md`](./UI_SPECS_NAVIGATION.md)
- **UI Specs (Overlays: detalle/menú sin romper filtros)**: [`UI_SPECS_OVERLAYS.md`](./UI_SPECS_OVERLAYS.md)

Referencia técnica (implementación API):
- Guía interna de endpoints/patrón por feature: `api/AGENTS.md`

Docs de referencia (NO editar salvo que se cambie el alcance):
- [`_legacy/`](./_legacy/README.md)

---

## 2.2 Backlog para agentes

- Tickets MVP con criterios de aceptación: [`BACKLOG.md`](./BACKLOG.md)

---

## 2.1 Mover a repo nuevo (recomendado)

Pasos (Windows):
- Copia la carpeta `occu_apps/` a una ruta limpia (ej. `C:\\OCCU\\occu_vnext\\`).
- Dentro de esa carpeta:
  - Crea `.env` desde `.env.example`.
  - Inicializa git: `git init`
  - Primer commit solo con docs + API/BD (sin `api/vendor/` y sin `.env`).
- Usa el runbook para levantar API/BD: [`RUNBOOK.md`](./RUNBOOK.md)

---

## 3) Reglas anti-improvisación (checklist)

- **Rutas**: no inventar rutas. Usar `FUNCTIONAL_MAP.md`.
- **Diseño**: no inventar colores/radius/tipografías. Usar `DESIGN_SYSTEM.md` (tokens CSS).
- **API**: no inventar payloads/keys. Usar `api-contracts/` y/o el código en `api/src/Features/*`.
- **Tablas**: no cambiar nombres de tablas existentes del dominio.
- **Estados UI obligatorios**: loading / empty / error / success.
- **Accesibilidad mínima**: focus visible, navegación teclado, labels en inputs.

---

## 4) Instrucciones para agentes (ejecución por rol)

Estas guías son prescriptivas:
- DB: [`agents/DB_AGENT.md`](./agents/DB_AGENT.md)
- API: [`agents/API_AGENT.md`](./agents/API_AGENT.md)
- Web Cliente: [`agents/WEB_CLIENTE_AGENT.md`](./agents/WEB_CLIENTE_AGENT.md)
- Web Panel: [`agents/WEB_PANEL_AGENT.md`](./agents/WEB_PANEL_AGENT.md)

Contratos por feature:
- [`api-contracts/`](./api-contracts/)

