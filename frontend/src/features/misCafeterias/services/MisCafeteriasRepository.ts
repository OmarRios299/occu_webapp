/**
 * Interface del repositorio de mis cafeterías
 * Permite intercambiar implementaciones (REST vs Legacy)
 */
import type { CafeteriaOwner, CafeteriaOwnerDetail } from '../types'

export interface MisCafeteriasRepository {
  /**
   * Obtiene todas las cafeterías del usuario autenticado
   */
  getCafeterias(token: string): Promise<CafeteriaOwner[]>

  /**
   * Obtiene detalles completos de una cafetería del usuario
   */
  getCafeteriaDetail(id: number, token: string): Promise<CafeteriaOwnerDetail>

  /**
   * Actualiza el estado de una cafetería (0 = activa, 1 = inactiva)
   */
  updateEstado(id: number, estado: 0 | 1, token: string): Promise<void>

  /**
   * Elimina una cafetería (soft delete: estado = 2)
   */
  deleteCafeteria(id: number, token: string): Promise<void>

  /**
   * Obtiene todos los servicios con indicador de si están registrados para la cafetería
   */
  getServicios(cafeteriaId: number, token: string): Promise<Array<{ id: number; nombre: string; imagen?: string; servicio_registrado?: number }>>

  /**
   * Actualiza los servicios de una cafetería (reemplazo total)
   */
  updateServicios(cafeteriaId: number, servicioIds: number[], token: string): Promise<void>

  /**
   * Crea una nueva cafetería
   */
  createCafeteria(data: {
    nombre: string
    correo_electronico?: string
    telefono: string
    direccion: string
    id_ciudad: number
    latitud: string
    longitud: string
    horario_apertura?: string
    horario_cierre?: string
    horario_diferente: 'SI' | 'NO'
    horarios_detallados?: Record<string, { apertura: string; cierre: string; cerrado: 'SI' | 'NO' }>
    descripcion?: string
    imagen?: string
  }, token: string): Promise<{ id: number }>

  /**
   * Actualiza una cafetería existente
   */
  updateCafeteria(
    cafeteriaId: number,
    data: {
      nombre: string
      correo_electronico?: string
      telefono: string
      direccion: string
      id_ciudad: number
      latitud: string
      longitud: string
      horario_apertura?: string
      horario_cierre?: string
      horario_diferente: 'SI' | 'NO'
      horarios_detallados?: Record<string, { apertura: string; cierre: string; cerrado: 'SI' | 'NO' }>
      descripcion?: string
      imagen?: string
    },
    token: string
  ): Promise<void>

  /**
   * Obtiene coordenadas de una ciudad
   */
  getCiudadCoordenadas(ciudadId: number): Promise<Array<{ lat: number; lng: number }> | null>

  /**
   * Sube una imagen y retorna la ruta
   */
  uploadImage(file: File, token: string): Promise<{ ruta: string; url: string }>

  /**
   * Obtiene todas las imágenes de la galería de una cafetería
   */
  getGaleriaImagenes(cafeteriaId: number, token: string): Promise<Array<{ id: number; id_cafeteria: number; imagen: string; descripcion: string; estado: number }>>

  /**
   * Sube una imagen a la galería de una cafetería
   */
  uploadGaleriaImagen(cafeteriaId: number, file: File, token: string): Promise<{ id: number; ruta: string; url: string }>

  /**
   * Elimina una imagen de la galería
   */
  deleteGaleriaImagen(cafeteriaId: number, imagenId: number, token: string): Promise<void>

  /**
   * Actualiza el estado de una imagen de la galería
   */
  updateGaleriaImagenEstado(cafeteriaId: number, imagenId: number, estado: 0 | 1, token: string): Promise<void>
}
