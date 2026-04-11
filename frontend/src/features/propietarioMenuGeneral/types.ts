/**
 * Tipos para el módulo de menú general del propietario
 */

export interface Paginated<T> {
  items: T[]
  page: number
  pageSize: number
  totalItems: number
  totalPages: number
}

// Producto en el menú del propietario
export interface ProductoMenuGeneral {
  id: number
  nombre: string
  id_subcategoria: number
  subcategoria_nombre: string | null
  id_categoria: number | null
  categoria_nombre: string | null
  imagen: string
  estado_menu: number // 0=inactivo, 1=activo
  precio_base: number
  id_propietario_menu_producto: number | null
  tamanos?: TamanoProducto[]
}

export interface TamanoProducto {
  id_tamano: number
  tamano_nombre: string
  unidad_medida: string
  medida: string
  precio: number
  estado_tamano: number
  id_propietario_menu_producto_tamano: number | null
}

// Reglas (reutiliza tipos del editor admin)
export type TipoSeleccion = 'unica' | 'multiple'
export type TipoPrecio = 'por_porcion' | 'fijo'

export interface ReglaCategoria {
  id_propietario_menu_producto: number
  id_ingrediente_categoria: number
  categoria_nombre: string
  tipo_seleccion: TipoSeleccion
  seleccion_minima: number
  seleccion_maxima: number | null
  orden: number
}

export interface ReglaIngrediente {
  id_propietario_menu_producto: number
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

// Payloads para actualizar productos
export interface ProductoUpdatePayload {
  id_producto: number
  estado?: number
  precio_base?: number
  tamanos?: TamanoUpdatePayload[]
}

export interface TamanoUpdatePayload {
  id_tamano: number
  precio: number
  estado?: number
}

export interface ProductosUpdatePayload {
  productos: ProductoUpdatePayload[]
}

// Payloads para guardar reglas (reutiliza del editor admin)
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
