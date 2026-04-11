/**
 * Tipos para el módulo de administración de catálogos de menú OCCU
 */

export interface Paginated<T> {
  items: T[]
  page: number
  pageSize: number
  totalItems: number
  totalPages: number
}

// Categorías
export interface Categoria {
  id: number
  nombre: string
  imagen: string
  estado: number
  es_bebida: string
  id_alta: number
  fecha_alta: string
}

export interface CategoriaCreatePayload {
  nombre: string
  imagen?: string
  estado?: number
  es_bebida?: string
}

export interface CategoriaUpdatePayload {
  nombre?: string
  imagen?: string
  estado?: number
  es_bebida?: string
}

// Subcategorías
export interface Subcategoria {
  id: number
  nombre: string
  id_categoria: number
  categoria_nombre?: string
  estado: number
  registro_occu: number
  id_propietario: number
  fecha_alta: string
}

export interface SubcategoriaCreatePayload {
  nombre: string
  id_categoria: number
  estado?: number
}

export interface SubcategoriaUpdatePayload {
  nombre?: string
  id_categoria?: number
  estado?: number
}

// Productos
export interface Producto {
  id: number
  nombre: string
  id_subcategoria: number
  subcategoria_nombre?: string
  id_categoria?: number
  categoria_nombre?: string
  imagen: string
  estado: number
  registro_occu: number
  id_alta: number
  fecha_alta: string
}

export interface ProductoCreatePayload {
  nombre: string
  id_subcategoria: number
  imagen?: string
  estado?: number
}

export interface ProductoUpdatePayload {
  nombre?: string
  id_subcategoria?: number
  imagen?: string
  estado?: number
}

// Tamaños
export interface Tamano {
  id: number
  nombre: string
  unidad_medida: string
  medida: string
  estado: number
}

export interface TamanoCreatePayload {
  nombre: string
  unidad_medida: string
  medida: string
  estado?: number
}

export interface TamanoUpdatePayload {
  nombre?: string
  unidad_medida?: string
  medida?: string
  estado?: number
}

// Categorías de Ingredientes
export interface IngredienteCategoria {
  id: number
  nombre: string
  para_bebidas: string
  para_alimentos: string
  estado: number
  id_alta: number
  fecha_alta: string
}

export interface IngredienteCategoriaCreatePayload {
  nombre: string
  para_bebidas?: string
  para_alimentos?: string
  estado?: number
}

export interface IngredienteCategoriaUpdatePayload {
  nombre?: string
  para_bebidas?: string
  para_alimentos?: string
  estado?: number
}

// Ingredientes
export interface Ingrediente {
  id: number
  nombre: string
  id_ingrediente_categoria: number
  categoria_nombre?: string
  registro_occu: number
  estado: number
  id_alta: number
  fecha_alta: string
}

export interface IngredienteCreatePayload {
  nombre: string
  id_ingrediente_categoria: number
  estado?: number
}

export interface IngredienteUpdatePayload {
  nombre?: string
  id_ingrediente_categoria?: number
  estado?: number
}

// Respuestas de API
export interface ApiResponse<T> {
  success: boolean
  data: T
}

export interface ApiPaginatedResponse<T> {
  data: T[]
  page: number
  pageSize: number
  totalItems: number
  totalPages: number
}

export interface ApiErrorResponse {
  success: false
  errorCode: string
  message: string
  details?: Record<string, unknown>
}
