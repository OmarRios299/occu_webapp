## Contratos API (vNext)

Regla: si un agente cambia un endpoint (URL, método, body, response), debe actualizar estos contratos en el mismo PR/commit.

Archivos:
- `auth.md`
- `cafeterias.md`
- `menu.md`
- `carrito.md`
- `owner.md`
- `admin-menu.md`

Convención de respuesta:
- OK: `{ "success": true, "data": ... }`
- Error: `{ "success": false, "errorCode": "...", "message": "...", "details": {...}? }`

