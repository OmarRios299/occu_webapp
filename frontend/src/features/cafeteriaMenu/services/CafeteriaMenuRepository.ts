import type { CategoriaMenu, ProductoDetalle } from '../types'

export interface CafeteriaMenuRepository {
  getMenu(cafeteriaId: number): Promise<CategoriaMenu[]>
  getProducto(cafeteriaId: number, productoId: number): Promise<ProductoDetalle>
}
