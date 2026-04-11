/**
 * Tipos para el menú público de cafeterías
 */

export type ProductoMenu = {
  id: number
  nombre: string
  imagen: string
  precio_base: number
  id_cafeteria_menu_producto: number
}

export type SubcategoriaMenu = {
  id: number
  nombre: string
  productos: ProductoMenu[]
}

export type CategoriaMenu = {
  id: number
  nombre: string
  subcategorias: SubcategoriaMenu[]
}

export type TamanoProducto = {
  id: number
  nombre: string
  unidad_medida: string
  medida: string
  precio: number
}

export type IngredienteRegla = {
  id: number
  nombre: string
  permite_cantidad: boolean
  cantidad_minima: number
  cantidad_maxima: number
  paso_cantidad: number
  cantidad_incluida: number
  tipo_precio: 'por_porcion' | 'fijo'
  precio_unitario: number
  es_recomendado: boolean
  agotado: boolean
}

export type ReglaCategoria = {
  id: number
  nombre: string
  tipo_seleccion: 'unica' | 'multiple'
  seleccion_minima: number
  seleccion_maxima: number | null
  orden: number
  ingredientes: IngredienteRegla[]
}

export type ProductoDetalle = {
  id: number
  nombre: string
  imagen: string
  precio_base: number
  id_cafeteria_menu_producto: number
  tamanos: TamanoProducto[]
  reglas_categorias: ReglaCategoria[]
}

/**
 * Selección de personalización del usuario
 */
export type SeleccionIngrediente = {
  ingredienteId: number
  cantidad: number
}

export type PersonalizacionProducto = {
  tamanoId?: number | null
  selecciones: SeleccionIngrediente[]
}
