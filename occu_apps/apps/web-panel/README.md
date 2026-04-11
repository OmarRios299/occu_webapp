## web-panel (OCCU vNext)

### Requisitos
- Node.js + npm

### Config
Crear `.env` basándote en `.env.example`.

### Correr en local
Desde el root del monorepo:

```bash
npm install
npm run dev:panel
```

URL:
- `http://localhost:5174`

API:
- `VITE_API_BASE_URL` debe apuntar a `http://127.0.0.1:8011/api`

### Fuente de verdad
- `docs/FUNCTIONAL_MAP.md`
- `docs/DESIGN_SYSTEM.md`
- `docs/api-contracts/*`

