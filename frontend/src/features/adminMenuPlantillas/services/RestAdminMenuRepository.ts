/**
 * Implementación REST del repositorio de administración de menú
 */
import { requestJson } from '@/services/http/httpClient'
import type { AdminMenuRepository } from './AdminMenuRepository'
import type {
  ProductoListItem,
  ProductoReglas,
  Paginated,
  GuardarReglasPayload,
  GuardarReglasResponse,
} from '../types'

type ApiPaginatedResponse<T> = {
  data: T[]
  page: number
  pageSize: number
  totalItems: number
  totalPages: number
}

export class RestAdminMenuRepository implements AdminMenuRepository {
  async getProductos(
    page: number,
    pageSize: number,
    token: string,
    q?: string
  ): Promise<Paginated<ProductoListItem>> {
    const params = new URLSearchParams()
    params.set('page', page.toString())
    params.set('pageSize', pageSize.toString())
    if (q) params.set('q', q)

    const response = await requestJson<{
      success: boolean
      data: ApiPaginatedResponse<ProductoListItem>
    }>(`/admin/menu/productos?${params.toString()}`, {
      token,
    })

    const paginatedData = response.data
    return {
      items: paginatedData.data,
      page: paginatedData.page,
      pageSize: paginatedData.pageSize,
      totalItems: paginatedData.totalItems,
      totalPages: paginatedData.totalPages,
    }
  }

  async getProductoReglas(productoId: number, token: string): Promise<ProductoReglas> {
    const response = await requestJson<{
      success: boolean
      data: ProductoReglas
    }>(`/admin/menu/productos/${productoId}/reglas`, {
      token,
    })

    return response.data
  }

  async guardarReglas(
    productoId: number,
    reglas: GuardarReglasPayload,
    token: string
  ): Promise<GuardarReglasResponse> {
    const response = await requestJson<{
      success: boolean
      data: GuardarReglasResponse
    }>(`/admin/menu/productos/${productoId}/reglas`, {
      method: 'PUT',
      body: reglas,
      token,
    })

    return response.data
  }
}
