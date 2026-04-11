/**
 * Tipos para el módulo de administración de plantillas de menú OCCU
 */

export type TipoSeleccion = 'unica' | 'multiple'
export type TipoPrecio = 'por_porcion' | 'fijo'

export interface ProductoListItem {
  id: number
  nombre: string
  subcategoria: string | null
  categoria: string | null
}

export interface ReglaCategoria {
  id_producto: number
  id_ingrediente_categoria: number
  categoria_nombre: string
  tipo_seleccion: TipoSeleccion
  seleccion_minima: number
  seleccion_maxima: number | null
  orden: number
}

export interface ReglaIngrediente {
  id_producto: number
  id_ingrediente: number
  ingrediente_nombre: string
  id_ingrediente_categoria: number
  categoria_nombre: string
  permite_cantidad: boolean
  cantidad_minima: number
  cantidad_maxima: number
  paso_cantidad: number
  cantidad_incluida: number
  tipo_precio: TipoPrecio
  precio_unitario: number
  es_recomendado: boolean
  estado: number
}

export interface CatalogoCategoria {
  id: number
  nombre: string
}

export interface CatalogoIngrediente {
  id: number
  nombre: string
  id_ingrediente_categoria: number
  categoria_nombre: string
}

export interface ProductoReglas {
  categorias: ReglaCategoria[]
  ingredientes: ReglaIngrediente[]
  catalogo_categorias: CatalogoCategoria[]
  catalogo_ingredientes: CatalogoIngrediente[]
}

export interface Paginated<T> {
  items: T[]
  page: number
  pageSize: number
  totalItems: number
  totalPages: number
}

// Payloads para guardar
export interface ReglaCategoriaPayload {
  id_ingrediente_categoria: number
  tipo_seleccion: TipoSeleccion
  seleccion_minima: number
  seleccion_maxima: number | null
  orden: number
}

export interface ReglaIngredientePayload {
  id_ingrediente: number
  permite_cantidad: boolean
  cantidad_minima: number
  cantidad_maxima: number
  paso_cantidad: number
  cantidad_incluida: number
  tipo_precio: TipoPrecio
  precio_unitario: number
  es_recomendado: boolean
}

export interface GuardarReglasPayload {
  categorias: ReglaCategoriaPayload[]
  ingredientes: ReglaIngredientePayload[]
}

export interface GuardarReglasResponse {
  message: string
  categorias_guardadas: number
  ingredientes_guardados: number
}
