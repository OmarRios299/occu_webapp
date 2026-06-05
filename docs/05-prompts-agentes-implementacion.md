## Prompts para agentes — Implementación por fases (OCCU app nueva)

Este archivo contiene prompts listos para ejecutar **uno a uno** con agentes hasta completar todas las fases del plan:
- `c:\xampp\htdocs\OCCU\occu_webApp\04-plan-menu-nuevo-occu.md`

### Contexto común (incluir en todos los prompts)
- Workspace: `c:\xampp\htdocs\OCCU\occu_webApp`
- **NO** tocar la app legacy (PHP viejo). Todo es para **app nueva** (`api/` + `frontend/`).
- BD usa Phinx. Migraciones: `bd/migrations/`. Seeds: `bd/seeds/`.
- Ya existe parte de Fase 2 (Plantillas OCCU por producto):
  - Backend: `api/src/Repositories/MenuProductosRepository.php`, rutas en `api/src/App.php`:
    - `GET /admin/menu/productos`
    - `GET /admin/menu/productos/{id}/reglas`
    - `PUT /admin/menu/productos/{id}/reglas`
  - Frontend: `frontend/src/features/adminMenuPlantillas/*` con rutas:
    - `/admin/menu/productos`
    - `/admin/menu/productos/:id/reglas`
- Esquema nuevo de menú/personalización está en la migración:
  - `bd/migrations/20260207000000_menu_personalizacion_v2.php`

- **No hagas preguntas** salvo que encuentres una contradicción técnica bloqueante. Si falta algo menor, **propón y ejecuta** la solución documentando el cambio.
- **No tocar legacy** (`views/`, `controllers/`, `models/` viejos) ni reutilizar AJAX legacy. Todo debe salir de `api/` y renderizarse en `frontend/`.
- **Arquitectura limpia**:
  - Frontend: separar UI / controller hooks / services (HTTP) / types. Evitar lógica de negocio en handlers (`onClick`, `onChange`).
  - Backend: usar repositorios/servicios simples, validación en backend, y respuestas con `JsonResponder` (`success/data`).
- **TypeScript estricto**: tipos explícitos, evitar `any`, contratos API consistentes.
- **Seguridad/roles**: rutas y endpoints admin/owner deben ir protegidos con JWT y chequeo de rol (no solo “estar logueado”).
- **Cambios pequeños y verificables**: arreglar una causa raíz a la vez, sin sobre-ingeniería.
- **Todo en español**: labels, textos, mensajes de error, títulos.
- **Definición de terminado**: ver sección siguiente; el agente debe cerrar con checklist y pasos de prueba manual.

### Definición de “terminado” para cada prompt
Cada agente debe entregar:
- Archivos tocados (lista)
- Endpoints/rutas creadas (lista)
- Cómo probar manualmente (pasos)
- Criterios de aceptación verificados (checklist)
- Si aplica:
  - Resultado de compilación del frontend (`npm run build` o equivalente) o explicación si no pudo ejecutarse.
  - Evidencia de que los endpoints responden en formato `{ success, data }` y errores `{ success:false, message, errorCode }`.

---

## Prompt 1 — Fase 2A: Admin OCCU (CRUD de catálogos)

Pega también: **Contexto común** + **Reglas obligatorias**.

**Objetivo**: Implementar en la app nueva el mantenimiento de catálogos por Administrador:
- `menu_categorias`
- `menu_subcategorias`
- `menu_productos`
- `menu_productos_tamanos`
- `menu_ingredientes_categorias`
- `menu_ingredientes`

**Alcance**
- Solo app nueva (`api/` + `frontend/`).
- CRUD mínimo para pruebas (listar, crear, editar, activar/inactivar, eliminar soft).

**Backend (Slim, `api/`)**
- Crear repositorios por catálogo (o uno por dominio “MenuCatalogosRepository”) con CRUD.
- Agregar rutas protegidas por rol Administrador en `api/src/App.php` (usar `JwtAuthMiddleware` + verificación de rol):
  - `GET /admin/catalogos/menu/categorias`
  - `POST /admin/catalogos/menu/categorias`
  - `PUT /admin/catalogos/menu/categorias/{id}`
  - (lo mismo para subcategorias, productos, tamanos, categorias-ingredientes, ingredientes)
- Usar soft delete/estado:
  - `estado=0` activo, `estado=1` inactivo, `estado=2` eliminado (consistente con legacy).
- Validaciones:
  - nombres únicos (definir la regla por entidad, p.ej. subcategoría única dentro de su categoría).
  - no permitir referencias inválidas (FK lógicas).
