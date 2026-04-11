import { requestJson } from '@/services/http/httpClient'
import type { CarritoRepository } from './CarritoRepository'
import type { Carrito, AgregarAlCarritoPayload, AgregarAlCarritoResponse } from '../types'

/** Opción: función que devuelve el access token actual (para rutas que requieren JWT) */
export type GetAccessToken = () => string | null

export class RestCarritoRepository implements CarritoRepository {
  constructor(private getToken: GetAccessToken = () => null) {}

  async agregarItem(payload: AgregarAlCarritoPayload): Promise<AgregarAlCarritoResponse> {
    const token = this.getToken()
    const response = await requestJson<{ success: boolean; data: AgregarAlCarritoResponse }>(
      '/carrito/items',
      {
        method: 'POST',
        body: payload,
        ...(token ? { token } : {}),
      }
    )
    if (!response.success || !response.data) {
      throw new Error('Error al agregar al carrito')
    }
    return response.data
  }

  async obtenerCarrito(cafeteriaId?: number): Promise<Carrito | null> {
    const token = this.getToken()
    const url = cafeteriaId ? `/carrito?cafeteria_id=${cafeteriaId}` : '/carrito'
    const response = await requestJson<{ success: boolean; data: Carrito | null }>(url, {
      ...(token ? { token } : {}),
    })
    if (!response.success) {
      throw new Error('Error al obtener el carrito')
    }
    return response.data || null
  }
}
