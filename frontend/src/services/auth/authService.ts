import type { ApiResponse } from '@/types/api'
import type { LoginRequest, LoginResponseData } from '@/types/auth'
import { requestJson } from '@/services/http/httpClient'

export async function login(input: LoginRequest): Promise<LoginResponseData> {
  const res = await requestJson<ApiResponse<LoginResponseData>>('/auth/login', {
    method: 'POST',
    body: input,
  })

  if (!res.success) {
    // No debería pasar si el backend usa status != 200 para errores,
    // pero lo mantenemos defensivo por compatibilidad futura.
    throw new Error(res.message)
  }

  return res.data
}

export async function logout(refreshToken: string): Promise<void> {
  await requestJson<ApiResponse<{ message: string }>>('/auth/logout', {
    method: 'POST',
    body: { refresh_token: refreshToken },
  })
}

