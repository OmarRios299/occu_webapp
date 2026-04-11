/**
 * Interface del repositorio de cafeterías para mapa
 */
import type { CafeteriaMapItem, CafeteriasMapaFilters } from '../types'
import type { Paginated } from '../../cafeteriasLista/types'

export interface CafeteriasMapaRepository {
  /**
   * Obtiene cafeterías con coordenadas para el mapa
   */
  getCafeteriasForMap(filters: CafeteriasMapaFilters & { page: number; pageSize: number }): Promise<Paginated<CafeteriaMapItem>>
}
