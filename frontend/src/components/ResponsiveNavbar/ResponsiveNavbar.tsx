import React, { useState } from 'react'
import { useNavigate, useLocation } from 'react-router-dom'
import {
  AppBar,
  Toolbar,
  Box,
  Button,
  IconButton,
  Menu,
  MenuItem,
  Badge,
  useTheme,
  useMediaQuery,
  BottomNavigation,
  BottomNavigationAction,
  Paper,
} from '@mui/material'
import { designTokens } from '@/theme/tokens'
import { useAuth } from '@/store/auth/AuthProvider'
import { getNavConfigForRole, type NavItem } from '@/config/navConfig'
import { getHomePathForRole } from '@/config/routeAccess'
import ShoppingCartIcon from '@mui/icons-material/ShoppingCart'
import PersonIcon from '@mui/icons-material/Person'
import LogoutIcon from '@mui/icons-material/Logout'
import ListIcon from '@mui/icons-material/List'

// Logo - intenta cargar desde la ruta pública, si no existe muestra texto
const Logo = () => {
  const [imgError, setImgError] = React.useState(false)
  
  if (imgError) {
    // Fallback: mostrar texto si la imagen no carga
    return (
      <Box
        sx={{
          color: 'white',
          fontWeight: 700,
          fontSize: '1.2rem',
          letterSpacing: 1,
        }}
      >
        OCCU
      </Box>
    )
  }
  
  return (
    <Box
      component="img"
      src="/logo_blanco.png"
      alt="OCCU Logo"
      sx={{
        height: { xs: 40, lg: 40 },
        width: 'auto',
        objectFit: 'contain',
      }}
      onError={() => setImgError(true)}
    />
  )
}

/**
 * Navbar responsive que muestra:
 * - Desktop (>= lg): Top navbar fijo con menú horizontal
 * - Mobile (< lg): Top bar con logo + Bottom nav fijo
 */
