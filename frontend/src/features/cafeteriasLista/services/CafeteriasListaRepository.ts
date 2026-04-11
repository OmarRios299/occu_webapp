/**
 * Interface del repositorio de cafeterías
 * Permite intercambiar implementaciones (REST vs Legacy)
 */
import type {
  CafeteriaListItem,
  CafeteriaDetail,
  CafeteriasListaFilters,
  Paginated,
  Servicio,
  Ciudad,
  Comentario,
} from '../types'

export interface CafeteriasListaRepository {
  /**
   * Obtiene cafeterías paginadas con filtros
   */
  getCafeterias(filters: CafeteriasListaFilters & { page: number; pageSize: number }): Promise<Paginated<CafeteriaListItem>>

  /**
   * Obtiene detalles completos de una cafetería
   */
  getCafeteriaDetail(id: number): Promise<CafeteriaDetail>

  /**
   * Obtiene comentarios paginados de una cafetería
   */
  getComentarios(cafeteriaId: number, page: number, pageSize: number): Promise<Paginated<Comentario>>

  /**
   * Registra un comentario (requiere autenticación)
   */
  createComentario(cafeteriaId: number, comentario: string, token: string): Promise<void>

  /**
   * Obtiene lista de servicios disponibles
   */
  getServicios(): Promise<Servicio[]>

  /**
   * Obtiene lista de ciudades disponibles
   */
  getCiudades(): Promise<Ciudad[]>
}
