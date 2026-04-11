export type ApiSuccess<T> = { success: true; data: T }
export type ApiError = { success: false; errorCode: string; message: string; details?: unknown }
export type ApiResponse<T> = ApiSuccess<T> | ApiError

export type AuthTokens = {
  access_token: string
  access_token_expires_in: number
  refresh_token: string
  refresh_token_expires_at: string
  user?: { id: number; nivel: string }
}

export class ApiClient {
  constructor(private readonly baseUrl: string) {}

  async postForm<T>(path: string, body: Record<string, string>): Promise<ApiResponse<T>> {
    const res = await fetch(this.baseUrl + path, {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: new URLSearchParams(body),
    })
    return (await res.json()) as ApiResponse<T>
  }
}

