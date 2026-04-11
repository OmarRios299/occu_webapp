/**
 * Tipos del feature cafeteriasLista
 * Basados en el comportamiento legacy
 */

export type CafeteriaListItem = {
  id: number
  nombre: string
  imagen: string // URL absoluta
  direccion: string
  isOpenNow: boolean
  todayScheduleLabel: string // ej: "08:00 - 18:00" o "Cerrado los Lunes"
}

export type CafeteriaDetail = {
  id: number
  nombre: string
  imagen: string
  direccion: string
  telefono: string
  correo: string
  descripcion: string
  ciudad: string
  entidad_federativa: string
  pais: string
  latitud: string
  longitud: string
  isOpenNow: boolean
  todayScheduleLabel: string
  imagenes: string[]
  servicios: Array<{
    id: number
    nombre: string
    imagen: string
  }>
}

export type Comentario = {
  id: number
  comentario: string
  nombre_usuario: string
  fecha_alta: string
}

export type CafeteriasListaFilters = {
  q?: string // búsqueda por texto
  ciudadId?: number | null
  horario?: 'todos' | 'abierto'
  servicioIds?: number[]
}

export type Paginated<T> = {
  items: T[]
  page: number
  pageSize: number
  totalItems: number
  totalPages: number
}

export type Servicio = {
  id: number
  nombre: string
}

export type Ciudad = {
  id: number
  nombre: string
}
