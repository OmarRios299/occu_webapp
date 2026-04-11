# Ingeniería Inversa - Sistema de Menú OCCU

## Objetivo
Documentar el funcionamiento exacto del sistema de menú actual, específicamente:
- Cómo los propietarios arman el menú
- Cómo se visualiza para hacer pedidos
- Cómo se identifican los ingredientes que lleva cada producto
- Cantidades de ingredientes
- Si lleva algo extra, etc.

---

## 1. Estructura de Base de Datos

### 1.1 Tablas Principales

#### `menu_categorias`
- Catálogo global de categorías (Bebidas, Comida, Panadería, etc.)
- Campo `es_bebida`: 'Si' o 'No' - determina si la categoría es para bebidas

#### `menu_subcategorias`
- Subcategorías dentro de cada categoría (ej: "Cafés Calientes", "Sandwiches")
- Campo `registro_occu`: 1=OCCU, 2=Propietario
- Campo `id_propietario`: ID del propietario que creó la subcategoría (si registro_occu=2)

#### `menu_productos`
- Catálogo global de productos
- Campo `registro_occu`: 1=OCCU, 2=Propietario
- Campo `id_alta`: ID del propietario que creó el producto (si registro_occu=2)

#### `menu_productos_tamanos`
- Catálogo global de tamaños disponibles (4 Oz, 6 Oz, 12 Oz, 16 Oz, 20 Oz, 24 Oz)
- Solo para productos que son bebidas

#### `menu_ingredientes_categorias`
- Categorías de ingredientes (ej: "Tipo de Leche", "Endulzantes", "Extras")
- Campos `para_bebidas` y `para_alimentos`: 'Si' o 'No' - determina para qué tipo de producto aplica
- Campo `obligatoria`: 'Si' o 'No' - determina si la categoría es obligatoria

#### `menu_ingredientes`
- Catálogo global de ingredientes
- Campo `registro_occu`: 1=OCCU, 2=Propietario
- Campo `id_alta`: ID del propietario que creó el ingrediente (si registro_occu=2)

### 1.2 Tablas de Configuración del Propietario

#### `propietarios_menu_subcategorias`
- Relación entre propietario y subcategorías que activa
- Campo `estado`: 1=Activa, 0=Inactiva

#### `propietarios_productos`
- **Tabla central** que relaciona productos con propietarios y cafeterías
- Campos clave:
  - `id_producto`: ID del producto del catálogo global
  - `id_propietario`: ID del propietario
  - `id_cafeteria`: **0 = configuración general del propietario**, **>0 = configuración específica de cafetería**
  - `precio_base`: Precio para alimentos (sin tamaño)
  - `estado`: 1=Activo, 0=Inactivo, 2=Eliminado

#### `propietarios_productos_tamanos`
- Configuración de tamaños y precios para bebidas
- Campos:
  - `id_propietario_producto`: FK a `propietarios_productos.id`
  - `id_tamano`: FK a `menu_productos_tamanos.id`
  - `precio`: Precio del tamaño para este producto
  - `estado`: 1=Activo, 0=Inactivo

#### `propietarios_ingredientes`
- **Tabla clave** para configuración de ingredientes por producto
- Campos:
  - `id_propietario_producto`: FK a `propietarios_productos.id`
  - `id_ingrediente`: FK a `menu_ingredientes.id`
  - `cantidad_gratis`: Cantidad incluida sin costo adicional
  - `precio`: Precio por unidad adicional
  - `costo_extra`: 'Si' o 'No' - indica si permite agregar extras con costo
  - `estado`: 1=Activo, 0=Inactivo

### 1.3 Tablas de Pedidos/Carrito

#### `ventas_carrito`
- Carrito activo del cliente
- Un carrito por usuario, asociado a una cafetería

#### `ventas_carrito_items`
- Items del carrito
- Campos: `id_producto`, `id_tamano`, `cantidad`

#### `ventas_carrito_items_ingredientes`
- Ingredientes seleccionados para cada item del carrito
- Campos: `id_carrito_item`, `id_ingrediente`, `cantidad`

---

## 2. Flujo: Propietario Arma el Menú

### 2.1 Activación de Subcategorías

**Proceso:**
1. El propietario ve todas las subcategorías disponibles del catálogo global
2. Activa/desactiva subcategorías mediante checkbox
3. Al activar, se crea registro en `propietarios_menu_subcategorias` con `estado=1`
4. Al desactivar, se actualiza `estado=0`

