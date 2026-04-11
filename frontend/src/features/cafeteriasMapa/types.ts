/**
 * Tipos del feature cafeteriasMapa
 */

export type CafeteriaMapItem = {
  id: number
  nombre: string
  direccion: string
  latitud: number | null
  longitud: number | null
  isOpenNow: boolean
  todayScheduleLabel: string
  imagen?: string
}

export type CafeteriasMapaFilters = {
  q?: string
  ciudadId?: number | null
  horario?: 'todos' | 'abierto'
  servicioIds?: number[]
}
