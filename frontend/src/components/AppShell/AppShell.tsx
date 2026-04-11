import React from 'react'
import { Box, useTheme, useMediaQuery } from '@mui/material'
import { designTokens } from '@/theme/tokens'
import { ResponsiveNavbar } from '../ResponsiveNavbar/ResponsiveNavbar'

type AppShellProps = {
  children: React.ReactNode
}

/**
 * AppShell que maneja el layout responsive:
 * - Padding superior para top navbar (desktop)
 * - Padding inferior para bottom nav (móvil)
 * - Padding superior para top logo bar (móvil)
 */
export function AppShell({ children }: AppShellProps) {
  const theme = useTheme()
  const isDesktop = useMediaQuery(theme.breakpoints.up('lg'))

  return (
    <Box
      sx={{
        minHeight: '100vh',
        backgroundColor: designTokens.colors.bgBody,
        // Desktop: padding top para navbar fijo
        // Mobile: padding top para logo bar + padding bottom para bottom nav
        paddingTop: {
          xs: designTokens.spacing.navbarHeight,
          lg: designTokens.spacing.navbarHeight,
        },
        // iOS safe area support incluido en paddingBottom
        paddingBottom: {
          xs: `calc(${designTokens.spacing.bottomNavHeight} + env(safe-area-inset-bottom))`,
          lg: 0,
        },
      }}
    >
      <ResponsiveNavbar />
      <Box component="main" sx={{ width: '100%' }}>
        {children}
      </Box>
    </Box>
  )
}
