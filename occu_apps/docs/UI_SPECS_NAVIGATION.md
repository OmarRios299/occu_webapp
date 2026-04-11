## UI Specs — Navegación (fuente de verdad)

Este documento define **exactamente** qué opciones existen en la navegación y **cómo** se presentan en **móvil vs desktop**.

Regla: los agentes **NO** pueden cambiar “cards vs tablas” ni mover acciones a otro lugar “por conveniencia”.

---

## 1) Web Cliente (público + Cliente)

### 1.1 Desktop (>= lg)
**Tipo**: top navbar (sticky).

**Estructura**
- **Izquierda**: Logo OCCU (click → `/cafeterias`)
- **Centro (links)**:
  1) **Lista de cafeterías** → `/cafeterias`
  2) **Ubicaciones (mapa)** → `/cafeterias/mapa`
  3) **Mis pedidos** → `/mis-pedidos` (solo si hay sesión Cliente; si no, redirige a `/login`)
- **Derecha (acciones)**:
  - **Carrito** (icon + badge count) → `/carrito` (si no hay sesión, redirige a `/login`)
  - **Sesión**:
    - Si NO hay sesión: botón “Iniciar sesión” → `/login`
    - Si hay sesión: botón “Cerrar sesión” (acción) y redirige a `/cafeterias`

### 1.2 Móvil/Tablet (< lg)
**Tipo**: top bar + bottom navigation fija.

**Top bar (siempre visible)**
- **Izquierda**: Logo OCCU (→ `/cafeterias`)
- **Derecha**: icon **Carrito** (badge) → `/carrito`

**Bottom navigation (3 tabs fijos)**
1) **Cafeterías** → `/cafeterias`
2) **Mapa** → `/cafeterias/mapa`
3) **Cuenta** → `/cuenta`

**Pantalla Cuenta** (`/cuenta`)
- Si NO hay sesión:
  - Card con CTA “Iniciar sesión” → `/login`
- Si hay sesión Cliente:
  - **Ver mis pedidos** → `/mis-pedidos`
  - **Cerrar sesión** (acción)

Notas:
- “Cerrar sesión” NO va en la bottom bar (es una acción, va en Cuenta).
- Carrito debe ser accesible en 1 tap (top bar).

---

## 2) Web Panel — Propietario

### 2.1 Desktop (>= lg)
**Tipo**: sidebar izquierda fija + topbar.

**Sidebar header**
- Logo OCCU + texto “OCCU” (click → `/panel/cafeterias`)

**Items obligatorios (orden)**
1) **Mis cafeterías** → `/panel/cafeterias`
2) **Mi menú** (menú general del propietario) → `/panel/menu-general`
3) **Productos OCCU** (activar productos para su menú) → `/panel/productos-occu`
4) **Subcategorías** (activar subcategorías / agregar más) → `/panel/subcategorias`

**Acción al final (sticky)**
- **Cerrar sesión** (acción) → `/panel/login`

### 2.2 Móvil (< md)
- Sidebar se vuelve **drawer** (hamburguesa en topbar).
- El orden y etiquetas de items es el mismo.

---

## 3) Web Panel — Administrador

### 3.1 Desktop (>= lg)
**Tipo**: sidebar izquierda fija + topbar.

**Sidebar header**
- Logo OCCU + texto “OCCU” (click → `/panel/admin`)

**Items obligatorios (orden)**
1) **Cafeterías** → `/panel/admin/cafeterias`
2) **Catálogo de categorías** → `/panel/admin/menu/categorias`
3) **Subcategorías** → `/panel/admin/menu/subcategorias`
4) **Productos** → `/panel/admin/menu/productos`
5) **Servicios** → `/panel/admin/servicios`
6) **Ingredientes** → `/panel/admin/menu/ingredientes`
7) **Categorías de ingredientes** → `/panel/admin/menu/ingredientes-categorias`

**Acción al final (sticky)**
- **Cerrar sesión** (acción) → `/panel/login`

### 3.2 Móvil (< md)
- Sidebar drawer (hamburguesa).

---

## 4) Reglas de comportamiento (todas las apps)

- Si un usuario inicia sesión con rol no permitido en la app:
  - Web Cliente: Owner/Admin → redirigir a Panel
  - Web Panel: Cliente → redirigir a Cliente
- Los items definidos aquí son el **MVP mínimo**. Se pueden agregar más a futuro, pero no cambiar estos.