**Lógica:**
- Si `id_registro = 'No'`: Se crea nuevo registro
- Si `id_registro` existe: Se cambia estado (toggle)

### 2.2 Activación de Productos

**Proceso:**
1. El propietario ve productos de las subcategorías activadas
2. Activa/desactiva productos mediante checkbox
3. Al activar, se crea registro en `propietarios_productos` con:
   - `id_cafeteria = 0` (configuración general del propietario)
   - `precio_base = '0'` (inicial)
   - `estado = 1`

**Lógica especial:**
- Si el producto es nuevo y no tiene ingredientes ni tamaños configurados:
  - El sistema busca un producto similar (misma categoría/subcategoría) que tenga configuración
  - Si encuentra uno y es bebida: copia automáticamente ingredientes y tamaños
  - Muestra notificación al propietario

### 2.3 Configuración de Precios

#### Para Bebidas (con tamaños):
1. Propietario hace clic en botón "Editar tamaños" del producto
2. Se muestra modal con todos los tamaños disponibles del catálogo global
3. Para cada tamaño:
   - Switch para activar/desactivar
   - Input para precio
   - Al activar tamaño: se crea/actualiza registro en `propietarios_productos_tamanos`
   - Precio se guarda en campo `precio` de esta tabla

#### Para Alimentos (sin tamaños):
1. Propietario hace clic en botón "Editar precio" del producto
2. Se muestra modal con input de precio
3. Se guarda en `propietarios_productos.precio_base`

### 2.4 Configuración de Ingredientes

**Proceso:**
1. Propietario hace clic en botón "Editar ingredientes" del producto
2. Se muestran categorías de ingredientes filtradas según tipo de producto:
   - Si es bebida: solo categorías con `para_bebidas = 'Si'`
   - Si es alimento: solo categorías con `para_alimentos = 'Si'`
3. Para cada categoría, se muestran ingredientes disponibles:
   - Ingredientes OCCU (`registro_occu = 1`)
   - Ingredientes del propietario (`registro_occu = 2` y `id_alta = id_propietario`)

**Configuración por ingrediente:**
- **Checkbox de activación**: Activa/desactiva el ingrediente para el producto
- **Checkbox "Costo extra"**: Indica si permite agregar extras con costo adicional
- **Porción incluida** (`cantidad_gratis`): Cantidad que viene incluida sin costo
- **Precio**: Precio por unidad adicional

**Lógica de guardado:**
- Al activar ingrediente: se crea registro en `propietarios_ingredientes` con:
  - `cantidad_gratis = 0` (inicial)
  - `precio = 0` o precio sugerido (busca precio usado en otros productos del propietario)
  - `costo_extra = 'No'`
  - `estado = 1`
- Al marcar "Costo extra": se actualiza `costo_extra = 'Si'`
- Al cambiar cantidad gratis o precio: se actualiza registro

**Regla importante:**
- Si se desactiva un ingrediente (`estado = 0`), automáticamente se desactiva `costo_extra = 'No'`

### 2.5 Publicación a Sucursales

**Opciones de publicación:**
1. **Actualizar menú completo**: Copia toda la configuración del propietario (`id_cafeteria = 0`) a todas las cafeterías
2. **Actualizar menú de sucursal**: Copia configuración a una cafetería específica
3. **Actualizar solo precios**: Actualiza solo precios manteniendo productos activos

**Proceso de copia:**
1. Se eliminan registros existentes de la cafetería (productos, tamaños, ingredientes)
2. Se copian productos activos del propietario (`id_cafeteria = 0`) a la cafetería
3. Se copian tamaños activos de cada producto
4. Se copian ingredientes activos de cada producto

---

## 3. Flujo: Cliente Visualiza y Hace Pedidos

### 3.1 Visualización del Menú Público

**Estructura:**
- Se muestran categorías → subcategorías → productos
- Solo se muestran productos activos (`propietarios_productos.estado = 1`) de la cafetería específica

### 3.2 Selección de Producto

**Al hacer clic en un producto:**
1. Se abre offcanvas con detalles del producto
2. Se carga información:
   - Nombre, imagen, descripción
   - Tamaños disponibles (si es bebida)
   - Ingredientes organizados por categorías

