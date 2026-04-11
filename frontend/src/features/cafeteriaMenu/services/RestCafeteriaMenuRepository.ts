import { requestJson } from '@/services/http/httpClient'
import type { CafeteriaMenuRepository } from './CafeteriaMenuRepository'
import type { CategoriaMenu, ProductoDetalle } from '../types'

export class RestCafeteriaMenuRepository implements CafeteriaMenuRepository {
  async getMenu(cafeteriaId: number): Promise<CategoriaMenu[]> {
    const response = await requestJson<{ success: boolean; data: CategoriaMenu[] }>(
      `/cafeterias/${cafeteriaId}/menu`
    )
    if (!response.success || !response.data) {
      throw new Error('Error al obtener el menú')
    }
    return response.data
  }

  async getProducto(cafeteriaId: number, productoId: number): Promise<ProductoDetalle> {
    const response = await requestJson<{ success: boolean; data: ProductoDetalle }>(
      `/cafeterias/${cafeteriaId}/menu/productos/${productoId}`
    )
    if (!response.success || !response.data) {
      throw new Error('Error al obtener el producto')
    }
    return response.data
  }
}
