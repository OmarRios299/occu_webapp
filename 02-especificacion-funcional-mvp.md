### Especificación funcional — OCCU (MVP a migrar)
Este documento define **exactamente qué debe hacer** la nueva versión del **MVP** de OCCU a nivel funcional: **pantallas**, **flujos**, **reglas**, **validaciones**, **permisos**, **estados** y **mensajes esperados**.

> Alcance: NO describe código, arquitectura ni base de datos. Solo comportamiento observable.

---

## 1) Alcance del MVP

### 1.1 Qué SÍ incluye (MVP)
- **Autenticación**: registro interno (correo+contraseña) + verificación por **PIN de 6 dígitos** + login + login con Google.
- **Roles operativos del MVP**:
  - **Cliente**: explorar cafeterías (lista/mapa), ver detalle, ver menú, armar carrito, elegir método de pago, crear pedido, ver mis pedidos (detalle e historial), cancelar cuando aplique, crear reseñas.
  - **Propietario**: administrar cafeterías (crear/editar/activar/desactivar), ubicación, horarios, galería de imágenes, servicios, menú, pedidos (aceptar/rechazar/terminar/entregar), QR de menú.
  - **Administrador**: supervisión y mantenimiento (usuarios, **catálogo de servicios**, catálogos de menú).

### 1.2 Qué NO incluye (por ahora)
- **Flujo Barista**: el rol existe, pero **no se define** su operación en este MVP. (Ver sección 2.2.)
- Cualquier módulo no mencionado en el alcance.

---

## 2) Roles, permisos y reglas de acceso

### 2.1 Roles
- **Cliente**: compra/pedidos.
- **Propietario**: operación de cafeterías.
- **Administrador**: control total y mantenimiento.
- **Barista**: existe como rol, pero **sin flujo** definido para el MVP.

### 2.2 Regla para “Barista” en el MVP
- Si existe en el sistema:
  - **Puede iniciar sesión** (si la cuenta existe).
  - **NO** debe poder crear pedidos ni operar carrito (funcionalmente “fuera de alcance”).
  - Su navegación/landing puede ser “pendiente” (o redirigir a una pantalla informativa).

### 2.3 Acceso público vs privado
- **Público (sin sesión)**: Inicio, Lista de cafeterías, Mapa, Detalle de cafetería, Menú público, Login, Registro.
- **Privado (requiere sesión)**: todo lo demás.
- Si un usuario no logueado intenta entrar a un módulo privado: el sistema lo envía a **Login**.

### 2.4 Inicio operativo (landing) por rol (al iniciar sesión)
- **Cliente** → **Cafeterías (lista)**.
- **Propietario** → **Mis cafeterías**.
- **Administrador** → **Dashboard**.

---

## 3) Conceptos y datos funcionales (diccionario)

### 3.1 Usuario
- **Datos**: nombre, apellido, ciudad, correo, contraseña (si registro interno), rol, verificado (sí/no), estado (activo/inactivo), sesión.
- **Verificación**: PIN de 6 dígitos enviado por correo en registro interno.

### 3.2 Cafetería
- **Datos mínimos**: nombre, ciudad, logo/imagen (opcional), dirección, ubicación (lat/long), estado (activa/inactiva).
- **Adicionales**: descripción, teléfono, correo (si se captura, debe ser único), horarios, galería de imágenes.

### 3.3 Servicios
- Catálogo global de servicios (con ícono) y asignación por cafetería.

### 3.4 Menú
- **Catálogo base**: categorías → subcategorías → productos.
- **Producto**:
  - Puede ser **bebida** (con tamaños y precio por tamaño) o **alimento** (precio base).
  - Puede tener **ingredientes** organizados en categorías.
  - Puede permitir ingredientes “extra” con costo según reglas.

### 3.5 Carrito
- Carrito activo por usuario, asociado a **una sola cafetería**.
- Contiene items (producto, cantidad, tamaño si aplica, ingredientes y extras).

### 3.6 Pedido (Venta)
- Pedido generado desde carrito: total, items, ingredientes, estado del pedido, timestamps y **código de verificación (6 dígitos)** para entrega.

### 3.7 Reseñas/Comentarios
- Comentario del cliente hacia una cafetería (visible en el detalle público).

---

## 4) Estados del pedido y transiciones permitidas

### 4.1 Estados (modelo mental)
- **1 — Pendiente**: pedido recién creado; esperando respuesta del propietario.
- **2 — En preparación** (“Preparando”): el propietario aceptó el pedido.
- **6 — Terminado / Pedido listo**: el pedido está listo para entregar.
- **4 — Entregado**: pedido entregado (requiere validación del código).
- **3 — Rechazado**: el propietario lo rechazó con motivo.
- **5 — Cancelado**: el cliente lo canceló antes de ser aceptado.

