/**
 * Tipos para el carrito de compras
 */

export type PersonalizacionItem = {
  id_ingrediente: number
  nombre: string
  cantidad: number
  precio_unitario: number
  monto_total: number
}

export type CarritoItem = {
  item_id: number
  id_producto: number
  id_tamano: number | null
  cantidad: number
  producto_nombre: string
  producto_imagen: string
  tamano_nombre: string | null
  tamano_unidad_medida: string | null
  tamano_medida: string | null
  precio_unitario: number
  subtotal: number
  personalizaciones: PersonalizacionItem[]
}

export type Carrito = {
  carrito_id: number
  id_cafeteria: number
  fecha_carrito: string
  cafeteria_nombre: string
  cafeteria_imagen: string
  items: CarritoItem[]
  total: number
}

export type AgregarAlCarritoPayload = {
  cafeteria_id: number
  producto_id: number
  tamano_id: number | null
  selecciones: Record<number, number> // ingredienteId => cantidad
  cantidad: number
}

export type AgregarAlCarritoResponse = {
  item_id: number
  cantidad_total: number
}
