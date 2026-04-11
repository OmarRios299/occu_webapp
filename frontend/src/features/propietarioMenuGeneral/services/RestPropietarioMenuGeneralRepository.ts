/**
 * Implementación REST del repositorio de menú general del propietario
 */
import { requestJson } from '@/services/http/httpClient'
import type { PropietarioMenuGeneralRepository } from './PropietarioMenuGeneralRepository'
import type {
  Paginated,
  ProductoMenuGeneral,
  ProductosUpdatePayload,
  ProductoReglas,
  GuardarReglasPayload,
  GuardarReglasResponse,
  ApiResponse,
  ApiPaginatedResponse,
} from '../types'

export class RestPropietarioMenuGeneralRepository implements PropietarioMenuGeneralRepository {
  async getProductos(
    page: number,
    pageSize: number,
    token: string,
    q?: string,
    idSubcategoria?: number,
    estado?: number
  ): Promise<Paginated<ProductoMenuGeneral>> {
    const params = new URLSearchParams()
    params.set('page', page.toString())
    params.set('pageSize', pageSize.toString())
    if (q) params.set('q', q)
    if (idSubcategoria !== undefined) params.set('id_subcategoria', idSubcategoria.toString())
    if (estado !== undefined) params.set('estado', estado.toString())

    const response = await requestJson<ApiResponse<ApiPaginatedResponse<ProductoMenuGeneral>>>(
      `/owner/menu-general/productos?${params.toString()}`,
      { token }
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

  async getProductoById(id: number, token: string): Promise<ProductoMenuGeneral> {
    const response = await requestJson<ApiResponse<ProductoMenuGeneral>>(
      `/owner/menu-general/productos/${id}`,
      { token }
    )
    return response.data
  }

  async updateProductos(data: ProductosUpdatePayload, token: string): Promise<{ message: string; actualizados: number }> {
    const response = await requestJson<ApiResponse<{ message: string; actualizados: number }>>(
      '/owner/menu-general/productos',
      {
        method: 'PUT',
        body: data,
        token,
      }
    )
    return response.data
  }

  async getProductoReglas(productoId: number, token: string): Promise<ProductoReglas> {
    const response = await requestJson<ApiResponse<ProductoReglas>>(
      `/owner/menu-general/productos/${productoId}/reglas`,
      { token }
    )
    return response.data
  }

  async guardarReglas(
    productoId: number,
    reglas: GuardarReglasPayload,
    token: string
  ): Promise<GuardarReglasResponse> {
    const response = await requestJson<ApiResponse<GuardarReglasResponse>>(
      `/owner/menu-general/productos/${productoId}/reglas`,
      {
        method: 'PUT',
        body: reglas,
        token,
      }
    )
    return response.data
  }
}
