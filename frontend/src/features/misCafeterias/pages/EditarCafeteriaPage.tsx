import { useState, useEffect, useCallback, useMemo } from 'react'
import { useParams, useNavigate } from 'react-router-dom'
import { Box, CircularProgress, Alert } from '@mui/material'
import { designTokens } from '@/theme/tokens'
import { useAuth } from '@/store/auth/AuthProvider'
import { RestMisCafeteriasRepository } from '../services/RestMisCafeteriasRepository'
import { CafeteriaForm, type CafeteriaFormData } from '../components/CafeteriaForm'
import { requestJson } from '@/services/http/httpClient'
import type { CafeteriaOwnerDetail } from '../types'

type Ciudad = {
  id: number
  nombre: string
}

export function EditarCafeteriaPage() {
  const { id } = useParams<{ id: string }>()
  const navigate = useNavigate()
  const { session } = useAuth()
  const repository = useMemo(() => new RestMisCafeteriasRepository(), [])
  const [ciudades, setCiudades] = useState<Ciudad[]>([])
  const [cafeteria, setCafeteria] = useState<CafeteriaOwnerDetail | null>(null)
  const [loading, setLoading] = useState(true)
  const [error, setError] = useState<string | null>(null)

  useEffect(() => {
    if (!id || !session?.accessToken) {
      setError('ID de cafetería inválido o no autenticado')
      setLoading(false)
      return
    }

    const cafeteriaId = parseInt(id, 10)
    if (isNaN(cafeteriaId)) {
      setError('ID de cafetería inválido')
      setLoading(false)
      return
    }

    // Cargar ciudades y cafetería en paralelo
    Promise.all([
      requestJson<{ success: boolean; data: Ciudad[] }>('/ciudades'),
      repository.getCafeteriaDetail(cafeteriaId, session.accessToken),
    ])
      .then(([ciudadesResponse, cafeteriaData]) => {
        setCiudades(ciudadesResponse.data)
        setCafeteria(cafeteriaData)
        setLoading(false)
      })
      .catch((err) => {
        setError(err instanceof Error ? err.message : 'Error al cargar datos')
        setLoading(false)
      })
  }, [id, session?.accessToken, repository])

  const handleLoadCiudadCoordenadas = useCallback(
    async (ciudadId: number): Promise<Array<{ lat: number; lng: number }> | null> => {
      return repository.getCiudadCoordenadas(ciudadId)
    },
    [repository]
  )

  const handleUploadImage = useCallback(
    async (file: File): Promise<{ ruta: string; url: string }> => {
      if (!session?.accessToken) {
        throw new Error('No autenticado')
      }
      return repository.uploadImage(file, session.accessToken)
    },
    [repository, session?.accessToken]
  )

  const handleSubmit = useCallback(
    async (data: CafeteriaFormData) => {
      if (!session?.accessToken || !id) {
        throw new Error('No autenticado o ID inválido')
      }

      const cafeteriaId = parseInt(id, 10)
      // Si imagen es string (ruta), usarla directamente. Si es File, ya debería haberse subido
      const imagenRuta = typeof data.imagen === 'string' ? data.imagen : undefined

      await repository.updateCafeteria(
        cafeteriaId,
        {
          nombre: data.nombre,
          correo_electronico: data.correo_electronico,
          telefono: data.telefono,
          direccion: data.direccion,
          id_ciudad: data.id_ciudad!,
          latitud: data.latitud,
          longitud: data.longitud,
          horario_apertura: data.horario_apertura,
          horario_cierre: data.horario_cierre,
          horario_diferente: data.horario_diferente,
          horarios_detallados: data.horarios_detallados,
          descripcion: data.descripcion,
          imagen: imagenRuta,
        },
        session.accessToken
      )
    },
    [repository, session?.accessToken, id]
  )

  // Memoizar initialData para evitar recrearlo en cada render
  // IMPORTANTE: Los hooks deben estar ANTES de cualquier return condicional
  const initialFormData = useMemo(() => {
    if (!cafeteria) return undefined
    return {
      nombre: cafeteria.nombre,
      correo_electronico: cafeteria.correo,
      telefono: cafeteria.telefono,
      direccion: cafeteria.direccion,
      id_ciudad: cafeteria.id_ciudad || null,
      latitud: cafeteria.latitud || '',
      longitud: cafeteria.longitud || '',
      horario_apertura: cafeteria.horario_apertura,
      horario_cierre: cafeteria.horario_cierre,
      horario_diferente: cafeteria.horario_diferente || 'NO',
      horarios_detallados: cafeteria.horarios_detallados,
      descripcion: cafeteria.descripcion,
    }
  }, [cafeteria])

  if (loading) {
    return (
      <Box sx={{ display: 'flex', justifyContent: 'center', py: 8 }}>
        <CircularProgress />
      </Box>
    )
  }

  if (error) {
    return (
      <Box sx={{ p: 3 }}>
        <Alert severity="error">{error}</Alert>
      </Box>
    )
  }

  if (!cafeteria) {
    return (
      <Box sx={{ p: 3 }}>
        <Alert severity="error">Cafetería no encontrada</Alert>
      </Box>
    )
  }

  return (
    <Box sx={{ minHeight: '100vh', backgroundColor: designTokens.colors.bgBody, py: 4 }}>
      <CafeteriaForm
        cafeteriaId={parseInt(id!, 10)}
        initialData={initialFormData}
        ciudades={ciudades}
        onLoadCiudadCoordenadas={handleLoadCiudadCoordenadas}
        onUploadImage={handleUploadImage}
        imagenUrl={cafeteria.imagen}
        onSubmit={handleSubmit}
      />
    </Box>
  )
}
