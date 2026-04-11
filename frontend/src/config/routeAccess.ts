/**
 * Configuración centralizada de acceso a rutas por nivel (rol)
 * Source of truth para permisos de rutas
 */

export type UserRole = 'Cliente' | 'Barista' | 'Propietario' | 'Administrador'

export type RouteAccessConfig = {
  public: string[]
  cliente: string[]
  barista: string[]
  propietario: string[]
  administrador: string[]
}

/**
 * Mapa de rutas accesibles por rol
 * Rutas públicas: accesibles sin sesión
 * Rutas por rol: requieren sesión y el rol correspondiente
 */
export const routeAccess: RouteAccessConfig = {
  // Rutas públicas (sin sesión requerida)
  public: [
    '/login',
    '/inicio',
    '/cafeterias_lista',
    '/cafeterias_mapa',
  ],

  // Rutas para Cliente
  cliente: [
    '/cafeterias_lista',
    '/cafeterias_mapa',
    '/mis_pedidos',
    '/carrito',
  ],

  // Rutas para Barista (mismas que Cliente por ahora)
  barista: [
    '/cafeterias_lista',
    '/cafeterias_mapa',
    '/mis_pedidos',
    '/carrito',
  ],

  // Rutas para Propietario
  propietario: [
    '/cafeterias',
    '/cafeterias/agregar',
    '/cafeterias/:id/editar',
    '/menu',
    '/menu/categorias',
    '/menu/productos',
  ],

  // Rutas para Administrador (incluye todo lo de Propietario + rutas admin)
  administrador: [
    '/cafeterias',
    '/cafeterias/agregar',
    '/cafeterias/:id/editar',
    '/menu',
    '/menu/categorias',
    '/menu/productos',
    '/admin/menu/productos',
    '/admin/menu/productos/:id/reglas',
  ],
}

/**
 * Obtiene las rutas permitidas para un rol específico
 */
export function getAllowedRoutesForRole(role: UserRole | null): string[] {
  if (!role) {
    return routeAccess.public
  }

  switch (role) {
    case 'Cliente':
      return [...routeAccess.public, ...routeAccess.cliente]
    case 'Barista':
      return [...routeAccess.public, ...routeAccess.barista]
    case 'Propietario':
      return [...routeAccess.public, ...routeAccess.propietario]
    case 'Administrador':
      return [...routeAccess.public, ...routeAccess.administrador]
    default:
      return routeAccess.public
  }
}

/**
 * Verifica si una ruta está permitida para un rol
 * Soporta rutas con parámetros dinámicos (ej: /cafeterias/:id/editar)
 */
export function isRouteAllowedForRole(
  path: string,
  role: UserRole | null
): boolean {
  const allowedRoutes = getAllowedRoutesForRole(role)

  // Verificación exacta
  if (allowedRoutes.includes(path)) {
    return true
  }

  // Verificación con parámetros dinámicos
  return allowedRoutes.some((allowedPath) => {
    // Convertir patrón con :param a regex
    const pattern = allowedPath.replace(/:[^/]+/g, '[^/]+')
    const regex = new RegExp(`^${pattern}$`)
    return regex.test(path)
  })
}

/**
 * Obtiene la ruta de inicio (home) según el nivel del usuario
 */
export function getHomePathForRole(nivel: string | null): string {
  if (!nivel) {
    // Público: redirige a lista de cafeterías (más útil que placeholder)
    return '/cafeterias_lista'
  }

  switch (nivel) {
    case 'Cliente':
    case 'Barista':
      // Cliente/Barista: lista de cafeterías
      return '/cafeterias_lista'
    case 'Propietario':
    case 'Administrador':
      // Propietario/Admin: gestión de cafeterías
      return '/cafeterias'
    default:
      return '/cafeterias_lista'
  }
}

/**
 * Obtiene una ruta segura de fallback cuando el acceso es denegado
 */
export function getFallbackPathForRole(nivel: string | null): string {
  return getHomePathForRole(nivel)
}
