## Auth

Base URL (local): `http://127.0.0.1:8011/api`

Headers:
- Privado: `Authorization: Bearer <access_token>`
- Body preferido: `Content-Type: application/json`

---

## POST /auth/register

**Roles**: público\n
**Body**:
- `nombre` string (required)
- `apellido` string (required)
- `correo` string (required)
- `contrasena` string (required)
- `nivel` string enum: `Cliente|Propietario|Administrador` (required)
- `ciudad` number (required, id_ciudad)

**200/201 OK**:
- `data.user_id` number
- `data.message` string

---

## POST /auth/verify-pin

**Body**:
- `correo` string
- `contrasena` string
- `pin` string (6 dígitos)

**OK**: emite tokens (ver login)

---

## POST /auth/resend-pin

**Body**:
- `correo` string

---

## POST /auth/login

**Body**:
- `correo` string (required)
- `contrasena` string (required)

**OK**:
- `data.access_token` string
- `data.access_token_expires_in` number (segundos)
- `data.refresh_token` string
- `data.refresh_token_expires_at` string (ISO datetime)
- `data.user.id` number
- `data.user.nivel` string

Errores relevantes:
- 422 `validation_error`: faltan campos
- 403 `verificacion`: cuenta no verificada
- 401 `invalido`: usuario/contraseña incorrectos

---

## POST /auth/refresh

**Body**:
- `refresh_token` string (required)

**OK**:
- `data.access_token` string
- `data.access_token_expires_in` number
- `data.refresh_token` string (rotado)
- `data.refresh_token_expires_at` string

---

## POST /auth/logout

**Body**: (depende de implementación; si no hace nada, debe responder OK)\n

---

## GET /auth/me

**Auth**: requerido\n
**OK**:
- `data.id` number
- `data.nombre` string|null
- `data.apellido` string|null
- `data.correo` string|null
- `data.nivel` string|null
- `data.verificado` string|null
- `data.ciudad` number|null
- `data.imagen` string|null

