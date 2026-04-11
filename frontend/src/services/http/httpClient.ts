import { getApiBaseUrl } from '@/utils/env'
import type { ApiErrorResponse } from '@/types/api'
import { HttpError } from './HttpError'

export type HttpMethod = 'GET' | 'POST' | 'PUT' | 'PATCH' | 'DELETE'

export type RequestOptions = {
  method?: HttpMethod
  headers?: Record<string, string>
  body?: unknown
  token?: string
  signal?: AbortSignal
}

async function parseJsonSafely(response: Response): Promise<unknown | undefined> {
  const text = await response.text()
  if (!text) return undefined
  try {
    return JSON.parse(text) as unknown
  } catch {
    return undefined
  }
}

export async function requestJson<T>(path: string, options: RequestOptions = {}): Promise<T> {
  const baseUrl = getApiBaseUrl()
  const url = `${baseUrl}${path.startsWith('/') ? '' : '/'}${path}`

  const headers: Record<string, string> = {
    Accept: 'application/json',
    ...options.headers,
  }

  const method = options.method ?? 'GET'
  const hasBody = options.body !== undefined && method !== 'GET'
  if (hasBody) headers['Content-Type'] = headers['Content-Type'] ?? 'application/json'
  if (options.token) headers.Authorization = `Bearer ${options.token}`

  const init: RequestInit = {
    method,
    headers,
  }
  if (hasBody) init.body = JSON.stringify(options.body)
  if (options.signal) init.signal = options.signal

  const response = await fetch(url, init)

  const payload = (await parseJsonSafely(response)) as unknown

  if (!response.ok) {
    const apiError = (payload && typeof payload === 'object' ? (payload as ApiErrorResponse) : undefined)
    const params: { status: number; url: string; message: string; payload?: ApiErrorResponse } = {
      status: response.status,
      url,
      message: apiError?.message ?? `HTTP ${response.status}`,
    }
    if (apiError) params.payload = apiError
    throw new HttpError(params)
  }

  return payload as T
}

export type FormDataRequestOptions = {
  method?: 'POST' | 'PUT' | 'PATCH'
  token?: string
  signal?: AbortSignal
}

/**
 * Envía una petición con FormData (útil para subir archivos)
 * No establece Content-Type para que el navegador lo establezca automáticamente con el boundary correcto
 */
export async function requestFormData<T>(path: string, formData: FormData, options: FormDataRequestOptions = {}): Promise<T> {
  const baseUrl = getApiBaseUrl()
  const url = `${baseUrl}${path.startsWith('/') ? '' : '/'}${path}`

  const headers: Record<string, string> = {
    Accept: 'application/json',
    // NO establecer Content-Type - el navegador lo hará automáticamente con el boundary correcto
  }

  if (options.token) {
    headers.Authorization = `Bearer ${options.token}`
  }

  const method = options.method ?? 'POST'

  const init: RequestInit = {
    method,
    headers,
    body: formData,
  }

  if (options.signal) {
    init.signal = options.signal
  }

  const response = await fetch(url, init)

  const payload = (await parseJsonSafely(response)) as unknown

  if (!response.ok) {
    const apiError = (payload && typeof payload === 'object' ? (payload as ApiErrorResponse) : undefined)
    const params: { status: number; url: string; message: string; payload?: ApiErrorResponse } = {
      status: response.status,
      url,
      message: apiError?.message ?? `HTTP ${response.status}`,
    }
    if (apiError) params.payload = apiError
    throw new HttpError(params)
  }

  return payload as T
}