export function ResponsiveNavbar() {
  const theme = useTheme()
  const isDesktop = useMediaQuery(theme.breakpoints.up('lg'))
  const navigate = useNavigate()
  const location = useLocation()
  const { session } = useAuth()

  const navItems = getNavConfigForRole(session?.user.nivel || null)
  const [accountMenuAnchor, setAccountMenuAnchor] = useState<null | HTMLElement>(null)
  const [mobileAccountMenuAnchor, setMobileAccountMenuAnchor] = useState<null | HTMLElement>(null)
  // Manejar múltiples dropdowns con un objeto
  const [dropdownAnchors, setDropdownAnchors] = useState<Record<string, HTMLElement | null>>({})
  
  // Filtrar items para móvil (sin children o solo "Cuenta" con children)
  const mobileNavItems = navItems.filter((item) => {
    if (item.label === 'Cuenta' && item.children) return true
    return !item.children || item.children.length === 0
  })
  
  // Sincronizar valor del BottomNavigation con la ruta actual
  const getMobileNavValue = () => {
    const currentIndex = mobileNavItems.findIndex((item) => {
      if (item.path === '#') return false
      return location.pathname === item.path || location.pathname.startsWith(item.path + '/')
    })
    return currentIndex >= 0 ? currentIndex : 0
  }

  // Determinar si un item está activo
  const isActive = (path: string) => {
    if (path === '#') return false
    return location.pathname === path || location.pathname.startsWith(path + '/')
  }

  // Manejar click en item de navegación
  const handleNavClick = (item: NavItem) => {
    // No navegar si está deshabilitado
    if (item.disabled) {
      return
    }
    
    if (item.children && item.children.length > 0) {
      // Si tiene children, abrir dropdown (solo desktop)
      return
    }
    if (item.path === '/salir') {
      // Navegar a /salir que ejecuta logout completo (backend + local) y redirige a login
      navigate('/salir')
    } else {
      navigate(item.path)
    }
    setAccountMenuAnchor(null)
  }

  // Renderizar item de cuenta con dropdown (desktop)
  const renderAccountDropdown = () => {
    const accountItem = navItems.find((item) => item.label === 'Cuenta' && item.children)
    if (!accountItem || !accountItem.children) return null

    return (
      <>
        <Button
          onClick={(e) => setAccountMenuAnchor(e.currentTarget)}
          sx={{
            color: 'white',
            textTransform: 'none',
            '&:hover': { backgroundColor: 'rgba(255,255,255,0.1)' },
          }}
          startIcon={<PersonIcon />}
        >
          {session?.user.nivel || 'Usuario'}
        </Button>
        <Menu
          anchorEl={accountMenuAnchor}
          open={Boolean(accountMenuAnchor)}
          onClose={() => setAccountMenuAnchor(null)}
          anchorOrigin={{ vertical: 'bottom', horizontal: 'right' }}
          transformOrigin={{ vertical: 'top', horizontal: 'right' }}
        >
          {accountItem.children.map((child) => (
            <MenuItem
              key={child.path}
              onClick={() => handleNavClick(child)}
              disabled={child.disabled}
              sx={{ 
                color: designTokens.colors.principal,
                opacity: child.disabled ? 0.6 : 1,
              }}
            >
              {child.icon && <Box sx={{ mr: 1 }}>{child.icon}</Box>}
              {child.label}
              {child.comingSoon && (
                <Box component="span" sx={{ ml: 1, fontSize: '0.75rem', fontStyle: 'italic' }}>
                  (Próximamente)
                </Box>
              )}
            </MenuItem>
          ))}
        </Menu>
      </>
    )
  }

  // Desktop Navbar (top)
  if (isDesktop) {
    return (
      <AppBar
        position="fixed"
        sx={{
          backgroundColor: designTokens.colors.sidebar,
          height: designTokens.spacing.navbarHeight,
          zIndex: theme.zIndex.drawer + 1,
        }}
      >
        <Toolbar
          sx={{
            display: 'flex',
            justifyContent: 'space-between',
            alignItems: 'center',
            height: '100%',
            px: 2,
          }}
        >
          {/* Logo y navegación principal */}
          <Box sx={{ display: 'flex', alignItems: 'center', gap: 2 }}>
            <IconButton
              onClick={() => navigate(getHomePathForRole(session?.user.nivel ?? null))}
              sx={{ p: 0, '&:hover': { backgroundColor: 'transparent' } }}
              aria-label="Ir al inicio"
            >
              <Logo />
            </IconButton>

            {/* Items de navegación sin children (sin Inicio: logo lleva a home por rol) */}
            {navItems
              .filter((item) => !item.children || item.children.length === 0)
              .filter((item) => item.label !== 'Cuenta')
              .map((item) => (
                <Button
                  key={item.path}
                  onClick={() => handleNavClick(item)}
                  disabled={item.disabled}
                  sx={{
                    color: 'white',
                    textTransform: 'none',
                    borderRadius: 1,
                    px: 1.5,
                    opacity: item.disabled ? 0.6 : 1,
                    '&:hover': { backgroundColor: item.disabled ? 'transparent' : 'rgba(255,255,255,0.1)' },
                    ...(isActive(item.path) && !item.disabled && {
                      backgroundColor: 'rgba(255,255,255,0.2)',
                    }),
                  }}
                  startIcon={item.icon}
                >
                  {item.label}
                  {item.comingSoon && (
                    <Box component="span" sx={{ ml: 0.5, fontSize: '0.7rem', fontStyle: 'italic' }}>
                      (Próximamente)
                    </Box>
                  )}
                </Button>
              ))}

            {/* Dropdowns (áreas con children) */}
            {navItems
              .filter((item) => item.children && item.children.length > 0 && item.label !== 'Cuenta')
              .map((item) => (
                <Box key={item.path}>
                  <Button
                    onClick={(e) =>
                      setDropdownAnchors((prev) => ({
                        ...prev,
                        [item.path]: e.currentTarget,
                      }))
                    }
                    sx={{
                      color: 'white',
                      textTransform: 'none',
                      borderRadius: 1,
                      px: 1.5,
                      '&:hover': { backgroundColor: 'rgba(255,255,255,0.1)' },
                    }}
                    endIcon={<Box sx={{ ml: 0.5 }}>▼</Box>}
                  >
                    {item.label}
                  </Button>
                  <Menu
                    anchorEl={dropdownAnchors[item.path] || null}
                    open={Boolean(dropdownAnchors[item.path])}
                    onClose={() =>
                      setDropdownAnchors((prev) => ({
                        ...prev,
                        [item.path]: null,
                      }))
                    }
                  >
                    {item.children!.map((child) => (
                      <MenuItem
                        key={child.path}
                        onClick={() => {
                          handleNavClick(child)
                          setDropdownAnchors((prev) => ({
                            ...prev,
                            [item.path]: null,
                          }))
                        }}
                        disabled={child.disabled}
                        sx={{
                          opacity: child.disabled ? 0.6 : 1,
                        }}
                      >
                        {child.label}
                        {child.comingSoon && (
                          <Box component="span" sx={{ ml: 1, fontSize: '0.75rem', fontStyle: 'italic' }}>
                            (Próximamente)
                          </Box>
                        )}
                      </MenuItem>
                    ))}
                  </Menu>
                </Box>
              ))}
          </Box>

          {/* Carrito y cuenta (derecha) */}
          <Box sx={{ display: 'flex', alignItems: 'center', gap: 1 }}>
            {/* Carrito solo para cliente/barista */}
            {session && (session.user.nivel === 'Cliente' || session.user.nivel === 'Barista') && (
              <IconButton
                onClick={() => navigate('/carrito')}
                sx={{
                  color: 'white',
                  '&:hover': { backgroundColor: 'rgba(255,255,255,0.1)' },
                }}
              >
                <Badge badgeContent={0} color="error">
                  <ShoppingCartIcon />
                </Badge>
              </IconButton>
            )}

            {/* Dropdown de cuenta o botón de login */}
            {session ? (
              renderAccountDropdown()
            ) : (
              <Button
                onClick={() => navigate('/login')}
                variant="outlined"
                sx={{
                  color: 'white',
                  borderColor: 'white',
                  textTransform: 'none',
                  '&:hover': {
                    borderColor: 'white',
                    backgroundColor: 'white',
                    color: designTokens.colors.principal,
                  },
                }}
              >
                Ingresar
              </Button>
            )}
          </Box>
        </Toolbar>
      </AppBar>
    )
  }

  // Mobile: Top bar con logo + Bottom nav
  return (
    <>
      {/* Top bar con logo (móvil) */}
      <AppBar
        position="fixed"
        sx={{
          top: 0,
          backgroundColor: designTokens.colors.sidebar,
          height: designTokens.spacing.navbarHeight,
          zIndex: theme.zIndex.drawer + 1,
        }}
      >
        <Toolbar
          sx={{
            display: 'flex',
            justifyContent: 'space-between',
            alignItems: 'center',
            height: '100%',
            px: 2,
          }}
        >
          <Box sx={{ width: 40 }} /> {/* Espaciador izquierdo */}
          <IconButton
            onClick={() => navigate(getHomePathForRole(session?.user.nivel ?? null))}
            sx={{ p: 0, '&:hover': { backgroundColor: 'transparent' } }}
            aria-label="Ir al inicio"
          >
            <Logo />
          </IconButton>
          <Box sx={{ display: 'flex', alignItems: 'center', gap: 1 }}>
            {/* Carrito solo para cliente/barista */}
            {session && (session.user.nivel === 'Cliente' || session.user.nivel === 'Barista') && (
              <IconButton
                onClick={() => navigate('/carrito')}
                sx={{
                  color: 'white',
                  '&:hover': { backgroundColor: 'rgba(255,255,255,0.1)' },
                }}
              >
                <Badge badgeContent={0} color="error">
                  <ShoppingCartIcon />
                </Badge>
              </IconButton>
            )}
            {/* Ingresar solo en esquina derecha (no en bottom nav) */}
            {!session && (
              <Button
                onClick={() => navigate('/login')}
                variant="outlined"
                size="small"
                sx={{
                  color: 'white',
                  borderColor: 'white',
                  textTransform: 'none',
                  '&:hover': {
                    borderColor: 'white',
                    backgroundColor: 'white',
                    color: designTokens.colors.principal,
                  },
                }}
              >
                Ingresar
              </Button>
            )}
          </Box>
        </Toolbar>
      </AppBar>

      {/* Bottom Navigation (móvil) */}
      <Paper
        sx={{
          position: 'fixed',
          bottom: 0,
          left: 0,
          right: 0,
          zIndex: theme.zIndex.drawer + 1,
          backgroundColor: designTokens.colors.sidebar,
          borderTopLeftRadius: designTokens.spacing.borderRadius.large,
          borderTopRightRadius: designTokens.spacing.borderRadius.large,
          boxShadow: '0 -2px 10px rgba(0,0,0,0.15)',
          paddingBottom: 'env(safe-area-inset-bottom)', // iOS safe area
        }}
        elevation={3}
      >
        <BottomNavigation
          value={getMobileNavValue()}
          onChange={(_, newValue) => {
            const item = mobileNavItems[newValue]
            if (item) {
              if (item.children && item.children.length > 0) {
                // Si es "Cuenta" con children, abrir menú desde abajo
                // Usar un timeout para obtener el elemento después del render
                setTimeout(() => {
                  const accountButton = document.querySelector(`[aria-label="${item.label}"]`) as HTMLElement
                  if (accountButton) {
                    setMobileAccountMenuAnchor(accountButton)
                  }
                }, 0)
              } else {
                handleNavClick(item)
              }
            }
          }}
          sx={{
            backgroundColor: 'transparent',
            '& .MuiBottomNavigationAction-root': {
              color: 'rgba(255,255,255,0.7)',
              minWidth: 70,
              paddingTop: 1,
              paddingBottom: 1,
              '&.Mui-selected': {
                color: 'white',
              },
            },
          }}
        >
          {mobileNavItems.map((item) => (
            <BottomNavigationAction
              key={item.path}
              label={item.label}
              icon={item.icon}
            />
          ))}
        </BottomNavigation>
        
        {/* Menu de cuenta para móvil (desde abajo) */}
        {(() => {
          const accountItem = mobileNavItems.find((item) => item.label === 'Cuenta' && item.children)
          if (!accountItem || !accountItem.children) return null
          
          return (
            <Menu
              anchorEl={mobileAccountMenuAnchor}
              open={Boolean(mobileAccountMenuAnchor)}
              onClose={() => setMobileAccountMenuAnchor(null)}
              anchorOrigin={{ vertical: 'top', horizontal: 'center' }}
              transformOrigin={{ vertical: 'bottom', horizontal: 'center' }}
              sx={{
                '& .MuiPaper-root': {
                  borderTopLeftRadius: designTokens.spacing.borderRadius.large,
                  borderTopRightRadius: designTokens.spacing.borderRadius.large,
                },
              }}
            >
              {accountItem.children.map((child) => (
                <MenuItem
                  key={child.path}
                  onClick={() => {
                    handleNavClick(child)
                    setMobileAccountMenuAnchor(null)
                  }}
                  disabled={child.disabled}
                  sx={{ 
                    color: designTokens.colors.principal,
                    opacity: child.disabled ? 0.6 : 1,
                  }}
                >
                  {child.icon && <Box sx={{ mr: 1 }}>{child.icon}</Box>}
                  {child.label}
                  {child.comingSoon && (
                    <Box component="span" sx={{ ml: 1, fontSize: '0.75rem', fontStyle: 'italic' }}>
                      (Próximamente)
                    </Box>
                  )}
                </MenuItem>
              ))}
            </Menu>
          )
        })()}
      </Paper>
    </>
  )
}
