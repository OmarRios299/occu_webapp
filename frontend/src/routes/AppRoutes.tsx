import { Routes, Route, Navigate } from 'react-router-dom'
import { useAuth } from '@/store/auth/AuthProvider'
import { getHomePathForRole } from '@/config/routeAccess'
import { RequireAuth, RequireRole, PublicOnly } from './guards'
import { LogoutPage } from './LogoutPage'

// Páginas de features
import { LoginPage } from '@/features/auth/pages/LoginPage'
import { CafeteriasListaPage } from '@/features/cafeteriasLista/pages/CafeteriasListaPage'
import { CafeteriasMapaPage } from '@/features/cafeteriasMapa/pages/CafeteriasMapaPage'
import { CafeteriaMenuPage } from '@/features/cafeteriaMenu/pages/CafeteriaMenuPage'
import { CarritoPage } from '@/features/carrito/pages/CarritoPage'
import { MisCafeteriasPage } from '@/features/misCafeterias/pages/MisCafeteriasPage'
import { CrearCafeteriaPage } from '@/features/misCafeterias/pages/CrearCafeteriaPage'
import { EditarCafeteriaPage } from '@/features/misCafeterias/pages/EditarCafeteriaPage'
import { AdminMenuProductosPage } from '@/features/adminMenuPlantillas/pages/AdminMenuProductosPage'
import { AdminProductoReglasPage } from '@/features/adminMenuPlantillas/pages/AdminProductoReglasPage'
import { AdminMenuCategoriasPage } from '@/features/adminMenuCatalogos/pages/AdminMenuCategoriasPage'
import { MenuGeneralPage } from '@/features/propietarioMenuGeneral/pages/MenuGeneralPage'
import { ProductoReglasPage } from '@/features/propietarioMenuGeneral/pages/ProductoReglasPage'

// Páginas placeholder (temporal hasta que se implementen)
const PlaceholderPage = ({ title }: { title: string }) => (
  <div style={{ padding: '2rem', textAlign: 'center' }}>
    <h1>{title}</h1>
    <p>Página en construcción</p>
  </div>
)

/**
 * Componente para redirección dinámica según el rol del usuario
 */
function HomeRedirect() {
  const { session } = useAuth()
  const homePath = getHomePathForRole(session?.user.nivel ?? null)
  return <Navigate to={homePath} replace />
}

/**
 * Componente para manejar rutas no encontradas (404)
 * Redirige a la ruta de inicio según el rol del usuario
 */
function NotFoundRedirect() {
  const { session } = useAuth()
  const homePath = getHomePathForRole(session?.user.nivel ?? null)
  return <Navigate to={homePath} replace />
}

export function AppRoutes() {
  return (
    <Routes>
      {/* Rutas públicas */}
      <Route
        path="/login"
        element={
          <PublicOnly>
            <LoginPage />
          </PublicOnly>
        }
      />
      <Route path="/inicio" element={<HomeRedirect />} />
      <Route
        path="/cafeterias_lista"
        element={<CafeteriasListaPage />}
      />
      <Route
        path="/cafeterias_mapa"
        element={<CafeteriasMapaPage />}
      />
      <Route
        path="/cafeterias/:id/menu"
        element={<CafeteriaMenuPage />}
      />

      {/* Rutas protegidas - Cliente/Barista */}
      <Route
        path="/mis_pedidos"
        element={
          <RequireRole allowedRoles={['Cliente', 'Barista']}>
            <PlaceholderPage title="Mis Pedidos" />
          </RequireRole>
        }
      />
      <Route
        path="/carrito"
        element={
          <RequireRole allowedRoles={['Cliente', 'Barista']}>
            <CarritoPage />
          </RequireRole>
        }
      />

      {/* Rutas protegidas - Propietario/Admin */}
      <Route
        path="/cafeterias"
        element={
          <RequireRole allowedRoles={['Propietario', 'Administrador']}>
            <MisCafeteriasPage />
          </RequireRole>
        }
      />
      <Route
        path="/cafeterias/agregar"
        element={
          <RequireRole allowedRoles={['Propietario', 'Administrador']}>
            <CrearCafeteriaPage />
          </RequireRole>
        }
      />
      <Route
        path="/cafeterias/:id/editar"
        element={
          <RequireRole allowedRoles={['Propietario', 'Administrador']}>
            <EditarCafeteriaPage />
          </RequireRole>
        }
      />
      <Route
        path="/menu"
        element={
          <RequireRole allowedRoles={['Propietario', 'Administrador']}>
            <MenuGeneralPage />
          </RequireRole>
        }
      />
      <Route
        path="/menu/productos/:id/reglas"
        element={
          <RequireRole allowedRoles={['Propietario', 'Administrador']}>
            <ProductoReglasPage />
          </RequireRole>
        }
      />
      <Route
        path="/menu/categorias"
        element={
          <RequireRole allowedRoles={['Propietario', 'Administrador']}>
            <PlaceholderPage title="Categorías" />
          </RequireRole>
        }
      />
      <Route
        path="/menu/productos"
        element={
          <RequireRole allowedRoles={['Propietario', 'Administrador']}>
            <PlaceholderPage title="Productos" />
          </RequireRole>
        }
      />

      {/* Rutas protegidas - Solo Administrador (Admin OCCU) */}
      <Route
        path="/admin/menu/productos"
        element={
          <RequireRole allowedRoles={['Administrador']}>
            <AdminMenuProductosPage />
          </RequireRole>
        }
      />
      <Route
        path="/admin/menu/productos/:id/reglas"
        element={
          <RequireRole allowedRoles={['Administrador']}>
            <AdminProductoReglasPage />
          </RequireRole>
        }
      />
      <Route
        path="/admin/catalogos/menu/categorias"
        element={
          <RequireRole allowedRoles={['Administrador']}>
            <AdminMenuCategoriasPage />
          </RequireRole>
        }
      />

      {/* Ruta de logout */}
      <Route path="/salir" element={<LogoutPage />} />

      {/* Default redirect - redirige según el rol */}
      <Route path="/" element={<HomeRedirect />} />
      <Route path="*" element={<NotFoundRedirect />} />
    </Routes>
  )
}
