/**
 * Tipos del feature misCafeterias
 * Basados en el comportamiento legacy para propietarios/administradores
 */

export type CafeteriaOwner = {
  id: number
  nombre: string
  imagen: string // URL absoluta o relativa
  direccion: string
  telefono: string
  correo?: string
  horario_apertura?: string
  horario_cierre?: string
  estado: number // 0 = activa, 1 = inactiva, 2 = eliminada (soft delete)
  ciudad?: string
  entidad_federativa?: string
  descripcion?: string
}

export type CafeteriaOwnerDetail = {
  id: number
  nombre: string
  imagen: string
  direccion: string
  telefono: string
  correo?: string
  horario_apertura?: string
  horario_cierre?: string
  horario_diferente?: 'SI' | 'NO'
  horarios_detallados?: Record<string, { apertura: string; cierre: string; cerrado: 'SI' | 'NO' }>
  estado: number
  id_ciudad?: number | null
  ciudad?: string
  entidad_federativa?: string
  latitud?: string
  longitud?: string
  descripcion?: string
}

export type CafeteriaImagen = {
  id: number
  id_cafeteria: number
  imagen: string // URL absoluta
  descripcion: string
  estado: number // 0 = activa, 1 = inactiva, 2 = eliminada
}
