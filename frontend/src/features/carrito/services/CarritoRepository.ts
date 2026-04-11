import type { Carrito, AgregarAlCarritoPayload, AgregarAlCarritoResponse } from '../types'

export interface CarritoRepository {
  agregarItem(payload: AgregarAlCarritoPayload): Promise<AgregarAlCarritoResponse>
  obtenerCarrito(cafeteriaId?: number): Promise<Carrito | null>
}
