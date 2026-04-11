## Plan de implementación — Menú nuevo OCCU (app nueva)

### Objetivo
Diseñar e implementar en la **nueva web app de OCCU** un sistema de menú **escalable** que soporte:

- **Reglas mixtas por categoría**:
  - **Selección única** (ej. Leche: obligatorio, elegir 1).
  - **Selección múltiple con cantidades por ingrediente** (ej. Esencias/Jarabes: Vainilla 0–3 pumps, Caramelo 0–3 pumps).
- **Límites por ingrediente** (confirmado).
- **Sin** límite total por categoría (confirmado).
- **Precio por porción/por unidad** (no depende del tamaño del vaso; confirmado).
- Plantillas **OCCU** por producto para que el propietario no parta de cero.
- **Cálculo determinista** del total y persistencia consistente (snapshot en carrito/pedido).

### Alcance / No alcance

- **Dentro de alcance**:
  - Catálogos OCCU (categorías, subcategorías, productos, categorías de ingredientes, ingredientes, tamaños).
  - Definición de **plantillas OCCU** de personalización por producto (reglas).
  - Menú publicado por cafetería (cada cafetería elige productos y ajusta reglas/precios).
  - Carrito/pedidos guardando selección de ingredientes con **cantidad** y costo.

- **Fuera de alcance**:
  - Modificar/rehacer la app existente actual (“legacy”).
  - Migrar datos productivos (actualmente no está en producción).
  - Precio de ingrediente dependiente de tamaño (por ahora no).
  - Límite total por categoría (por ahora no).

> Regla de oro: **todo se implementa en la app nueva**. La app existente no se toca.

---

## Diseño funcional (reglas)

### 1) Categoría de ingredientes (ej. “Leche”, “Jarabes”, “Toppings”)
Cada categoría aplicada a un producto define:

- **tipo_seleccion**: `unica` | `multiple`
- **seleccion_minima**: 0..N
- **seleccion_maxima**: 0..N
- **orden**: para UX

Ejemplos:
- **Leche**: `unica`, min=1, max=1
- **Jarabes/Esencias**: `multiple`, min=0, max=999 (o un número grande), y el control de límite real es por ingrediente

### 2) Ingrediente (ej. “Vainilla”, “Leche de Avena”)
Cada ingrediente aplicado a un producto define:

- **habilitado**: si aparece como opción en ese producto
- **permite_cantidad**: true/false
- **cantidad_minima**, **cantidad_maxima**, **paso_cantidad**
- **cantidad_incluida** (si aplica)
- **tipo_precio**: `por_porcion` | `fijo`
- **precio_unitario**
- **es_recomendado** (para UX)

Ejemplos:
- **Leche de Avena**: permite_cantidad=false, (cantidad implícita 1 si se selecciona), precio_unitario puede ser 0 o un adicional fijo.
- **Vainilla**: permite_cantidad=true, min=0, max=3, paso=1, tipo_precio=por_porcion, precio_unitario=3.00

---

## Diseño de datos (modelo recomendado)

> Nota: nombres en español, consistentes con el estilo actual `menu_*`, `cafeterias_*`, `ventas_*`.

### A) Catálogos OCCU (base)
- `menu_categorias`
- `menu_subcategorias`
- `menu_productos`
- `menu_productos_tamanos`
- `menu_ingredientes_categorias`
- `menu_ingredientes`

### B) Plantillas OCCU por producto (reglas)
Permiten que OCCU defina cómo se personaliza cada producto.

- `menu_productos_reglas_ingredientes_categorias`
  - `id_producto`
  - `id_ingrediente_categoria`
  - `tipo_seleccion` (unica|multiple)
  - `seleccion_minima`, `seleccion_maxima`
  - `orden`, `estado`, `id_alta`, `fecha_alta`

- `menu_productos_reglas_ingredientes`
  - `id_producto`
  - `id_ingrediente`
  - `permite_cantidad`
  - `cantidad_minima`, `cantidad_maxima`, `paso_cantidad`
  - `cantidad_incluida`
  - `tipo_precio` (por_porcion|fijo)
  - `precio_unitario`
  - `es_recomendado`
  - `estado`, `id_alta`, `fecha_alta`

### C) Menú por cafetería (publicación real)
La cafetería “publica” productos y hereda la plantilla OCCU (copiándola) para ajustes locales.

- `cafeterias_menu_productos`
  - `id_cafeteria`
  - `id_producto`
  - `precio_base` (DECIMAL)
  - `estado`, `id_alta`, `fecha_alta`

- `cafeterias_menu_productos_tamanos`
  - `id_cafeteria_menu_producto`
  - `id_tamano`
  - `precio` (DECIMAL)
  - `estado`

- `cafeterias_menu_productos_reglas_ingredientes_categorias`
  - como plantilla pero por `id_cafeteria_menu_producto`

