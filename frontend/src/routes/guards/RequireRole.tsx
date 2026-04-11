import { Navigate } from 'react-router-dom'
import { useAuth } from '@/store/auth/AuthProvider'
import { getFallbackPathForRole } from '@/config/routeAccess'
import type { UserRole } from '@/config/routeAccess'

/**
 * Guard que requiere uno de los roles especificados
 * Si no hay sesión, redirige a /login
 * Si el rol no está permitido, redirige a la ruta de inicio según el rol del usuario
 */
export function RequireRole({
  children,
  allowedRoles,
}: {
  children: React.ReactNode
  allowedRoles: UserRole[]
}) {
  const { session } = useAuth()

  if (!session) {
    return <Navigate to="/login" replace />
  }

  const userRole = session.user.nivel as UserRole

  if (!allowedRoles.includes(userRole)) {
    // Redirige a la ruta de inicio según el rol del usuario (no a una ruta prohibida)
    const fallbackPath = getFallbackPathForRole(userRole)
    return <Navigate to={fallbackPath} replace />
  }

  return <>{children}</>
}