### 3.3 Selección de Tamaño (Bebidas)

**Proceso:**
- Se muestran tamaños activos (`propietarios_productos_tamanos.estado = 1`)
- Cada tamaño muestra: nombre, unidad de medida, precio
- Selección obligatoria mediante radio buttons
- Al seleccionar tamaño: se actualiza precio base del producto

### 3.4 Selección de Ingredientes

**Estructura de visualización:**

#### Por Categoría de Ingredientes:
1. **Título de categoría** con badge "Obligatorio" si aplica
2. **Sección de selección única** (radio buttons):
   - Muestra todos los ingredientes activos de la categoría
   - Solo se puede seleccionar UN ingrediente por categoría
   - Si el ingrediente tiene `cantidad_gratis > 0`: viene incluido
   - Si el ingrediente tiene `precio > 0` y `cantidad_gratis = 0`: se cobra desde el inicio
   - Badge muestra precio si aplica: "+MX$X.XX"

3. **Sección de extras** (solo si aplica):
   - Solo se muestra si hay ingredientes con `cantidad_gratis > 0` y `precio > 0`
   - Permite agregar porciones adicionales con botones +/-
   - Muestra contador de cantidad extra
   - Cada porción extra se cobra según `precio`

**Lógica de cálculo de precio:**
- Ingrediente seleccionado con precio y sin cantidad gratis: se cobra inmediatamente
- Ingrediente seleccionado con cantidad gratis: no se cobra la cantidad incluida
- Porciones extra: se cobran según cantidad seleccionada

**Validación:**
- Categorías obligatorias: deben tener un ingrediente seleccionado
- Tamaño (bebidas): debe estar seleccionado

### 3.5 Agregar al Carrito

**Proceso:**
1. Se valida que todas las opciones obligatorias estén seleccionadas
2. Se prepara objeto con:
   - `id_producto`
   - `id_tamano` (0 si es alimento)
   - `cantidad` (cantidad del producto)
   - `ingredientes`: array con `id` y `cantidad` de cada ingrediente

**Cálculo de cantidad de ingredientes:**
- Para ingrediente seleccionado en radio:
  - `cantidad_total = cantidad_gratis + cantidad_extras`
  - Si `cantidad_gratis = 0`: `cantidad_total = 1 + cantidad_extras`
- Para ingredientes solo en sección de extras (no seleccionados en radio):
  - `cantidad_total = cantidad_extras`

**Detección de items repetidos:**
- El sistema busca si ya existe un item en el carrito con:
  - Mismo `id_producto`
  - Mismo `id_tamano`
  - Mismos ingredientes (mismo `id` y misma `cantidad`)
- Si encuentra coincidencia exacta: aumenta cantidad del item existente
- Si no encuentra: crea nuevo item

### 3.6 Visualización del Carrito

**Información mostrada por item:**
- Nombre del producto
- Tamaño (si aplica)
- Lista de ingredientes con cantidades
- Cantidad del producto
- Precio unitario (precio base + costo de ingredientes)
- Precio total (precio unitario × cantidad)

**Cálculo de precio en carrito:**
- Precio base: precio del tamaño (bebidas) o `precio_base` (alimentos)
- Costo de ingredientes:
  - Para cada ingrediente en el item:
    - Si `cantidad_gratis > 0`:
      - Solo se cobra la cantidad que excede `cantidad_gratis`
      - Se aplica solo al primer ingrediente de su categoría (menor ID)
    - Si `cantidad_gratis = 0`:
      - Se cobra toda la cantidad × precio
- Precio unitario = precio base + costo ingredientes
- Precio total = precio unitario × cantidad del producto

---

## 4. Reglas de Negocio Importantes

### 4.1 Jerarquía de Configuración

1. **Configuración general del propietario** (`id_cafeteria = 0`):
   - Base de configuración
   - Se puede copiar a cafeterías específicas

2. **Configuración por cafetería** (`id_cafeteria > 0`):
   - Sobrescribe configuración general
   - Permite personalización por sucursal

### 4.2 Estados

- **Productos**: 1=Activo, 0=Inactivo, 2=Eliminado
- **Tamaños**: 1=Activo, 0=Inactivo
- **Ingredientes**: 1=Activo, 0=Inactivo
- **Carrito**: 0=Activo, 2=Procesado/Eliminado

### 4.3 Reglas de Ingredientes

