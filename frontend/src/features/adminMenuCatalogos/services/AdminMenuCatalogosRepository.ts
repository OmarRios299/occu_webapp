/**
 * Interfaz del repositorio de catálogos de menú
 */
import type {
  Paginated,
  Categoria,
  CategoriaCreatePayload,
  CategoriaUpdatePayload,
  Subcategoria,
  SubcategoriaCreatePayload,
  SubcategoriaUpdatePayload,
  Producto,
  ProductoCreatePayload,
  ProductoUpdatePayload,
  Tamano,
  TamanoCreatePayload,
  TamanoUpdatePayload,
  IngredienteCategoria,
  IngredienteCategoriaCreatePayload,
  IngredienteCategoriaUpdatePayload,
  Ingrediente,
  IngredienteCreatePayload,
  IngredienteUpdatePayload,
} from '../types'

export interface AdminMenuCatalogosRepository {
  // Categorías
  getCategorias(
    page: number,
    pageSize: number,
    token: string,
    q?: string
  ): Promise<Paginated<Categoria>>
  getCategoriaById(id: number, token: string): Promise<Categoria>
  createCategoria(data: CategoriaCreatePayload, token: string): Promise<{ id: number; message: string }>
  updateCategoria(id: number, data: CategoriaUpdatePayload, token: string): Promise<{ message: string }>
  deleteCategoria(id: number, token: string): Promise<{ message: string }>

  // Subcategorías
  getSubcategorias(
    page: number,
    pageSize: number,
    token: string,
    q?: string,
    idCategoria?: number
  ): Promise<Paginated<Subcategoria>>
  getSubcategoriaById(id: number, token: string): Promise<Subcategoria>
  createSubcategoria(data: SubcategoriaCreatePayload, token: string): Promise<{ id: number; message: string }>
  updateSubcategoria(id: number, data: SubcategoriaUpdatePayload, token: string): Promise<{ message: string }>
  deleteSubcategoria(id: number, token: string): Promise<{ message: string }>

  // Productos
  getProductos(
    page: number,
    pageSize: number,
    token: string,
    q?: string,
    idSubcategoria?: number
  ): Promise<Paginated<Producto>>
  getProductoById(id: number, token: string): Promise<Producto>
  createProducto(data: ProductoCreatePayload, token: string): Promise<{ id: number; message: string }>
  updateProducto(id: number, data: ProductoUpdatePayload, token: string): Promise<{ message: string }>
  deleteProducto(id: number, token: string): Promise<{ message: string }>

  // Tamaños
  getTamanos(
    page: number,
    pageSize: number,
    token: string,
    q?: string
  ): Promise<Paginated<Tamano>>
  getTamanoById(id: number, token: string): Promise<Tamano>
  createTamano(data: TamanoCreatePayload, token: string): Promise<{ id: number; message: string }>
  updateTamano(id: number, data: TamanoUpdatePayload, token: string): Promise<{ message: string }>
  deleteTamano(id: number, token: string): Promise<{ message: string }>

  // Categorías de Ingredientes
  getIngredientesCategorias(
    page: number,
    pageSize: number,
    token: string,
    q?: string
  ): Promise<Paginated<IngredienteCategoria>>
  getIngredienteCategoriaById(id: number, token: string): Promise<IngredienteCategoria>
  createIngredienteCategoria(
    data: IngredienteCategoriaCreatePayload,
    token: string
  ): Promise<{ id: number; message: string }>
  updateIngredienteCategoria(
    id: number,
    data: IngredienteCategoriaUpdatePayload,
    token: string
  ): Promise<{ message: string }>
  deleteIngredienteCategoria(id: number, token: string): Promise<{ message: string }>

  // Ingredientes
  getIngredientes(
    page: number,
    pageSize: number,
    token: string,
    q?: string,
    idCategoria?: number
  ): Promise<Paginated<Ingrediente>>
  getIngredienteById(id: number, token: string): Promise<Ingrediente>
  createIngrediente(data: IngredienteCreatePayload, token: string): Promise<{ id: number; message: string }>
  updateIngrediente(id: number, data: IngredienteUpdatePayload, token: string): Promise<{ message: string }>
  deleteIngrediente(id: number, token: string): Promise<{ message: string }>
}
