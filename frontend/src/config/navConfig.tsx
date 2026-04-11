import React from 'react'
import CoffeeIcon from '@mui/icons-material/Coffee'
import LocationOnIcon from '@mui/icons-material/LocationOn'
import PersonIcon from '@mui/icons-material/Person'
import ListIcon from '@mui/icons-material/List'
import LogoutIcon from '@mui/icons-material/Logout'
import DashboardIcon from '@mui/icons-material/Dashboard'
import MenuBookIcon from '@mui/icons-material/MenuBook'
import StoreIcon from '@mui/icons-material/Store'

export type NavItem = {
  label: string
  path: string
  icon?: React.ReactNode
  children?: NavItem[] // Para dropdowns
  disabled?: boolean // Si es true, la ruta está deshabilitada (placeholder)
  comingSoon?: boolean // Si es true, muestra "Próximamente"
}

export type NavConfig = {
  public: NavItem[]
  clienteBarista: NavItem[]
  propietarioAdmin: NavItem[]
  administrador: NavItem[] // Navegación específica para Administrador (incluye rutas admin)
}

/**
 * Configuración de navegación por rol
 * Estructura lista para ser reemplazada por datos del backend cuando esté disponible
 */
export const navConfig: NavConfig = {
  // Navegación pública: sin "Inicio" ni "Ingresar" en nav (logo = home por rol, Ingresar solo esquina derecha)
  public: [
    {
      label: 'Cafeterías',
      path: '/cafeterias_lista',
      icon: <CoffeeIcon />,
    },
    {
      label: 'Ubicaciones',
      path: '/cafeterias_mapa',
      icon: <LocationOnIcon />,
    },
  ],

  // Navegación para Cliente/Barista
  clienteBarista: [
    {
      label: 'Cafeterías',
      path: '/cafeterias_lista',
      icon: <CoffeeIcon />,
    },
    {
      label: 'Ubicaciones',
      path: '/cafeterias_mapa',
      icon: <LocationOnIcon />,
    },
    {
      label: 'Cuenta',
      path: '#',
      icon: <PersonIcon />,
      children: [
        {
          label: 'Mis pedidos',
          path: '/mis_pedidos',
          icon: <ListIcon />,
          disabled: true,
          comingSoon: true,
        },
        {
          label: 'Cerrar sesión',
          path: '/salir',
          icon: <LogoutIcon />,
        },
      ],
    },
  ],

  // Navegación para Propietario
  propietarioAdmin: [
    {
      label: 'Mis cafeterías',
      path: '/cafeterias',
      icon: <StoreIcon />,
    },
    {
      label: 'Menú',
      path: '/menu',
      icon: <MenuBookIcon />,
      children: [
        {
          label: 'Menú General',
          path: '/menu',
        },
      ],
    },
    {
      label: 'Cuenta',
      path: '#',
      icon: <PersonIcon />,
      children: [
        {
          label: 'Cerrar sesión',
          path: '/salir',
          icon: <LogoutIcon />,
        },
      ],
    },
  ],

  // Navegación para Administrador (incluye todo lo de Propietario + rutas admin)
  administrador: [
    {
      label: 'Mis cafeterías',
      path: '/cafeterias',
      icon: <StoreIcon />,
    },
    {
      label: 'Menú',
      path: '/menu',
      icon: <MenuBookIcon />,
      children: [
        {
          label: 'Menú General',
          path: '/menu',
        },
      ],
    },
    {
      label: 'Admin OCCU',
      path: '#',
      icon: <DashboardIcon />,
      children: [
        {
          label: 'Plantillas de productos',
          path: '/admin/menu/productos',
        },
        {
          label: 'Categorías',
          path: '/admin/catalogos/menu/categorias',
        },
      ],
    },
    {
      label: 'Cuenta',
      path: '#',
      icon: <PersonIcon />,
      children: [
        {
          label: 'Cerrar sesión',
          path: '/salir',
          icon: <LogoutIcon />,
        },
      ],
    },
  ],
}

/**
 * Obtiene la configuración de navegación según el nivel del usuario
 */
export function getNavConfigForRole(nivel: string | null): NavItem[] {
  if (!nivel) {
    return navConfig.public
  }

  // Cliente y Barista comparten la misma navegación
  if (nivel === 'Cliente' || nivel === 'Barista') {
    return navConfig.clienteBarista
  }

  // Administrador tiene su propia navegación (incluye rutas admin)
  if (nivel === 'Administrador') {
    return navConfig.administrador
  }

  // Propietario usa navegación básica
  if (nivel === 'Propietario') {
    return navConfig.propietarioAdmin
  }

  // Fallback a público si el nivel no coincide
  return navConfig.public
}
