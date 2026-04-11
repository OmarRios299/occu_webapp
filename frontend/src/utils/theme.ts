import { createTheme } from '@mui/material/styles'
import { designTokens } from '@/theme/tokens'

export const appTheme = createTheme({
  palette: {
    mode: 'light',
    // Colores principales basados en tokens OCCU
    primary: {
      main: designTokens.colors.principal,
      light: designTokens.colors.sidebarLight,
      dark: designTokens.colors.sidebarDark,
    },
    secondary: {
      main: designTokens.colors.secundario,
    },
    tertiary: {
      main: designTokens.colors.tercero,
    },
    quaternary: {
      main: designTokens.colors.cuarto,
    },
    background: {
      default: designTokens.colors.bgBody,
      paper: designTokens.colors.blanco,
    },
    text: {
      primary: designTokens.colors.texto,
      secondary: designTokens.colors.gris,
    },
    // Colores semánticos adicionales
    error: {
      main: designTokens.colors.rojo,
    },
    warning: {
      main: designTokens.colors.amarillo,
    },
    info: {
      main: designTokens.colors.azulCielo,
    },
    success: {
      main: designTokens.colors.verde,
    },
  },
  typography: {
    fontFamily: designTokens.typography.fontFamily,
    h1: {
      fontFamily: designTokens.typography.fontFamily,
      fontWeight: 700,
    },
    h2: {
      fontFamily: designTokens.typography.fontFamily,
      fontWeight: 700,
    },
    h3: {
      fontFamily: designTokens.typography.fontFamily,
      fontWeight: 600,
    },
    h4: {
      fontFamily: designTokens.typography.fontFamily,
      fontWeight: 600,
    },
    h5: {
      fontFamily: designTokens.typography.fontFamily,
      fontWeight: 600,
    },
    h6: {
      fontFamily: designTokens.typography.fontFamily,
      fontWeight: 600,
    },
    body1: {
      fontFamily: designTokens.typography.fontFamily,
      fontWeight: 400,
    },
    body2: {
      fontFamily: designTokens.typography.fontFamily,
      fontWeight: 400,
    },
    button: {
      fontFamily: designTokens.typography.fontFamily,
      fontWeight: 600,
      textTransform: 'none',
    },
  },
  shape: {
    borderRadius: parseInt(designTokens.spacing.borderRadius.medium),
  },
  components: {
    MuiCssBaseline: {
      styleOverrides: {
        body: {
          backgroundColor: designTokens.colors.bgBody,
          color: designTokens.colors.texto,
          fontFamily: designTokens.typography.fontFamily,
        },
      },
    },
  },
})

// Extender el tema para incluir nuestros colores personalizados
declare module '@mui/material/styles' {
  interface Palette {
    tertiary: Palette['primary']
    quaternary: Palette['primary']
  }
  interface PaletteOptions {
    tertiary?: PaletteOptions['primary']
    quaternary?: PaletteOptions['primary']
  }
}

