import { useState, useEffect, useCallback, useRef } from 'react'
import { useSearchParams } from 'react-router-dom'
import type { CafeteriaMapItem, CafeteriasMapaFilters } from '../types'
import type { Paginated, Servicio, Ciudad } from '../../cafeteriasLista/types'
import { RestCafeteriasMapaRepository } from '../services/RestCafeteriasMapaRepository'
import { RestCafeteriasListaRepository } from '../../cafeteriasLista/services/RestCafeteriasListaRepository'

type CafeteriasMapaState = {
  cafeterias: Paginated<CafeteriaMapItem> | null
  servicios: Servicio[]
  ciudades: Ciudad[]
  loading: boolean
  error: string | null
}

type CafeteriasMapaActions = {
  setSearchQuery: (q: string) => void
  setHorario: (horario: 'todos' | 'abierto') => void
  setCiudadId: (ciudadId: number | null) => void
  toggleServicio: (servicioId: number) => void
  setServicioIds: (ids: number[]) => void
  clearFilters: () => void
  applyFilters: () => void
  setSelectedCafeteriaId: (id: number | null) => void
  retry: () => void
}

export function useCafeteriasMapaController() {
  const [searchParams, setSearchParams] = useSearchParams()
  const [state, setState] = useState<CafeteriasMapaState>({
    cafeterias: null,
    servicios: [],
    ciudades: [],
    loading: false,
    error: null,
  })

  const [selectedCafeteriaId, setSelectedCafeteriaId] = useState<number | null>(() => {
    const id = searchParams.get('selected')
    return id ? parseInt(id, 10) : null
  })

  const repository = useRef(new RestCafeteriasMapaRepository())
  const listaRepository = useRef(new RestCafeteriasListaRepository())
  const abortControllerRef = useRef<AbortController | null>(null)
  const debounceTimerRef = useRef<number | null>(null)

  // Parsear filtros desde URL
  const getFiltersFromUrl = useCallback((): CafeteriasMapaFilters & { page: number; pageSize: number } => {
    return {
      q: searchParams.get('q') || undefined,
      ciudadId: searchParams.get('ciudadId') ? parseInt(searchParams.get('ciudadId')!, 10) : null,
      horario: (searchParams.get('horario') as 'todos' | 'abierto') || 'todos',
      servicioIds: searchParams.get('servicios')
        ? searchParams.get('servicios')!.split(',').map((id) => parseInt(id, 10)).filter((id) => !isNaN(id))
        : [],
      page: parseInt(searchParams.get('page') || '1', 10),
      pageSize: parseInt(searchParams.get('pageSize') || '200', 10), // Más items para mapa
    }
  }, [searchParams])

  // Actualizar URL con filtros
  const updateUrl = useCallback((updates: Partial<CafeteriasMapaFilters & { page: number; pageSize: number; selected?: number | null }>) => {
    setSearchParams((prev) => {
      const newParams = new URLSearchParams(prev)
      
      if (updates.q !== undefined) {
        if (updates.q) newParams.set('q', updates.q)
        else newParams.delete('q')
      }
      if (updates.ciudadId !== undefined) {
        if (updates.ciudadId) newParams.set('ciudadId', updates.ciudadId.toString())
        else newParams.delete('ciudadId')
      }
      if (updates.horario !== undefined) {
        newParams.set('horario', updates.horario)
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
      if (updates.selected !== undefined) {
        if (updates.selected) newParams.set('selected', updates.selected.toString())
        else newParams.delete('selected')
      }

      return newParams
    })
  }, [setSearchParams])

  // Cargar cafeterías
  const loadCafeterias = useCallback(async (filters: CafeteriasMapaFilters & { page: number; pageSize: number }) => {
    // Cancelar request anterior
    if (abortControllerRef.current) {
      abortControllerRef.current.abort()
    }
    abortControllerRef.current = new AbortController()

    setState((prev) => ({ ...prev, loading: true, error: null }))

    try {
      const result = await repository.current.getCafeteriasForMap(filters)
      setState((prev) => ({ ...prev, cafeterias: result, loading: false }))
    } catch (err) {
      if (err instanceof Error && err.name === 'AbortError') {
        return // Request cancelado, ignorar
      }
      setState((prev) => ({
        ...prev,
        error: err instanceof Error ? err.message : 'Error al cargar cafeterías',
        loading: false,
      }))
    }
  }, [])

  // Cargar servicios y ciudades
  useEffect(() => {
    let mounted = true

    Promise.all([
      listaRepository.current.getServicios(),
      listaRepository.current.getCiudades(),
    ])
      .then(([servicios, ciudades]) => {
        if (mounted) {
          setState((prev) => ({ ...prev, servicios, ciudades }))
        }
      })
      .catch((err) => {
        if (mounted) {
          console.error('Error cargando servicios/ciudades:', err)
        }
      })

    return () => {
      mounted = false
    }
  }, [])

  // Cargar cafeterías cuando cambian los filtros (con debounce para q)
  useEffect(() => {
    const filters = getFiltersFromUrl()

    // Debounce para búsqueda
    if (debounceTimerRef.current) {
      clearTimeout(debounceTimerRef.current)
    }

    debounceTimerRef.current = window.setTimeout(() => {
      loadCafeterias(filters)
    }, filters.q ? 300 : 0) // 300ms debounce solo si hay búsqueda

    return () => {
      if (debounceTimerRef.current) {
        clearTimeout(debounceTimerRef.current)
      }
    }
  }, [searchParams, getFiltersFromUrl, loadCafeterias])

  // Sincronizar selectedCafeteriaId con URL
  useEffect(() => {
    const id = searchParams.get('selected')
    setSelectedCafeteriaId(id ? parseInt(id, 10) : null)
  }, [searchParams])

  const actions: CafeteriasMapaActions = {
    setSearchQuery: (q: string) => {
      updateUrl({ q, page: 1 })
    },
    setHorario: (horario: 'todos' | 'abierto') => {
      updateUrl({ horario, page: 1 })
    },
    setCiudadId: (ciudadId: number | null) => {
      updateUrl({ ciudadId, page: 1 })
    },
    toggleServicio: (servicioId: number) => {
      const filters = getFiltersFromUrl()
      const currentIds = filters.servicioIds || []
      const newIds = currentIds.includes(servicioId)
        ? currentIds.filter((id) => id !== servicioId)
        : [...currentIds, servicioId]
      updateUrl({ servicioIds: newIds, page: 1 })
    },
    setServicioIds: (ids: number[]) => {
      updateUrl({ servicioIds: ids, page: 1 })
    },
    clearFilters: () => {
      updateUrl({
        q: undefined,
        ciudadId: null,
        horario: 'todos',
        servicioIds: [],
        page: 1,
      })
    },
    applyFilters: () => {
      // Los filtros ya están en URL, solo recargar
      const filters = getFiltersFromUrl()
      loadCafeterias(filters)
    },
    setSelectedCafeteriaId: (id: number | null) => {
      setSelectedCafeteriaId(id)
      updateUrl({ selected: id })
    },
    retry: () => {
      const filters = getFiltersFromUrl()
      loadCafeterias(filters)
    },
  }

  return {
    state,
    actions,
    filters: getFiltersFromUrl(),
    selectedCafeteriaId,
  }
}
