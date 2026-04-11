/**
 * Implementación REST del repositorio de publicación de menú
 */
import { requestJson } from '@/services/http/httpClient'
import type { PropietarioMenuPublicacionRepository } from './PropietarioMenuPublicacionRepository'
import type {
  PublicarMenuPayload,
  PublicarMenuResponse,
  EstadosActualizacion,
  ApiResponse,
} from '../types'

export class RestPropietarioMenuPublicacionRepository implements PropietarioMenuPublicacionRepository {
  async publicarMenu(data: PublicarMenuPayload, token: string): Promise<PublicarMenuResponse> {
    const response = await requestJson<ApiResponse<PublicarMenuResponse>>('/owner/menu-publicar', {
      method: 'POST',
      body: data,
      token,
    })
    return response.data
  }

  async getEstadosActualizacion(token: string): Promise<EstadosActualizacion> {
    const response = await requestJson<ApiResponse<EstadosActualizacion>>('/owner/cafeterias/estado-menu', {
      token,
    })
    return response.data
  }
}