- Respuestas con `JsonResponder` y status codes correctos (400 validación, 401 auth, 403 rol, 404 no existe).
- Guardados críticos con transacción (si hay más de una tabla afectada).

**Frontend (React, `frontend/`)**
- Crear feature `frontend/src/features/adminMenuCatalogos/` con páginas:
  - `AdminMenuCategoriasPage`
  - `AdminMenuSubcategoriasPage`
  - `AdminMenuProductosCrudPage`
  - `AdminMenuTamanosPage`
  - `AdminIngredientesCategoriasPage`
  - `AdminIngredientesPage`
- UI mínima (MUI): tabla + buscador + modal crear/editar + activar/inactivar + eliminar.
- Agregar rutas en `frontend/src/routes/AppRoutes.tsx` bajo rol Administrador.
- Agregar items en `frontend/src/config/navConfig.tsx` bajo “Admin OCCU”.
- No metas validaciones de negocio complejas en UI; muestra errores del backend de forma clara.

**Criterios de aceptación**
- Admin puede crear/editar/desactivar/eliminar cada entidad.
- El editor de plantillas (`adminMenuPlantillas`) sigue funcionando y consume estos catálogos.

**Cómo probar**
- Crear una categoría y subcategoría nuevas.
- Crear un producto en esa subcategoría.
- Crear una categoría de ingrediente y un ingrediente.
- Ver que aparecen al editar reglas del producto en `/admin/menu/productos/:id/reglas`.

---

## Prompt 2 — Fase 2B: Completar Plantillas OCCU (hardening)

Pega también: **Contexto común** + **Reglas obligatorias**.

**Objetivo**: Revisar y completar el módulo existente de plantillas OCCU para cumplir al 100% el plan.

**Tareas**
- Asegurar que `seleccion_maxima` soporta `null` correctamente en frontend y backend.
- Asegurar que `tipo_precio=fijo` fuerza `permite_cantidad=false` y `cantidad_maxima=1`.
- Asegurar que “recomendados” solo se **resaltan** (no autoselección).
- Mejorar UX del editor:
  - agregar ayuda contextual por categoría (Leche única, Jarabes múltiple con cantidad).
  - filtros/buscador en ingredientes.
- Asegurar mensajes de error claros si backend rechaza reglas.
- Garantizar que el guardado es atómico (transacción) y que la respuesta devuelve resumen de cambios.
- Evitar lógica de negocio en eventos de UI: centralizar en controller hook y services.

**Criterios de aceptación**
- Guardar reglas inválidas devuelve error explicativo.
- Guardar reglas válidas persiste y recarga correctamente.
- Seed `bd/seeds/MenuReglasEjemploSeed.php` se visualiza bien en el editor.

---

## Prompt 3 — Fase 3A: Propietario — Menú general (borrador)

Pega también: **Contexto común** + **Reglas obligatorias**.

**Objetivo**: Implementar “Menú general del propietario” en la app nueva, que NO afecta cafeterías hasta publicar.

**Requisitos**
- El propietario arma un menú base (productos activos + precios + reglas) independiente del catálogo OCCU.
- No se refleja en cafeterías hasta que use “Publicar”.
- Debe reutilizar (cuando aplique) componentes/validaciones del editor admin, sin duplicar lógica.

**Backend**
- Crear tablas (si aún no existen) para el menú general del propietario:
  - `propietarios_menu_productos`
  - `propietarios_menu_productos_tamanos`
  - `propietarios_menu_productos_reglas_ingredientes_categorias`
  - `propietarios_menu_productos_reglas_ingredientes`
- Endpoints protegidos Propietario:
  - `GET /owner/menu-general/productos`
  - `PUT /owner/menu-general/productos` (activar/desactivar, precios base)
  - `GET /owner/menu-general/productos/{id}/reglas`
  - `PUT /owner/menu-general/productos/{id}/reglas`
- Validar que el owner solo toca su menú y que el backend es fuente de verdad para reglas.

**Frontend**
- Implementar `/menu` (quitar placeholder) como “Menú general”.
- Vista tipo lista de subcategorías/productos para activar y editar:
  - precios (alimento/bebida por tamaño)
  - reglas por categoría/ingrediente (reutilizar componentes del editor admin cuando aplique)
- Mantener estado en URL cuando aplique (categoría seleccionada, búsqueda) para facilitar pruebas.

**Criterios de aceptación**
- Propietario puede cambiar menú general sin que cambie nada en menús de cafeterías.

---