> Nota: Estos nombres son los que se muestran al usuario. El objetivo aquí es fijar el lenguaje y las reglas.

### 4.2 Transiciones (qué cambios de estado se permiten)
- **Cliente**
  - Pendiente (1) → Cancelado (5): permitido.
  - Cualquier otro estado → Cancelado: NO permitido.
- **Propietario**
  - Pendiente (1) → En preparación (2): “Aceptar”.
  - Pendiente (1) → Rechazado (3): “Rechazar” (motivo obligatorio).
  - En preparación (2) → Terminado (6): “Terminar”.
  - En preparación (2) → Entregado (4): “Entregar” (requiere código).
  - Terminado (6) → Entregado (4): “Entregar” (requiere código).
- **Administrador**
  - **Solo lectura**: puede ver y supervisar pedidos, pero **NO puede cambiar estados** en el MVP.

### 4.3 Regla del código de entrega (6 dígitos)
- Se genera al **Aceptar** el pedido (cuando pasa a En preparación).
- Para **Entregar**:
  - El propietario solicita el código al cliente.
  - Si coincide: estado pasa a **Entregado**.
  - Si no coincide: el sistema **no** cambia el estado y muestra error.

---

## 5) Navegación y pantallas (mapa de producto)

### 5.1 Pantallas públicas
- **Login**: correo/contraseña + acceso con Google.
- **Registro**: selección de perfil + datos + envío de PIN.
- **Verificación (PIN)**: validar cuenta y entrar.
- **Cafeterías (lista)**: explorar y filtrar.
- **Cafeterías (mapa)**: explorar y filtrar.
- **Detalle de cafetería**: información, servicios, galería, reseñas.
- **Menú público**: categorías, productos, detalle de producto (para elegir tamaño/ingredientes).

### 5.2 Pantallas privadas — Cliente
- **Carrito** (panel lateral): ver items, editar cantidades/eliminar, ir a pagar.
- **Método de pago**: resumen y selección de método.
- **Mis pedidos**: pedido en curso + historial + detalle + cancelación si aplica.

### 5.3 Pantallas privadas — Propietario
- **Mis cafeterías**: listado y acciones.
- **Crear/Editar cafetería**: información + ubicación + horarios + contacto + imágenes.
- **Servicios de cafetería**: seleccionar servicios del catálogo.
- **Menú (propietario)**: activar categorías/subcategorías/productos, configurar precios/tamaños/ingredientes, publicar a sucursales.
- **Pedidos**: tabs por estado, acciones por pedido, detalle, imprimir comanda, entrega con código.
- **QR del menú**: ver/descargar.

### 5.4 Pantallas privadas — Administrador
- **Dashboard**: métricas.
- **Usuarios**: alta/edición/activación.
- **Pedidos**: ver listado y detalle (**solo lectura**, sin cambios de estado).
- **Catálogo de servicios**: administrar servicios disponibles (para que las cafeterías los puedan elegir).
- **Catálogo de menú (global)**: categorías/subcategorías/productos/ingredientes.

---

## 6) Flujos detallados (lo que se debe programar)

## Flujo A — Registro, verificación y login

### A1) Registro interno (correo + contraseña)
**Objetivo**: crear cuenta “no verificada” y enviar PIN.

**Entrada (campos obligatorios)**
- Tipo de perfil: **Cliente** o **Propietario** (Barista visible, pero no MVP).
- Nombre, Apellido, Ciudad
- Correo electrónico
- Contraseña y Confirmación de contraseña

**Validaciones**
- El correo **no debe existir** previamente.
- Contraseña y confirmación deben coincidir.
- Campos obligatorios completos.

**Resultado**
- El usuario queda creado como **no verificado**.
- Se envía un **PIN de 6 dígitos** al correo.
- El sistema redirige a pantalla de **Verificación (PIN)**.

### A2) Verificación por PIN (6 dígitos)
**Entrada**
- Correo
- Contraseña
- PIN (6 dígitos)

**Validaciones**
- Correo + contraseña corresponden a una cuenta válida.
- La cuenta está en estado “no verificada”.
- El PIN coincide con el PIN vigente de la cuenta.

**Resultado (si es correcto)**
- La cuenta pasa a **verificada**.
- Se inicia sesión.
- Se redirige según rol (ver 2.4).

**Resultado (si es incorrecto)**
- La cuenta permanece no verificada.
- Se muestra mensaje de error y se permite reintentar.

