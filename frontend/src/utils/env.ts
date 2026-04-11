function normalizeBaseUrl(value: string): string {
  const trimmed = value.trim()
  return trimmed.endsWith('/') ? trimmed.slice(0, -1) : trimmed
}

export function getApiBaseUrl(): string {
  const raw = import.meta.env.VITE_API_BASE_URL
  if (!raw) {
    // En dev, fallamos temprano para evitar "fetch a undefined/...".
    throw new Error(
      'Falta VITE_API_BASE_URL. Crea frontend/.env (o usa frontend/.env.example) con la URL base de la API.'
    )
  }
  return normalizeBaseUrl(raw)
}

