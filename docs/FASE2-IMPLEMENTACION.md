# FASE 2 - Implementación Completada y Pendiente

## ✅ COMPLETADO

### 1. Modal de Servicios
- **Archivo**: `frontend/src/features/misCafeterias/components/ServiciosModal.tsx`
- **Funcionalidad**: Modal con grid de servicios, checkboxes preseleccionados, guardado de cambios
- **Endpoints Backend**:
  - `GET /owner/cafeterias/{id}/servicios` - Obtiene servicios con estado (registrado/no registrado)
  - `PUT /owner/cafeterias/{id}/servicios` - Actualiza servicios (reemplazo total)
- **Integración**: Integrado en `CafeteriaOwnerDrawer.tsx` con botón "Gestionar Servicios"

### 2. Repositorio Backend - Métodos de Servicios
- **Archivo**: `api/src/Repositories/OwnerCafeteriasRepository.php`
- **Métodos agregados**:
  - `getServiciosConEstado(int $cafeteriaId): array` - Obtiene servicios con indicador de registro
  - `updateServicios(int $cafeteriaId, array $servicioIds): bool` - Actualiza servicios (soft delete + insert)

---

## 🔄 PENDIENTE (Para completar FASE 2)

### 3. Formulario Crear/Editar Cafetería
**Archivo a crear**: `frontend/src/features/misCafeterias/components/CafeteriaForm.tsx`

**Campos requeridos** (basado en legacy):
- Nombre (requerido)
- Correo electrónico (opcional, validación de email)
- Teléfono (requerido)
- Dirección (requerido)
- Ciudad (select, requerido)
- Latitud/Longitud (desde LocationPickerModal)
- Horario simple (apertura/cierre) o detallado por día
- Descripción (textarea)
- Imagen principal (file upload)

**Validaciones**:
- Nombre único (validado en backend)
- Email único si se proporciona (validado en backend)
- Ciudad seleccionada
- Al menos un horario si es detallado

**Endpoints Backend necesarios**:
- `POST /owner/cafeterias` - Crear cafetería
- `PUT /owner/cafeterias/{id}` - Editar cafetería
- `GET /ciudades` - Ya existe (público)

### 4. Modal de Ubicación con Leaflet
**Archivo a crear**: `frontend/src/features/misCafeterias/components/LocationPickerModal.tsx`

**Funcionalidad** (basado en legacy `cafeterias.js`):
- Modal con mapa Leaflet
- Cargar polígono de ciudad seleccionada (desde `GET /ciudades/{id}` o similar)
- Click en mapa para colocar marker
- Validar que el punto esté dentro del polígono de la ciudad
- Reverse geocoding con Nominatim para obtener dirección
- Guardar lat/long + dirección

**Dependencias**: Ya instaladas (`leaflet`, `react-leaflet`)

**Endpoint Backend necesario**:
- `GET /ciudades/{id}` o incluir coordenadas en `GET /ciudades` (ya existe pero sin coordenadas)

### 5. Endpoints Backend CRUD
**Archivo**: `api/src/App.php`

**Endpoints a agregar**:
- `POST /owner/cafeterias` - Crear cafetería
  - Body: `{ nombre, correo_electronico?, telefono, direccion, id_ciudad, latitud, longitud, horario_apertura?, horario_cierre?, horarios_detallados?, descripcion?, imagen? }`
  - Validaciones: nombre único, email único si se proporciona
  - Retorna: `{ success: true, data: { id, ... } }`

- `PUT /owner/cafeterias/{id}` - Editar cafetería
  - Mismo body que POST
  - Validaciones: nombre único (excepto propia), email único (excepto propia)
  - Retorna: `{ success: true, data: { message: "..." } }`

**Métodos en OwnerCafeteriasRepository**:
- `create(array $data, int $userId): int` - Crea cafetería y retorna ID
- `update(int $cafeteriaId, array $data, int $userId, string $userLevel): bool` - Actualiza cafetería

### 6. Rutas y Navegación
**Archivo**: `frontend/src/routes/AppRoutes.tsx`

**Rutas a agregar**:
- `/cafeterias/agregar` - Crear nueva cafetería
- `/cafeterias/{id}/editar` - Editar cafetería existente

**Integración**:
- Botón "Agregar Cafetería" en `MisCafeteriasPage.tsx` ya navega a `/cafeterias/agregar`
- Botón "Editar Cafetería" en `CafeteriaOwnerDrawer.tsx` ya navega a `/cafeterias/{id}/editar`

---

## 📝 Notas de Implementación

### Estructura de Horarios
El legacy maneja dos tipos:
1. **Horario simple**: `horario_apertura` y `horario_cierre` en tabla `cafeterias`
2. **Horario detallado**: Tabla `cafeteria_horarios` con campos `dia`, `hora_apertura`, `hora_cierre`, `cerrado`

El campo `horario_diferente` en `cafeterias` indica si usa horario detallado ("SI") o simple ("NO").

### Manejo de Imágenes
- **Imagen principal**: Se sube al crear/editar y se guarda en campo `imagen` de `cafeterias`
- **Galería**: Tabla `cafeterias_imagenes` (FASE 2 opcional, puede dejarse para después)

### Validaciones Backend
- Nombre único: `SELECT COUNT(*) FROM cafeterias WHERE nombre = ? AND estado != 2 [AND id != ?]`
- Email único: `SELECT COUNT(*) FROM cafeterias WHERE correo_electronico = ? AND estado != 2 [AND id != ?]`

---

## 🎯 Próximos Pasos

1. Crear `LocationPickerModal.tsx` con Leaflet
2. Crear `CafeteriaForm.tsx` con todos los campos
3. Agregar métodos `create` y `update` en `OwnerCafeteriasRepository.php`
4. Agregar endpoints `POST` y `PUT` en `App.php`
5. Crear páginas `CrearCafeteriaPage.tsx` y `EditarCafeteriaPage.tsx`
6. Agregar rutas en `AppRoutes.tsx`
7. Integrar `LocationPickerModal` en `CafeteriaForm.tsx`

---

## ✅ Estado Actual

- ✅ Modal de servicios funcional
- ✅ Endpoints de servicios implementados
- ✅ Integración en drawer completa
- ⏳ Formulario crear/editar (pendiente)
- ⏳ Selector de ubicación Leaflet (pendiente)
- ⏳ Endpoints CRUD cafeterías (pendiente)
