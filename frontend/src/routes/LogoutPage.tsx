import { useEffect, useState } from 'react'
import { useNavigate } from 'react-router-dom'
import { useAuth } from '@/store/auth/AuthProvider'

/**
 * Página de logout que ejecuta logout() completo (backend + local) y redirige a login
 * Sin lógica de negocio en handlers, todo en el componente
 */
export function LogoutPage() {
  const { logout } = useAuth()
  const navigate = useNavigate()
  const [isLoggingOut, setIsLoggingOut] = useState(true)

  useEffect(() => {
    async function performLogout() {
      try {
        await logout()
      } catch (error) {
        // Si falla, igual redirigimos (el logout ya limpió localmente)
        console.warn('Error durante logout:', error)
      } finally {
        setIsLoggingOut(false)
        navigate('/login', { replace: true })
      }
    }
    performLogout()
  }, [logout, navigate])

  // Mientras cierra sesión, mostrar mensaje breve
  return (
    <div style={{ padding: '2rem', textAlign: 'center' }}>
      <p>{isLoggingOut ? 'Cerrando sesión...' : 'Redirigiendo...'}</p>
    </div>
  )
}
