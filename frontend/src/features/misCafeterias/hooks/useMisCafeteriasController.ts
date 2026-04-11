/**
 * Hook controlador para mis cafeterías
 * Maneja estado, carga de datos y mutaciones
 */
import { useCallback, useEffect, useMemo, useState } from 'react'
import { useAuth } from '@/store/auth/AuthProvider'
import { RestMisCafeteriasRepository } from '../services/RestMisCafeteriasRepository'
import type { CafeteriaOwner, CafeteriaOwnerDetail } from '../types'

export type Servicio = {
  id: number
  nombre: string
  imagen?: string
  servicio_registrado?: number
}

type ControllerState = {
  cafeterias: CafeteriaOwner[]
  selectedCafeteria: CafeteriaOwnerDetail | null
  loading: boolean
  mutating: boolean
  error: string | null
}

type ControllerActions = {
  loadCafeterias: () => Promise<void>
  loadCafeteriaDetail: (id: number) => Promise<void>
  toggleEstado: (id: number, nuevoEstado: 0 | 1) => Promise<void>
  deleteCafeteria: (id: number) => Promise<void>
  clearSelected: () => void
  retry: () => Promise<void>
}

export function useMisCafeteriasController(): {
  state: ControllerState
  actions: ControllerActions
} {
  const { session } = useAuth()
  const repository = useMemo(() => new RestMisCafeteriasRepository(), [])

  const [cafeterias, setCafeterias] = useState<CafeteriaOwner[]>([])
  const [selectedCafeteria, setSelectedCafeteria] = useState<CafeteriaOwnerDetail | null>(null)
  const [loading, setLoading] = useState(false)
  const [mutating, setMutating] = useState(false)
  const [error, setError] = useState<string | null>(null)

  const token = session?.accessToken || ''

  // Cargar cafeterías
  const loadCafeterias = useCallback(async () => {
    if (!token) {
      setError('No autenticado')
      return
    }

    setLoading(true)
    setError(null)

    try {
      const data = await repository.getCafeterias(token)
      setCafeterias(data)
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Error al cargar cafeterías')
      setCafeterias([])
    } finally {
      setLoading(false)
    }
  }, [repository, token])

  // Cargar detalle de cafetería
  const loadCafeteriaDetail = useCallback(
    async (id: number) => {
      if (!token) {
        setError('No autenticado')
        return
      }

      setLoading(true)
      setError(null)

      try {
        const data = await repository.getCafeteriaDetail(id, token)
        setSelectedCafeteria(data)
      } catch (err) {
        setError(err instanceof Error ? err.message : 'Error al cargar detalles')
        setSelectedCafeteria(null)
      } finally {
        setLoading(false)
      }
    },
    [repository, token]
  )

  // Toggle estado
  const toggleEstado = useCallback(
    async (id: number, nuevoEstado: 0 | 1) => {
      if (!token) {
        setError('No autenticado')
        return
      }

      setMutating(true)
      setError(null)

      try {
        await repository.updateEstado(id, nuevoEstado, token)
        // Actualizar estado local sin recargar toda la lista
        setCafeterias((prev) =>
          prev.map((c) => (c.id === id ? { ...c, estado: nuevoEstado } : c))
        )
        // Actualizar también el detalle si está seleccionado
        if (selectedCafeteria?.id === id) {
          setSelectedCafeteria((prev) => (prev ? { ...prev, estado: nuevoEstado } : null))
        }
      } catch (err) {
        setError(err instanceof Error ? err.message : 'Error al actualizar estado')
        throw err // Re-lanzar para que el componente pueda manejar
      } finally {
        setMutating(false)
      }
    },
    [repository, token, selectedCafeteria]
  )

  // Eliminar cafetería
  const deleteCafeteria = useCallback(
    async (id: number) => {
      if (!token) {
        setError('No autenticado')
        return
      }

      setMutating(true)
      setError(null)

      try {
        await repository.deleteCafeteria(id, token)
        // Remover de la lista local
        setCafeterias((prev) => prev.filter((c) => c.id !== id))
        // Limpiar selección si era la eliminada
        if (selectedCafeteria?.id === id) {
          setSelectedCafeteria(null)
        }
      } catch (err) {
        setError(err instanceof Error ? err.message : 'Error al eliminar cafetería')
        throw err // Re-lanzar para que el componente pueda manejar
      } finally {
        setMutating(false)
      }
    },
    [repository, token, selectedCafeteria]
  )

  // Cargar servicios
  const loadServicios = useCallback(
    async (cafeteriaId: number) => {
      if (!token) {
        throw new Error('No autenticado')
      }

      try {
        const data = await repository.getServicios(cafeteriaId, token)
        return data
      } catch (err) {
        throw err instanceof Error ? err : new Error('Error al cargar servicios')
      }
    },
    [repository, token]
  )

  // Guardar servicios
  const saveServicios = useCallback(
    async (cafeteriaId: number, servicioIds: number[]) => {
      if (!token) {
        throw new Error('No autenticado')
      }

      try {
        await repository.updateServicios(cafeteriaId, servicioIds, token)
        // Recargar detalle para reflejar cambios
        if (selectedCafeteria?.id === cafeteriaId) {
          await loadCafeteriaDetail(cafeteriaId)
        }
      } catch (err) {
        throw err instanceof Error ? err : new Error('Error al guardar servicios')
      }
    },
    [repository, token, selectedCafeteria, loadCafeteriaDetail]
  )

  // Cargar cafeterías al montar
  useEffect(() => {
    if (token) {
      loadCafeterias()
    }
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [token]) // loadCafeterias está memoizado con useCallback, pero mejor solo depender de token

  const actions: ControllerActions = {
    loadCafeterias,
    loadCafeteriaDetail,
    toggleEstado,
    deleteCafeteria,
    loadServicios,
    saveServicios,
    clearSelected: useCallback(() => {
      setSelectedCafeteria(null)
      setError(null)
    }, []),
    retry: loadCafeterias,
  }

  return {
    state: {
      cafeterias,
      selectedCafeteria,
      loading,
      mutating,
      error,
    },
    actions,
  }
}