**Acción: Reenviar código**
- El usuario puede solicitar reenviar.
- El sistema genera un PIN nuevo y lo envía al correo.
- El PIN anterior deja de ser válido.

### A3) Login (correo + contraseña)
**Reglas**
- Si la cuenta no está verificada: se debe dirigir al flujo de verificación (o mostrar mensaje claro).
- Si el correo/contraseña no coincide: error.

**Resultado**
- Se crea sesión y redirección según rol.

### A4) Login con Google
**Reglas**
- Si es usuario nuevo: el sistema crea la cuenta con datos disponibles de Google.
- Si falta información para operar:
  - Se solicita completar al menos el **tipo de usuario** (Cliente/Propietario/Barista) y, si aplica, **ciudad**.
- Luego inicia sesión y redirige según rol.

---

## Flujo B — Cliente: explorar cafeterías, carrito, pago, pedido, historial

### B1) Explorar cafeterías (lista)
**Qué muestra**
- Tarjetas/lista de cafeterías.
- Indicador **abierta/cerrada** según horario.

**Filtros**
- Búsqueda por nombre
- Ciudad
- “Abiertas ahora”
- Servicios (selección múltiple)

**Acciones**
- Ver detalle
- Ver menú
- Ir al mapa

### B2) Explorar cafeterías (mapa)
**Qué muestra**
- Marcadores por cafetería.
- Panel con información al seleccionar marcador.

**Filtros**
- Los mismos que la lista.

### B3) Detalle de cafetería
**Qué muestra**
- Galería de imágenes
- Dirección, teléfono, correo (si existe)
- Servicios
- Estado abierto/cerrado + horario del día

**Acciones**
- “Ir a ubicación”
- “Ver menú”

**Reseñas**
- Ver reseñas con paginación.
- Crear reseña si está logueado (Cliente).

### B4) Menú: seleccionar producto y personalizar
**Objetivo**: armar un item de carrito.

**Reglas**
- Solo **Cliente** puede agregar al carrito en el MVP.
- Si el producto es bebida: requiere seleccionar **tamaño** (si hay tamaños activos).
- Ingredientes:
  - Pueden venir por **categorías**.
  - Una categoría puede ser **obligatoria** (si lo es, debe forzar selección).
  - Puede haber selección base incluida y “extras” con costo adicional.

**Resultado**
- El item queda agregado al carrito activo.

### B5) Carrito (reglas y operaciones)
**Regla 1: un carrito activo a la vez (por usuario)**
- Si el cliente intenta agregar productos de **otra cafetería**:
  - Si el carrito anterior está vacío: se reemplaza automáticamente.
  - Si el carrito anterior tiene productos: el sistema pregunta si desea **eliminar el carrito anterior** y continuar.

**Operaciones**
- Ver items (producto, tamaño si aplica, ingredientes).
- Cambiar cantidades:
  - Si la cantidad baja a 0, el item se elimina.
- Ver total actualizado.
- Ir a “Método de pago”.

**Validación**
- No se permite continuar a pago con carrito vacío (mensaje: “Carrito vacío”).

### B6) Método de pago y creación del pedido
**Entrada**
- Método: **tarjeta** o **efectivo**.

**Validaciones**
- Carrito no vacío.
- Debe seleccionarse un método de pago.
- No se puede crear un nuevo pedido si ya existe un pedido “en proceso” (pendiente/preparación/terminado) para el cliente.

**Qué se registra**
- Pedido principal (cafetería, cliente, total, estado inicial).
- Items y sus ingredientes.
- El carrito se marca como cerrado/procesado.

**Resultado**
- Se crea pedido en estado **Pendiente (1)**.
- El cliente puede ver el pedido en “Mis pedidos”.

### B7) Mis pedidos (seguimiento e historial)
**Qué muestra**
- Pedido en curso (si existe).
- Historial paginado.
- Detalle de pedido: items + ingredientes + totales.

**Acción: Cancelar pedido**
- Solo permitido si el pedido está **Pendiente (1)**.
- Al cancelar: estado pasa a **Cancelado (5)** y debe reflejarse en la lista y el detalle.

---

## Flujo C — Propietario: cafeterías, servicios, menú, pedidos, QR

### C1) Mis cafeterías
**Qué muestra**
- Listado de cafeterías del propietario.

**Acciones por cafetería**
- Editar
- Activar/desactivar
- Gestionar servicios
- Gestionar menú
- Ver pedidos
- Ver/descargar QR del menú
- Eliminar (si aplica y está permitido)