## Prompt 4 — Fase 3B: Propietario — Menú por cafetería + publicación

Pega también: **Contexto común** + **Reglas obligatorias**.

**Objetivo**: Implementar menú independiente por cafetería y publicación desde menú general.

**Requisitos**
- Cada cafetería tiene su menú publicado e independiente:
  - puede tener productos que otra cafetería no
- Acciones:
  - Publicar TODO a una cafetería
  - Publicar TODO a varias cafeterías (selección múltiple)
  - Actualizar SOLO PRECIOS (sin tocar activaciones/reglas)

**Backend**
- Usar tablas:
  - `cafeterias_menu_productos*` y reglas `cafeterias_menu_productos_reglas_*` (ya en migración v2)
- Agregar tabla de auditoría:
  - `propietarios_menu_publicaciones` (registro de acción, tipo, cafeterías afectadas, fecha)
- Endpoints:
  - `POST /owner/menu-publicar` con payload:
    - `cafeteria_ids: number[]`
    - `modo: 'todo' | 'solo_precios'`
- Implementar copia/transacción y reglas del modo.
- Agregar “estado desactualizado” por cafetería (por versión/fecha de última publicación).
- Validación y autorización estricta: owner solo publica a cafeterías suyas; admin puede operar con bandera explícita si aplica.

**Frontend**
- En “Mis cafeterías” agregar botón “Menú de esta cafetería” y estado “Actualizado/Desactualizado”.
- Pantalla de publicación con selección de cafeterías y modo.

**Criterios de aceptación**
- Publicar todo reemplaza menú de cafetería según modo.
- “Solo precios” no cambia activaciones/reglas.

---

## Prompt 5 — Fase 4A: Cliente — Menú público por cafetería

Pega también: **Contexto común** + **Reglas obligatorias**.

**Objetivo**: Mostrar menú publicado por cafetería y permitir personalización según reglas.

**Backend**
- Endpoint público:
  - `GET /cafeterias/{id}/menu` (categorías/subcategorías/productos activos)
  - `GET /cafeterias/{id}/menu/productos/{productoId}`:
    - producto + tamaños activos
    - reglas por categoría
    - ingredientes con reglas (cantidad, incluido, precio, recomendado)
    - “agotado” debe bloquear ingrediente

**Frontend**
- UI en cafetería detalle para “Ver menú”.
- Al abrir producto:
  - categoría `unica` → radios
  - categoría `multiple` sin cantidad → checkbox
  - categoría `multiple` con cantidad → stepper 0..max por ingrediente
- Total en tiempo real (solo display; backend valida).
- Recomendados: solo resaltado.
- Accesibilidad y responsive: controles cómodos en móvil, sin depender de estilos legacy.

**Criterios de aceptación**
- Se cumplen escenarios canónicos 1,2,7,8 del plan.

---

## Prompt 6 — Fase 4B: Carrito + snapshot de personalizaciones

Pega también: **Contexto común** + **Reglas obligatorias**.

**Objetivo**: Agregar al carrito y guardar snapshot consistente.

**Backend**
- Endpoint:
  - `POST /carrito/items` (privado Cliente)
    - valida reglas (min/max, agotados, límites por ingrediente)
    - calcula precio por ingrediente aplicando `cantidad_incluida`
    - guarda snapshot en `ventas_carrito_items_personalizaciones`
- Al crear venta:
  - copiar snapshots a `ventas_items_personalizaciones`
- No duplicar cálculo en frontend: el backend debe validar y calcular el total final.

**Frontend**
- Integrar “Agregar al carrito” desde el menú.
- Render del carrito mostrando ingredientes y cantidades + totales.
- Merge de items: igualdad exacta por (producto,tamaño,selección+cantidades).

**Criterios de aceptación**
- Se cumplen escenarios canónicos 9 y 10.

---

## Prompt 7 — Fase 5: Pruebas y hardening (escenarios canónicos)

Pega también: **Contexto común** + **Reglas obligatorias**.

**Objetivo**: Verificar los 10 escenarios canónicos del plan con pruebas manuales y automatizadas mínimas.

**Tareas**
- Checklist de pruebas manuales.
- (Opcional) tests unitarios de validación y cálculo de total en backend.
- Revisión de índices y consultas N+1.
- Manejo de errores y mensajes claros.
- Documentar decisiones y contratos API finales para que móvil (Expo/RN) consuma igual.

**Criterios de aceptación**
- Los 10 escenarios pasan.
- No hay inconsistencias de total por cambios de reglas gracias a snapshot.

