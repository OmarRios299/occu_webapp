export type LoginRequest = {
  correo: string
  contrasena: string
}

export type AuthUser = {
  id: number
  nivel: string
}

export type LoginResponseData = {
  access_token: string
  access_token_expires_in: number
  refresh_token: string
  refresh_token_expires_at: string
  user: AuthUser
}

