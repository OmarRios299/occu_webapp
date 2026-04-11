## Guía de agente — Web Panel (React + Tailwind)

### Objetivo del agente
Implementar la **Web Panel** (roles `Propietario` y `Administrador`) con layout prescriptivo (sidebar/topbar) y módulos MVP.

**Fuente de verdad**
- Rutas/pantallas: `docs/FUNCTIONAL_MAP.md`
- Patrones UI: `docs/DESIGN_SYSTEM.md`
- UI exacta de navegación: `docs/UI_SPECS_NAVIGATION.md`
- Contratos API: `docs/api-contracts/*`

---

## Layout obligatorio

- Desktop: sidebar fijo (~240px) + topbar
- `md..lg`: sidebar colapsable (solo iconos)
- Móvil: sidebar en drawer overlay
- Tablas dentro de `overflow-x-auto`

---

## Módulos MVP (orden recomendado)

### Propietario
1) Mis cafeterías: `GET /owner/cafeterias`
2) Crear/editar cafetería: `POST/PUT /owner/cafeterias`
3) Servicios por cafetería: `GET/PUT /owner/cafeterias/{id}/servicios`
4) Menú general (habilitar productos): `GET/PUT /owner/menu-general/productos`
5) Reglas por producto (owner): `GET/PUT /owner/menu-general/productos/{id}/reglas`
6) Publicar: `POST /owner/menu-publicar`

### Administrador
1) Catálogos de menú + servicios
2) Plantillas/reglas OCCU por producto: `GET/PUT /admin/menu/productos/{id}/reglas`

---

## Sesión/Auth

- Login panel usa `/auth/login`.
- Roles permitidos: `Propietario`, `Administrador`.
- Si el rol es `Cliente`: redirigir fuera (Web Cliente).

