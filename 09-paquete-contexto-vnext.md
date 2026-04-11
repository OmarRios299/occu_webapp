## Paquete de contexto (para llevarte al proyecto vNext)

Como el proyecto vNext va en **otra ruta/repositorio**, estos archivos son el “kit” para que cualquier agente tenga contexto completo sin improvisar.

### Documentos base (obligatorios)
- `06-arquitectura-2-apps-react-tailwind.md`
- `07-mapa-funcional-por-app.md`
- `02-especificacion-funcional-mvp.md` (fuente de verdad del MVP)
- `03-ingenieria-inversa-menu.md` (referencia legacy del menú)
- `04-plan-menu-nuevo-occu.md` (modelo v2 de reglas + snapshot)
- `08-runbook-dev-local-vnext.md` (cómo correr todo en local)

### Artefactos técnicos a copiar tal cual
- `api/` (backend Slim)
- `bd/` (migraciones/seeds)
- `dev-router.php` (para correr API con PHP built-in server)

### Convenciones clave (resumen)
- **Barista**: eliminado por el momento (no existe como rol).
- **2 web apps**:
  - Cliente (público + Cliente)
  - Panel (Propietario + Administrador)
- **API base local**: `http://127.0.0.1:8010/api`
- **Regla de estructura de API**: rutas por Feature (ver `api/AGENTS.md`), `App.php` no debe crecer en rutas.
- **Estados/soft delete**: `estado` con `0=activo,1=inactivo,2=eliminado`.

