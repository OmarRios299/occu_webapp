/**
 * Tokens de diseño del sistema OCCU
 * Extraídos de views/assets/css/css/style.css (:root)
 * Única fuente de verdad para colores y estilos
 */

export const designTokens = {
  // Colores principales del proyecto
  colors: {
    sidebar: '#f05540', // --color-sidebar
    sidebarLight: '#f16c5b', // --color-claro-sidebar
    sidebarDark: '#f16c5b', // --color-oscuro-sidebar
    principal: '#f16c5b', // --principal
    plantillaClaro: '#fcece6', // --plantilla-claro
    secundario: '#fedd64', // --secundario
    tercero: '#3E51A0', // --tercero
    cuarto: '#10343f', // --cuarto
    
    // Fondos
    bgBody: '#ebeef6', // --bg-body
    bgPrincipalClaro: '#d8e7ec', // --bg-principal-claro
    bgSecundarioClaro: '#fff7da', // --bg-secundario-claro
    bgTerceroClaro: '#d5ddff', // --bg-tercero-claro
    bgCuartoClaro: '#d6f2fa', // --bg-cuarto-claro
    
    // Colores para textos
    texto: '#50535f', // --color-texto
    label: '#ccc', // --color-label
    blanco: '#fff', // --color-blanco
    negro: '#000', // --color-negro
    
    // Colores de la plantilla (semánticos)
    rosa: '#ff58b2',
    rojo: '#ff585b',
    rojoOscuro: '#ef4635',
    verde: '#03cd82',
    verdeClaro: '#c1e358',
    verdeOscuro: '#528368',
    amarillo: '#ffc00f',
    naranja: '#ff9949',
    verdeAzulado: '#20c997',
    azul: '#3b5cfc',
    azulClaro: '#53d4f9',
    azulCielo: '#0fa1f6',
    azulMarino: '#23376d',
    indigo: '#586bfe',
    morado: '#8158fe',
    lila: '#cc4bff',
    grisOscuro: '#50535f',
    gris: '#869499',
    grisClaro: '#edf1f3',
    transparente: 'transparent',
  },
  
  // Tipografía
  typography: {
    fontFamily: "'Poppins', sans-serif",
    fontFamilyMonospace: "'Inconsolata', monospace",
  },
  
  // Medidas y espaciados
  spacing: {
    borderRadius: {
      small: '6px',
      medium: '10px',
      large: '20px',
    },
    navbarHeight: '60px',
    bottomNavHeight: '70px', // Aproximado con padding
  },
  
  // Breakpoints (alineados con Bootstrap lg = 992px)
  breakpoints: {
    mobile: 0,
    tablet: 768,
    desktop: 992, // lg breakpoint
  },
} as const

// Helper para obtener colores con fallback
export const getColor = (key: keyof typeof designTokens.colors): string => {
  return designTokens.colors[key]
}
