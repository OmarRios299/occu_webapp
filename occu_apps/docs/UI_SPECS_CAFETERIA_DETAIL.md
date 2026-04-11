## UI Specs — Detalle de cafetería (legacy parity)

Objetivo: que el Detalle (drawer/offcanvas) en vNext se vea **igual o muy parecido** al legacy definido en:
- `views/modules/cafeterias_lista/ver_cafeteria_contenido.php`

Regla: los agentes NO sustituyen esta UI por “una tabla” u otro layout. Es **cards + secciones**.

---

## 1) Contenedor (Offcanvas/Drawer)

### Header
- Fondo: estilo “unified header” (legacy usa header con icono y botón cerrar blanco).
- Contenido:
  - Icono taza (ej. `bi-cup-hot-fill`)
  - Título: “Información de Cafetería”
  - Botón cerrar (X) a la derecha

### Body
- Contenido **scrollable** dentro del drawer.
- Scrollbar estilizado (en legacy se personaliza para `.cafeteria-content-offcanvas` / `.offcanvas-body`).

---

## 2) Layout interno (orden exacto)

Dentro del drawer, renderizar en este orden:

1) **Galería**
   - Card tipo `modern-card` pero con `padding: 0` y overflow visible.
   - Contenedor donde se pinta galería (en vNext: componente `Gallery`).

2) **Información General** (card)
   - Card `modern-card info-card`
   - Header de la card:
     - icono en “chip” con fondo claro (legacy: icono con padding y radius ~12)
     - título: “Información General”
   - Lista de items (cada uno es una fila “info-item”):
     - Dirección
     - Teléfono (con feedback “¡Copiado!”)
     - Correo electrónico (con feedback “¡Copiado!”)
     - Servicios disponibles (texto/resumen)

3) **Botones de acción** (debajo del último info-item)
   - Contenedor `action-buttons` (flex, wrap, gap)
   - Botón primario: **Ir a Ubicación**
     - Estilo legacy: gradiente verde, hover levanta 2px y shadow verde.
   - Botón secundario: **Ver Menú**
     - Estilo legacy: outline con color principal, hover rellena principal y shadow rojo.

4) **Servicios** (sección)
   - Título con icono + “Servicios”
   - Grid/wrap de iconos:
     - Imagen ~98x98
     - Nombre debajo (font ~0.85rem, weight 600)

5) **Reseñas de Clientes** (sección tipo card)
   - Contenedor con fondo blanco, radius grande (~24), shadow suave.
   - Header:
     - Título + icono
     - Botón: **Agregar Reseña** (gradiente azul/tercero, hover shadow)
   - Formulario (oculto/visible):
     - Área gris clara con radius ~16
     - Textarea con focus ring del color principal
     - Acciones: Cancelar / Publicar
   - Lista de comentarios:
     - Item con fondo gris claro, radius ~16, padding ~1.5rem
   - Paginación:
     - Contenedor `pagination-modern` debajo de la lista

---

## 3) Estilo (valores clave extraídos del legacy)

### Cards principales (`modern-card`)
- Fondo: blanco
- Radius: **20px**
- Shadow: `0 5px 25px rgba(0,0,0,0.08)`
- Padding: **2rem**
- Hover: translateY(-2px) + shadow más fuerte

### “Info items” (filas)
- Hover: fondo claro + “bleed” lateral (legacy hace margin negativo y padding lateral), radius ~10

### Botones
- Base:
  - Padding vertical ~0.9rem
  - Radius: **12px**
  - Font weight: 600
- Ir a ubicación:
  - Gradiente verde (success)
- Ver menú:
  - Outline primary (2px)

### Servicios (iconos)
- Imagen: **98x98** `object-fit: contain`

### Reseñas
- Contenedor: radius **24px**, shadow suave
- Comment item: radius **16px**, padding **1.5rem**

---

## 4) Responsive (legacy)

Legacy tiene offcanvas desktop y mobile separados.
En vNext:
- Se mantiene el mismo **contenido y orden**.
- El drawer se adapta por breakpoints (mismo look, sin perder secciones).

---

## 5) Reglas de interacción

- “Ver menú” abre el overlay del menú **sin afectar** filtros/búsqueda de la lista (ver `UI_SPECS_OVERLAYS.md`).
- “Ir a ubicación” abre Google Maps (o link externo) con lat/lng si existen.
- Copiar teléfono/correo muestra feedback “¡Copiado!”.

