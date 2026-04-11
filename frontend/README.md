## Frontend (React + TypeScript + MUI)

### Objetivo
Frontend desacoplado del PHP, consumiendo la API Slim montada en `/api`.

### Estructura (obligatoria)
`src/`
- `features/`: módulos funcionales (pantallas + controllers/hooks por feature)
- `components/`: componentes reutilizables SOLO web
- `services/`: acceso a datos (API REST)
- `store/`: estado global (auth/sesión)
- `hooks/`: hooks reutilizables
- `types/`: tipos TypeScript
- `utils/`: helpers (sin lógica de negocio)

### Configuración
1) Crea `frontend/.env` a partir de `frontend/.env.example`:

```bash
VITE_API_BASE_URL=http://localhost/OCCU/occu_webApp/api
```

2) Instala y ejecuta:

```bash
npm install
npm run dev
```

### Nota backend
Reglas/validaciones finales viven en el backend. En el frontend solo se hace validación mínima de UX (por ejemplo campos requeridos) para evitar llamadas innecesarias.

