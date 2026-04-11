/**
 * Interfaz del repositorio de menú general del propietario
 */
import type {
  Paginated,
  ProductoMenuGeneral,
  ProductosUpdatePayload,
  ProductoReglas,
  GuardarReglasPayload,
  GuardarReglasResponse,
} from '../types'

export interface PropietarioMenuGeneralRepository {
  getProductos(
    page: number,
    pageSize: number,
    token: string,
    q?: string,
    idSubcategoria?: number,
    estado?: number
  ): Promise<Paginated<ProductoMenuGeneral>>
  getProductoById(id: number, token: string): Promise<ProductoMenuGeneral>
  updateProductos(data: ProductosUpdatePayload, token: string): Promise<{ message: string; actualizados: number }>
  getProductoReglas(productoId: number, token: string): Promise<ProductoReglas>
  guardarReglas(productoId: number, reglas: GuardarReglasPayload, token: string): Promise<GuardarReglasResponse>
}
