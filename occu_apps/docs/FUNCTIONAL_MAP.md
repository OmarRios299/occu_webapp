## Mapa funcional por app (vNext) — rutas, pantallas y reglas

**Fuente**: este documento consolida el MVP y la app legacy, pero con la decisión vNext de **NO incluir Barista**.

Regla: **no inventar rutas**. Si una pantalla no está listada aquí, se agrega primero.

---

## 1) Roles y landings

- `Cliente` → `/cafeterias`
- `Propietario` → `/panel/cafeterias`
- `Administrador` → `/panel/admin`

---

## 2) Web Cliente — rutas

### 2.1 Público
- `/` → redirect a `/cafeterias`
- `/login`
- `/registro`
- `/verificar` (PIN)
- `/cafeterias` (lista + filtros)
- `/cafeterias/mapa`
- `/cafeterias/:id`
- `/cafeterias/:id/menu`
- `/cuenta`

### 2.2 Privado (`Cliente`)
- `/carrito`
- `/pago`
- `/mis-pedidos`
- `/mis-pedidos/:id`

---

## 3) Web Cliente — reglas UX obligatorias (personalización v2)

### Controles por tipo de selección
- Categoría `unica` → **radio** (si `min=1`, no permitir continuar sin seleccionar)
- Categoría `multiple` + `permite_cantidad=0` → **checkbox**
- Categoría `multiple` + `permite_cantidad=1` → **stepper** de `0..max` con `paso_cantidad`

### Cálculo de precio (UI)
- Mostrar “incluido” (`cantidad_incluida`) y el costo por porción/fijo.
- Mostrar total estimado en tiempo real.
- **El backend valida y calcula** el total final.

### Recomendados
- `es_recomendado=1` solo resalta visualmente. **Nunca** auto-seleccionar.

---

## 4) Web Panel — rutas

### 4.1 Auth + shell
- `/panel/login`

### 4.2 Propietario
- `/panel/cafeterias`
- `/panel/cafeterias/crear`
- `/panel/cafeterias/:id/editar`
- `/panel/cafeterias/:id/servicios`
- `/panel/cafeterias/:id/menu`
- `/panel/menu-general`
- `/panel/productos-occu`
- `/panel/subcategorias`
- `/panel/menu/publicar`
- `/panel/cafeterias/:id/pedidos`
- `/panel/cafeterias/:id/qr`

### 4.3 Administrador
- `/panel/admin`
- `/panel/admin/cafeterias`
- `/panel/admin/usuarios`
- `/panel/admin/pedidos` (solo lectura)
- `/panel/admin/servicios`
- `/panel/admin/menu`
- `/panel/admin/menu/categorias`
- `/panel/admin/menu/subcategorias`
- `/panel/admin/menu/productos`
- `/panel/admin/menu/ingredientes`
- `/panel/admin/menu/ingredientes-categorias`
- `/panel/admin/menu/plantillas`

---

## 5) Estados de pedido (MVP)

- `1` Pendiente
- `2` En preparación
- `6` Terminado/Listo
- `4` Entregado
- `3` Rechazado
- `5` Cancelado

Transiciones exactas: ver `_legacy/02-especificacion-funcional-mvp.md`.