- `cafeterias_menu_productos_reglas_ingredientes`
  - como plantilla pero por `id_cafeteria_menu_producto`

### D) Carrito/pedido (snapshot recomendado)
Para que cambios de reglas/precios no afecten carritos/pedidos ya armados:

- `ventas_carrito_items_personalizaciones`
  - `id_carrito_item`
  - `id_ingrediente`
  - `cantidad`
  - `precio_unitario_snapshot`
  - `monto_total_snapshot`
  - `estado`, `fecha_alta`

- `ventas_items_personalizaciones`
  - `id_venta_item`
  - `id_ingrediente`
  - `cantidad`
  - `precio_unitario_snapshot`
  - `monto_total_snapshot`
  - `estado`, `fecha_alta`

### Índices/unique keys (obligatorio para escalar)
- Unique:
  - `(id_producto, id_ingrediente_categoria)` en reglas por categoría
  - `(id_producto, id_ingrediente)` en reglas por ingrediente
  - `(id_cafeteria, id_producto)` en `cafeterias_menu_productos`
  - `(id_cafeteria_menu_producto, id_tamano)` en tamaños
  - `(id_carrito_item, id_ingrediente)` en snapshot de carrito
  - `(id_venta_item, id_ingrediente)` en snapshot de venta

---

## APIs / Casos de uso (app nueva)

### 1) Admin OCCU (catálogos + plantillas)
- Crear/editar:
  - categorías/subcategorías/productos
  - categorías de ingredientes / ingredientes
  - tamaños
- Para cada producto:
  - seleccionar categorías aplicables
  - definir reglas por categoría (unica/multiple, min/max, orden)
  - definir reglas por ingrediente (habilitar, cantidades, precio, incluido, recomendado)

### 2) Propietario (app nueva)
- Agregar producto OCCU al menú de su cafetería:
  - se crea `cafeterias_menu_productos`
  - se copian reglas plantilla OCCU → reglas de cafetería (para ajustes)
- Ajustar por cafetería:
  - activar/desactivar ingredientes
  - límites por ingrediente
  - precios por porción
  - para bebidas, precios por tamaño

### 3) Cliente (app nueva)
- Ver menú de cafetería:
  - productos activos
  - al abrir producto: render según reglas
- Personalizar:
  - categorías `unica` → radio
  - categorías `multiple` sin cantidad → checkbox
  - categorías `multiple` con cantidad → stepper por ingrediente (0..max)
- Total:
  - cálculo determinista en front
  - validación determinista en back
- Agregar al carrito:
  - backend valida reglas (min/max y límites de cantidad)
  - backend genera snapshot de costos por ingrediente seleccionado

---

## UX sugerida (para que sea intuitivo)

### Cliente
- Cada categoría muestra:
  - etiqueta de obligatorio si min>0
  - “elige 1” o “elige varias” según tipo
  - si hay cantidad: stepper con etiqueta “pumps/porciones”
  - costo incremental visible (“+MX$3 por porción”)
- Recomendados:
  - “recomendado” marcado visualmente (sin auto-seleccionar si no quieres)

### Propietario
- Plantilla OCCU ya trae configuración base.
- UI de edición separada en:
  - reglas por categoría
  - reglas por ingrediente (con búsqueda y filtros por categoría)

---

## Plan de trabajo (fases)

### Fase 0 — Alineación y escenarios
Definir 8–10 escenarios canónicos (ej. Latte, bebida sin leche, frappé, sandwich) y validar que el modelo los cubre.

#### Escenarios canónicos (checklist de implementación)

> Regla general: **frontend no auto-selecciona recomendados** (solo resalta). El backend siempre valida reglas y genera snapshot.

1) **Latte (bebida con leche)**
- **Tamaños**: obligatorio elegir 1 (ej. 12oz o 16oz).
- **Categoría “Leche”**: `unica`, min=1, max=1. Opciones sin cantidad.
- **Categoría “Jarabes/Esencias”**: `multiple`, min=0, max=NULL. Opciones con cantidad 0..3 por ingrediente, precio `por_porcion`.
- **Validación**: no permite agregar si no elige tamaño o no elige leche.
- **Cálculo**: total = precio_tamaño + Σ(porciones * precio_unitario).

2) **Americano (bebida sin leche)**
- **Tamaños**: obligatorio elegir 1.
- **Categoría “Leche”**: NO debe aplicar (no aparece).
- **Categoría “Jarabes/Esencias”**: sí puede aplicar (múltiple con cantidad).
- **Validación**: solo tamaño obligatorio.
- **Objetivo**: confirmar que la plantilla OCCU controla qué categorías aplican por producto.

3) **Frappé (bebida con “Base” y toppings)**
- **Tamaños**: obligatorio elegir 1 (si aplica a frappés).
- **Categoría “Base”**: `unica`, min=1, max=1.
- **Categoría “Toppings”**: `multiple`, min=0, max=NULL.
  - Algunas opciones sin cantidad (checkbox).
  - Opcional: algunas con cantidad (0..2) si se requiere.
