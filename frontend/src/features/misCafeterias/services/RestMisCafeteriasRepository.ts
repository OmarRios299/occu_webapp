/**
 * Implementación REST del repositorio de mis cafeterías
 * Consume endpoints privados de la API Slim
 */
import { requestJson, requestFormData } from '@/services/http/httpClient'
import type { MisCafeteriasRepository } from './MisCafeteriasRepository'
import type { CafeteriaOwner, CafeteriaOwnerDetail } from '../types'

type ApiCafeteriaOwnerResponse = {
  id: number
  nombre: string
  imagen: string
  direccion: string
  telefono: string
  correo_electronico?: string
  horario_apertura?: string
  horario_cierre?: string
  horario_diferente?: 'SI' | 'NO'
  horarios_detallados?: Record<string, { apertura: string; cierre: string; cerrado: 'SI' | 'NO' }>
  estado: number
  ciudad?: string
  entidad_federativa?: string
  descripcion?: string
  id_ciudad?: number | null
  latitud?: string
  longitud?: string
}

export class RestMisCafeteriasRepository implements MisCafeteriasRepository {
  async getCafeterias(token: string): Promise<CafeteriaOwner[]> {
    const response = await requestJson<{ success: boolean; data: ApiCafeteriaOwnerResponse[] }>(
      '/owner/cafeterias',
      {
        method: 'GET',
        token,
      }
    )

    return response.data.map((c) => ({
      id: c.id,
      nombre: c.nombre,
      imagen: c.imagen,
      direccion: c.direccion,
      telefono: c.telefono,
      correo: c.correo_electronico,
      horario_apertura: c.horario_apertura,
      horario_cierre: c.horario_cierre,
      estado: c.estado,
      ciudad: c.ciudad,
      entidad_federativa: c.entidad_federativa,
      descripcion: c.descripcion,
    }))
  }

  async getCafeteriaDetail(id: number, token: string): Promise<CafeteriaOwnerDetail> {
    const response = await requestJson<{ success: boolean; data: ApiCafeteriaOwnerResponse }>(
      `/owner/cafeterias/${id}`,
      {
        method: 'GET',
        token,
      }
    )

    const c = response.data
    return {
      id: c.id,
      nombre: c.nombre,
      imagen: c.imagen,
      direccion: c.direccion,
      telefono: c.telefono,
      correo: c.correo_electronico,
      horario_apertura: c.horario_apertura,
      horario_cierre: c.horario_cierre,
      horario_diferente: c.horario_diferente,
      horarios_detallados: c.horarios_detallados,
      estado: c.estado,
      id_ciudad: c.id_ciudad,
      ciudad: c.ciudad,
      entidad_federativa: c.entidad_federativa,
      latitud: c.latitud,
      longitud: c.longitud,
      descripcion: c.descripcion,
    }
  }

  async updateEstado(id: number, estado: 0 | 1, token: string): Promise<void> {
    await requestJson<{ success: boolean }>(`/owner/cafeterias/${id}/estado`, {
      method: 'PATCH',
      body: { estado },
      token,
    })
  }

  async deleteCafeteria(id: number, token: string): Promise<void> {
    await requestJson<{ success: boolean }>(`/owner/cafeterias/${id}`, {
      method: 'DELETE',
      token,
    })
  }

  async getServicios(cafeteriaId: number, token: string): Promise<Array<{ id: number; nombre: string; imagen?: string; servicio_registrado?: number }>> {
    const response = await requestJson<{
      success: boolean
      data: Array<{ id: number; nombre: string; imagen?: string; servicio_registrado?: number }>
    }>(`/owner/cafeterias/${cafeteriaId}/servicios`, {
      method: 'GET',
      token,
    })

    return response.data
  }

  async updateServicios(cafeteriaId: number, servicioIds: number[], token: string): Promise<void> {
    await requestJson<{ success: boolean }>(`/owner/cafeterias/${cafeteriaId}/servicios`, {
      method: 'PUT',
      body: { servicios: servicioIds },
      token,
    })
  }

  async createCafeteria(
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
  ): Promise<{ id: number }> {
    const response = await requestJson<{ success: boolean; data: { id: number } }>('/owner/cafeterias', {
      method: 'POST',
      body: data,
      token,
    })
    return response.data
  }

  async updateCafeteria(
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
  ): Promise<void> {
    await requestJson<{ success: boolean }>(`/owner/cafeterias/${cafeteriaId}`, {
      method: 'PUT',
      body: data,
      token,
    })
  }

  async getCiudadCoordenadas(ciudadId: number): Promise<Array<{ lat: number; lng: number }> | null> {
    const response = await requestJson<{
      success: boolean
      data: { id: number; nombre: string; coordenadas: Array<{ lat: number; lng: number }> | null }
    }>(`/ciudades/${ciudadId}`)
    return response.data.coordenadas
  }

  async uploadImage(file: File, token: string): Promise<{ ruta: string; url: string }> {
    const formData = new FormData()
    formData.append('imagen', file)

    const response = await requestFormData<{ success: boolean; data: { ruta: string; url: string } }>(
      '/owner/cafeterias/upload-image',
      formData,
      {
        method: 'POST',
        token,
      }
    )

    return response.data
  }

  async getGaleriaImagenes(
    cafeteriaId: number,
    token: string
  ): Promise<Array<{ id: number; id_cafeteria: number; imagen: string; descripcion: string; estado: number }>> {
    const response = await requestJson<{
      success: boolean
      data: Array<{ id: number; id_cafeteria: number; imagen: string; descripcion: string; estado: number }>
    }>(`/owner/cafeterias/${cafeteriaId}/galeria`, {
      method: 'GET',
      token,
    })
    return response.data
  }

  async uploadGaleriaImagen(
    cafeteriaId: number,
    file: File,
    token: string
  ): Promise<{ id: number; ruta: string; url: string }> {
    const formData = new FormData()
    formData.append('imagen', file)

    const response = await requestFormData<{
      success: boolean
      data: { id: number; ruta: string; url: string }
    }>(`/owner/cafeterias/${cafeteriaId}/galeria`, formData, {
      method: 'POST',
      token,
    })

    return response.data
  }

  async deleteGaleriaImagen(cafeteriaId: number, imagenId: number, token: string): Promise<void> {
    await requestJson<{ success: boolean }>(`/owner/cafeterias/${cafeteriaId}/galeria/${imagenId}`, {
      method: 'DELETE',
      token,
    })
  }

  async updateGaleriaImagenEstado(
    cafeteriaId: number,
    imagenId: number,
    estado: 0 | 1,
    token: string
  ): Promise<void> {
    await requestJson<{ success: boolean }>(`/owner/cafeterias/${cafeteriaId}/galeria/${imagenId}/estado`, {
      method: 'PATCH',
      body: { estado },
      token,
    })
  }
}
