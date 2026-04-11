/**
 * Tipos para el módulo de publicación de menú a cafeterías
 */

export type ModoPublicacion = 'todo' | 'solo_precios'

export interface PublicarMenuPayload {
  cafeteria_ids: number[]
  modo: ModoPublicacion
}

export interface PublicarMenuResponse {
  message: string
  cafeterias_publicadas: number
  productos_publicados: number
}

export interface EstadoActualizacionCafeteria {
  fecha_ultima_publicacion: string | null
  desactualizado: boolean
}

export interface EstadosActualizacion {
  [cafeteriaId: number]: EstadoActualizacionCafeteria
}

export interface ApiResponse<T> {
  success: boolean
  data: T
}