1. **Cantidad gratis**:
   - Si `cantidad_gratis > 0`: esa cantidad viene incluida sin costo
   - Las porciones adicionales se cobran según `precio`

2. **Costo extra**:
   - Si `costo_extra = 'Si'`: permite agregar extras con costo
   - Requiere que `cantidad_gratis > 0` y `precio > 0`

3. **Categorías obligatorias**:
   - Si una categoría es obligatoria, el cliente DEBE seleccionar un ingrediente
   - Se valida antes de agregar al carrito

4. **Selección única por categoría**:
   - Solo se puede seleccionar UN ingrediente por categoría mediante radio buttons
   - Los extras permiten agregar más porciones del mismo ingrediente o de otros

### 4.4 Reglas de Precios

1. **Bebidas**:
   - Precio viene del tamaño seleccionado (`propietarios_productos_tamanos.precio`)
   - Si no hay tamaños activos: no se puede agregar al carrito

2. **Alimentos**:
   - Precio viene de `propietarios_productos.precio_base`
   - No requiere selección de tamaño

### 4.5 Reglas de Carrito

1. **Un carrito activo por usuario**:
   - Solo puede haber un carrito con `estado = 0` por usuario
   - Si intenta agregar producto de otra cafetería:
     - Si carrito anterior está vacío: se reemplaza automáticamente
     - Si carrito anterior tiene productos: se pregunta si desea eliminar y continuar

2. **Items repetidos**:
   - Se detectan por coincidencia exacta de producto, tamaño e ingredientes
   - Si coincide: se aumenta cantidad
   - Si no coincide: se crea nuevo item

---

## 5. Áreas de Oportunidad Identificadas

### 5.1 Base de Datos

#### Problemas:
1. **Campo `precio_base` como TEXT**:
   - Debería ser `DECIMAL(10,2)` para mejor manejo de precios
   - Actualmente se almacena como texto, puede causar problemas de cálculo

2. **Falta de índices**:
   - No se ven índices explícitos en consultas frecuentes
   - Tablas como `propietarios_productos` se consultan constantemente

3. **Normalización de `cantidad_gratis`**:
   - El campo `cantidad_gratis` en `propietarios_ingredientes` puede ser confuso
   - No está claro si es "cantidad incluida" o "cantidad gratis adicional"

4. **Falta de timestamps**:
   - No hay campos `fecha_modificacion` en tablas de configuración
   - Dificulta auditoría y debugging

#### Mejoras sugeridas:
- Cambiar `precio_base` a `DECIMAL(10,2)`
- Agregar índices en:
  - `propietarios_productos(id_propietario, id_cafeteria, estado)`
  - `propietarios_productos_tamanos(id_propietario_producto, estado)`
  - `propietarios_ingredientes(id_propietario_producto, estado)`
- Agregar campos de auditoría (`fecha_modificacion`, `id_modificacion`)

### 5.2 Lógica de Negocio

#### Problemas:
1. **Cálculo complejo de ingredientes**:
   - La lógica de `cantidad_gratis` aplicada solo al primer ingrediente de categoría es confusa
   - Puede causar inconsistencias si hay múltiples ingredientes de la misma categoría

2. **Copia automática de configuración**:
   - La copia automática de ingredientes/tamaños puede no ser deseada siempre
   - No hay opción de desactivar esta funcionalidad

3. **Validación de precios**:
   - No hay validación de que precios sean mayores a 0
   - No hay validación de que cantidad_gratis no sea negativa

4. **Manejo de ingredientes desactivados**:
   - Si un ingrediente se desactiva después de estar en carritos activos, puede causar problemas
   - No hay validación de consistencia

#### Mejoras sugeridas:
- Simplificar lógica de `cantidad_gratis`: aplicar siempre, no solo al primero
- Agregar validaciones de negocio:
  - Precios > 0
  - Cantidades >= 0
  - Verificar ingredientes activos antes de mostrar
- Agregar opción para desactivar copia automática

### 5.3 Interfaz de Usuario

#### Problemas:
1. **Confusión en selección de ingredientes**:
   - La diferencia entre selección única y extras no está clara para el usuario
   - No se muestra claramente qué cantidad viene incluida

2. **Falta de feedback visual**:
   - No se muestra claramente el precio total mientras se selecciona
   - No está claro cuánto cuesta cada opción antes de seleccionar

