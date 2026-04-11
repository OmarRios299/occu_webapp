import { Navigate } from 'react-router-dom'
import { useAuth } from '@/store/auth/AuthProvider'

/**
 * Guard que requiere autenticación
 * Si no hay sesión, redirige a /login
 */
export function RequireAuth({ children }: { children: React.ReactNode }) {
  const { session } = useAuth()

  if (!session) {
    return <Navigate to="/login" replace />
  }

  return <>{children}</>
}
