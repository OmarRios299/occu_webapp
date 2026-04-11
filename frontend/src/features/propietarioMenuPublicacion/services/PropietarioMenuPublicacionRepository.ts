/**
 * Interfaz del repositorio de publicación de menú
 */
import type {
  PublicarMenuPayload,
  PublicarMenuResponse,
  EstadosActualizacion,
} from '../types'

export interface PropietarioMenuPublicacionRepository {
  publicarMenu(data: PublicarMenuPayload, token: string): Promise<PublicarMenuResponse>
  getEstadosActualizacion(token: string): Promise<EstadosActualizacion>
}
