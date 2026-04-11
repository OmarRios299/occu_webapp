import React, { createContext, useCallback, useContext, useEffect, useMemo, useState } from 'react'
import type { AuthUser, LoginResponseData } from '@/types/auth'
import * as authService from '@/services/auth/authService'

export type AuthSession = {
  accessToken: string
  accessTokenExpiresIn: number
  refreshToken: string
  refreshTokenExpiresAt: string
  user: AuthUser
}

type AuthContextValue = {
  session: AuthSession | null
  setSessionFromLogin: (data: LoginResponseData) => void
  logout: () => Promise<void>
  logoutLocal: () => void
}

const AuthContext = createContext<AuthContextValue | null>(null)

const STORAGE_KEY = 'occu_auth_session'

// Helper para restaurar sesión desde localStorage
function restoreSessionFromStorage(): AuthSession | null {
  try {
    const stored = localStorage.getItem(STORAGE_KEY)
    if (!stored) return null
    const parsed = JSON.parse(stored) as AuthSession
    // Validación básica de estructura
    if (
      parsed.accessToken &&
      parsed.refreshToken &&
      parsed.user &&
      typeof parsed.user.id === 'number'
    ) {
      return parsed
    }
    return null
  } catch {
    // Si hay error de parsing, limpiar el storage corrupto
    localStorage.removeItem(STORAGE_KEY)
    return null
  }
}

// Helper para guardar sesión en localStorage
function saveSessionToStorage(session: AuthSession | null): void {
  if (session) {
    localStorage.setItem(STORAGE_KEY, JSON.stringify(session))
  } else {
    localStorage.removeItem(STORAGE_KEY)
  }
}

export function AuthProvider(props: { children: React.ReactNode }) {
  // Inicializar con sesión restaurada desde localStorage
  const [session, setSession] = useState<AuthSession | null>(() => restoreSessionFromStorage())

  // Restaurar sesión al montar (por si acaso cambió en otra pestaña)
  useEffect(() => {
    const restored = restoreSessionFromStorage()
    if (restored && !session) {
      setSession(restored)
    }
  }, []) // eslint-disable-line react-hooks/exhaustive-deps

  const setSessionFromLogin = useCallback((data: LoginResponseData) => {
    const newSession: AuthSession = {
      accessToken: data.access_token,
      accessTokenExpiresIn: data.access_token_expires_in,
      refreshToken: data.refresh_token,
      refreshTokenExpiresAt: data.refresh_token_expires_at,
      user: data.user,
    }
    setSession(newSession)
    saveSessionToStorage(newSession)
  }, [])

  const logout = useCallback(async () => {
    const currentSession = session
    // Limpiar estado local primero (para UX inmediata)
    setSession(null)
    saveSessionToStorage(null)
    
    // Intentar revocar refresh_token en el backend
    // Si falla, no importa porque ya limpiamos localmente
    if (currentSession?.refreshToken) {
      try {
        await authService.logout(currentSession.refreshToken)
      } catch (error) {
        // Silenciar errores: si el backend falla, igual limpiamos localmente
        // para que el usuario pueda seguir usando la app
        console.warn('Error al revocar refresh token en logout:', error)
      }
    }
  }, [session])

  const logoutLocal = useCallback(() => {
    // Método legacy: solo limpia local sin llamar al backend
    // Mantenido por compatibilidad, pero preferir usar logout()
    setSession(null)
    saveSessionToStorage(null)
  }, [])

  const value = useMemo<AuthContextValue>(
    () => ({ session, setSessionFromLogin, logout, logoutLocal }),
    [session, setSessionFromLogin, logout, logoutLocal]
  )

  return <AuthContext.Provider value={value}>{props.children}</AuthContext.Provider>
}

export function useAuth(): AuthContextValue {
  const ctx = useContext(AuthContext)
  if (!ctx) throw new Error('useAuth debe usarse dentro de <AuthProvider>.')
  return ctx
}