### C2) Crear cafetería
**Entrada**
- Información básica: nombre (obligatorio), país (si aplica), ciudad (obligatoria), logo/imagen (opcional).
- Ubicación: punto en mapa + dirección (obligatoria) + lat/long.
- Información adicional: descripción (opcional).
- Horarios: “mismo horario todos los días” (sí/no), apertura/cierre o por día.
- Contacto: teléfono (opcional), correo (opcional).

**Validaciones**
- Nombre de cafetería: **único**.
- Si hay correo de cafetería: **único**.
- Dirección obligatoria.
- Ciudad obligatoria.

**Resultado**
- Cafetería creada (por defecto activa o inactiva debe definirse; recomendado: activa si completa).

### C3) Editar cafetería + galería de imágenes
**Reglas**
- Puede modificar los mismos datos que al crear.
- Puede subir imágenes adicionales para galería pública.

### C4) Gestionar servicios de la cafetería
**Regla**
- Selección de servicios del catálogo (checkbox).

**Resultado**
- Servicios visibles para clientes en lista/mapa/detalle.

### C5) Gestionar menú (publicación)
**Objetivo**: definir qué se vende y a qué precio por cafetería/sucursal.

**Acciones**
- Activar/desactivar categorías y subcategorías.
- Activar/desactivar productos.
- Configurar precios:
  - Bebidas: precios por tamaño.
  - Alimentos: precio base.
- Configurar ingredientes:
  - Activar ingredientes disponibles.
  - Definir porción incluida (gratis) y costo extra cuando aplique.
- (Opcional) Crear elementos extra (subcategorías/productos/ingredientes personalizados).
- Publicar/actualizar:
  - Puede aplicarse a todas las sucursales o a una sucursal.
  - Puede existir acción “solo actualizar precios” (si aplica).

**Resultado**
- Menú visible para clientes.

### C6) Pedidos de una cafetería (operación)
**Vista**
- Tabs por estado: Pendientes, En preparación, Terminados, Entregados, Rechazados.
- Cada pedido muestra folio, cliente, total, tiempo transcurrido y estado.
- Acciones disponibles dependen del estado (ver 4.2).

**Acciones**
- Aceptar (genera código).
- Rechazar (motivo obligatorio).
- Terminar.
- Entregar (solicita código de 6 dígitos).
- Ver detalle.
- Imprimir comanda (si existe).

### C7) QR del menú
**Regla**
- El QR apunta al **menú público** de la cafetería.

**Resultado**
- QR descargable/compartible.

---

## Flujo D — Administrador (MVP)

### D1) Dashboard
- Muestra métricas generales (cafeterías, propietarios, clientes y, si existe, baristas).

### D2) Gestión de usuarios
- Crear usuario.
- Ver detalle.
- Editar.
- Activar/desactivar.

### D3) Pedidos (solo lectura)
- Ver listado de pedidos (con filtros/búsqueda si aplica).
- Ver detalle del pedido (items, ingredientes, totales, timestamps, estado).
- **No** puede: aceptar, rechazar, terminar, entregar, cancelar, ni “forzar” estados.

### D4) Catálogo de servicios
- Crear/editar/activar/desactivar **Servicios** (los que las cafeterías pueden seleccionar).
- Cada servicio debe tener al menos: nombre e ícono/imagen (si aplica).

### D5) Catálogo global de menú
- Mantener categorías, subcategorías, productos e ingredientes.

---

## 7) Mensajes y manejo de errores (mínimos)
- **Acceso privado sin sesión**: “Debes iniciar sesión” → redirigir a Login.
- **Registro**:
  - “Este correo ya se encuentra registrado”.
  - “Las contraseñas no coinciden”.
- **Verificación**:
  - “PIN inválido” / “PIN expirado o reemplazado”.
  - “Reenviado: revisa tu correo”.
- **Carrito**:
  - “Carrito vacío: agrega productos antes de continuar”.
  - “Tu carrito pertenece a otra cafetería. ¿Deseas eliminarlo y continuar?”
- **Pedidos**:
  - Rechazo requiere motivo: “Motivo obligatorio”.
  - Entrega con código inválido: “Código incorrecto”.
  - Cancelación no permitida si no está Pendiente: “No se puede cancelar en este estado”.

---

## 8) Criterios de “terminado” (Definition of Done) para el MVP
- Cada flujo A–D puede ejecutarse de principio a fin cumpliendo validaciones y mostrando mensajes definidos.
- Las reglas de permisos/roles se respetan (Cliente no opera cafeterías; Propietario no opera admin; etc.).
- El ciclo de vida del pedido (Pendiente → … → Entregado/Rechazado/Cancelado) es consistente en todas las pantallas.


