## Planeación funcional (vNext) — Basado en MVP + app vieja

### Fuentes
- `02-especificacion-funcional-mvp.md`: **fuente de verdad** de flujos MVP (A–D).
- `03-ingenieria-inversa-menu.md`: reglas actuales de menú/carrito (legacy) y oportunidades.
- `04-plan-menu-nuevo-occu.md`: modelo recomendado de **reglas v2** + snapshots.

---

## 1) Roles y reglas de acceso

### Roles
- `Cliente`: compra/pedidos, reseñas.
- `Propietario`: operación de cafeterías y pedidos, publicación de menú.
- `Administrador`: catálogos globales, plantillas/reglas, usuarios, supervisión.
> Nota vNext: el rol `Barista` **se elimina por el momento** (no existe en el producto).

### Landings por rol (al iniciar sesión)
- Cliente → `/cafeterias` (lista)
- Propietario → `/panel/cafeterias`
- Administrador → `/panel/admin`

---

## 2) Web Cliente (React+Tailwind) — Rutas y pantallas

> Regla: esta app contiene **todo lo público** del MVP, aunque no haya sesión.

### 2.1 Público (sin sesión)
- **Inicio**: `/` → redirige a `/cafeterias`
- **Login**: `/login`
  - correo/contraseña
  - Google login
  - mensaje claro si cuenta no verificada
- **Registro**: `/registro`
  - selección rol visible: Cliente/Propietario (Barista visible pero “no MVP”)
  - nombre, apellido, ciudad, correo, contraseña, confirmación
- **Verificación PIN**: `/verificar`
  - correo + contraseña + PIN 6 dígitos
  - acción “Reenviar código”
- **Cafeterías lista**: `/cafeterias`
  - filtros: nombre, ciudad, “abiertas ahora”, servicios (multi)
  - acciones: ver detalle, ver menú, ir al mapa
- **Cafeterías mapa**: `/cafeterias/mapa`
  - mismos filtros
  - selección en mapa abre panel con info y CTA “Ver menú”
- **Detalle cafetería**: `/cafeterias/:id`
  - galería, dirección, contacto, servicios, abierto/cerrado, reseñas
- **Menú público cafetería**: `/cafeterias/:id/menu`
  - categorías → subcategorías → productos
  - abrir producto → modal/drawer de personalización (reglas v2)

### 2.2 Privado (solo `Cliente`)
- **Carrito**: `/carrito`
  - regla: un carrito activo por usuario y por cafetería
  - merge de items por igualdad exacta (producto+tamaño+personalización/cantidades)
- **Método de pago**: `/pago`
  - método: tarjeta / efectivo
  - no permite continuar si carrito vacío
- **Mis pedidos**: `/mis-pedidos`
  - pedido en curso + historial paginado
  - cancelar solo si estado = Pendiente (1)
- **Detalle pedido**: `/mis-pedidos/:id`
  - items + personalizaciones (snapshot) + total + estado + timestamps

### 2.3 Reglas de UX obligatorias (Cliente)
- Ingredientes:
  - categoría `unica` → radio
  - categoría `multiple` sin cantidad → checkbox
  - categoría `multiple` con cantidad → stepper 0..max por ingrediente
  - mostrar “incluido” (cantidad_incluida) y “+MX$ por porción”
- Total visible en tiempo real (display), pero el **backend valida y calcula** total final.
- Recomendados: solo resaltar (no auto-seleccionar).

---

## 3) Web Panel (React+Tailwind) — Rutas y pantallas

> Regla: esta app NO muestra cafeterías públicas; es operación interna (owner/admin).

### 3.1 Auth y shell
- **Login Panel**: `/panel/login`
- Layout:
  - Sidebar izquierda (desktop) + Topbar
  - Drawer de navegación (móvil)

### 3.2 Propietario
- **Mis cafeterías**: `/panel/cafeterias`
  - listado, activar/desactivar, editar
  - acciones: servicios, menú, pedidos, QR
- **Crear cafetería**: `/panel/cafeterias/crear`
- **Editar cafetería**: `/panel/cafeterias/:id/editar`
  - info, ubicación (map), horarios, contacto, galería imágenes
- **Servicios cafetería**: `/panel/cafeterias/:id/servicios`
  - selección del catálogo global de servicios
- **Menú (por cafetería)**: `/panel/cafeterias/:id/menu`
  - activar productos
  - precios base / precios por tamaño (bebidas)
  - reglas v2 por ingrediente (agotado, cantidad, incluido, precio, recomendado)
- **Publicación de menú**: `/panel/menu/publicar`
  - publicar todo a 1..N cafeterías
  - modo: `todo` | `solo_precios`
  - historial de publicaciones
- **Pedidos**: `/panel/cafeterias/:id/pedidos`
  - tabs por estado: Pendientes(1), Preparando(2), Rechazados(3), Entregados(4), Cancelados(5), Terminados(6)
  - acciones:
    - Aceptar: 1→2 (genera código 6 dígitos)
    - Rechazar: 1→3 (motivo obligatorio)
    - Terminar: 2→6
    - Entregar: 2/6→4 (requiere código)
- **QR menú**: `/panel/cafeterias/:id/qr`
  - QR apunta a `cliente` → `/cafeterias/:id/menu`

### 3.3 Administrador
- **Dashboard**: `/panel/admin`
  - métricas básicas (placeholder hasta definir)
- **Usuarios**: `/panel/admin/usuarios`
  - crear/editar/activar/desactivar
- **Pedidos (solo lectura)**: `/panel/admin/pedidos`
  - listado + detalle, sin cambios de estado
- **Catálogo de servicios**: `/panel/admin/servicios`
- **Catálogo de menú (global)**: `/panel/admin/menu`
  - categorías, subcategorías, productos, tamaños
  - categorías de ingredientes, ingredientes
- **Plantillas OCCU (reglas por producto)**: `/panel/admin/menu/plantillas`
  - lista productos + editor reglas (como en plan v2)

---

## 4) Estados de pedido (MVP) — fuente de verdad
- 1 Pendiente
- 2 En preparación
- 6 Terminado / Listo
- 4 Entregado
- 3 Rechazado
- 5 Cancelado

Transiciones: ver `02-especificacion-funcional-mvp.md` (sección 4).

