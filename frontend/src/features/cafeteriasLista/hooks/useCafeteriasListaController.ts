/**
 * Hook controlador para la lista de cafeterías
 * Maneja estado, query params, debounce y orquesta la carga de datos
 */
import { useCallback, useEffect, useMemo, useRef, useState } from 'react'
import { useSearchParams } from 'react-router-dom'
import { RestCafeteriasListaRepository } from '../services/RestCafeteriasListaRepository'
import type { CafeteriaListItem, CafeteriasListaFilters, Paginated, Servicio, Ciudad } from '../types'

const PAGE_SIZE = 9 // Mismo que legacy
const DEBOUNCE_MS = 500

type ControllerState = {
  cafeterias: Paginated<CafeteriaListItem> | null
  servicios: Servicio[]
  ciudades: Ciudad[]
  loading: boolean
  error: string | null
}

type ControllerActions = {
  setSearchQuery: (q: string) => void
  setCiudadId: (id: number | null) => void
  setHorario: (horario: 'todos' | 'abierto') => void
  setServicioIds: (ids: number[]) => void
  toggleServicio: (id: number) => void
  setPage: (page: number) => void
  clearFilters: () => void
  applyFilters: () => void
  retry: () => void
}

export function useCafeteriasListaController(): {
  state: ControllerState
  actions: ControllerActions
  filters: CafeteriasListaFilters & { page: number; pageSize: number }
} {
  const [searchParams, setSearchParams] = useSearchParams()
  const repository = useMemo(() => new RestCafeteriasListaRepository(), [])

  // Estado
  const [cafeterias, setCafeterias] = useState<Paginated<CafeteriaListItem> | null>(null)
  const [servicios, setServicios] = useState<Servicio[]>([])
  const [ciudades, setCiudades] = useState<Ciudad[]>([])
  const [loading, setLoading] = useState(false)
  const [error, setError] = useState<string | null>(null)

  // Filtros desde URL (fuente de verdad)
  const filters = useMemo<CafeteriasListaFilters & { page: number; pageSize: number }>(() => {
    const q = searchParams.get('q') || undefined
    const ciudadIdParam = searchParams.get('ciudadId')
    const ciudadId = ciudadIdParam ? parseInt(ciudadIdParam, 10) : null
    const horario = (searchParams.get('horario') as 'todos' | 'abierto') || 'todos'
    const serviciosParam = searchParams.get('servicios')
    const servicioIds = serviciosParam ? serviciosParam.split(',').map(Number) : []
    const page = parseInt(searchParams.get('page') || '1', 10)
    const pageSize = parseInt(searchParams.get('pageSize') || PAGE_SIZE.toString(), 10)

    return {
      q,
      ciudadId: ciudadId || undefined,
      horario,
      servicioIds: servicioIds.length > 0 ? servicioIds : undefined,
      page,
      pageSize,
    }
  }, [searchParams])

  // Refs para debounce y cancelación
  const debounceTimerRef = useRef<NodeJS.Timeout | null>(null)
  const abortControllerRef = useRef<AbortController | null>(null)

  // Función para actualizar query params
  const updateSearchParams = useCallback(
    (updates: Partial<CafeteriasListaFilters & { page: number; pageSize: number }>) => {
      setSearchParams((prev) => {
        const newParams = new URLSearchParams(prev)
        
        // Resetear página a 1 si cambian filtros (excepto si es el mismo cambio de página)
        if (updates.q !== undefined || updates.ciudadId !== undefined || 
            updates.horario !== undefined || updates.servicioIds !== undefined) {
          newParams.set('page', '1')
        }

        if (updates.q !== undefined) {
          if (updates.q) {
            newParams.set('q', updates.q)
          } else {
            newParams.delete('q')
          }
        }

        if (updates.ciudadId !== undefined) {
          if (updates.ciudadId) {
            newParams.set('ciudadId', updates.ciudadId.toString())
          } else {
            newParams.delete('ciudadId')
          }
        }

        if (updates.horario !== undefined) {
          if (updates.horario === 'todos') {
            newParams.delete('horario')
          } else {
            newParams.set('horario', updates.horario)
          }
        }

        if (updates.servicioIds !== undefined) {
          if (updates.servicioIds.length > 0) {
            newParams.set('servicios', updates.servicioIds.join(','))
          } else {
            newParams.delete('servicios')
          }
        }

        if (updates.page !== undefined) {
          newParams.set('page', updates.page.toString())
        }

        if (updates.pageSize !== undefined) {
          newParams.set('pageSize', updates.pageSize.toString())
        }

        return newParams
      })
    },
    [setSearchParams]
  )

  // Cargar cafeterías
  const loadCafeterias = useCallback(async () => {
    // Cancelar request anterior
    if (abortControllerRef.current) {
      abortControllerRef.current.abort()
    }

    abortControllerRef.current = new AbortController()
    setLoading(true)
    setError(null)

    try {
      const result = await repository.getCafeterias(filters)
      setCafeterias(result)
    } catch (err) {
      if (err instanceof Error && err.name === 'AbortError') {
        return // Request cancelado, ignorar
      }
      setError(err instanceof Error ? err.message : 'Error al cargar cafeterías')
      setCafeterias(null)
    } finally {
      setLoading(false)
    }
  }, [repository, filters])

  // Cargar servicios y ciudades (solo una vez)
  useEffect(() => {
    Promise.all([repository.getServicios(), repository.getCiudades()])
      .then(([serviciosData, ciudadesData]) => {
        setServicios(serviciosData)
        setCiudades(ciudadesData)
      })
      .catch((err) => {
        console.error('Error al cargar servicios/ciudades:', err)
      })
  }, [repository])

  // Cargar cafeterías cuando cambian los filtros (con debounce para búsqueda)
  useEffect(() => {
    // Limpiar timer anterior
    if (debounceTimerRef.current) {
      clearTimeout(debounceTimerRef.current)
    }

    // Si hay búsqueda, aplicar debounce
    if (filters.q) {
      debounceTimerRef.current = setTimeout(() => {
        loadCafeterias()
      }, DEBOUNCE_MS)
    } else {
      // Sin búsqueda, cargar inmediatamente
      loadCafeterias()
    }

    return () => {
      if (debounceTimerRef.current) {
        clearTimeout(debounceTimerRef.current)
      }
      if (abortControllerRef.current) {
        abortControllerRef.current.abort()
      }
    }
  }, [filters, loadCafeterias])

  // Actions
  const actions: ControllerActions = {
    setSearchQuery: useCallback(
      (q: string) => {
        updateSearchParams({ q })
      },
      [updateSearchParams]
    ),

    setCiudadId: useCallback(
      (id: number | null) => {
        updateSearchParams({ ciudadId: id || undefined })
      },
      [updateSearchParams]
    ),

    setHorario: useCallback(
      (horario: 'todos' | 'abierto') => {
        updateSearchParams({ horario })
      },
      [updateSearchParams]
    ),

    setServicioIds: useCallback(
      (ids: number[]) => {
        updateSearchParams({ servicioIds: ids })
      },
      [updateSearchParams]
    ),

    toggleServicio: useCallback(
      (id: number) => {
        const currentIds = filters.servicioIds || []
        const newIds = currentIds.includes(id)
          ? currentIds.filter((i) => i !== id)
          : [...currentIds, id]
        updateSearchParams({ servicioIds: newIds })
      },
      [filters.servicioIds, updateSearchParams]
    ),

    setPage: useCallback(
      (page: number) => {
        updateSearchParams({ page })
      },
      [updateSearchParams]
    ),

    clearFilters: useCallback(() => {
      setSearchParams({})
    }, [setSearchParams]),

    applyFilters: useCallback(() => {
      // Los filtros ya están en la URL, solo recargar
      loadCafeterias()
    }, [loadCafeterias]),

    retry: useCallback(() => {
      loadCafeterias()
    }, [loadCafeterias]),
  }

  return {
    state: {
      cafeterias,
      servicios,
      ciudades,
      loading,
      error,
    },
    actions,
    filters,
  }
}
