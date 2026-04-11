import { requestJson } from '@/services/http/httpClient'
import type { CafeteriasMapaRepository } from './CafeteriasMapaRepository'
import type { CafeteriaMapItem, CafeteriasMapaFilters } from '../types'
import type { Paginated } from '../../cafeteriasLista/types'

/**
 * Implementación REST del repositorio de cafeterías para mapa
 * Reutiliza el endpoint GET /cafeterias que ahora incluye latitud/longitud
 */
export class RestCafeteriasMapaRepository implements CafeteriasMapaRepository {
  async getCafeteriasForMap(
    filters: CafeteriasMapaFilters & { page: number; pageSize: number }
  ): Promise<Paginated<CafeteriaMapItem>> {
    const params = new URLSearchParams()
    params.set('page', filters.page.toString())
    params.set('pageSize', filters.pageSize.toString())

    if (filters.q) params.set('q', filters.q)
    if (filters.ciudadId) params.set('ciudadId', filters.ciudadId.toString())
    if (filters.horario) params.set('horario', filters.horario)
    if (filters.servicioIds && filters.servicioIds.length > 0) {
      params.set('servicios', filters.servicioIds.join(','))
    }

    const response = await requestJson<{
      success: boolean
      data: {
        data: Array<{
          id: number
          nombre: string
          imagen: string
          direccion: string
          latitud: number | null
          longitud: number | null
          isOpenNow: boolean
          todayScheduleLabel: string
        }>
        page: number
        pageSize: number
        totalItems: number
        totalPages: number
      }
    }>(`/cafeterias?${params.toString()}`)

    const paginatedData = response.data
    return {
      items: paginatedData.data.map((item) => ({
        id: item.id,
        nombre: item.nombre,
        direccion: item.direccion,
        latitud: item.latitud,
        longitud: item.longitud,
        isOpenNow: item.isOpenNow,
        todayScheduleLabel: item.todayScheduleLabel,
        imagen: item.imagen,
      })),
      page: paginatedData.page,
      pageSize: paginatedData.pageSize,
      totalItems: paginatedData.totalItems,
      totalPages: paginatedData.totalPages,
    }
  }
}
