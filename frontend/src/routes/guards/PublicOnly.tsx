import { Navigate } from 'react-router-dom'
import { useAuth } from '@/store/auth/AuthProvider'
import { getHomePathForRole } from '@/config/routeAccess'

/**
 * Guard para rutas públicas que no deben ser accesibles si ya hay sesión
 * Útil para /login: si ya estás autenticado, redirige a tu home según tu rol
 */
export function PublicOnly({ children }: { children: React.ReactNode }) {
  const { session } = useAuth()

  if (session) {
    const homePath = getHomePathForRole(session.user.nivel)
    return <Navigate to={homePath} replace />
  }

  return <>{children}</>
}