3. **Validación tardía**:
   - Las validaciones se hacen al intentar agregar al carrito
   - Sería mejor validar en tiempo real

#### Mejoras sugeridas:
- Mejorar UI para mostrar claramente:
  - Cantidad incluida vs cantidad extra
  - Precio de cada opción antes de seleccionar
  - Total actualizado en tiempo real
- Agregar validación en tiempo real de opciones obligatorias

### 5.4 Rendimiento

#### Problemas:
1. **Consultas múltiples**:
   - Se hacen múltiples consultas para obtener ingredientes por categoría
   - Se podría optimizar con una sola consulta con JOINs

2. **Carga de datos**:
   - Se cargan todos los ingredientes aunque no estén activos
   - Se podría filtrar en la consulta inicial

#### Mejoras sugeridas:
- Optimizar consultas con JOINs apropiados
- Agregar caché para datos que no cambian frecuentemente (catálogo global)
- Implementar paginación si hay muchos productos

### 5.5 Escalabilidad

#### Problemas:
1. **Copia de configuración**:
   - El proceso de copiar configuración a todas las cafeterías puede ser lento
   - No hay proceso asíncrono para operaciones grandes

2. **Búsqueda de productos similares**:
   - La búsqueda de productos similares puede ser costosa con muchos productos
   - No hay límite en la búsqueda

#### Mejoras sugeridas:
- Implementar procesos asíncronos para operaciones grandes
- Agregar límites y optimizaciones en búsquedas
- Considerar usar colas de trabajo para operaciones pesadas

---

## 6. Flujos Críticos Detallados

### 6.1 Flujo Completo: Propietario Configura Producto Bebida

1. Propietario activa subcategoría → `propietarios_menu_subcategorias`
2. Propietario activa producto → `propietarios_productos` (id_cafeteria=0)
3. Sistema busca producto similar → Si encuentra, copia ingredientes y tamaños
4. Propietario configura tamaños:
   - Activa tamaño 12 Oz → `propietarios_productos_tamanos` (precio=45.00)
   - Activa tamaño 16 Oz → `propietarios_productos_tamanos` (precio=50.00)
5. Propietario configura ingredientes:
   - Activa "Leche Entera" → `propietarios_ingredientes` (cantidad_gratis=1, precio=0)
   - Activa "Leche Deslactosada" → `propietarios_ingredientes` (cantidad_gratis=0, precio=5.00)
   - Marca "Costo extra" en "Leche Entera" → `propietarios_ingredientes` (costo_extra='Si')
   - Configura precio extra "Leche Entera" → `propietarios_ingredientes` (precio=3.00)
6. Propietario publica a cafetería → Se copia todo a `id_cafeteria=X`

### 6.2 Flujo Completo: Cliente Agrega Producto al Carrito

1. Cliente selecciona producto "Latte"
2. Sistema carga:
   - Tamaños: 12 Oz ($45), 16 Oz ($50)
   - Ingredientes categoría "Tipo de Leche":
     - Leche Entera (incluida 1, extra +$3)
     - Leche Deslactosada (+$5)
3. Cliente selecciona tamaño 16 Oz → precio_base = $50
4. Cliente selecciona "Leche Entera" → cantidad_gratis=1 incluida
5. Cliente agrega 2 extras de "Leche Entera" → extras = 2 × $3 = $6
6. Cliente selecciona cantidad producto = 2
7. Cálculo:
   - Precio unitario = $50 (tamaño) + $6 (extras) = $56
   - Precio total = $56 × 2 = $112
8. Sistema guarda en carrito:
   - `ventas_carrito_items`: id_producto, id_tamano=16 Oz, cantidad=2
   - `ventas_carrito_items_ingredientes`: id_ingrediente=Leche Entera, cantidad=3 (1 incluida + 2 extras)

---

## 7. Conclusiones

El sistema actual funciona correctamente pero tiene áreas de mejora en:
- **Normalización de datos**: Mejorar tipos de datos y agregar índices
- **Claridad de lógica**: Simplificar cálculos de ingredientes
- **UX**: Mejorar feedback visual y validaciones en tiempo real
- **Rendimiento**: Optimizar consultas y procesos pesados
- **Escalabilidad**: Considerar procesos asíncronos para operaciones grandes

La estructura general es sólida y permite flexibilidad, pero necesita optimizaciones para crecer eficientemente.
