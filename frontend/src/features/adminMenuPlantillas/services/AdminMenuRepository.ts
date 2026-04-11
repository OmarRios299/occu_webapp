/**
 * Interfaz del repositorio de administración de menú
 */
import type {
  ProductoListItem,
  ProductoReglas,
  Paginated,
  GuardarReglasPayload,
  GuardarReglasResponse,
} from '../types'

export interface AdminMenuRepository {
  getProductos(
    page: number,
    pageSize: number,
    token: string,
    q?: string
  ): Promise<Paginated<ProductoListItem>>

  getProductoReglas(productoId: number, token: string): Promise<ProductoReglas>

  guardarReglas(
    productoId: number,
    reglas: GuardarReglasPayload,
    token: string
  ): Promise<GuardarReglasResponse>
}
