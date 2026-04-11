/**
 * Implementación REST del repositorio de cafeterías
 * Consume endpoints de la API Slim
 */
import { requestJson } from '@/services/http/httpClient'
import type { CafeteriasListaRepository } from './CafeteriasListaRepository'
import type {
  CafeteriaListItem,
  CafeteriaDetail,
  CafeteriasListaFilters,
  Paginated,
  Servicio,
  Ciudad,
  Comentario,
} from '../types'

type ApiPaginatedResponse<T> = {
  data: T[]
  page: number
  pageSize: number
  totalItems: number
  totalPages: number
}

type ApiCafeteriaResponse = {
  id: number
  nombre: string
  imagen: string
  direccion: string
  isOpenNow: boolean
  todayScheduleLabel: string
}

export class RestCafeteriasListaRepository implements CafeteriasListaRepository {
  async getCafeterias(
    filters: CafeteriasListaFilters & { page: number; pageSize: number }
  ): Promise<Paginated<CafeteriaListItem>> {
    const params = new URLSearchParams()
    params.set('page', filters.page.toString())
    params.set('pageSize', filters.pageSize.toString())

    if (filters.q) params.set('q', filters.q)
    if (filters.ciudadId) params.set('ciudadId', filters.ciudadId.toString())
    if (filters.horario && filters.horario !== 'todos') params.set('horario', filters.horario)
    if (filters.servicioIds && filters.servicioIds.length > 0) {
      params.set('servicios', filters.servicioIds.join(','))
    }

    // La API devuelve { success: true, data: { data: [...], page, pageSize, totalItems, totalPages } }
    const response = await requestJson<{ success: boolean; data: ApiPaginatedResponse<ApiCafeteriaResponse> }>(
      `/cafeterias?${params.toString()}`
    )

    const paginatedData = response.data
    return {
      items: paginatedData.data,
      page: paginatedData.page,
      pageSize: paginatedData.pageSize,
      totalItems: paginatedData.totalItems,
      totalPages: paginatedData.totalPages,
    }
  }

  async getCafeteriaDetail(id: number): Promise<CafeteriaDetail> {
    const response = await requestJson<{ success: boolean; data: CafeteriaDetail }>(`/cafeterias/${id}`)
    return response.data
  }

  async getComentarios(cafeteriaId: number, page: number, pageSize: number): Promise<Paginated<Comentario>> {
    const params = new URLSearchParams()
    params.set('page', page.toString())
    params.set('pageSize', pageSize.toString())

    const response = await requestJson<{
      success: boolean
      data: {
        data: Comentario[]
        page: number
        pageSize: number
        totalItems: number
        totalPages: number
      }
    }>(`/cafeterias/${cafeteriaId}/comentarios?${params.toString()}`)

    const paginatedData = response.data
    return {
      items: paginatedData.data,
      page: paginatedData.page,
      pageSize: paginatedData.pageSize,
      totalItems: paginatedData.totalItems,
      totalPages: paginatedData.totalPages,
    }
  }

  async createComentario(cafeteriaId: number, comentario: string, token: string): Promise<void> {
    await requestJson<{ success: boolean }>(`/cafeterias/${cafeteriaId}/comentarios`, {
      method: 'POST',
      body: { comentario },
      token,
    })
  }

  async getServicios(): Promise<Servicio[]> {
    // La API devuelve { success: true, data: Servicio[] }
    const response = await requestJson<{ success: boolean; data: Servicio[] }>('/servicios')
    return response.data || []
  }

  async getCiudades(): Promise<Ciudad[]> {
    // La API devuelve { success: true, data: Ciudad[] }
    const response = await requestJson<{ success: boolean; data: Ciudad[] }>('/ciudades')
    return response.data || []
  }
}