- **Validación**: base obligatoria.

4) **Bebida “Chocolate caliente” con ingrediente de precio fijo**
- Ejemplo: “extra shot” o “doble cacao” cobrado **una sola vez**.
- **Regla**: ingrediente con `tipo_precio=fijo` y `permite_cantidad=false`.
- **Validación**: si se selecciona, se suma el fijo 1 vez.
- **Objetivo**: confirmar semántica de `tipo_precio=fijo`.

5) **Sándwich (alimento) con selección única + múltiples sin cantidad**
- **Sin tamaños**.
- **Categoría “Pan/Base”**: `unica`, min=1, max=1.
- **Categoría “Proteína”**: `unica` o `multiple` según producto (definir en plantilla).
- **Categoría “Vegetales”**: `multiple`, min=0, max=NULL, opciones sin cantidad (checkbox).
- **Validación**: pan/base obligatorio.

6) **Ensalada con límites por ingrediente (cantidades)**
- **Categoría “Proteína”**: `multiple`, min=0, max=NULL.
  - Ingredientes con `permite_cantidad=true` (ej. Pollo 0..2 porciones).
- **Objetivo**: validar “cantidad por ingrediente” también en alimentos.

7) **Ingrediente con “cantidad_incluida”**
- Ejemplo: “Vainilla incluye 1 pump” y permite hasta 3 pumps total.
- **Regla**: `cantidad_incluida=1`, `precio_unitario=3.00`, `permite_cantidad=true`.
- **Cálculo esperado**:
  - Si cantidad=0 → suma 0.
  - Si cantidad=1 → suma 0 (incluida).
  - Si cantidad=3 → suma (3-1)*3.00.
- **Objetivo**: confirmar que el cálculo no depende de orden y aplica incluido por ingrediente.

8) **Ingrediente “agotado” por cafetería**
- El propietario marca un ingrediente como `agotado=true` (por cafetería).
- **UX cliente**: no debe mostrarse o debe mostrarse deshabilitado (decidir una sola política).
- **Backend**: si llega una selección con ingrediente agotado → rechazar.

9) **Cambio de reglas/precios mientras hay carrito (consistencia)**
- Cliente agrega producto con personalizaciones al carrito.
- Luego el propietario cambia reglas/precios del producto.
- **Esperado**: el carrito y el pedido mantienen total gracias a **snapshot**.

10) **Merge de items en carrito**
- Misma cafetería, mismo producto, mismo tamaño y misma personalización (mismos ingredientes + cantidades) → debe sumar cantidad.
- Si cambia cualquier cantidad (ej. Vainilla 1 vs 2) → debe crear item distinto.
- **Objetivo**: definir igualdad exacta por selección y cantidades.

#### Criterios de “Fase 0 completada”
- Los 10 escenarios anteriores se pueden representar con el modelo (plantilla OCCU y reglas por cafetería).
- Para cada escenario se define:
  - reglas mínimas por categoría/ingrediente
  - validaciones obligatorias
  - fórmula de total (incluidos y precios)
  - comportamiento de UI (radio/checkbox/stepper)

### Fase 1 — Base técnica (BD + seeds)
- Implementar migraciones del esquema.
- Crear seeds mínimos para ejemplos (1–2 productos con reglas completas).

### Fase 2 — Admin OCCU (catálogos + plantillas)
- Pantallas/API para editar reglas por producto:
  - categorías aplicables
  - reglas por categoría
  - reglas por ingrediente

### Fase 3 — Menú por cafetería (propietario)
- “Agregar producto” copia plantilla OCCU → cafetería.
- Edición de reglas y precios por cafetería.

### Fase 4 — Cliente (menú + carrito)
- Render de reglas (unica/multiple/stepper).
- Validaciones backend.
- Snapshot en carrito y venta.

### Fase 5 — Pruebas y hardening
- Pruebas de escenarios canónicos.
- Pruebas de concurrencia básica (dos cambios de reglas mientras hay carrito).
- Revisión de índices.

---

## Riesgos y mitigaciones
- **Complejidad de reglas**: mitigación: escenarios canónicos + UI guiada.
- **Inconsistencias de precio**: mitigación: snapshot + validación backend siempre.
- **Ambigüedad de nombres**: mitigación: convención única para llaves (`id_cafeteria_menu_producto`).

---

## Pendientes a decidir (cuando se requiera)
- ¿Se auto-seleccionan “recomendados” por defecto o solo se resaltan?
Respuesta: solo se resaltan
- ¿Se manejarán “agotados” por ingrediente por cafetería?
respuesta: si el proietario lo marca, si
- ¿Se requiere unidad de cantidad configurable (pumps, shots, gramos)?
respuesta: por el momento no

