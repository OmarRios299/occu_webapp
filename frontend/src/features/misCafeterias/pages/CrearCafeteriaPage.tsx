import { useState, useEffect, useCallback, useMemo } from 'react'
import { Box, CircularProgress, Alert } from '@mui/material'
import { designTokens } from '@/theme/tokens'
import { useAuth } from '@/store/auth/AuthProvider'
import { RestMisCafeteriasRepository } from '../services/RestMisCafeteriasRepository'
import { CafeteriaForm, type CafeteriaFormData } from '../components/CafeteriaForm'
import { requestJson } from '@/services/http/httpClient'

type Ciudad = {
  id: number
  nombre: string
}

export function CrearCafeteriaPage() {
  const { session } = useAuth()
  const repository = useMemo(() => new RestMisCafeteriasRepository(), [])
  const [ciudades, setCiudades] = useState<Ciudad[]>([])
  const [loading, setLoading] = useState(true)
  const [error, setError] = useState<string | null>(null)

  useEffect(() => {
    // Cargar ciudades
    requestJson<{ success: boolean; data: Ciudad[] }>('/ciudades')
      .then((response) => {
        setCiudades(response.data)
        setLoading(false)
      })
      .catch((err) => {
        setError(err instanceof Error ? err.message : 'Error al cargar ciudades')
        setLoading(false)
      })
  }, [])

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
      if (!session?.accessToken) {
        throw new Error('No autenticado')
      }

      // Si imagen es string (ruta), usarla directamente. Si es File, ya debería haberse subido
      const imagenRuta = typeof data.imagen === 'string' ? data.imagen : undefined

      await repository.createCafeteria(
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
    [repository, session?.accessToken]
  )

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

  return (
    <Box sx={{ minHeight: '100vh', backgroundColor: designTokens.colors.bgBody, py: 4 }}>
      <CafeteriaForm
        ciudades={ciudades}
        onLoadCiudadCoordenadas={handleLoadCiudadCoordenadas}
        onUploadImage={handleUploadImage}
        onSubmit={handleSubmit}
      />
    </Box>
  )
}
